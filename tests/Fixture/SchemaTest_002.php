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

$givenData = [
    'transports' => [
        'sync' => [ 'classs' => 'foobar' ],
        'async' => [ 'class' => null ],
        'pubsub' => [ 'class' => [ 'foo' => 'bar' ] ]
    ]
];

$expectedErrors = [
    'transports' => [
        'sync' => [ 'class' => [ "key_missing" ] ],
        'async' => [ 'class' => [ "value_missing" ] ],
        'pubsub' => [ 'class' => [ "non_scalar" ] ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenData' => $givenData,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
