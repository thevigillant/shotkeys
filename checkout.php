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
    // Generate a Simulate Gateway ID
    $gatewayId = 'PAY-' . strtoupper(bin2hex(random_bytes(8)));

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total_cents, payment_gateway_id) VALUES (?, 'PENDING', ?, ?)");
    $stmt->execute([$_SESSION['user_id'], (int)$product['price_cents'], $gatewayId]);
    $order_id = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare("
      INSERT INTO order_items (order_id, product_id, title, quantity, unit_price_cents, type)
      VALUES (?, ?, ?, 1, ?, ?)
    ");
    $stmt->execute([$order_id, (int)$product['id'], $product['title'], (int)$product['price_cents'], $product['type']]);

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout | ShotKeys</title>

  <!-- Base URL -->
  <base href="https://shotkeys.store" />

  <!-- FavIcon -->
  <link rel="icon" href="assets/icons/favicon/logo-Shot-Keys.ico" type="image/x-icon" />

  <!-- Google Fonts -->
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Momo+Trust+Display&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap");
  </style>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

  <!-- Navbar Global -->
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="glass-panel text-center">
          <div class="mb-4">
             <h1 class="archivofont text-uppercase mb-1">Checkout</h1>
             <p class="text-white-50">Confirme os detalhes do seu pedido</p>
          </div>

          <!-- Product Summary -->
          <div class="bg-dark-transparent p-4 rounded-3 mb-4 text-start">
             <span class="badge bg-custom mb-3">Item do Pedido #<?= $order_id ?></span>
             <h2 class="h4 fw-bold text-white mb-2"><?= htmlspecialchars($product['title']) ?></h2>
             <p class="text-white-50 small mb-0"><?= htmlspecialchars($product['description']) ?></p>
             
             <hr class="border-secondary opacity-25 my-3">
             
             <div class="d-flex justify-content-between align-items-center">
                <span class="text-white">Total a pagar:</span>
                <span class="fs-3 fw-bold text-accent">R$ <?= $price ?></span>
             </div>
          </div>

          <form method="POST" action="pay_simulate.php">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <button type="submit" class="btn btn-custom w-100 btn-lg shadow-lg">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
               Pagar Agora (Simulado)
            </button>
          </form>

          <div class="mt-4">
            <a href="meus_pedidos.php" class="text-white-50 text-decoration-none small hover-link">
               &larr; Cancelar e voltar para Meus Pedidos
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
