<?php
declare(strict_types=1);

require_once __DIR__ . '/PaymentGateway.php';

class SimulatedGateway implements PaymentGateway {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createTransaction(int $orderId, int $amountCents, string $description): string {
        // Generate a detailed simulated Transaction ID
        $txId = 'SIM-' . strtoupper(bin2hex(random_bytes(12)));

        // Update Order with this reference
        $stmt = $this->pdo->prepare("UPDATE orders SET payment_gateway_id = ? WHERE id = ?");
        $stmt->execute([$txId, $orderId]);

        // Return the Simulation Page URL
        // In real world, this would return 'https://mercadopago.com/checkout/...'
        return "payment_simulate.php?order_id={$orderId}&tx={$txId}";
    }
}
