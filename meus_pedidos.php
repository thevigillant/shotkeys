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
    /* Accordion Customization */
    .accordion-item {
        background: rgba(20, 20, 35, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        margin-bottom: 1rem;
        border-radius: 12px !important;
        overflow: hidden;
    }
    .accordion-button {
        background: transparent !important;
        color: #fff !important;
        font-family: 'Rajdhani', sans-serif;
        font-weight: 500;
        box-shadow: none !important;
    }
    .accordion-button:not(.collapsed) {
        background: rgba(188, 19, 254, 0.1) !important;
        border-bottom: 1px solid rgba(188, 19, 254, 0.3);
    }
    .accordion-button::after {
        filter: invert(1);
        transform: scale(0.8);
    }
    .accordion-body {
        background: rgba(0, 0, 0, 0.2);
        color: #ccc;
    }
    .order-summary-title {
        color: var(--neon-blue);
        font-weight: bold;
        letter-spacing: 0.5px;
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
      
      <div class="accordion d-flex flex-column gap-3" id="ordersAccordion">
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
            
            // Fetch items for this order (simple query inside loop is fine for MVP, or could eager load)
            $stmtItems = $pdo->prepare("
                SELECT p.title, p.image_url 
                FROM order_items oi
                JOIN products p ON p.id = oi.product_id
                WHERE oi.order_id = ?
            ");
            $stmtItems->execute([$o['id']]);
            $orderSimpleItems = $stmtItems->fetchAll();
            $firstItemTitle = $orderSimpleItems[0]['title'] ?? 'Produto desconhecido';
            $itemsCount = count($orderSimpleItems);
            $summaryTitle = $itemsCount > 1 ? "$firstItemTitle e mais " . ($itemsCount - 1) : $firstItemTitle;
          ?>
          
          <div class="accordion-item glass-panel border border-opacity-25 border-secondary rounded-4 overflow-hidden mb-0" style="background: transparent;">
            <h2 class="accordion-header" id="heading<?= $o['id'] ?>">
              <button class="accordion-button collapsed p-4 bg-dark-transparent text-white shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $o['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $o['id'] ?>">
                <div class="row w-100 align-items-center g-3 me-0">
                  
                  <!-- ID -->
                  <div class="col-6 col-md-2">
                     <span class="d-block text-white-50 small mb-1">PEDIDO #<?= str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT) ?></span>
                     <div class="fw-bold text-truncate"><?= htmlspecialchars($summaryTitle) ?></div>
                  </div>
                  
                  <!-- Data -->
                  <div class="col-6 col-md-3">
                     <span class="d-block text-white-50 small mb-1">DATA</span>
                     <span><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></span>
                  </div>

                  <!-- Valor -->
                  <div class="col-6 col-md-3">
                     <span class="d-block text-white-50 small mb-1">TOTAL</span>
                     <span class="fw-bold text-accent">R$ <?= number_format(((int)$o['total_cents'])/100, 2, ',', '.') ?></span>
                  </div>

                  <!-- Status -->
                  <div class="col-6 col-md-3 text-md-center">
                     <span class="badge rounded-pill px-3 py-2 <?= $statusClass ?> bg-opacity-75">
                        <?= $statusLabel ?>
                     </span>
                  </div>
                </div>
              </button>
            </h2>
            <div id="collapse<?= $o['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $o['id'] ?>" data-bs-parent="#ordersAccordion">
              <div class="accordion-body bg-black bg-opacity-25 border-top border-secondary border-opacity-25">
                 
                 <div class="d-flex flex-column gap-2 mb-3">
                    <?php foreach ($orderSimpleItems as $item): ?>
                        <div class="d-flex align-items-center gap-3 p-2 rounded bg-dark border border-secondary border-opacity-10">
                            <img src="<?= htmlspecialchars(($item['image_url'] ?? '') ?: 'assets/keys/glock/glock.png') ?>" style="width: 50px; height: 50px; object-fit: contain; background: rgba(255,255,255,0.05); border-radius: 4px;">
                            <span class="fw-bold"><?= htmlspecialchars($item['title']) ?></span>
                        </div>
                    <?php endforeach; ?>
                 </div>

                 <div class="text-end">
                     <?php if ($o['status'] === 'PENDING'): ?>
                        <a href="pedido.php?id=<?= $o['id'] ?>" class="btn btn-warning btn-sm fw-bold">
                            PAGAR AGORA &rarr;
                        </a>
                     <?php else: ?>
                        <a href="pedido.php?id=<?= $o['id'] ?>" class="btn btn-outline-light btn-sm">
                            VER DETALHES / KEY &rarr;
                        </a>
                     <?php endif; ?>
                 </div>

              </div>
            </div>
          </div>
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
