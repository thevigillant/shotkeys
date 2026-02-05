<?php
require __DIR__ . '/config.php';
require __DIR__ . '/services/SimulatedGateway.php';
require_login();

$slug = $_GET['product'] ?? '';
$error = '';

// 1. Fetch Product
if ($slug) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 'ACTIVE' LIMIT 1");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
}

if (!$product) {
    die("Produto inválido ou fora de estoque.");
}

// 2. Handle POST (Checkout Confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // A. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total_cents) VALUES (?, 'PENDING', ?)");
        $stmt->execute([$_SESSION['user_id'], (int)$product['price_cents']]);
        $orderId = (int)$pdo->lastInsertId();

        // B. Add Item
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, title, quantity, unit_price_cents, type)
            VALUES (?, ?, ?, 1, ?, ?)
        ");
        $stmt->execute([$orderId, $product['id'], $product['title'], $product['price_cents'], $product['type']]);

        $pdo->commit();

        // C. Initialize Payment (Strategy)
        $method = $_POST['payment_method'] ?? 'pix'; // Default
        
        // Factory logic (simple for now)
        $gateway = new SimulatedGateway($pdo); 
        
        $redirectUrl = $gateway->createTransaction(
            $orderId, 
            (int)$product['price_cents'], 
            "ShotKeys: " . $product['title']
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
  <meta charset="UTF-8">
  <title>Checkout | ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
             </div>
          </div>

          <form method="POST" action="pay_simulate.php">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <button type="submit" class="btn btn-custom w-100 btn-lg shadow-lg">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
               Pagar Agora (Simulado)
            </button>
          </form>

          <div class="mt-4">
            <a href="meus_pedidos.php" class="text-white-50 text-decoration-none small hover-link">
               &larr; Cancelar e voltar para Meus Pedidos
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
