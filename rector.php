<?php

declare(strict_types=1);

// Lib-local config — shared rules live in config/rector.php (reusable by consumers)
$builder = require __DIR__ . '/config/rector.php';

return $builder
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRootFiles()
    ->withCache(__DIR__ . '/var/cache/rector');
