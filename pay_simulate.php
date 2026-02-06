<?php
require __DIR__ . '/config.php';
require_login();

$orderId = $_GET['order_id'] ?? $_POST['order_id'] ?? 0;
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
  <title>Pagamento Seguro | ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
  <style>
      .bank-loader {
          border: 4px solid rgba(255,255,255,0.1);
          border-left-color: var(--color-accent);
          border-radius: 50%;
          width: 50px;
          height: 50px;
          animation: spin 1s linear infinite;
          margin: 0 auto 20px;
      }
      @keyframes spin { 100% { transform: rotate(360deg); } }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100" style="background: #000;">

  <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <!-- Simulated "External Bank" Page -->
            <div class="glass-panel p-5 text-center position-relative overflow-hidden">
                
                <!-- Mock Header -->
                <div class="mb-4">
                    <span class="badge bg-secondary text-uppercase ls-1 mb-3">Ambiente Seguro (Simulado)</span>
                    <h1 class="h4 text-white">Gateway de Pagamento</h1>
                    <p class="text-white-50">Pedido #<?= $order['id'] ?></p>
                </div>

                <!-- Amount -->
                <h2 class="display-3 fw-bold text-white mb-5">
                    R$ <?= number_format($order['total_cents']/100, 2, ',', '.') ?>
                </h2>

                <!-- Loading State (Fake) -->
                <div id="connecting" class="mb-4">
                    <div class="bank-loader"></div>
                    <p class="text-white small animate-pulse">Conectando ao banco...</p>
                </div>

                <!-- Action Controls (Hidden initially) -->
                <div id="controls" style="display:none; opacity: 0; transition: opacity 0.5s;">
                    <button onclick="approvePayment()" class="btn btn-success btn-lg w-100 mb-3 py-3 shadow-lg fw-bold">
                        ✅ APROVAR PAGAMENTO
                    </button>
                    <a href="checkout.php?cancel=true" class="btn btn-outline-danger w-100 btn-sm">
                        Cancelar Operação
                    </a>
                </div>

                <!-- Footer -->
                <div class="mt-4 pt-4 border-top border-secondary border-opacity-10">
                    <p class="text-white-50 small mb-0">ShotKeys Payments LLC &copy; 2024</p>
                </div>
            </div>
        </div>
    </div>
  </div>

  <script>
    // Simulate finding the payment details
    setTimeout(() => {
        document.getElementById('connecting').style.display = 'none';
        const controls = document.getElementById('controls');
        controls.style.display = 'block';
        requestAnimationFrame(() => controls.style.opacity = '1');
    }, 1500);

    async function approvePayment() {
        const btn = document.querySelector('.btn-success');
        btn.innerHTML = 'Processando...';
        btn.disabled = true;

        const gatewayId = "<?= $order['payment_gateway_id'] ?>";

        try {
            const response = await fetch('webhook_simulate.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    gateway_id: gatewayId,
                    status: 'PAID'
                })
            });
            
            const result = await response.json();
            
            if(result.status === 'success' || (result.message && result.message.includes('Order Paid'))) {
                btn.innerHTML = '🎉 Sucesso! Redirecionando...';
                btn.classList.add('pulse');
                setTimeout(() => {
                    window.location.href = 'pedido.php?id=<?= $order['id'] ?>&paid=true';
                }, 1000);
            } else {
                alert("Erro: " + (result.error || result.message));
                btn.innerHTML = '✅ APROVAR PAGAMENTO';
                btn.disabled = false;
            }
        } catch (e) {
            alert("Erro de conexão.");
            console.error(e);
            btn.disabled = false;
        }
    }
  </script>
</body>
</html>
