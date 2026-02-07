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
    
    <!-- Top Nav -->
    <div class="mb-4">
        <a href="meus_pedidos.php" class="text-decoration-none text-white-50 hover-link">
            &larr; Voltar para Meus Pedidos
        </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="glass-panel position-relative overflow-hidden p-0">
          
          <!-- Decorative Header Bar -->
          <div style="height: 6px; background: linear-gradient(90deg, var(--color-accent), #bc13fe);"></div>

          <div class="p-4 p-md-5">
              <!-- Header do Pedido -->
              <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-5 border-bottom border-white border-opacity-10 pb-4">
                 <div>
                    <span class="d-block text-accent fw-bold small text-uppercase mb-1 tracking-wider">COMPROVANTE DE PEDIDO</span>
                    <h1 class="h2 archivofont text-white mb-1">PEDIDO #<?= str_pad((string)$order['id'], 4, '0', STR_PAD_LEFT) ?></h1>
                    <span class="text-white-50 small">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                      <?= date('d/m/Y \à\s H:i', strtotime($order['created_at'])) ?>
                    </span>
                 </div>
                 
                 <?php 
                   $statusConfig = match($order['status']) {
                       'PAID' => ['bg-success', 'Aprovado'],
                       'PENDING'  => ['bg-warning text-dark', 'Pendente'],
                       default    => ['bg-secondary', $order['status']]
                   };
                 ?>
                 <span class="badge <?= $statusConfig[0] ?> bg-opacity-75 rounded-pill px-3 py-2 fs-6 shadow-sm">
                    <?= $statusConfig[1] ?>
                 </span>
              </div>
    
              <!-- Lista de Itens -->
              <div class="mb-5">
                <h2 class="h6 text-white-50 text-uppercase fw-bold mb-3 tracking-wider">Itens Adquiridos</h2>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($items as $it): ?>
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded bg-dark d-flex align-items-center justify-content-center border border-secondary" style="width: 40px; height: 40px;">
                                🎮
                            </div>
                            <div>
                                <strong class="d-block text-white"><?= htmlspecialchars($it['title']) ?></strong>
                                <span class="badge bg-dark border border-white border-opacity-25 text-white-50 small" style="font-size: 0.65rem;"><?= htmlspecialchars($it['type']) ?></span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-white">
                                R$ <?= number_format(((int)$it['unit_price_cents'])/100, 2, ',', '.') ?>
                            </div>
                            <small class="text-white-50">x<?= (int)$it['quantity'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
              </div>
    
              <!-- Área da Key (Se pago) -->
              <?php if ($order['status'] === 'PAID' || $order['status'] === 'DELIVERED'): ?>
                  <div class="bg-dark p-4 rounded-4 border border-accent position-relative overflow-hidden mb-4">
                      <!-- Glow effect -->
                      <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 50% 50%, rgba(0, 243, 255, 0.1) 0%, transparent 70%); pointer-events: none;"></div>
                      
                      <div class="text-center position-relative z-1">
                          <h3 class="h5 text-accent mb-3 fw-bold text-uppercase tracking-wider">
                              🔓 SUA CHAVE DE ATIVAÇÃO
                          </h3>
                          
                          <?php
                             // Fetch Key Logic (Simplified for View)
                             require_once __DIR__ . '/services/KeyService.php';
                             $keyStmt = $pdo->prepare("SELECT key_encrypted FROM product_keys WHERE order_id = ? LIMIT 1");
                             $keyStmt->execute([$order['id']]);
                             $keyRow = $keyStmt->fetch();
                             $finalKey = "Erro ao recuperar key";
                             if ($keyRow) {
                                 try { $finalKey = KeyService::decrypt($keyRow['key_encrypted']); } catch(Exception $e){}
                             }
                          ?>
    
                          <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                              <code class="fs-4 fw-bold text-white bg-black bg-opacity-50 px-4 py-2 rounded-3 border border-white border-opacity-10" id="gameKey">
                                  <?= htmlspecialchars($finalKey) ?>
                              </code>
                              <button class="btn btn-outline-light btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('gameKey').innerText).then(() => alert('Copiado!'))" title="Copiar">
                                  📋
                              </button>
                          </div>
    
                          <p class="text-white-50 small mb-0">
                              Ative este código na plataforma correspondente (Steam, Xbox, etc).
                          </p>
                      </div>
                  </div>
              <?php else: ?>
                  <!-- Área de Pagamento (Se pendente) -->
                  <div class="bg-warning bg-opacity-10 border border-warning border-opacity-50 p-4 rounded-4 text-center mb-4">
                      <h3 class="h5 text-warning fw-bold mb-3">⚠️ Pagamento Pendente</h3>
                      <p class="text-white-50 mb-4">Finalize o pagamento para receber sua chave imediatamente.</p>
                      
                      <form action="_dev_tools/payment_simulate.php" method="POST">
                          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                          <button type="submit" class="btn btn-warning fw-bold text-dark px-5 py-3 rounded-pill shadow-lg hover-scale">
                              PAGAR AGORA ⚡
                          </button>
                      </form>
                  </div>
              <?php endif; ?>
    
              <!-- Total -->
              <div class="d-flex justify-content-between align-items-center border-top border-white border-opacity-10 pt-4">
                  <span class="text-white h5 fw-normal">Total do Pedido</span>
                  <span class="text-accent h2 archivofont mb-0">
                      R$ <?= number_format(((int)$order['total_cents'])/100, 2, ',', '.') ?>
                  </span>
              </div>

          </div>
        </div>

      </div>
    </div>
  </main>

  <style>
      .hover-link:hover { color: var(--color-accent) !important; text-decoration: underline !important; }
      .tracking-wider { letter-spacing: 0.1em; }
      .hover-scale { transition: transform 0.2s; }
      .hover-scale:hover { transform: scale(1.05); }
  </style>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
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
