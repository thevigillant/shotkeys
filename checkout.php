<?php
require __DIR__ . '/config.php';
require_login();

$slug = $_GET['product'] ?? '';
if ($slug === '') {
  http_response_code(400);
  exit('Produto inválido.');
}

// Busca produto e valida tipo
$stmt = $pdo->prepare("
  SELECT id, slug, title, description, type, price_cents
  FROM products
  WHERE slug = ? AND status = 'ACTIVE'
  LIMIT 1
");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
  http_response_code(404);
  exit('Produto não encontrado.');
}

if (!in_array($product['type'], ['OWN_KEY', 'RANDOM_BOX'], true)) {
  http_response_code(400);
  exit('Este produto não possui checkout interno.');
}

if (empty($product['price_cents'])) {
  http_response_code(500);
  exit('Preço não configurado.');
}

// Cria pedido + item (idempotência simples via sessão)
if (!isset($_SESSION['pending_order_for']) || $_SESSION['pending_order_for'] !== $slug) {
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total_cents) VALUES (?, 'PENDING', ?)");
    $stmt->execute([$_SESSION['user_id'], (int)$product['price_cents']]);
    $order_id = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare("
      INSERT INTO order_items (order_id, product_id, quantity, unit_price_cents)
      VALUES (?, ?, 1, ?)
    ");
    $stmt->execute([$order_id, (int)$product['id'], (int)$product['price_cents']]);

    $pdo->commit();

    $_SESSION['pending_order_for'] = $slug;
    $_SESSION['pending_order_id'] = $order_id;
  } catch (Throwable $e) {
    $pdo->rollBack();
    error_log("CHECKOUT ERROR: " . $e->getMessage());
    http_response_code(500);
    exit('Erro ao criar pedido.');
  }
}

$order_id = (int)($_SESSION['pending_order_id'] ?? 0);
if ($order_id <= 0) {
  http_response_code(500);
  exit('Pedido pendente inválido.');
}

$price = number_format(((int)$product['price_cents']) / 100, 2, ',', '.');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Checkout | ShotKeys</title>
</head>
<body>
  <h1>Checkout</h1>

  <h2><?= htmlspecialchars($product['title']) ?></h2>
  <p><?= htmlspecialchars($product['description']) ?></p>
  <p><strong>Total: R$ <?= $price ?></strong></p>

  <p>Pedido: #<?= $order_id ?> (status: PENDING)</p>

  <form method="POST" action="pay_simulate.php">
    <input type="hidden" name="order_id" value="<?= $order_id ?>">
    <button type="submit">Pagar (simulado)</button>
  </form>

  <p style="margin-top:16px;">
    <a href="meus_pedidos.php">Ver meus pedidos</a>
  </p>
</body>
</html>
