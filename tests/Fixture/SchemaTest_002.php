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
                                ':any_name:' => [ 'type' => 'any' ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$givenData = [
    'transports' => [
        'sync' => [ 'classs' => 'foobar' ],
        'async' => [ 'class' => null ],
        'pubsub' => [ 'class' => [ 'foo' => 'bar' ] ]
    ]
];

$expectedErrors = [
    'transports' => [
        'sync' => [
            'class' => [ Shrink0r\PhpSchema\Error::MISSING_KEY, Shrink0r\PhpSchema\Error::MISSING_VALUE ],
            'classs' => [ Shrink0r\PhpSchema\Error::UNEXPECTED_KEY ]
        ],
        'async' => [ 'class' => [ Shrink0r\PhpSchema\Error::MISSING_VALUE ] ],
        'pubsub' => [ 'class' => [ Shrink0r\PhpSchema\Error::NON_SCALAR ] ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenData' => $givenData,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
