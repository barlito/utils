<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/*
 * Shareable Rector base config — use it from your project's rector.php:
 *
 *     $builder = require 'vendor/barlito/utils/config/rector.php';
 *
 *     return $builder
 *         ->withPaths([__DIR__ . '/src'])
 *         ->withCache(__DIR__ . '/var/cache/rector');
 *
 * Intentionally contains no paths / cache: those are project specific.
 * The composer-based sets adapt to the including project's dependencies.
 */
return RectorConfig::configure()
    ->withPhpSets(php84: true)
    ->withComposerBased(doctrine: true, phpunit: true, symfony: true)
    ->withAttributesSets(symfony: true, doctrine: true, phpunit: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withImportNames(importShortClasses: false, removeUnusedImports: true);
