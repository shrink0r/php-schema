# php-schema documentation

## Add/override properties

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

## Define custom/complex types

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

## List of supported properties:

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
