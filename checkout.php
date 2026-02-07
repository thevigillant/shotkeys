<?php
require __DIR__ . '/config.php';
require __DIR__ . '/services/SimulatedGateway.php';
require_login();

// Check if direct buy (legacy) or cart checkout
$directSlug = $_GET['product'] ?? '';
$cartItems = [];
$totalCents = 0;

// 1. Prepare Items
if ($directSlug) {
    // Direct Buy Mode
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 'ACTIVE' LIMIT 1");
    $stmt->execute([$directSlug]);
    $product = $stmt->fetch();

    if (!$product) die("Produto inválido ou fora de estoque.");

    $cartItems[] = [
        'id' => $product['id'],
        'title' => $product['title'],
        'price' => $product['price_cents'],
        'qty' => 1,
        'type' => $product['type'],
        'image_url' => $product['image_url'] ?? ''
    ];
    $totalCents = $product['price_cents'];

} else {
    // Cart Mode
    if (empty($_SESSION['cart'])) {
        header("Location: products.php"); // Empty cart redirect
        exit;
    }

    foreach ($_SESSION['cart'] as $item) {
        $cartItems[] = $item;
        $totalCents += ($item['price'] * $item['qty']);
    }
}

// 2. Handle POST (Checkout Confirmation)
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($cartItems)) throw new Exception("Carrinho vazio.");

        $pdo->beginTransaction();

        // A. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total_cents) VALUES (?, 'PENDING', ?)");
        $stmt->execute([$_SESSION['user_id'], $totalCents]);
        $orderId = (int)$pdo->lastInsertId();

        // B. Add Items
        $probType = 'KEY'; // Default for gateway logic
        $stmtExpr = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, title, quantity, unit_price_cents, type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($cartItems as $cItem) {
            $stmtExpr->execute([
                $orderId, 
                $cItem['id'], 
                $cItem['title'], 
                $cItem['qty'], 
                $cItem['price'], 
                $cItem['type']
            ]);
            // Just for gateway description
            $probType = $cItem['type'];
        }

        $pdo->commit();

        // Clear Cart if successful
        if (!$directSlug) {
            unset($_SESSION['cart']);
        }

        // C. Initialize Payment
        $gateway = new SimulatedGateway($pdo); 
        $desc = count($cartItems) . " ite(ns) - ShotKeys";
        
        $redirectUrl = $gateway->createTransaction(
            $orderId, 
            $totalCents, 
            $desc
        );

        header("Location: $redirectUrl");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = "Erro ao processar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Finalizar Compra | ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
  <!-- Google Fonts -->
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Momo+Trust+Display&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;500;700&display=swap");
  </style>
</head>
<body>

  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    
    <div class="row g-5">
        <!-- Order Summary -->
        <div class="col-lg-7">
            <h1 class="h3 archivofont text-white mb-4">REVISÃO DO PEDIDO</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="glass-panel p-0 overflow-hidden">
                <div class="d-flex flex-column gap-0">
                    <?php foreach ($cartItems as $idx => $item): ?>
                        <div class="d-flex gap-3 align-items-center p-4 <?= $idx < count($cartItems)-1 ? 'border-bottom border-white border-opacity-10' : '' ?>" style="background: rgba(0,0,0,0.2);">
                             <img src="<?= htmlspecialchars($item['image_url'] ?: 'assets/keys/glock/glock.png') ?>" style="width: 70px; height: 70px; object-fit: contain; background: rgba(0,0,0,0.3); border-radius: 8px; padding: 5px;">
                             
                             <div class="flex-grow-1">
                                 <h4 class="h6 text-white fw-bold mb-1"><?= htmlspecialchars($item['title']) ?></h4>
                                 <span class="badge bg-secondary opacity-50 small"><?= htmlspecialchars($item['type']) ?></span>
                             </div>

                             <div class="text-end">
                                 <div class="fw-bold text-accent">R$ <?= number_format($item['price']/100, 2, ',', '.') ?></div>
                                 <div class="text-white-50 small">x<?= $item['qty'] ?></div>
                             </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-5">
            <div class="glass-panel p-4 sticky-top" style="top: 100px;">
                <h2 class="h5 text-white fw-bold mb-4">Resumo da Conta</h2>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50">Subtotal</span>
                    <span class="text-white">R$ <?= number_format($totalCents/100, 2, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-white-50">Taxas</span>
                    <span class="text-white">R$ 0,00</span>
                </div>
                
                <div class="d-flex justify-content-between mb-4 pt-3 border-top border-white border-opacity-10">
                    <span class="h4 text-white fw-bold">TOTAL</span>
                    <span class="h4 text-accent fw-bold archivofont">R$ <?= number_format($totalCents/100, 2, ',', '.') ?></span>
                </div>

                <form method="POST">
                    <button type="submit" class="btn btn-custom w-100 py-3 fw-bold text-uppercase shadow-lg hover-scale">
                        Confirmar Pagamento
                    </button>
                    <div class="text-center mt-3">
                        <small class="text-white-50">Transação segura e simulada.</small>
                    </div>
                </form>

            </div>
        </div>
    </div>

  </main>

  <style>
      .hover-scale { transition: transform 0.2s; }
      .hover-scale:hover { transform: scale(1.02); }
  </style>

  <!-- Needs navbar script for Offcanvas but we are in checkout loop so maybe user wants to go back to shop -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
