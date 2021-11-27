<?php

use PhpCsFixer\{
    Finder,
    Config,
};

$finder = Finder::create()
    ->in(__DIR__);

$config = (new Config())
    ->setRules([
        "@PSR12" => true,
        "single_import_per_statement" => false,
        "group_import" => true,
        "ordered_imports" => true,
    ])
    ->setFinder($finder);

return $config;
