<?php

/**
 * @see https://mlocati.github.io/php-cs-fixer-configurator
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()->exclude('tests')->in(__DIR__);

return (new Config())
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,

        // Arrays
        'array_indentation' => true,
        'array_push' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'no_whitespace_before_comma_in_array' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],

        // Classes
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
            ],
        ],
        'new_with_braces' => true,
        'no_blank_lines_after_class_opening' => true,

        // Operators
        'assign_null_coalescing_to_coalesce_equal' => true,
        'binary_operator_spaces' => ['default' => 'single_space'],
        'logical_operators' => true,
        'not_operator_with_successor_space' => true,
        'object_operator_without_whitespace' => true,

        // Code fixes
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'explicit_string_variable' => true,
        'implode_call' => true,
        'lambda_not_used_import' => true,
        'no_superfluous_elseif' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'return_assignment' => true,
        'ternary_to_null_coalescing' => true,

        // Case transformations
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_casing' => true,

        // Whitespace
        'align_multiline_comment' => ['comment_type' => 'all_multiline'],
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => ['statements' => ['if', 'for', 'foreach', 'do', 'while', 'switch', 'try', 'return']],
        'cast_spaces' => ['space' => 'single'],
        'clean_namespace' => true,
        'comment_to_phpdoc' => ['ignored_tags' => []],
        'concat_space' => ['spacing' => 'one'],
        'heredoc_indentation' => ['indentation' => 'same_as_start'],
        'linebreak_after_opening_tag' => true,
        'method_chaining_indentation' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_extra_blank_lines' => [
            'tokens' => [
                'continue',
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
                'switch',
                'case',
                'default',
            ],
        ],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => ['positions' => ['inside', 'outside']],
        'no_trailing_whitespace' => true,
        'operator_linebreak' => ['position' => 'beginning'],
        'simple_to_complex_string_variable' => true,
        'single_blank_line_before_namespace' => true,
        'types_spaces' => ['space' => 'single'],
        'whitespace_after_comma_in_array' => true,
    ]);
