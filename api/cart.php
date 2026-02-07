<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $productId = (int)$_POST['id'];
            $qty = (int)($_POST['qty'] ?? 1);
            
            // Check stock or validity (simplified for now)
            $stmt = $pdo->prepare("SELECT id, title, price_cents, image_url, type FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) throw new Exception("Produto não encontrado");

            // Add or Update
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['qty'] += $qty;
            } else {
                $_SESSION['cart'][$productId] = [
                    'id' => $product['id'],
                    'title' => $product['title'],
                    'price' => $product['price_cents'],
                    'image_url' => $product['image_url'],
                    'type' => $product['type'],
                    'qty' => $qty
                ];
            }
            
            echo json_encode(['success' => true, 'cart' => getCartSummary()]);
            break;

        case 'remove':
            $productId = (int)$_POST['id'];
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => true, 'cart' => getCartSummary()]);
            break;
            
        case 'update':
            $productId = (int)$_POST['id'];
            $qty = (int)$_POST['qty'];
            
            if (isset($_SESSION['cart'][$productId])) {
                if ($qty > 0) {
                    $_SESSION['cart'][$productId]['qty'] = $qty;
                } else {
                    unset($_SESSION['cart'][$productId]);
                }
            }
            echo json_encode(['success' => true, 'cart' => getCartSummary()]);
            break;

        case 'get':
            echo json_encode(['success' => true, 'cart' => getCartSummary()]);
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception("Ação inválida");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getCartSummary() {
    $items = [];
    $totalCents = 0;
    $count = 0;

    foreach ($_SESSION['cart'] as $item) {
        $items[] = $item;
        $totalCents += ($item['price'] * $item['qty']);
        $count += $item['qty'];
    }

    return [
        'items' => $items,
        'total_cents' => $totalCents,
        'count' => $count
    ];
}
