<?php
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/VendingUser.php';
require_once __DIR__ . '/../../models/Transaction.php';
require_once __DIR__ . '/../../core/utils/Validation.php';
require_once __DIR__ . '/../../core/auth/JWT.php';

class ProductController {
    private $productModel;
    private $userModel;
    private $transactionModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->userModel = new VendingUser();
        $this->transactionModel = new Transaction();
    }


    public function authenticate() {
        $headers = $this->getAuthorizationHeader();
        
        if (!$headers) {
            return false;
        }
        
        $token = str_replace('Bearer ', '', $headers);
        
        if (!JWT::isValid($token)) {
            return false;
        }
        
        $payload = JWT::decode($token);
        return $payload['user_id'] ?? false;
    }
    
    // API route dispatcher methods
    public function dispatchProducts() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'GET') {
            $this->getAllProducts();
        } elseif ($method === 'POST') {
            // Authentication required for POST
            $userId = $this->authenticate();
            if (!$userId) {
                $this->sendErrorResponse(401, 'Unauthorized');
                return;
            }
            $this->createProduct();
        } else {
            $this->sendErrorResponse(405, 'Method not allowed');
        }
    }
    
    public function dispatchProductById() {
        $method = $_SERVER['REQUEST_METHOD'];
        // Get ID from route parameters
        $id = $GLOBALS['route_params'][0] ?? null;
        
        if (!$id) {
            $this->sendErrorResponse(400, 'Product ID is required');
            return;
        }
        
        if ($method === 'GET') {
            // GET is public - no authentication required
            $this->getProductById($id);
        } elseif ($method === 'PUT') {
            // Authentication required for PUT
            $userId = $this->authenticate();
            if (!$userId) {
                $this->sendErrorResponse(401, 'Unauthorized');
                return;
            }
            $this->updateProduct($id);
        } elseif ($method === 'DELETE') {
            // Authentication required for DELETE
            $userId = $this->authenticate();
            if (!$userId) {
                $this->sendErrorResponse(401, 'Unauthorized');
                return;
            }
            $this->deleteProduct($id);
        } else {
            $this->sendErrorResponse(405, 'Method not allowed');
        }
    }
    
    public function dispatchPurchase() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'POST') {
            $this->sendErrorResponse(405, 'Method not allowed');
            return;
        }
        
        // Authentication required for purchase
        $userId = $this->authenticate();
        if (!$userId) {
            $this->sendErrorResponse(401, 'Unauthorized');
            return;
        }
        
        $this->purchaseProduct($userId);
    }
    
    public function dispatchLogin() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'POST') {
            $this->sendErrorResponse(405, 'Method not allowed');
            return;
        }
        
        $this->login();
    }

    private function getAuthorizationHeader() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // NGINX or Apache with rewrite
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['username']) || !isset($input['password'])) {
            $this->sendErrorResponse(400, 'Username and password are required');
            return;
        }
        
        $user = $this->userModel->verifyPassword($input['username'], $input['password']);
        
        if ($user) {
            // Create JWT token
            $payload = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'exp' => time() + (7 * 24 * 60 * 60) // Token expires in 7 days
            ];
            
            $token = JWT::encode($payload);
            
            $response = [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ];
            
            echo json_encode($response);
        } else {
            $this->sendErrorResponse(401, 'Invalid credentials');
        }
    }

    public function getAllProducts() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 10;
        $offset = ($page - 1) * $limit;
        
        $orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';
        $orderDir = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';
        
        $products = $this->productModel->getAll($limit, $offset, $orderBy, $orderDir);
        $total = $this->productModel->countAll();
        
        $response = [
            'success' => true,
            'data' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ];
        
        echo json_encode($response);
    }

    public function getProductById($id) {
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->sendErrorResponse(404, 'Product not found');
            return;
        }
        
        $response = [
            'success' => true,
            'data' => $product
        ];
        
        echo json_encode($response);
    }

    public function createProduct() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendErrorResponse(400, 'Invalid JSON input');
            return;
        }
        
        $errors = Validation::validateProduct($input);
        
        if (!empty($errors)) {
            $this->sendErrorResponse(400, 'Validation failed', $errors);
            return;
        }
        
        $data = [
            'name' => Validation::sanitize($input['name']),
            'price' => floatval($input['price']),
            'quantity_available' => intval($input['quantity_available'])
        ];
        
        $result = $this->productModel->create($data);
        
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Product created successfully'
            ];
            http_response_code(201);
            echo json_encode($response);
        } else {
            $this->sendErrorResponse(500, 'Failed to create product');
        }
    }

    public function updateProduct($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendErrorResponse(400, 'Invalid JSON input');
            return;
        }
        
        $errors = Validation::validateProduct($input);
        
        if (!empty($errors)) {
            $this->sendErrorResponse(400, 'Validation failed', $errors);
            return;
        }
        
        $existingProduct = $this->productModel->getById($id);
        if (!$existingProduct) {
            $this->sendErrorResponse(404, 'Product not found');
            return;
        }
        
        $data = [
            'name' => Validation::sanitize($input['name']),
            'price' => floatval($input['price']),
            'quantity_available' => intval($input['quantity_available'])
        ];
        
        $result = $this->productModel->update($id, $data);
        
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Product updated successfully'
            ];
            echo json_encode($response);
        } else {
            $this->sendErrorResponse(500, 'Failed to update product');
        }
    }

    public function deleteProduct($id) {
        $existingProduct = $this->productModel->getById($id);
        if (!$existingProduct) {
            $this->sendErrorResponse(404, 'Product not found');
            return;
        }
        
        $result = $this->productModel->delete($id);
        
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Product deleted successfully'
            ];
            echo json_encode($response);
        } else {
            $this->sendErrorResponse(500, 'Failed to delete product');
        }
    }

    public function purchaseProduct($userId) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendErrorResponse(400, 'Invalid JSON input');
            return;
        }
        
        $errors = Validation::validatePurchase($input);
        
        if (!empty($errors)) {
            $this->sendErrorResponse(400, 'Validation failed', $errors);
            return;
        }
        
        $productId = intval($input['product_id']);
        $quantity = intval($input['quantity']);
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $this->sendErrorResponse(404, 'Product not found');
            return;
        }
        
        if ($product['quantity_available'] < $quantity) {
            $this->sendErrorResponse(400, 'Insufficient stock');
            return;
        }
        
        $totalPrice = $product['price'] * $quantity;
        
        $newQuantity = $product['quantity_available'] - $quantity;
        $updateResult = $this->productModel->updateQuantity($productId, $newQuantity);
        
        if ($updateResult) {
            $this->transactionModel->create($userId, $productId, $quantity, $totalPrice);
            
            $response = [
                'success' => true,
                'message' => 'Purchase completed successfully',
                'data' => [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice
                ]
            ];
            echo json_encode($response);
        } else {
            $this->sendErrorResponse(500, 'Failed to update product quantity');
        }
    }

    private function sendErrorResponse($code, $message, $details = null) {
        http_response_code($code);
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($details) {
            $response['details'] = $details;
        }
        
        echo json_encode($response);
    }
}