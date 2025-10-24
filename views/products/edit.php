<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; text-decoration: none; background-color: #007cba; color: white; border: none; cursor: pointer; }
        .btn-back { background-color: #6c757d; }
        .btn-delete { background-color: #dc3545; }
        .error { color: red; }
        .error-message { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>Edit Product</h1>
    
    <?php if (isset($errors['general'])): ?>
        <div class="error"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>
    
    <form method="post" action="" id="productForm">
        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
            <?php if (isset($errors['name'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" min="0" 
                   value="<?= htmlspecialchars($formData['price'] ?? '') ?>" required>
            <?php if (isset($errors['price'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['price']) ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="quantity_available">Quantity Available:</label>
            <input type="number" id="quantity_available" name="quantity_available" min="0" 
                   value="<?= htmlspecialchars($formData['quantity_available'] ?? '') ?>" required>
            <?php if (isset($errors['quantity_available'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['quantity_available']) ?></div>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn">Update Product</button>
        <a href="/products" class="btn btn-back">Back to Products</a>
        <a href="/products/delete?id=<?= $product['id'] ?>" class="btn btn-delete" 
           onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
    </form>
    
    <script>
        // Client-side validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const price = parseFloat(document.getElementById('price').value);
            const quantity = parseInt(document.getElementById('quantity_available').value);
            
            let errors = [];
            
            if (!name) {
                errors.push('Product name is required.');
            }
            
            if (isNaN(price) || price <= 0) {
                errors.push('Price must be a positive value.');
            }
            
            if (isNaN(quantity) || quantity < 0) {
                errors.push('Quantity available must be non-negative.');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fix the following errors:\n' + errors.join('\n'));
            }
        });
        
        // Real-time validation for price and quantity
        document.getElementById('price').addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (isNaN(value) || value <= 0) {
                this.style.borderColor = 'red';
            } else {
                this.style.borderColor = '';
            }
        });
        
        document.getElementById('quantity_available').addEventListener('input', function() {
            const value = parseInt(this.value);
            if (isNaN(value) || value < 0) {
                this.style.borderColor = 'red';
            } else {
                this.style.borderColor = '';
            }
        });
    </script>
</body>
</html>