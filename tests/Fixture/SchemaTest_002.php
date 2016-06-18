<?php

// @codeCoverageIgnoreStart

$givenSchema = [
    'type' => 'assoc',
    'properties' => [
        'transports' => [
            'type' => 'assoc',
            'properties' => [
                ':any_name:' => [
                    'type' => 'assoc',
                    'required' => false,
                    'properties' => [
                        'class' => [ 'type' => 'scalar' ],
                        'settings' => [
                            'type' => 'assoc',
                            'required' => false,
                            'properties' => [
                                ':any_name:' => [ 'type' => 'dynamic' ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$givenConfig = [
    'transports' => [
        'sync' => [ 'classs' => 'foobar' ],
        'async' => [ 'class' => null ]
    ]
];

$expectedErrors = [
    'transports' => [
        'sync' => [ 'class' => [ "Missing required key: 'class'" ] ],
        'async' => [ 'class' => [ "Missing required value for key: 'class'" ] ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenConfig' => $givenConfig,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
