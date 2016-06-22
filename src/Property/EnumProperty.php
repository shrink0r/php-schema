<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

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
     *                                          this must be the creating parent property.
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
        foreach ($this->allowedTypes as $type) {
            $result = (preg_match('/^&/', $type)
                ? $this->getCustomType($type)
                : $this->getSchema()->getFactory()->createProperty(
                    [ 'type' => $type, 'name' => $this->getName().'_item', 'required' => true ],
                    $this->getSchema(),
                    $this
                )
            )->validate($value);

            if ($result instanceof Ok) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * Retrieves a custom type definition by name.
     *
     * @param string $type The name/key of the type prefixed with a '&'.
     *
     * @return SchemaInterface
     */
    protected function getCustomType($type)
    {
        $customTypes = $this->getSchema()->getCustomTypes();
        $type = ltrim($type, '&');

        if (isset($customTypes[$type])) {
            return $customTypes[$type];
        }
        throw new Exception("Unable to resolve '$type' to a custom type-definition.");
    }
}
