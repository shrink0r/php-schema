# Basic usage

## Step 1: Define a schema

The schema is given an (array)description of the data-structure, that shall be verified upon invocation of the schema's validation routine.

```php
<?php

use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Schema;

// define a schema
$schemaDefinition = [
    'type' => 'assoc',
    'properties' => [
        "post_id" => [ "type" => "int" ],
        "rating" => [ "type" => "float" ],
        "tags" => [
            "type" => "sequence",
            "one_of" => [ "string" ]
        ],
        "coords" => [
            "type" => "assoc",
            "properties" => [
                "lon" => [ "type" => "float" ],
                "lat" => [ "type" => "float" ]
            ]
        ]
    ]
];

$schema = new Schema('vote', $schemaDefinition, new Factory());

```

## Step 2: Utilize the builder

A builder instance is created and using it's fluent api some data is set according to the above schema.

```php 
<?php

use Shrink0r\PhpSchema\Builder;

$builder = new Builder($schema);

$builder
    ->tags([ "cool", "neat" ])
    ->coords
        ->lon(12.3)
        ->lat(15.6)
    ->end();
```

## Step 3: Deal with the result

The builder's build method is invoked passing in some extraData, that will be merged into the result. The builder builds the array, validates it against the schema and returns the result.

```php
<?php

use Shrink0r\PhpSchema\Ok;

$extraData = [ 'post_id' => 42, 'rating' => 0.8 ];
$result = $builder->build($extraData);

if ($result instanceof Ok) {
    // everything's good, lets unwrap our data
    $validData = $result->unwrap();
} else {
    // there was at least one error somewhere in the data
    // we have two options here. Number one is handle the errors manually:
    foreach ($result->getValue() as $key => $errors) {
        var_dump($key, $errors);
    }
    // Number two is bail by unwrapping the error, thus triggering an exception
    $result->unwrap();
}

```

## Finally

The above example is suitable for simple cases and gives an idea of the library's basic crud. When trying to realize complex schemas it may be helpful to extend or override some of the library's default behavior. See [here](https://github.com/shrink0r/php-schema/blob/master/docs/documentation.md) for further details.
