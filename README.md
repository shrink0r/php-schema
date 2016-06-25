# php-schema

[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)
[![Build Status](https://travis-ci.org/shrink0r/php-schema.svg?branch=master)](https://travis-ci.org/shrink0r/php-schema)
[![Coverage Status](https://coveralls.io/repos/github/shrink0r/php-schema/badge.svg?branch=master)](https://coveralls.io/github/shrink0r/php-schema?branch=master)
[![Code Climate](https://codeclimate.com/github/shrink0r/php-schema/badges/gpa.svg)](https://codeclimate.com/github/shrink0r/php-schema)
[![Dependency Status](https://www.versioneye.com/user/projects/576dcc347bc681004a3f9b68/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/576dcc347bc681004a3f9b68)

Lib for building and validating array structures in php.

## Usage

### Step 1: Define a schema

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

### Step 2: Utilize the builder

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

### Step 3: Deal with the result

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

### Finally

The above example is suitable for simple cases and gives an idea of the library's basic crud. When trying to realize complex schemas it may be helpful to extend or override some of the library's default behavior. See [here](/docs/documentation.md) for further details.

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

Alternatively, you can download a release archive from the [github releases](https://github.com/shrink0r/php-schema/releases).

## Documentation

At the moment there are some basic usage example from above and some additional [documentation here](docs/documentation.md).
The brave may look into the `tests` folder for more insights.
Feel free to ask via IRC or the [issue tracker](https://github.com/shrink0r/php-schema/issues).

## Community

None, but you may join the freenode IRC
[`irc://irc.freenode.org/honeybee`](irc://irc.freenode.org/honeybee) channel, where a some folks that use this library might be online. :-)

## Contributing

Please contribute by [forking](https://help.github.com/articles/fork-a-repo/) and sending a [pull request](https://help.github.com/articles/using-pull-requests/). More information can be found in the [`CONTRIBUTING.md`](CONTRIBUTING.md) file. The authors and contributors are mentioned in the [github contributors graph](https://github.com/shrink0r/php-schema/graphs/contributors) of this repository.

The code tries to adhere to the following PHP-FIG standards: [PSR-4][6], [PSR-1][7] and [PSR-2][8].

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md) for more information about changes.

## License

This project is MIT licensed. See the [linked license](LICENSE.md) for details.

* Total Composer Downloads: [![Composer Downloads](https://poser.pugx.org/shrink0r/php-schema/d/total.png)](https://packagist.org/packages/shrink0r/php-schema)

[6]: http://www.php-fig.org/psr/psr-4/ "PSR-4 Autoloading Standard"
[7]: http://www.php-fig.org/psr/psr-1/ "PSR-1 Basic Coding Standard"
[8]: http://www.php-fig.org/psr/psr-2/ "PSR-2 Coding Style Guide"


