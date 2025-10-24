<!DOCTYPE html>
<html>
<head>
    <title>Vending Machine</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .product { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
        .product h3 { margin-top: 0; }
        .product-form { margin-top: 10px; }
        .btn { padding: 10px 15px; text-decoration: none; background-color: #007cba; color: white; border: none; cursor: pointer; }
        .btn-admin { background-color: #28a745; }
        .success { 
            color: green; 
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }
        .error { 
            color: red; 
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }
        .alert-fade {
            opacity: 0;
        }
    </style>
</head>
<body>
    <h1>Vending Machine</h1>
    
    <?php if (isset($_GET['success']) && $_GET['success'] === 'purchase_completed'): ?>
        <div class="success" id="success-alert">Purchase completed successfully!</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'insufficient_stock'): ?>
            <div class="error" id="error-alert">Error: Insufficient stock for this product!</div>
        <?php elseif ($_GET['error'] === 'invalid_quantity'): ?>
            <div class="error" id="error-alert">Error: Invalid quantity selected!</div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div>
        <a href="/logout" class="btn">Logout</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="/products" class="btn btn-admin">Admin Panel</a>
        <?php endif; ?>
    </div>

    <h2>Available Products</h2>
    
    <?php if (empty($products)): ?>
        <p>No products available at the moment.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <?php if ($product['quantity_available'] > 0): ?>
            <div class="product">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p>Price: $<?= number_format($product['price'], 2) ?></p>
                <p>Available: <?= $product['quantity_available'] ?></p>
                
                <form method="post" action="/purchase" class="product-form">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <label for="quantity_<?= $product['id'] ?>">Quantity:</label>
                    <input type="number" id="quantity_<?= $product['id'] ?>" name="quantity" min="1" max="<?= $product['quantity_available'] ?>" value="1">
                    <button type="submit" class="btn" <?= $product['quantity_available'] == 0 ? 'disabled' : '' ?>>Purchase</button>
                </form>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

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