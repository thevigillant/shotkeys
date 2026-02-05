<?php
require __DIR__ . '/config.php';
require __DIR__ . '/services/DeliveryService.php';

// Simulate a Webhook Callback from a Payment Gateway
// In production, this would be a POST request from Stripe/MercadoPago

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$gatewayId = $input['gateway_id'] ?? '';
$status = $input['status'] ?? '';

if (!$gatewayId || $status !== 'PAID') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid webhook payload']);
    exit;
}

try {
    // 1. Find the order by Gateway ID
    $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE payment_gateway_id = ?");
    $stmt->execute([$gatewayId]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    if ($order['status'] === 'PAID' || $order['status'] === 'DELIVERED') {
        echo json_encode(['message' => 'Order already processed']);
        exit;
    }

    // 2. Mark Order as PAID
    $pdo->prepare("UPDATE orders SET status = 'PAID' WHERE id = ?")->execute([$order['id']]);

    // 3. Trigger Atomic Delivery
    // We need to find which product was bought. For MVP: Single item cart.
    $itemStmt = $pdo->prepare("SELECT product_id FROM order_items WHERE order_id = ? LIMIT 1");
    $itemStmt->execute([$order['id']]);
    $item = $itemStmt->fetch();

    if ($item) {
        $deliveryService = new DeliveryService($pdo);
        $key = $deliveryService->deliverKey((int)$order['id'], (int)$item['product_id']);
        
        if ($key) {
            echo json_encode(['status' => 'success', 'message' => 'Order Paid and Key Delivered']);
        } else {
            // Paid but no key stock? 
            // In real world: send alert to admin
            echo json_encode(['status' => 'warning', 'message' => 'Order Paid but Key Stock Empty']);
        }
    } else {
        echo json_encode(['error' => 'No items in order']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
