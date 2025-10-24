<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Vending System</title>
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
            max-width: 1400px;
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
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
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
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 1.5rem;
            border-left: 4px solid #dc3545;
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
        
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            user-select: none;
        }
        
        th:hover {
            opacity: 0.9;
        }
        
        th.sortable::after {
            content: ' ↕️';
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.5rem 0.75rem;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            padding: 0.75rem 1rem;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #007cba;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #007cba;
            color: white;
            border-color: #007cba;
        }
        
        .pagination .current {
            background: #007cba;
            color: white;
            border-color: #007cba;
            font-weight: bold;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .action-buttons {
                justify-content: center;
            }
            
            .table-container {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
            }
            
            .actions-cell {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .pagination {
                gap: 0.25rem;
            }
            
            .pagination a, .pagination span {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Products Management</h1>
            <p>Manage your vending machine inventory</p>
        </div>
        
        <div class="actions-bar">
            <div class="action-buttons">
                <a href="/products/create" class="btn btn-primary">➕ Add New Product</a>
                <a href="/vending" class="btn btn-secondary">🛒 Back to Vending</a>
                <a href="/logout" class="btn btn-logout">🚪 Logout</a>
            </div>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                ⚠️ <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <div class="content">
            <h2 class="section-title">Product Inventory</h2>
            
            <?php if (!empty($products)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th onclick="sortTable('id')" class="sortable">ID</th>
                                <th onclick="sortTable('name')" class="sortable">Name</th>
                                <th onclick="sortTable('price')" class="sortable">Price</th>
                                <th onclick="sortTable('quantity_available')" class="sortable">Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['id']) ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td>
                                    <?= htmlspecialchars($product['quantity_available']) ?>
                                    <?php if ($product['quantity_available'] < 5): ?>
                                        <span style="color: red; font-size: 0.8em;">⚠️ Low Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="/products/edit?id=<?= $product['id'] ?>" class="action-btn btn-edit">✏️ Edit</a>
                                    <a href="/products/delete?id=<?= $product['id'] ?>" 
                                       class="action-btn btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this product?')">🗑️ Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&sort=<?= $orderBy ?>&order=<?= $orderDir ?>">‹ Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&sort=<?= $orderBy ?>&order=<?= $orderDir ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&sort=<?= $orderBy ?>&order=<?= $orderDir ?>">Next ›</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>📭 No products found. <a href="/products/create" class="btn btn-primary">Add your first product</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function sortTable(sortBy) {
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort');
            const currentOrder = urlParams.get('order');
            
            let newOrder = 'asc';
            if (currentSort === sortBy && currentOrder === 'asc') {
                newOrder = 'desc';
            }
            
            urlParams.set('sort', sortBy);
            urlParams.set('order', newOrder);
            urlParams.set('page', 1);
            
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>