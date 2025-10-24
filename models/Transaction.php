<?php
require_once __DIR__ . '/../core/Database.php';

class Transaction {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function create($userId, $productId, $quantity, $totalPrice) {
        $stmt = $this->pdo->prepare('INSERT INTO transactions (user_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$userId, $productId, $quantity, $totalPrice]);
    }

    public function getAllByUser($userId, $limit = null, $offset = null) {
        $sql = 'SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id = p.id WHERE t.user_id = ? ORDER BY t.transaction_date DESC';
        $params = [$userId];
        
        if ($limit !== null && $offset !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params = [$userId, $limit, $offset];
        } elseif ($limit !== null) {
            $sql .= ' LIMIT ?';
            $params = [$userId, $limit];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAll($limit = null, $offset = null) {
        $sql = 'SELECT t.*, p.name as product_name, u.username FROM transactions t JOIN products p ON t.product_id = p.id JOIN users u ON t.user_id = u.id ORDER BY t.transaction_date DESC';
        
        if ($limit !== null && $offset !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params = [$limit, $offset];
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } elseif ($limit !== null) {
            $sql .= ' LIMIT ?';
            $params = [$limit];
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
}