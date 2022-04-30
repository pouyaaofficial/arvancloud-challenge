<?php

use PhpCsFixer\Config;
use Symfony\Component\Finder\Finder;

$finder = Finder::create()
    ->ignoreVCS(true)
    ->ignoreVCSIgnored(true)
    ->ignoreDotFiles(true)
    ->in(__DIR__);

return (new Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
    ])
    ->setFinder($finder)
    ->setLineEnding("\n");
