<?php
require __DIR__ . '/../config.php';
require_login();

$orderId = $_GET['order_id'] ?? 0;

if (!$orderId) {
    die("Pedido não informado.");
}

// 1. Verify Order Ownership
$stmt = $pdo->prepare("SELECT id, status, payment_gateway_id FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Pedido não encontrado ou não pertence a você.");
}

// 2. Call the Webhook Logic Internally (Simpler than cURL)
// We essentially copy the webhook logic here but for this specific user action
try {
    // Mark as PAID
    $pdo->prepare("UPDATE orders SET status = 'PAID' WHERE id = ?")->execute([$orderId]);

    // Deliver Key
    require __DIR__ . '/../services/DeliveryService.php';
    $itemStmt = $pdo->prepare("SELECT product_id FROM order_items WHERE order_id = ? LIMIT 1");
    $itemStmt->execute([$orderId]);
    $item = $itemStmt->fetch();

    if ($item) {
        $deliveryService = new DeliveryService($pdo);
        $key = $deliveryService->deliverKey((int)$orderId, (int)$item['product_id']);
    }

    // 3. Redirect to Order Details
    header("Location: ../pedido.php?id=$orderId&paid=true");
    exit;

} catch (Exception $e) {
    die("Erro ao processar pagamento: " . $e->getMessage());
}
