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
        'sync' => [ 'class' => [ Shrink0r\Configr\Error::MISSING_KEY ] ],
        'async' => [ 'class' => [ Shrink0r\Configr\Error::MISSING_VALUE ] ],
        'pubsub' => [ 'class' => [ Shrink0r\Configr\Error::NON_SCALAR ] ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenData' => $givenData,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
