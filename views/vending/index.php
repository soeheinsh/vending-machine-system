<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vending Machine - Product Purchase</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: white;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .alerts {
            padding: 1rem 1.5rem;
        }
        
        .success { 
            color: #155724; 
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }
        
        .error { 
            color: #721c24; 
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }
        
        .alert-fade {
            opacity: 0;
        }
        
        .content {
            padding: 1.5rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .product-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .product-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-size: 1.1rem;
            color: #28a745;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .product-quantity {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .product-form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .form-group label {
            font-weight: 500;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .form-group input {
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .purchase-btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .purchase-btn:hover {
            transform: translateY(-2px);
        }
        
        .purchase-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .no-products {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .nav-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Vending Machine</h1>
            <p>Enjoy our premium products and services</p>
        </div>
        
        <div class="nav-bar">
            <div class="nav-links">
                <a href="/logout" class="btn btn-logout">Logout</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/products" class="btn btn-admin">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="alerts">
            <?php if (isset($_GET['success']) && $_GET['success'] === 'purchase_completed'): ?>
                <div class="success" id="success-alert">✅ Purchase completed successfully!</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] === 'insufficient_stock'): ?>
                    <div class="error" id="error-alert">❌ Error: Insufficient stock for this product!</div>
                <?php elseif ($_GET['error'] === 'invalid_quantity'): ?>
                    <div class="error" id="error-alert">❌ Error: Invalid quantity selected!</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="content">
            <h2 class="section-title">Available Products</h2>
            
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>📭 No products available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <?php if ($product['quantity_available'] > 0): ?>
                        <div class="product-card">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                            <p class="product-quantity">📦 Available: <?= $product['quantity_available'] ?></p>
                            
                            <form method="post" action="/purchase" class="product-form">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                
                                <div class="form-group">
                                    <label for="quantity_<?= $product['id'] ?>">Quantity:</label>
                                    <input type="number" id="quantity_<?= $product['id'] ?>" name="quantity" min="1" max="<?= $product['quantity_available'] ?>" value="1">
                                </div>
                                
                                <button type="submit" class="purchase-btn" <?= $product['quantity_available'] == 0 ? 'disabled' : '' ?>>
                                    💰 Purchase Now
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-hide success and error messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            
            // Hide success message after 5 seconds
            if (successAlert) {
                setTimeout(function() {
                    successAlert.classList.add('alert-fade');
                    setTimeout(function() {
                        successAlert.remove();
                    }, 500);
                }, 5000);
            }
            
            // Hide error message after 7 seconds
            if (errorAlert) {
                setTimeout(function() {
                    errorAlert.classList.add('alert-fade');
                    setTimeout(function() {
                        errorAlert.remove();
                    }, 500);
                }, 7000);
            }
        });
    </script>
</body>
</html>