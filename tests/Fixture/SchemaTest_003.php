<?php

// @codeCoverageIgnoreStart

$givenSchema = [
    'type' => 'assoc',
    'properties' => [
        'username' => [ 'type' => 'scalar' ],
        'firstname' => [ 'type' => 'scalar' ],
        'lastname' => [ 'type' => 'scalar' ],
        'email' => [ 'type' => 'scalar' ],
        'tags' => [ 'type' => 'sequence', 'one_of' => [ 'scalar' ] ],
        'address' => [
            'type' =>'assoc',
            'properties' => [
                'street' => [ 'type' => 'scalar' ],
                'zipCode' => [ 'type' => 'scalar' ],
                'city' => [ 'type' => 'scalar' ]
            ]
        ]
    ]
];

$givenData = [
    'username' => 'superuser',
    'firstname' => 'clark',
    'lastname' => 'kent',
    'email' => 'superuser@example.com',
    'tags' => [ 'hero', [ 'security' ] ],
    'address' => [
        'street' => 'fleetstreet 23',
        'zipCode' => '23542',
        'city' => [ 'melmac' ]
    ]
];

$expectedErrors = [
    'tags' => [ '@1' => Shrink0r\Configr\Error::NON_SCALAR ],
    'address' => [
        'city' => [ Shrink0r\Configr\Error::NON_SCALAR ]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenData' => $givenData,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
