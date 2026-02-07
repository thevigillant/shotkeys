<?php
require __DIR__ . '/config.php';
require_login();

// 1. Try to get Order ID directly
$orderId = $_GET['order_id'] ?? $_POST['order_id'] ?? 0;

// 2. Fallback: Try to find by Transaction ID (Robustness Fix)
if (!$orderId && isset($_GET['tx'])) {
    $txId = $_GET['tx'];
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE payment_gateway_id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$txId, $_SESSION['user_id']]);
    $found = $stmt->fetchColumn();
    if ($found) {
        $orderId = $found;
    }
}

if (!$orderId) {
    // Styling the Error Page instead of plain text
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Erro | ShotKeys</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
    </head>
    <body class="d-flex align-items-center justify-content-center min-vh-100" style="background: #000;">
        <div class="glass-panel p-5 text-center">
            <h1 class="h3 text-danger mb-3">Pedido Inválido</h1>
            <p class="text-white-50">Não foi possível identificar o pedido para pagamento.</p>
            <a href="my_orders.php" class="btn btn-outline-light mt-3">Voltar aos Pedidos</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Fetch Order to get Gateway ID and Amount
$stmt = $pdo->prepare("SELECT id, payment_gateway_id, total_cents FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Pedido nao encontrado."); // Could style this too, but logic above catches most
}

// Handle Payment Simulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    // Logic handled via JS fetch usually, but if fallback form submit:
    // This part is mostly for the JS to interact with webhook, but purely server-side simulation:
    
    // We rely on the frontend JS to call webhook_simulate.php
    // If we wanted server-side:
    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://shotkeys.store/webhook_simulate.php");
    ...
    */
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

                <!-- QR Code & Pix Info -->
                <!-- QR Code & Pix Info -->
                <div class="mb-4 bg-white rounded p-3 text-dark">
                    <h5 class="fw-bold mb-2">Pague via Pix</h5>
                    
                    <?php
                    // Gerar Payload Pix Válido
                    // Use __DIR__ to ensure correct path relative to this file
                    $pixServicePath = __DIR__ . '/services/PixService.php';
                    
                    if (!file_exists($pixServicePath)) {
                        echo "<p class='text-danger'>Erro: Arquivo PixService.php não encontrado.</p>";
                        $payloadPix = "Erro: PixService.php não encontrado."; // Default value to prevent errors later
                    } else {
                        require_once $pixServicePath;
                        
                        // Dados do Recebedor (Você)
                        $pixKeyProprio = '02060330602'; // CPF Limpo
                        $nomeRecebedor = 'Bruno S'; // Nome curto (max 25 chars)
                        $cidadeRecebedor = 'BRASILIA'; // Cidade (max 15 chars)
                        $valor = ((float)$order['total_cents']) / 100;
                        
                        $payloadPix = PixService::createPayload(
                            $pixKeyProprio, 
                            "PEDIDO " . $orderId, 
                            $nomeRecebedor, 
                            $cidadeRecebedor, 
                            "SHOTKEYS" . $orderId, 
                            $valor
                        );
                    }
                    ?>
                    <!-- QR Code Oficial (JS Generated for Reliability) -->
                    <div id="qrcode-container" class="d-flex justify-content-center mb-3 p-2 bg-white rounded"></div>
                    
                    <div class="mt-2 text-start p-2 border rounded bg-light">
                        <small class="d-block text-muted fw-bold mb-1">Copia e Cola (Chave Completa):</small>
                        
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($payloadPix) ?>" id="pixCopyPaste" readonly style="font-size: 0.8rem;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyPix()">Copiar</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                             <div class="lh-1">
                                <small class="d-block text-muted" style="font-size: 0.75rem;">Beneficiário</small>
                                <span class="fw-bold text-uppercase"><?= $nomeRecebedor ?></span>
                             </div>
                             <div class="lh-1 text-end">
                                <small class="d-block text-muted" style="font-size: 0.75rem;">CPF</small>
                                <span class="fw-bold">***.603.306-**</span>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- QRCode.js Library (CDN) -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                <script>
                    // Generate QR Code on client side to avoid URL length limits
                    var qrcode = new QRCode(document.getElementById("qrcode-container"), {
                        text: "<?= $payloadPix ?>",
                        width: 200,
                        height: 200,
                        colorDark : "#000000",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.M
                    });

                    function copyPix() {
                        var copyText = document.getElementById("pixCopyPaste");
                        copyText.select();
                        copyText.setSelectionRange(0, 99999); 
                        navigator.clipboard.writeText(copyText.value);
                        alert("Código Pix copiado! Abra seu app e pague.");
                    }
                </script>

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
    // Display controls immediately to prevent "infinite loading"
    document.getElementById('connecting').style.display = 'none';
    const controls = document.getElementById('controls');
    controls.style.display = 'block';
    controls.style.opacity = '1';

    // Simulate finding the payment details (Fallback just in case)
    /*
    window.addEventListener('load', function() {
       // logic removed for speed
    });
    */

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
                    // Redirect to order details
                    window.location.href = 'order_details.php?id=<?= $order['id'] ?>&paid=true';
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
