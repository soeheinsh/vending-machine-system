<?php
class Database {
    private static $instance = null;
    private $pdo;

    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset = 'utf8mb4';

    private function __construct() {
        // Load environment variables if not already loaded
        $this->loadEnv();
        
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db = $_ENV['DB_NAME'] ?? 'vending_machine';
        $this->user = $_ENV['DB_USER'] ?? 'soeheindev';
        $this->pass = $_ENV['DB_PASS'] ?? 'soeheinsh';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    private function loadEnv() {
        if (!file_exists(__DIR__ . '/../.env')) {
            return;
        }

        $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = $this->stripQuotes($value);
                
                $_ENV[$key] = $value;
            }
        }
    }
    
    private function stripQuotes($value) {
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

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
} 