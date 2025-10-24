<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; cursor: pointer; }
        .actions { margin: 10px 0; }
        .pagination { margin-top: 20px; }
        .pagination a { padding: 8px 16px; text-decoration: none; border: 1px solid #ddd; }
        .btn { padding: 10px 15px; text-decoration: none; background-color: #007cba; color: white; border: none; cursor: pointer; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <h1>Product Management</h1>
    <div class="actions">
        <a href="/products/create" class="btn">Add New Product</a>
        <a href="/vending" class="btn">Back to Vending</a>
        <a href="/logout" class="btn">Logout</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div style="color: red;"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th onclick="sortTable('id')">ID <span>↕️</span></th>
                <th onclick="sortTable('name')">Name <span>↕️</span></th>
                <th onclick="sortTable('price')">Price <span>↕️</span></th>
                <th onclick="sortTable('quantity_available')">Quantity <span>↕️</span></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>$<?= number_format($product['price'], 2) ?></td>
                <td><?= htmlspecialchars($product['quantity_available']) ?></td>
                <td>
                    <a href="/products/edit?id=<?= $product['id'] ?>">Edit</a> |
                    <a href="/products/delete?id=<?= $product['id'] ?>" class="btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&sort=<?= $orderBy ?>&order=<?= $orderDir ?>" 
               <?= $i == $page ? 'style="background-color: #007cba; color: white;"' : '' ?>>
                <?= $i ?>
            </a>
        <?php endfor; ?>
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