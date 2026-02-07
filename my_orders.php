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
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Meus Pedidos | ShotKeys</title>
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

  <style>
    .order-item {
      transition: transform 0.2s ease, background 0.2s ease;
      cursor: pointer;
      text-decoration: none;
      display: block;
    }
    .order-item:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.08) !important;
      border-color: var(--color-accent) !important;
    }
    .status-badge {
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      font-weight: 700;
    }
  </style>
</head>
<body>

  <!-- Navbar Global -->
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
      <div>
        <h1 class="h2 archivofont mb-1 text-white">HISTÓRICO DE PEDIDOS</h1>
        <p class="text-white-50 mb-0">Gerencie suas compras e acesse suas chaves.</p>
      </div>
      <a href="products.php" class="btn btn-custom shadow-lg">
        <span class="me-2">+</span>Novo Pedido
      </a>
    </div>

    <?php if (!$orders): ?>
      <div class="glass-panel text-center py-5">
          <div class="mb-3 opacity-50">
             <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          </div>
          <h3 class="h4 text-white mb-3">Você ainda não tem nenhum loot!</h3>
          <p class="text-white-50 mb-4" style="max-width: 400px; margin: 0 auto;">Assim que você garantir sua primeira key ou random box, ela aparecerá aqui instantaneamente.</p>
          <a href="products.php" class="btn btn-outline-light rounded-pill px-4">Explorar Loja</a>
      </div>
    <?php else: ?>
      
      <div class="d-flex flex-column gap-3">
        <?php foreach ($orders as $o): ?>
          <?php 
            $statusClass = match($o['status']) {
                'PAID' => 'bg-success text-white',
                'PENDING'  => 'bg-warning text-dark',
                default    => 'bg-secondary text-white'
            };
            $statusLabel = match($o['status']) {
                'PAID' => 'Aprovado',
                'PENDING'  => 'Pendente',
                default    => $o['status']
            };
            $borderClass = $o['status'] === 'PENDING' ? 'border-warning' : 'border-secondary';
          ?>
          
          <a href="order_details.php?id=<?= (int)$o['id'] ?>" class="order-item glass-panel p-4 rounded-4 border border-opacity-25 <?= $borderClass ?>" style="min-height: auto;">
            <div class="row align-items-center g-3">
              
              <!-- ID e Data -->
              <div class="col-6 col-md-2">
                 <span class="d-block text-white-50 small mb-1">PEDIDO</span>
                 <span class="h5 fw-bold text-white mb-0">#<?= str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT) ?></span>
              </div>
              
              <div class="col-6 col-md-3">
                 <span class="d-block text-white-50 small mb-1">DATA</span>
                 <span class="text-white"><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></span>
              </div>

              <!-- Valor -->
              <div class="col-6 col-md-3">
                 <span class="d-block text-white-50 small mb-1">TOTAL</span>
                 <span class="fw-bold text-accent h5 mb-0">R$ <?= number_format(((int)$o['total_cents'])/100, 2, ',', '.') ?></span>
              </div>

              <!-- Status -->
              <div class="col-6 col-md-2 text-md-center">
                 <span class="status-badge badge rounded-pill px-3 py-2 <?= $statusClass ?> bg-opacity-75">
                    <?= $statusLabel ?>
                 </span>
              </div>

              <!-- Icone Seta -->
              <div class="col-12 col-md-2 text-end d-none d-md-block">
                 <div class="btn btn-icon btn-sm btn-outline-light rounded-circle p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                 </div>
              </div>

            </div>
          </a>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>
    
    <div class="mt-5 text-center">
       <a href="dashboard.php" class="text-white-50 text-decoration-none small hover-link">
         &larr; Voltar para o Dashboard
       </a>
    </div>

  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
