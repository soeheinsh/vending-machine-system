<?php
require_once __DIR__ . '/DIContainer.php';

class App {
    private $container;

    public function __construct() {
        $this->container = new DIContainer();
        $this->registerServices();
    }

    private function registerServices() {
        // Register models as services
        $this->container->set('productModel', function($container) {
            return new \Product();
        });
        
        $this->container->set('userModel', function($container) {
            return new \VendingUser();
        });
        
        $this->container->set('transactionModel', function($container) {
            return new \Transaction();
        });
        
        // Register controller with dependencies
        $this->container->set('productsController', function($container) {
            return new \ProductsController(
                $container->get('productModel'),
                $container->get('userModel'),
                $container->get('transactionModel')
            );
        });
        
        $this->container->set('authController', function($container) {
            return new \AuthController();
        });
        
        $this->container->set('router', function($container) {
            $routes = require __DIR__ . '/../routes/web.php';
            return new \Router($routes);
        });
    }

    public function run() {
        session_start();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        if (strpos($path, '/api/') === 0) {
            
            $apiRoutes = require __DIR__ . '/../routes/api.php';
            
            // Create API router instance with the routes
            $apiRouter = new Router($apiRoutes);
            
            // Set JSON response headers for API
            header('Content-Type: application/json');
            
            // Dispatch the API request
            $apiRouter->dispatch($path);
            exit;
        }
        
        $router = $this->container->get('router');
        
        // Handle root path
        if ($path === '/') {
            if (!empty($_SESSION['user_id'])) {
                if ($_SESSION['role'] === 'admin') {
                    header('Location: /products');
                } else {
                    header('Location: /vending');
                }
                exit;
            } else {
                header('Location: /login');
                exit;
            }
        }
        
        $router->dispatch($path);
    }
    
    public function getContainer() {
        return $this->container;
    }
}
