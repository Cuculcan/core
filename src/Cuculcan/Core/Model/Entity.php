<?php

namespace Cuculcan\Core\Model;

use Cuculcan\Core\Errors\InternalException;

abstract class Entity implements Interfaces\EntityInterface
{

    protected $fields;

    public function export()
    {
        return $this->fields;
    }

    public function get($field)
    {
        $this->checkFieldExist($field);
        return $this->fields[$field];
    }

    public function import(array $data)
    {
        foreach ($data as $name => $value) {
            $this->checkFieldExist($name);
            $this->fields[$name] = $value;
        }
    }

    public function set($field, $value)
    {
        $this->checkFieldExist($field);
        $this->fields[$field] = $value;
    }

    private function checkFieldExist($fieldName)
    {
        if (!isset($this->fields[$fieldName])) {
            throw new InternalException("Field '" . $fieldName . "' not found in object " . static::class);
        }
    }

    public function fieldsFromAttributes()
    {
        $sClass = get_called_class(); // unavailable in PHP < 5.3.0
        $rflClass = new ReflectionClass($sClass);

        $mapFields = [];

        foreach ($rflClass->getProperties() as $rflProperty) {
            $sComment = $rflProperty->getDocComment();
            if (preg_match_all('%^\s*\*\s*@field\s*$%im', $sComment, $result, PREG_PATTERN_ORDER)) {
                $prop = $rflProperty->GetName();
                $value = $this->$prop;

                $mapFields[$prop] = $value;
            }
        }

        return $mapFields;
    }

}
