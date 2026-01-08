<?php
declare(strict_types=1);

spl_autoload_register(function (string $class) {

    $baseDir = dirname(__DIR__) . '/'; 

    $map = [
        'core\\'         => 'core/',
        'config\\'       => 'config/',
        'entities\\'     => 'entities/',
        'repositories\\' => 'repositories/',
        'services\\'     => 'services/',
        'utils\\'        => 'utils/',
        'exceptions\\'   => 'exceptions/',
    ];

    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
            return;
        }
    }
});
