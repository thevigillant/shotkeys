<?php
require __DIR__ . '/config.php';
require_login();

$stmt = $pdo->prepare("
  SELECT id, status, total_cents, created_at
  FROM orders
  WHERE user_id = ?
  ORDER BY id DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Meus pedidos</title></head>
<body>
<h1>Meus pedidos</h1>

<?php if (!$orders): ?>
  <p>Você ainda não tem pedidos.</p>
<?php endif; ?>

<ul>
<?php foreach ($orders as $o): ?>
  <li>
    <a href="pedido.php?id=<?= (int)$o['id'] ?>">Pedido #<?= (int)$o['id'] ?></a>
    — <?= htmlspecialchars($o['status']) ?>
    — R$ <?= number_format(((int)$o['total_cents'])/100, 2, ',', '.') ?>
    — <?= htmlspecialchars($o['created_at']) ?>
  </li>
<?php endforeach; ?>
</ul>

<p><a href="produtos.php">Voltar ao catálogo</a></p>
</body>
</html>
