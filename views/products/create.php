<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Vending System</title>
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
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 1rem;
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
            font-size: 0.9rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .form-container {
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 1.5rem;
        }
        
        .form-section h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group input.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.5rem;
            border-radius: 5px;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            border-left: 4px solid #dc3545;
        }
        
        .general-error {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border-left: 4px solid #dc3545;
        }
        
        .submit-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .submit-btn {
            flex: 1;
            padding: 1rem;
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        
        .back-btn {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .submit-group {
                flex-direction: column;
            }
            
            .back-btn {
                width: 100%;
            }
            
            .nav-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Add New Product</h1>
            <p>Enter product details to add to inventory</p>
        </div>
        
        <div class="nav-bar">
            <div class="nav-links">
                <a href="/products" class="btn btn-secondary">← Back to Products</a>
            </div>
        </div>
        
        <div class="form-container">
            <?php if (isset($errors['general'])): ?>
                <div class="general-error">
                    ⚠️ <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="" id="productForm">
                <div class="form-section">
                    <h2>Product Details</h2>
                    
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="<?= isset($errors['name']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($formData['name'] ?? '') ?>" 
                               required
                               placeholder="Enter product name">
                        <?php if (isset($errors['name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price ($)*</label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               step="0.01" 
                               min="0"
                               class="<?= isset($errors['price']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($formData['price'] ?? '') ?>" 
                               required
                               placeholder="0.00">
                        <?php if (isset($errors['price'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['price']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity_available">Quantity Available *</label>
                        <input type="number" 
                               id="quantity_available" 
                               name="quantity_available" 
                               min="0"
                               class="<?= isset($errors['quantity_available']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($formData['quantity_available'] ?? '') ?>" 
                               required
                               placeholder="Enter available quantity">
                        <?php if (isset($errors['quantity_available'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['quantity_available']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="submit-group">
                    <button type="submit" class="submit-btn">➕ Create Product</button>
                    <a href="/products" class="back-btn">← Back to Products</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Client-side validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const price = parseFloat(document.getElementById('price').value);
            const quantity = parseInt(document.getElementById('quantity_available').value);
            
            // Reset error classes
            document.querySelectorAll('input.error').forEach(input => {
                input.classList.remove('error');
            });
            
            let errors = [];
            
            if (!name) {
                errors.push('Product name is required.');
                document.getElementById('name').classList.add('error');
            }
            
            if (isNaN(price) || price <= 0) {
                errors.push('Price must be a positive value.');
                document.getElementById('price').classList.add('error');
            }
            
            if (isNaN(quantity) || quantity < 0) {
                errors.push('Quantity available must be non-negative.');
                document.getElementById('quantity_available').classList.add('error');
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
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        document.getElementById('quantity_available').addEventListener('input', function() {
            const value = parseInt(this.value);
            if (isNaN(value) || value < 0) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
    </script>
</body>
</html>