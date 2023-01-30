<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'align_multiline_comment' => ['comment_type' => 'all_multiline'],
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'assign_null_coalescing_to_coalesce_equal' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'case',
                'declare',
                'default',
                'phpdoc',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
                'yield_from',
            ]
        ],
        'cast_spaces' => ['space' => 'none'],
        'class_definition' => false,
        'class_reference_name_casing' => true,
        'concat_space' => ['spacing' => 'one'],
        'control_structure_continuation_position' => ['position' => 'same_line'],
        'date_time_create_from_format_call' => true,
        'declare_parentheses' => true,
        'declare_strict_types' => true,
        'doctrine_annotation_array_assignment' => true,
        'doctrine_annotation_indentation' => true,
        'doctrine_annotation_spaces' => true,
        'explicit_string_variable' => true,
        'fopen_flag_order' => true,
        'fopen_flags' => true,
        'function_typehint_space' => true,
        'general_phpdoc_tag_rename' => true,
        'global_namespace_import' => true,
        'heredoc_indentation' => true,
        'lambda_not_used_import' => true,
        'list_syntax' => true,
        'method_chaining_indentation' => true,
        'multiline_comment_opening_closing' => true,
        'multiline_whitespace_before_semicolons' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'allow_unused_params' => false,
            'remove_inheritdoc' => false,
        ],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'non_printable_character' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'operator_linebreak' => true,
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'php_unit_namespaced' => true,
        'php_unit_test_annotation' => true,
        'php_unit_test_class_requires_covers' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_no_empty_return' => true,
        'phpdoc_order' => true,
        'phpdoc_tag_casing' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'protected_to_private' => true,
        'return_assignment' => true,
        'simplified_if_return' => true,
        'simplified_null_return' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'ternary_to_null_coalescing' => true,
        'types_spaces' => [
            'space' => 'single',
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder);
