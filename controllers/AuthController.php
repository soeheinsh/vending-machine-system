<?php
require_once __DIR__ . '/../models/VendingUser.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new VendingUser();
    }

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->userModel->verifyPassword($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    header('Location: /products');
                } else {
                    header('Location: /vending');
                }
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];
            if ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } elseif ($this->userModel->findByUsername($username)) {
                $error = 'Username already exists.';
            } else {
                $this->userModel->create($username, $password);
                header('Location: /login');
                exit;
            }
        }
        require __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
} 