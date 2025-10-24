<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/VendingUser.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../core/utils/Validation.php';
require_once __DIR__ . '/../core/DIContainer.php';

class ProductsController {
    private $productModel;
    private $userModel;
    private $transactionModel;

    public function __construct($productModel = null, $userModel = null, $transactionModel = null) {
        // Use dependency injection if provided, otherwise create new instances
        $this->productModel = $productModel ?: new Product();
        $this->userModel = $userModel ?: new VendingUser();
        $this->transactionModel = $transactionModel ?: new Transaction();
    }

    private function requireAuth() {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    private function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['role'] !== 'admin') {
            header('Location: /vending');
            exit;
        }
    }

    public function index() {
        $this->requireAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 2;
        $offset = ($page - 1) * $limit;
        
        $orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'id';
        $orderDir = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';
        
        $products = $this->productModel->getAll($limit, $offset, $orderBy, $orderDir);
        $totalProducts = $this->productModel->countAll();
        $totalPages = ceil($totalProducts / $limit);
        
        require __DIR__ . '/../views/products/index.php';
    }

    public function create() {
        $this->requireAdmin();
        $errors = [];
        $formData = [
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? '',
            'quantity_available' => $_POST['quantity_available'] ?? ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = Validation::validateProduct($_POST);
            
            if (empty($errors)) {
                $data = [
                    'name' => Validation::sanitize($_POST['name']),
                    'price' => floatval($_POST['price']),
                    'quantity_available' => intval($_POST['quantity_available'])
                ];
                
                if ($this->productModel->create($data)) {
                    header('Location: /products');
                    exit;
                } else {
                    $errors['general'] = 'Failed to create product.';
                }
            } else {
                // Preserve form data on validation failure
                $formData = [
                    'name' => Validation::sanitize($_POST['name']),
                    'price' => $_POST['price'],
                    'quantity_available' => $_POST['quantity_available']
                ];
            }
        }
        
        require __DIR__ . '/../views/products/create.php';
    }

    public function edit() {
        $this->requireAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id && isset($GLOBALS['route_params'][0])) {
            $id = $GLOBALS['route_params'][0];
        }
        
        if (!$id) {
            header('Location: /products');
            exit;
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            header('Location: /products');
            exit;
        }
        
        $errors = [];
        $formData = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity_available' => $product['quantity_available']
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = Validation::validateProduct($_POST);
            
            if (empty($errors)) {
                $data = [
                    'name' => Validation::sanitize($_POST['name']),
                    'price' => floatval($_POST['price']),
                    'quantity_available' => intval($_POST['quantity_available'])
                ];
                
                if ($this->productModel->update($id, $data)) {
                    header('Location: /products');
                    exit;
                } else {
                    $errors['general'] = 'Failed to update product.';
                }
            } else {
                // Preserve form data on validation failure
                $formData = [
                    'name' => Validation::sanitize($_POST['name']),
                    'price' => $_POST['price'],
                    'quantity_available' => $_POST['quantity_available']
                ];
            }
        }
        
        require __DIR__ . '/../views/products/edit.php';
    }

    public function delete() {
        $this->requireAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id && isset($GLOBALS['route_params'][0])) {
            $id = $GLOBALS['route_params'][0];
        }
        
        if ($id) {
            $this->productModel->delete($id);
        }
        header('Location: /products');
        exit;
    }

    // Handle purchase process
    public function purchase() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = intval($_POST['product_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);
            
            if ($quantity <= 0) {
                header('Location: /vending?error=invalid_quantity');
                exit;
            }
            
            $product = $this->productModel->getById($productId);
            
            if (!$product || $product['quantity_available'] < $quantity) {
                header('Location: /vending?error=insufficient_stock');
                exit;
            }
            
            $totalPrice = $product['price'] * $quantity;
            
            $newQuantity = $product['quantity_available'] - $quantity;
            $this->productModel->updateQuantity($productId, $newQuantity);
            
            require_once __DIR__ . '/../models/Transaction.php';
            $transactionModel = new Transaction();
            $transactionModel->create($_SESSION['user_id'], $productId, $quantity, $totalPrice);
            
            header('Location: /vending?success=purchase_completed');
            exit;
        }
        
        header('Location: /vending');
        exit;
    }
    
    // Vending machine view - available products for users
    public function vending() {
        $this->requireAuth();
        
        $products = $this->productModel->getAll();
        require __DIR__ . '/../views/vending/index.php';
    }
}