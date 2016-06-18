<?php

namespace Shrink0r\Configr\Property;

use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class Property implements PropertyInterface
{
    protected $schema;

    protected $name;

    protected $required;

    protected $parent;

    public function __construct(SchemaInterface $schema, $name, array $definition, PropertyInterface $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->schema = $schema;
        $this->required = isset($definition['required']) ? $definition['required'] : true;
    }

    public function validate(array $config, array $handledKeys = [])
    {
        $propName = $this->getName();

        $errors = [];
        if ($propName === ':any_name:') {
            foreach ($config as $key => $value) {
                $result = $this->validateValue($value);
                if ($result instanceof Error) {
                    $errors[$key] = $result->unwrap();
                }
            }
        } else {
            $value = isset($config[$propName]) ? $config[$propName] : null;
            if (!array_key_exists($propName, $config) && $this->isRequired()) {
                $errors[] = Error::MISSING_KEY;
            } else if (null === $value && $this->isRequired()) {
                $errors[] = Error::MISSING_VALUE;
            } else if ($value !== null) {
                $result = $this->validateValue($value);
                if ($result instanceof Error) {
                    $errors = $result->unwrap();
                }
            }
        }

        return empty($errors) ? Ok::unit() : Error::unit($errors);
    }

    protected function validateValue($value)
    {
        return Ok::unit();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return $this->parent instanceof PropertyInterface;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
