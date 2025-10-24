<?php
require_once __DIR__ . '/../core/Database.php';

class Product {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll($limit = null, $offset = null, $orderBy = 'name', $orderDir = 'ASC') {
        $sql = 'SELECT * FROM products';
        $params = [];
        
        if ($orderBy && in_array($orderBy, ['id', 'name', 'price', 'quantity_available'])) {
            $sql .= " ORDER BY {$orderBy} {$orderDir}";
        }
        
        if ($limit !== null && $offset !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params = [$limit, $offset];
        } elseif ($limit !== null) {
            $sql .= ' LIMIT ?';
            $params = [$limit];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll() {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM products');
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO products (name, price, quantity_available) VALUES (?, ?, ?)');
        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['quantity_available']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE products SET name = ?, price = ?, quantity_available = ? WHERE id = ?');
        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['quantity_available'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Update quantity after purchase
    public function updateQuantity($id, $quantity) {
        $stmt = $this->pdo->prepare('UPDATE products SET quantity_available = ? WHERE id = ?');
        return $stmt->execute([$quantity, $id]);
    }
    
    // For testing purposes
    public function getConnection() {
        return $this->pdo;
    }
}