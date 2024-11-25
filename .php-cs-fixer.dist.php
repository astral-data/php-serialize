<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('vendor') // 排除目录
    ->name('*.php') // 匹配文件
    ->notName('*.blade.php') // 排除文件
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space'],
        'no_unused_imports' => true, // 移除未使用的 use
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