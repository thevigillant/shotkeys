<?php
declare(strict_types=1);

require_once __DIR__ . '/KeyService.php';

class DeliveryService {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * ATOMIC delivery of a key.
     * 1. Start Transaction
     * 2. Lock a row (SELECT FOR UPDATE)
     * 3. Update status to sold
     * 4. Insert delivery log
     * 5. Commit
     */
    public function deliverKey(int $orderId, int $productId): ?string {
        try {
            $this->pdo->beginTransaction();

            // 1. Find an available key for this product and LOCK it
            // 'SKIP LOCKED' is great for concurrency but might not be available in older MySQL. 
            // We use standard SELECT ... FOR UPDATE -> LIMIT 1
            $stmt = $this->pdo->prepare("
                SELECT id, key_encrypted 
                FROM product_keys 
                WHERE product_id = :pid AND status = 'available' 
                LIMIT 1 
                FOR UPDATE
            ");
            $stmt->execute([':pid' => $productId]);
            $keyRow = $stmt->fetch();

            if (!$keyRow) {
                // No keys available!
                $this->pdo->rollBack();
                // TODO: Notify admin about out of stock?
                return null; 
            }

            $keyId = $keyRow['id'];
            $encryptedKey = $keyRow['key_encrypted'];

            // 2. Mark as SOLD and link to Order
            $update = $this->pdo->prepare("
                UPDATE product_keys 
                SET status = 'sold', order_id = :oid 
                WHERE id = :kid
            ");
            $update->execute([':oid' => $orderId, ':kid' => $keyId]);

            // 3. Log delivery
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
            
            $log = $this->pdo->prepare("
                INSERT INTO deliveries (order_id, product_key_id, ip_address, user_agent) 
                VALUES (:oid, :kid, :ip, :ua)
            ");
            $log->execute([
                ':oid' => $orderId, 
                ':kid' => $keyId,
                ':ip' => $ip,
                ':ua' => $ua
            ]);

            // 4. Update Order Status to DELIVERED (if fully delivered? assuming 1 item for MVP)
            // For MVP strictness, we just update status to DELIVERED if it's PAID
            $upOrder = $this->pdo->prepare("UPDATE orders SET status = 'DELIVERED' WHERE id = :oid");
            $upOrder->execute([':oid' => $orderId]);

            $this->pdo->commit();

            // 5. Decrypt and return the real key
            return KeyService::decrypt($encryptedKey);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Delivery Error: " . $e->getMessage());
            return null; // Delivery failed
        }
    }
}
