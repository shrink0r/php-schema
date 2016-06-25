<?php

namespace Shrink0r\PhpSchema;

use Shrink0r\PhpSchema\Property\AssocProperty;
use Shrink0r\PhpSchema\Property\BoolProperty;
use Shrink0r\PhpSchema\Property\ChoiceProperty;
use Shrink0r\PhpSchema\Property\EnumProperty;
use Shrink0r\PhpSchema\Property\FloatProperty;
use Shrink0r\PhpSchema\Property\FqcnProperty;
use Shrink0r\PhpSchema\Property\IntProperty;
use Shrink0r\PhpSchema\Property\Property;
use Shrink0r\PhpSchema\Property\PropertyInterface;
use Shrink0r\PhpSchema\Property\ScalarProperty;
use Shrink0r\PhpSchema\Property\SequenceProperty;
use Shrink0r\PhpSchema\Property\StringProperty;

class Factory implements FactoryInterface
{
    /**
     * @var string[] $defaultClassMap
     */
    protected static $defaultClassMap = [
        'scalar' => ScalarProperty::class,
        'string' => StringProperty::class,
        'int' => IntProperty::class,
        'float' => FloatProperty::class,
        'bool' => BoolProperty::class,
        'assoc' => AssocProperty::class,
        'enum' => EnumProperty::class,
        'sequence' => SequenceProperty::class,
        'fqcn' => FqcnProperty::class,
        'choice' => ChoiceProperty::class,
        'any' => Property::class
    ];

    /**
     * @var string[] $classMap
     */
    protected $classMap;

    /**
     * @param string[] $classMap An array containing type keys and corresponding fqcn values.
     */
    public function __construct(array $classMap = [])
    {
        $this->classMap = $this->verifyClassMap(array_merge(self::$defaultClassMap, $classMap));
    }

    /**
     * {@inheritdoc}
     */
    public function createSchema($name, array $definition, PropertyInterface $parent = null)
    {
        return new Schema($name, $definition, $this, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function createProperties(array $propDefintions, SchemaInterface $schema, PropertyInterface $parent = null)
    {
        $properties = [];
        foreach ($propDefintions as $name => $definition) {
            $definition['name'] = $name;
            $properties[$name] = $this->createProperty($definition, $schema, $parent);
        }

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function createProperty(array $definition, SchemaInterface $schema, PropertyInterface $parent = null)
    {
        if (!isset($definition['type'])) {
            throw new Exception("Missing required key 'type' within property definition.");
        }
        $type = $definition['type'];
        if (!isset($this->classMap[$type])) {
            throw new Exception("Given property type '$type' has not been registered.");
        }
        if (!isset($definition['name'])) {
            throw new Exception("Missing required key 'name' within property definition.");
        }
        $class = $this->classMap[$type];

        return new $class($schema, $definition['name'], $definition, $parent);
    }

    /**
     * Ensures that the given class map contains valid values.
     *
     * @param string[] $classMap
     *
     * @return string[] Returns the verified class map.
     */
    protected function verifyClassMap(array $classMap)
    {
        foreach ($classMap as $type => $class) {
            if (!class_exists($class)) {
                throw new Exception("Class '$class' that has been registered for type '$type' does not exist");
            }
            if (!in_array(PropertyInterface::class, class_implements($class))) {
                throw new Exception("Class '$class' does not implement: " . PropertyInterface::class);
            }
        }

        return $classMap;
    }
}
