<?php
require __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método não permitido.');
}

$order_id = (int)($_POST['order_id'] ?? 0);
if ($order_id <= 0) {
  http_response_code(400);
  exit('Pedido inválido.');
}

// Garante que o pedido é do usuário logado e está PENDING
$stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
  http_response_code(404);
  exit('Pedido não encontrado.');
}

if ($order['status'] !== 'PENDING') {
  header("Location: pedido.php?id={$order_id}");
  exit;
}

// Marca como PAID
$stmt = $pdo->prepare("UPDATE orders SET status = 'PAID' WHERE id = ? AND status = 'PENDING'");
$stmt->execute([$order_id]);

// Limpa pendência da sessão
unset($_SESSION['pending_order_for'], $_SESSION['pending_order_id']);

header("Location: pedido.php?id={$order_id}");
exit;
