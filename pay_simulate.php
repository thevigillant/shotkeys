<?php
require __DIR__ . '/config.php';
require_login();

$orderId = $_POST['order_id'] ?? 0;
if (!$orderId) {
    die("Pedido invalido.");
}

// Fetch Order to get Gateway ID
$stmt = $pdo->prepare("SELECT id, payment_gateway_id, total_cents FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Pedido nao encontrado.");
}

// Handle Payment Simulation
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    
    // Simulate Webhook Call (Self-Request)
    $webhookUrl = 'https://shotkeys.store/webhook_simulate.php'; // In local dev we might need localhost logic, but let's assume relative helper
    
    // For local dev without internet/DNS loops, we can just require the logic or use cURL to localhost?
    // Let's use internal logic for MVP simplicity or proper cURL if possible.
    // Better: JS fetch in frontend? No, backend-to-backend is safer simulation.
    // Let's use cURL to localhost. (Assuming localhost/ShotKeys structure from workspace info)
    
    $payload = json_encode([
        'gateway_id' => $order['payment_gateway_id'],
        'status' => 'PAID'
    ]);

    // NOTE: In a real scenario, the payment provider sends this.
    // Here we simulate it by posting to OUR OWN webhook.
    
    // Hack: For this environment, we just manually invoke the webhook logic via HTTP if possible,
    // OR we just include the file? No, include changes scope.
    // Let's try cURL to local server.
    
    $ch = curl_init();
    // Assuming the server is running on localhost (setup default)
    // We try to guess the URL or just use strict localhost if running locally.
    // Since we don't know the exact port loopback, we might just include the logic for the simulation script.
    // BUT strict separation is requested.
    
    // Let's use a trick: `pay_simulate.php` will show a button that triggers a JS fetch to `webhook_simulate.php`.
    
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Simular Pagamento | ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="glass-panel text-center">
                <h1 class="h3 text-white mb-4">Gateway de Pagamento (Simulado)</h1>
                <p class="text-white-50">Pedido #<?= $order['id'] ?></p>
                <h2 class="display-4 fw-bold text-accent mb-4">
                    R$ <?= number_format($order['total_cents']/100, 2, ',', '.') ?>
                </h2>

                <div id="loading" style="display:none;" class="mb-3">
                    <div class="spinner-border text-accent" role="status"></div>
                    <p class="text-white mt-2">Processando pagamento...</p>
                </div>

                <div id="actions">
                    <button onclick="approvePayment()" class="btn btn-success btn-lg w-100 mb-3 shadow-lg">
                        ✅ Aprovar Pagamento
                    </button>
                    <a href="checkout.php?cancel=true" class="btn btn-outline-danger w-100">
                        ❌ Recusar
                    </a>
                </div>
            </div>
        </div>
    </div>
  </main>

  <script>
    async function approvePayment() {
        document.getElementById('actions').style.display = 'none';
        document.getElementById('loading').style.display = 'block';

        const gatewayId = "<?= $order['payment_gateway_id'] ?>";

        try {
            // Call our webhook simulator
            const response = await fetch('webhook_simulate.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    gateway_id: gatewayId,
                    status: 'PAID'
                })
            });
            
            const result = await response.json();
            
            if(result.status === 'success' || result.message.includes('Order Paid')) {
                // Redirect to Success Page (which is actually order details)
                window.location.href = 'pedido.php?id=<?= $order['id'] ?>&paid=true';
            } else {
                alert("Erro: " + (result.error || result.message));
                document.getElementById('actions').style.display = 'block';
                document.getElementById('loading').style.display = 'none';
            }
        } catch (e) {
            alert("Erro de conexão.");
            console.error(e);
        }
    }
  </script>
</body>
</html>
