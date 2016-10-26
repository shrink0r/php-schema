<?php

namespace Shrink0r\PhpSchema\Property;

use Shrink0r\PhpSchema\Exception;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\ResultInterface;
use Shrink0r\PhpSchema\SchemaInterface;

class EnumProperty extends Property
{
    /**
     * @var string[] $allowedTypes
     */
    protected $allowedTypes;

    /**
     * @param SchemaInterface $schema The schema that the property is part of.
     * @param string $name The name of the schema.
     * @param mixed[] $definition Must contain a key named 'one_of' containing the value types,
     *                            that will be allowed to pass the property's validation.
     * @param PropertyInterface $parent If the schema is created by an assoc or sequence prop,
     *                                  this must be the creating parent property.
     */
    public function __construct(SchemaInterface $schema, $name, array $definition, PropertyInterface $parent = null)
    {
        parent::__construct($schema, $name, $definition, $parent);

        $this->allowedTypes = isset($definition['one_of']) ? $definition['one_of'] : [];
    }

    /**
     * Tells if a given value adhere's to one of the property's allowed types.
     *
     * @param mixed $value
     *
     * @return ResultInterface Returns Ok if the value is valid, otherwise an Error is returned.
     */
    public function validate($value)
    {
        $result = Ok::unit();

        foreach ($this->allowedTypes as $type) {
            $typeChecker = preg_match('/^&/', $type) ? $this->getCustomType($type) : $this->createProperty($type);
            $result = $typeChecker->validate($value);

            if ($result instanceof Ok) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * Retrieves a custom type definition by name, that will be used to proxy the property's validation to.
     *
     * @param string $type The name/key of the type prefixed with a '&'.
     *
     * @return SchemaInterface
     */
    protected function getCustomType($type)
    {
        $root = $this;
        while ($cur = $root->getParent()) {
            $root = $cur;
        }
        $customTypes = $root->getSchema()->getCustomTypes();
        $type = ltrim($type, '&');

        if (isset($customTypes[$type])) {
            return $customTypes[$type];
        }

        throw new Exception("Unable to resolve '$type' to a custom type-definition.");
    }

    /**
     * Creates a child-property that will be used as a proxy-target for the parent property's validation.
     *
     * @param string $type The type key of the property to create.
     *
     * @return PropertyInterface
     */
    protected function createProperty($type)
    {
        return $this->getSchema()->getFactory()->createProperty(
            [ 'type' => $type, 'name' => $this->getName().'_item', 'required' => true ],
            $this->getSchema(),
            $this
        );
    }
}
