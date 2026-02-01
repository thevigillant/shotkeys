<?php
require __DIR__ . '/config.php';

$stmt = $pdo->query("
  SELECT id, slug, title, description, type, price_cents, affiliate_url
  FROM products
  WHERE status = 'ACTIVE'
  ORDER BY created_at DESC
");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Produtos | ShotKeys</title>
</head>
<body>

<h1>Catálogo</h1>

<?php foreach ($products as $p): ?>
  <div style="border:1px solid #ccc; padding:16px; margin-bottom:16px;">
    <h2><?= htmlspecialchars($p['title']) ?></h2>
    <p><?= htmlspecialchars($p['description']) ?></p>

    <?php if ($p['type'] === 'AFFILIATE'): ?>
      <a href="<?= htmlspecialchars($p['affiliate_url']) ?>" target="_blank">
        Comprar na loja parceira
      </a>

    <?php elseif ($p['type'] === 'RANDOM_BOX'): ?>
      <strong>R$ <?= number_format($p['price_cents']/100, 2, ',', '.') ?></strong><br>
      <a href="checkout.php?product=<?= $p['slug'] ?>">
        Comprar Random Key
      </a>

    <?php else: ?>
      <strong>Produto próprio</strong>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

</body>
</html>
