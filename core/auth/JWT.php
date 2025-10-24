<?php
class JWT {
    private static $secretKey;
    private static $algorithm = 'HS256';

    public static function getSecretKey() {
        if (self::$secretKey === null) {
            self::loadEnv();
            self::$secretKey = $_ENV['JWT_SECRET'] ?? 'soeheinsh1234007';
        }
        return self::$secretKey;
    }
    
    private static function loadEnv() {
        $envPath = __DIR__ . '/../../.env';
        if (!file_exists($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = self::stripQuotes($value);
                
                $_ENV[$key] = $value;
            }
        }
    }
    
    private static function stripQuotes($value) {
        $length = strlen($value);
        if ($length < 2) {
            return $value;
        }
        
        $firstChar = $value[0];
        $lastChar = $value[$length - 1];
        
        if (($firstChar === '"' && $lastChar === '"') || 
            ($firstChar === "'" && $lastChar === "'")) {
            return substr($value, 1, -1);
        }
        
        return $value;
    }

    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        $payload = json_encode($payload);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::getSecretKey(), true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    public static function decode($jwt) {
        $tokenParts = explode('.', $jwt);
        
        if (count($tokenParts) !== 3) {
            throw new Exception('Invalid token structure');
        }

        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature = $tokenParts[2];

        // Verify signature
        $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], 
            base64_encode(hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], self::getSecretKey(), true)));
        
        if ($signature !== $expectedSignature) {
            throw new Exception('Invalid token signature');
        }

        return json_decode($payload, true);
    }

    public static function isValid($jwt) {
        try {
            $payload = self::decode($jwt);
            
            // Check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}