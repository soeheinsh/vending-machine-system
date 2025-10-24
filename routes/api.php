<?php

return [
    'attribute' => [
        '/api/products' => [
            'controller' => 'ProductController',
            'action' => 'dispatchProducts',
            'method' => 'GET|POST',
            'auth_required' => false
        ],
        '/api/products/{id}' => [
            'controller' => 'ProductController',
            'action' => 'dispatchProductById',
            'method' => 'GET|PUT|DELETE',
            'auth_required' => false
        ],
        '/api/purchase' => [
            'controller' => 'ProductController',
            'action' => 'dispatchPurchase',
            'method' => 'POST',
            'auth_required' => false
        ],
        '/api/auth/login' => [
            'controller' => 'ProductController',
            'action' => 'dispatchLogin',
            'method' => 'POST',
            'auth_required' => false
        ]
    ]
];