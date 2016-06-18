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

$givenConfig = [
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
        'sync' => [ 'class' => [ 'Class \'SyncnTransport\' does not exist.' ] ]
    ],
    'subscriptions' => [
        'commands' => [
            'foh.system_account.user.create_user' => [ 'handler' => [ 'Class \'CrateUserHandler\' does not exist.' ] ]
        ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenConfig' => $givenConfig,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
