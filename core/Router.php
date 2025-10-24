<?php
class Router {
    private $routes = [];
    private $authRoutes = [];
    private $adminRoutes = [];
    private $attributeRoutes = [];

    public function __construct($routes) {
        $this->routes = $routes['public'] ?? [];
        $this->authRoutes = $routes['auth'] ?? [];
        $this->adminRoutes = $routes['admin'] ?? [];
        $this->attributeRoutes = $routes['attribute'] ?? [];
    }

    public function dispatch($path) {
        // Check public routes first
        if (isset($this->routes[$path])) {
            $this->loadAndExecute($this->routes[$path]);
            return;
        }

        // Check authenticated routes
        if (isset($this->authRoutes[$path])) {
            $this->checkAuth();
            $this->loadAndExecute($this->authRoutes[$path]);
            return;
        }

        // Check admin routes
        if (isset($this->adminRoutes[$path])) {
            $this->checkAdmin();
            $this->loadAndExecute($this->adminRoutes[$path]);
            return;
        }

        // Check attribute routes (with parameter support)
        if ($this->checkAttributeRoutes($path)) {
            return; // Route was handled
        }
        
        http_response_code(404);
        echo 'Page not found.';
    }

    private function checkAttributeRoutes($path) {
        foreach ($this->attributeRoutes as $routePattern => $routeConfig) {
            // Convert route pattern with placeholders to regex
            $routeRegex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePattern);
            $routeRegex = '#^' . $routeRegex . '$#';
            
            if (preg_match($routeRegex, $path, $matches)) {
                // Remove full match
                array_shift($matches);
                
                // Check if method matches
                $allowedMethods = explode('|', $routeConfig['method']);
                $currentMethod = $_SERVER['REQUEST_METHOD'];
                
                if (!in_array($currentMethod, $allowedMethods)) {
                    continue; // Method not allowed for this route
                }
                
                // Authentication checks
                if ($routeConfig['auth_required'] ?? false) {
                    $this->checkAuth();
                    
                    if (($routeConfig['role_required'] ?? '') === 'admin') {
                        $this->checkAdmin();
                    }
                }
                
                // Load and execute controller action
                $this->loadAndExecuteAttributeRoute($routeConfig, $matches);
                return true; // Indicate route was handled
            }
        }
        return false; // No route was handled
    }

    private function loadAndExecute($route) {
        list($controllerName, $action) = $route;
        // Use autoloader instead of hardcoded path
        $controller = new $controllerName();
        $controller->$action();
    }

    private function loadAndExecuteAttributeRoute($routeConfig, $params) {
        $controllerName = $routeConfig['controller'];
        $action = $routeConfig['action'];
        
        // Store route parameters in a global variable for the controller to access
        $GLOBALS['route_params'] = $params;
        
        $controller = new $controllerName();
        $controller->$action();
    }

    private function checkAuth() {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    private function checkAdmin() {
        $this->checkAuth();
        if ($_SESSION['role'] !== 'admin') {
            header('Location: /vending');
            exit;
        }
    }
} 