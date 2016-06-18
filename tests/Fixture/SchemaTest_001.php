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
                        'class' => [ 'type' => 'fqcn' ],
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
        ],
        'subscriptions' => [
            'type' => 'sequence',
            'required' => true,
            'one_of' => [ '&subscription' ]
        ]
    ],
    'customTypes' => [
        'subscription' => [
            'type' => 'assoc',
            'properties' => [
                'transport' => [ 'type' => 'scalar' ],
                'commands' => [
                    'type' => 'assoc',
                    'properties' => [
                        ':any_name:' => [
                            'type' => 'assoc',
                            'properties' => [
                                'handler' => [ 'type' => 'fqcn' ],
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
        ]
    ]
];

$givenData = [
    'transports' => [
        'sync' => [ 'class' => SyncnTransport::class ]
    ],
    'subscriptions' => [
        [
            'transport' => 'sync',
            'commands' => [
                'foh.system_account.user.create_user' => [ 'handler' => CrateUserHandler::class ]
            ]
        ]
    ]
];

$expectedErrors = [
    'transports' => [
        'sync' => [ 'class' => [ 'class_not_exists' ] ]
    ],
    'subscriptions' => [
        '@0' => [
            'commands' => [
                'foh.system_account.user.create_user' => [ 'handler' => [ 'class_not_exists' ] ]
            ]
        ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenData' => $givenData,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
