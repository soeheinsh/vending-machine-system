<?php
return [
    'public' => [
        // Auth routes
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
        '/logout' => ['AuthController', 'logout'],
    ],
    'auth' => [
        // Vending machine route (all logged-in users)
        '/vending' => ['ProductsController', 'vending'],
        '/purchase' => ['ProductsController', 'purchase'],
    ],
    'admin' => [
        // Product management routes (admin only)
        '/products' => ['ProductsController', 'index'],
        '/products/create' => ['ProductsController', 'create'],
        '/products/edit' => ['ProductsController', 'edit'],
        '/products/delete' => ['ProductsController', 'delete'],
    ],
    'attribute' => [
        // Attribute-style routes for SEO-friendly URLs
        '/vending/purchase' => [
            'controller' => 'ProductsController',
            'action' => 'purchase',
            'method' => 'POST',
            'auth_required' => true
        ],
    ]
];
