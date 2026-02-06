<?php
require __DIR__ . '/config.php';
require_login();

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
  http_response_code(400);
  exit('Pedido inválido.');
}

$stmt = $pdo->prepare("SELECT id, status, total_cents, created_at, payment_gateway_id FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
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
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Pedido #<?= (int)$order['id'] ?> | ShotKeys</title>
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
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="glass-panel">
          <!-- Header do Pedido -->
          <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary border-opacity-25 pb-4 mb-4">
             <div>
                <h1 class="h3 archivofont text-white mb-1">Pedido #<?= (int)$order['id'] ?></h1>
                <span class="text-white-50 small">
                  Criado em <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                </span>
             </div>
             
             <?php 
               $statusClass = match($order['status']) {
                   'PAID' => 'bg-success',
                   'PENDING'  => 'bg-warning text-dark',
                   default    => 'bg-secondary'
               };
               $statusLabel = match($order['status']) {
                   'PAID' => 'Pago / Aprovado',
                   'PENDING'  => 'Aguardando Pagamento',
                   default    => $order['status']
               };
             ?>
             <span class="badge <?= $statusClass ?> bg-opacity-75 rounded-pill px-3 py-2 fs-6">
                <?= $statusLabel ?>
             </span>
          </div>

          <!-- Lista de Itens -->
          <h2 class="h5 text-white mb-3 fw-bold">Itens do Pedido</h2>
          <div class="d-flex flex-column gap-3 mb-4">
            <?php foreach ($items as $it): ?>
              <div class="bg-dark-transparent p-3 rounded-3 d-flex justify-content-between align-items-center">
                 <div>
                    <strong class="d-block text-white"><?= htmlspecialchars($it['title']) ?></strong>
                    <span class="badge bg-secondary opacity-75 small"><?= htmlspecialchars($it['type']) ?></span>
                    <span class="text-white-50 small ms-2">Qtd: <?= (int)$it['quantity'] ?></span>
                 </div>
                 <div class="fw-bold text-accent">
                    R$ <?= number_format(((int)$it['unit_price_cents'])/100, 2, ',', '.') ?>
                 </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Total e Resumo -->
          <div class="d-flex justify-content-between align-items-center border-top border-secondary border-opacity-25 pt-4">
             <span class="text-white h5">Total</span>
             <span class="text-accent h3 archivofont">
                R$ <?= number_format(((int)$order['total_cents'])/100, 2, ',', '.') ?>
             </span>
          </div>

          <!-- Rodapé de Status e Ação -->
          <div class="mt-4 p-3 rounded-3 bg-dark-transparent border border-secondary border-opacity-10 text-center">
             <?php if ($order['status'] === 'PAID' || $order['status'] === 'DELIVERED'): ?>
              <div class="bg-success bg-opacity-10 border border-success border-opacity-25 p-4 rounded-3 animate-fade-in">
                 <h3 class="h5 text-success mb-3">🎉 Compra Confirmada! Sua Key:</h3>
                 
                 <?php
                    // Fetch the Key
                    require_once __DIR__ . '/services/KeyService.php';
                    $keyStmt = $pdo->prepare("SELECT key_encrypted FROM product_keys WHERE order_id = ? LIMIT 1");
                    $keyStmt->execute([$order['id']]);
                    $keyRow = $keyStmt->fetch();
                    
                    if ($keyRow):
                        try {
                            $decryptedKey = KeyService::decrypt($keyRow['key_encrypted']);
                        } catch (Exception $e) {
                            $decryptedKey = "Erro na decriptação. Contate suporte.";
                        }
                 ?>
                    <div class="user-select-all font-monospace fs-4 fw-bold text-white bg-dark p-3 rounded border border-secondary mb-2 text-wrap" style="word-break: break-all;">
                        <?= htmlspecialchars($decryptedKey) ?>
                    </div>
                    <p class="text-white-50 small mb-0">Esta chave é única e agora é sua.</p>
                 <?php else: ?>
                    <p class="text-warning">A chave está sendo alocada... Atualize a página em instantes.</p>
                 <?php endif; ?>

                 <div class="mt-3">
                    <span class="badge bg-info text-dark">📧 Email de confirmação enviado! (Simulado)</span>
                 </div>
              </div>
            <?php else: ?>
              <p class="mb-3 text-warning">
                 ⚠️ O pagamento ainda não foi confirmado.
              </p>
              <!-- Recuperar slug do primeiro produto para voltar ao checkout se precisar, 
                   embora o ideal fosse ter um link direto de pagamento no pedido -->
              <input type="hidden" id="gw_id" value="<?= $order['payment_gateway_id'] ?>">
              
              <!-- Updated Action to new payment_simulate.php -->
              <form action="payment_simulate.php" method="POST">
                  <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                  <button type="submit" class="btn btn-custom btn-sm">Ir para Pagamento (Simulado)</button>
              </form>
            <?php endif; ?>
          </div>

        </div>

        <div class="mt-4 text-center">
           <a href="my_orders.php" class="text-white-50 text-decoration-none small">&larr; Voltar para Meus Pedidos</a>
        </div>

      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
  <?php if (isset($_GET['paid'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script>
      // Celebration Confetti
      window.addEventListener('load', () => {
          var duration = 3 * 1000;
          var animationEnd = Date.now() + duration;
          var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

          function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
          }

          var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
              return clearInterval(interval);
            }

            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
          }, 250);
      });
  </script>
  <?php endif; ?>
</body>
</html>
