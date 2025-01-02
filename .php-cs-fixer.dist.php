<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor','tests'])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_trailing_whitespace' => true,
        'no_empty_statement' => true,
        'line_ending' => true,
        'blank_line_after_opening_tag' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
    ])
    ->setFinder($finder);
