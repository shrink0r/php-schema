<?php

// @codeCoverageIgnoreStart

$givenSchema = [
    'type' => 'assoc',
    'properties' => [
        'username' => [ 'type' => 'string' ],
        'firstname' => [ 'type' => 'string' ],
        'lastname' => [ 'type' => 'string' ],
        'email' => [ 'type' => 'string' ],
        'rating' => [ 'type' => 'enum', 'one_of' => [ 'int', 'float' ] ],
        'tags' => [ 'type' => 'sequence', 'one_of' => [ 'string' ] ],
        'is_active' => [ 'type' => 'bool' ],
        'address' => [
            'type' =>'assoc',
            'properties' => [
                'street' => [ 'type' => 'string' ],
                'zipCode' => [ 'type' => 'int' ],
                'city' => [ 'type' => 'choice', 'one_of' => [ 'berlin', 'new york' ] ],
                'coords' => [
                    'type' => 'assoc',
                    'properties' => [
                        'lon' => [ 'type' => 'float' ],
                        'lat' => [ 'type' => 'float' ]
                    ]
                ]
            ]
        ]
    ]
];

$givenData = [
    'username' => 'superuser',
    'firstname' => 'clark',
    'lastname' => 'kent',
    'email' => 'superuser@example.com',
    'tags' => [ 'hero', 2 ],
    'rating' => 23.0,
    'is_active' => true,
    'address' => [
        'street' => 'fleetstreet 23',
        'zipCode' => 23542,
        'city' => [ 'melmac' ],
        'coords' => [
            'lon' => 12.34,
            'lat' => 13
        ]
    ]
];

$expectedErrors = [
    'tags' => [ 1 => [ Shrink0r\Configr\Error::NON_STRING ] ],
    'address' => [
        'city' => [ Shrink0r\Configr\Error::INVALID_CHOICE ],
        'coords' => [ 'lat' => [ Shrink0r\Configr\Error::NON_FLOAT ]]
    ]
];

return [
    'givenSchema' => $givenSchema,
    'givenData' => $givenData,
    'expectedErrors' => $expectedErrors
];

// @codeCoverageIgnoreEnd
