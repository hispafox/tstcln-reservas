<?php

$carpetas = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
    ])
    ->setFinder($carpetas);
