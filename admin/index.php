<?php
require __DIR__ . '/../config.php';

// Simple Admin Check
if (!is_logged_in() || ($_SESSION['user_role'] ?? 'user') !== 'admin') {
    die("Acesso negado. Apenas administradores.");
}

// Fetch stats
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalKeys = $pdo->query("SELECT COUNT(*) FROM product_keys WHERE status = 'available'")->fetchColumn();
$totalSold = $pdo->query("SELECT COUNT(*) FROM product_keys WHERE status = 'sold'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 text-white">Admin Dashboard</h1>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass-panel text-center">
                <h3 class="h1 fw-bold text-accent"><?= $totalOrders ?></h3>
                <p class="text-white-50">Pedidos Totais</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-panel text-center">
                <h3 class="h1 fw-bold text-success"><?= $totalKeys ?></h3>
                <p class="text-white-50">Keys em Estoque</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-panel text-center">
                <h3 class="h1 fw-bold text-white"><?= $totalSold ?></h3>
                <p class="text-white-50">Keys Vendidas</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <a href="admin/products.php" class="glass-panel d-flex align-items-center gap-3 text-decoration-none h-100">
                <div class="bg-dark rounded-circle p-3">📦</div>
                <div>
                    <h3 class="h5 text-white mb-1">Gerenciar Produtos</h3>
                    <p class="text-white-50 small mb-0">Criar, editar e listar produtos.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="admin/keys.php" class="glass-panel d-flex align-items-center gap-3 text-decoration-none h-100">
                <div class="bg-dark rounded-circle p-3">🔑</div>
                <div>
                    <h3 class="h5 text-white mb-1">Importar Keys</h3>
                    <p class="text-white-50 small mb-0">Upload de CSV para estoque.</p>
                </div>
            </a>
        </div>
    </div>

  </main>
</body>
</html>
