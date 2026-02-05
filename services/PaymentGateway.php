<?php
declare(strict_types=1);

interface PaymentGateway {
    /**
     * Initializes a transaction.
     * @param int $orderId
     * @param int $amountCents
     * @param string $description
     * @return string The URL to redirect the user to (or payment data)
     */
    public function createTransaction(int $orderId, int $amountCents, string $description): string;
}
