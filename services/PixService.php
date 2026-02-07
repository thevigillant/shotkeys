<?php

class PixService {
    
    /**
     * Gera o payload do Pix (Copia e Cola)
     */
    public static function createPayload(string $pixKey, string $description, string $merchantName, string $merchantCity, string $txId, float $amount): string {
        $pixKey = preg_replace('/[^0-9a-zA-Z@.]/', '', $pixKey); // Clean key
        $merchantName = substr($merchantName, 0, 25);
        $merchantCity = substr($merchantCity, 0, 15);
        $txId = substr($txId, 0, 25) ?: '***';
        $amountStr = number_format($amount, 2, '.', '');

        // Montagem do Payload (IDs do Banco Central)
        $payload = "000201";
        $payload .= self::getValue(26, "0014BR.GOV.BCB.PIX01" . strlen($pixKey) . $pixKey . "02" . strlen($description) . $description);
        $payload .= "52040000";
        $payload .= "5303986";
        $payload .= self::getValue(54, $amountStr);
        $payload .= "5802BR";
        $payload .= self::getValue(59, $merchantName);
        $payload .= self::getValue(60, $merchantCity);
        $payload .= self::getValue(62, "05" . strlen($txId) . $txId);
        $payload .= "6304"; // ID do CRC16

        // Calcular CRC16
        $payload .= self::getCRC16($payload);

        return $payload;
    }

    private static function getValue(string $id, string $value): string {
        $len = str_pad((string)strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $len . $value;
    }

    private static function getCRC16(string $payload): string {
        $polynomial = 0x1021;
        $resultado = 0xFFFF;

        if (strlen($payload) > 0) {
            for ($offset = 0; $offset < strlen($payload); $offset++) {
                $resultado ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($resultado <<= 1) & 0x10000) $resultado ^= $polynomial;
                    $resultado &= 0xFFFF;
                }
            }
        }
        return strtoupper(dechex($resultado));
    }
}
