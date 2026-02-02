<?php
require __DIR__ . '/config.php';
require_login();

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
  http_response_code(400);
  exit('Pedido inválido.');
}

$stmt = $pdo->prepare("SELECT id, status, total_cents, created_at FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
  http_response_code(404);
  exit('Pedido não encontrado.');
}

$stmt = $pdo->prepare("
  SELECT p.title, p.type, oi.quantity, oi.unit_price_cents
  FROM order_items oi
  JOIN products p ON p.id = oi.product_id
  WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Pedido #<?= (int)$order['id'] ?></title></head>
<body>
<h1>Pedido #<?= (int)$order['id'] ?></h1>

<p>Status: <strong><?= htmlspecialchars($order['status']) ?></strong></p>
<p>Total: R$ <?= number_format(((int)$order['total_cents'])/100, 2, ',', '.') ?></p>
<p>Criado em: <?= htmlspecialchars($order['created_at']) ?></p>

<h2>Itens</h2>
<ul>
<?php foreach ($items as $it): ?>
  <li>
    <?= htmlspecialchars($it['title']) ?>
    (<?= htmlspecialchars($it['type']) ?>)
    — <?= (int)$it['quantity'] ?>x
    — R$ <?= number_format(((int)$it['unit_price_cents'])/100, 2, ',', '.') ?>
  </li>
<?php endforeach; ?>
</ul>

<?php if ($order['status'] === 'PAID'): ?>
  <p><strong>Pagamento aprovado.</strong> (Próximo passo: entrega automática)</p>
<?php else: ?>
  <p>Aguardando pagamento.</p>
<?php endif; ?>

<p><a href="meus_pedidos.php">Voltar</a></p>
</body>
</html>
