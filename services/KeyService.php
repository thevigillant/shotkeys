<?php
declare(strict_types=1);

class KeyService {
    private const METHOD = 'aes-256-cbc';

    /**
     * Encrypts a key for storage
     */
    public static function encrypt(string $plainKey): string {
        if (strlen(ENCRYPTION_KEY) !== 32) {
            throw new Exception("Encryption key must be exactly 32 bytes.");
        }

        $iv = random_bytes(openssl_cipher_iv_length(self::METHOD));
        $encrypted = openssl_encrypt($plainKey, self::METHOD, ENCRYPTION_KEY, 0, $iv);

        if ($encrypted === false) {
            throw new Exception("Encryption failed.");
        }

        // Store IV + Encrypted Data (Example: iv::encrypted_string)
        return base64_encode($iv . '::' . $encrypted);
    }

    /**
     * Decrypts a key for delivery
     */
    public static function decrypt(string $storedValue): string {
        $data = base64_decode($storedValue);
        if ($data === false || strpos($data, '::') === false) {
            throw new Exception("Invalid encrypted data format.");
        }

        [$iv, $encrypted] = explode('::', $data, 2);

        $decrypted = openssl_decrypt($encrypted, self::METHOD, ENCRYPTION_KEY, 0, $iv);

        if ($decrypted === false) {
            throw new Exception("Decryption failed.");
        }

        return $decrypted;
    }

    /**
     * Generates a safe partial hash for display/logs (e.g., "XXXX-XXXX-A1B2")
     * If key is standard 5x5 format, it shows last segment.
     * Otherwise it shows last 4 chars.
     */
    public static function generateHashPartial(string $key): string {
        $clean = trim($key);
        $len = strlen($clean);
        
        if ($len <= 4) return '****';

        // Show last 4 chars
        return '****-' . substr($clean, -4);
    }
}
