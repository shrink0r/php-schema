<?php

require __DIR__.'/vendor/autoload.php';

$config = [
    'transports' => [
        'sync' => [
            'class' => SyncnTransport::class
        ]
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

$configScheme = [
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

$scheme = new Shrink0r\Configr\Scheme('command_bus', $configScheme);
$errors = $scheme->validate($config);

var_dump($errors);
