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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
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
    <div class="glass-panel">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 archivofont mb-0 text-white">Meus Pedidos</h1>
        <a href="produtos.php" class="btn btn-sm btn-outline-light rounded-pill">Novo Pedido</a>
      </div>

      <?php if (!$orders): ?>
        <div class="text-center py-5">
           <p class="text-white-50 mb-4">Você ainda não realizou nenhuma compra.</p>
           <a href="produtos.php" class="btn btn-custom">Explorar Loja</a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
           <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
             <thead>
               <tr class="text-uppercase small text-white-50">
                 <th scope="col" class="py-3">Pedido #</th>
                 <th scope="col" class="py-3">Data</th>
                 <th scope="col" class="py-3">Total</th>
                 <th scope="col" class="py-3">Status</th>
                 <th scope="col" class="py-3 text-end">Ações</th>
               </tr>
             </thead>
             <tbody class="border-top-0">
               <?php foreach ($orders as $o): ?>
                 <?php 
                   $statusClass = match($o['status']) {
                       'PAID' => 'text-success',
                       'PENDING'  => 'text-warning',
                       default    => 'text-secondary'
                   };
                   $statusLabel = match($o['status']) {
                       'PAID' => 'Pago',
                       'PENDING'  => 'Pendente',
                       default    => $o['status']
                   };
                 ?>
                 <tr>
                   <td class="py-3 fw-bold text-white">#<?= (int)$o['id'] ?></td>
                   <td class="py-3 text-white-50"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                   <td class="py-3 text-white">R$ <?= number_format(((int)$o['total_cents'])/100, 2, ',', '.') ?></td>
                   <td class="py-3">
                     <span class="badge bg-dark border border-secondary <?= $statusClass ?> bg-opacity-50 rounded-pill px-3">
                       <?= $statusLabel ?>
                     </span>
                   </td>
                   <td class="py-3 text-end">
                     <a href="pedido.php?id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-light rounded-pill">
                       Ver Detalhes
                     </a>
                   </td>
                 </tr>
               <?php endforeach; ?>
             </tbody>
           </table>
        </div>
      <?php endif; ?>
      
    </div>
    
    <div class="mt-4 text-center">
       <a href="dashboard.php" class="text-white-50 text-decoration-none small">&larr; Voltar ao Painel</a>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
