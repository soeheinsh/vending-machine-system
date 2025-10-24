<?php
// Autoload classes from controllers, models, core, and API controllers
spl_autoload_register(function ($class) {
    $paths = ['controllers/', 'models/', 'core/', 'controllers/api/'];
    foreach ($paths as $path) {
        $file = __DIR__ . '/../' . $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/../core/App.php';

$app = new App();
$app->run();

