# php-schema

Lib for building and validating array structures in php.

[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)
[![Build Status](https://travis-ci.org/shrink0r/php-schema.svg?branch=master)](https://travis-ci.org/shrink0r/php-schema)
[![Coverage Status](https://coveralls.io/repos/github/shrink0r/php-schema/badge.svg?branch=master)](https://coveralls.io/github/shrink0r/php-schema?branch=master)
[![Code Climate](https://codeclimate.com/github/shrink0r/php-schema/badges/gpa.svg)](https://codeclimate.com/github/shrink0r/php-schema)
[![Dependency Status](https://www.versioneye.com/user/projects/576dcc347bc681004a3f9b68/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/576dcc347bc681004a3f9b68)

## Usage

### Step 1: Define a schema

The schema is given an (array)description of the data-structure, that shall be verified upon invocation of the schema's validation routine.

```php
<?php

use Shrink0r\Configr\Factory;
use Shrink0r\Configr\Schema;

// define a schema
$schemaDefinition = [
    'type' => 'assoc',
    'properties' => [
        "post_id" => [ "type": "int" ],
        "rating" => [ "type" => float ],
        "tags" => [
            "type" => "sequence",
            "one_of" => [ "string" ]
        ],
        "coords" => [
            "type" => "assoc",
            "properties" => [
                "lon" => [ "type" => float ],
                "lat" => [ "type" => float ]
            ]
        ]
    ]
];

$schema = new Schema('vote', $schemaDefinition, new Factory());

```

### Step 2: Utilize the builder

A builder instance is created and using it's fluent api some data is set according to the above schema.

```php 
<?php

use Shrink0r\Configr\Builder;

$builder = new Builder($schema);

$builder
    ->tags([ "cool", "neat" ])
    ->coords
        ->lon(12.3)
        ->lat(15.6)
    ->end();
```

### Step 3: Deal with the result

The builder's build method is invoked passing in some extraData, that will be merged into the result. The builder builds the array, validates it against the schema and returns the result.

```php
<?php

use Shrink0r\Configr\Ok;

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

### Finally

The above example is suitable for simple cases and gives an idea of the library's basic crud. When trying to realize complex schemas it may be helpful to extend or override some of the library's default behavior.

**add/override properties**

When needing more than the shipped default functionality, additional properties can by registered to the factory and existing one's may be overridden. See the following example.

```php
<?php

use Shrink0r\Configr\Factory;
use Shrink0r\Configr\Schema;
use My\Ns\StringProperty;
use My\Ns\FunkyProperty;

$myClassMap = [
    // override the existing behavior for the string type
    'string' =>  StringProperty::class, 
    // add a new type "funky_prop"
    'funky_prop' => FunkyProperty::class 
]; 

$schema = new Schema('my_type', $schemaDefinition, new Factory($myClassMap));

```

**define custom/complex types**

Next to inlining nested-types by using the **assoc** property, you can define a **customTypes** key in your schema and define types that may be referred to from other parts of the schema:

```php
<?php

// define a schema
$schemaDefinition = [
    'type' => 'assoc',
    'properties' => [
        "post_id" => [ "type": "int" ],
        "rating" => [ "type" => float ],
        "tags" => [
            "type" => "sequence",
            "one_of" => [ "string" ]
        ],
        "coords" => [
            "type" => "assoc",
            // &wgs84 being a reference to a defined custom type
            "one_of" => [ "&wgs84" ]
        ]
    ],
    "customTypes" => [
        "wgs84" => [
            "type" => "assoc",
            "properties" => [
                "lon" => [ "type" => float ],
                "lat" => [ "type" => float ]
            ]
        ]
    ]
];

```

### List of supported properties:

**any**:

```php
<?php

[
    "type" => "any"
    "required" => true|false
]
```

**assoc**:

```php
<?php

[
    "type" => "assoc"
    "required" => true|false,
    "properties" => [
        ":any_name:" => [
            "type" => "any" 
        ]
    ]
]
```

**bool**:

```php
<?php

[
    "type" => "bool"
    "required" => true|false
]
```

**choice**:

```php
<?php

[
    "type" => "choice"
    "required" => true|false,
    "one_of" => [ "option1", "option2", "option3" ]
]
```

**enum**:

```php
<?php

[
    "type" => "enum"
    "required" => true|false,
    "one_of" => [ "int", "float", "&wgs84" ]
    // &wgs84 being a reference to a defined custom type
]
```

**float**:

```php
<?php

[
    "type" => "float"
    "required" => true|false
]
```

**fqcn**:

```php
<?php

[
    "type" => "fqcn"
    "required" => true|false
]
```

**int**:

```php
<?php

[
    "type" => "int"
    "required" => true|false
]
```

**scalar**:

```php
<?php

[
    "type" => "scalar"
    "required" => true|false
]
```

**sequence**:

```php
<?php

[
    "type" => "sequence"
    "required" => true|false,
    "one_of" => [ "string", "bool", "&wgs84" ]
    // &wgs84 being a reference to a defined custom type
]
```

**string**:

```php
<?php

[
    "type" => "string"
    "required" => true|false
]
```

## Requirements and installation

- PHP v5.6+

Install the library via [Composer](http://getcomposer.org/):

```./composer.phar require shrink0r/php-schema [optional version]```

Adding it manually as a vendor library requirement to the `composer.json` file
of your project works as well:

```json
{
    "require": {
        "shrink0r/php-schema": "master@dev"
    }
}
```

Alternatively, you can download a release archive from the [github releases](releases).

## Documentation

At the moment there are some basic usage example from above.
The brave may look into the `tests` folder for more insights.
Feel free to ask via IRC or the [issue tracker](/issues).

## Community

None, but you may join the freenode IRC
[`irc://irc.freenode.org/honeybee`](irc://irc.freenode.org/honeybee) channel, where a some folks that use this library might be online. :-)

## Contributing

Please contribute by [forking](http://help.github.com/forking/) and sending a [pull request](http://help.github.com/pull-requests/). More information can be found in the [`CONTRIBUTING.md`](CONTRIBUTING.md) file. The authors and contributors are mentioned in the [github contributors graph](https://github.com/shrink0r/php-schema/graphs/contributors) of this repository.

The code tries to adhere to the following PHP-FIG standards: [PSR-4][6], [PSR-1][7] and [PSR-2][8].

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md) for more information about changes.

## License

This project is MIT licensed. See the [linked license](LICENSE.md) for details.

* Total Composer Downloads: [![Composer Downloads](https://poser.pugx.org/shrink0r/php-schema/d/total.png)](https://packagist.org/packages/shrink0r/php-schema)

[6]: http://www.php-fig.org/psr/psr-4/ "PSR-4 Autoloading Standard"
[7]: http://www.php-fig.org/psr/psr-1/ "PSR-1 Basic Coding Standard"
[8]: http://www.php-fig.org/psr/psr-2/ "PSR-2 Coding Style Guide"


