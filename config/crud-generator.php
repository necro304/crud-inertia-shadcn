<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Model Namespace
    |--------------------------------------------------------------------------
    |
    | The default namespace for generated Model classes.
    |
    */
    'model_namespace' => 'App\Models',

    /*
    |--------------------------------------------------------------------------
    | Default Controller Namespace
    |--------------------------------------------------------------------------
    |
    | The default namespace for generated Controller classes.
    |
    */
    'controller_namespace' => 'App\Http\Controllers',

    /*
    |--------------------------------------------------------------------------
    | Default Request Namespace
    |--------------------------------------------------------------------------
    |
    | The default namespace for generated Form Request classes.
    |
    */
    'request_namespace' => 'App\Http\Requests',

    /*
    |--------------------------------------------------------------------------
    | Default Resource Namespace
    |--------------------------------------------------------------------------
    |
    | The default namespace for generated API Resource classes.
    |
    */
    'resource_namespace' => 'App\Http\Resources',

    /*
    |--------------------------------------------------------------------------
    | Vue Components Path
    |--------------------------------------------------------------------------
    |
    | The directory path where Vue components will be generated.
    |
    */
    'vue_path' => 'resources/js/Pages',

    /*
    |--------------------------------------------------------------------------
    | Migrations Path
    |--------------------------------------------------------------------------
    |
    | The directory path where migrations will be generated.
    |
    */
    'migrations_path' => 'database/migrations',

    /*
    |--------------------------------------------------------------------------
    | Stubs Path
    |--------------------------------------------------------------------------
    |
    | The directory path where stub templates are located.
    |
    */
    'stubs_path' => base_path('stubs/crud-generator'),

    /*
    |--------------------------------------------------------------------------
    | Field Type Mappings
    |--------------------------------------------------------------------------
    |
    | Mapping of field types to database column types and validation rules.
    |
    */
    'field_types' => [
        'string' => [
            'column' => 'string',
            'cast' => 'string',
            'validation' => ['string', 'max:255'],
        ],
        'text' => [
            'column' => 'text',
            'cast' => 'string',
            'validation' => ['string'],
        ],
        'integer' => [
            'column' => 'integer',
            'cast' => 'integer',
            'validation' => ['integer'],
        ],
        'decimal' => [
            'column' => 'decimal',
            'cast' => 'decimal:2',
            'validation' => ['numeric'],
        ],
        'boolean' => [
            'column' => 'boolean',
            'cast' => 'boolean',
            'validation' => ['boolean'],
        ],
        'date' => [
            'column' => 'date',
            'cast' => 'date',
            'validation' => ['date'],
        ],
        'datetime' => [
            'column' => 'dateTime',
            'cast' => 'datetime',
            'validation' => ['date'],
        ],
        'timestamp' => [
            'column' => 'timestamp',
            'cast' => 'datetime',
            'validation' => ['date'],
        ],
        'json' => [
            'column' => 'json',
            'cast' => 'array',
            'validation' => ['json'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for code generation.
    |
    */
    'defaults' => [
        'soft_deletes' => true,
        'auditing' => true,
        'generate_views' => true,
        'timestamps' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reserved Words
    |--------------------------------------------------------------------------
    |
    | Reserved words that cannot be used as resource names.
    |
    */
    'reserved_words' => [
        'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch',
        'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach',
        'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final',
        'finally', 'fn', 'for', 'foreach', 'function', 'global', 'goto', 'if',
        'implements', 'include', 'include_once', 'instanceof', 'insteadof',
        'interface', 'isset', 'list', 'match', 'namespace', 'new', 'or', 'print',
        'private', 'protected', 'public', 'readonly', 'require', 'require_once',
        'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use',
        'var', 'while', 'xor', 'yield',
    ],
];
