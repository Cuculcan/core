<?php

namespace Cuculcan\Core\Model;

use Cuculcan\Core\Errors\InternalException;
use Cuculcan\Core\Interfaces\EntityInterface;

abstract class Entity implements EntityInterface
{
    /**
     * @sphinx
     * @var int
     */
    protected $id = 0;
    protected $fields;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function __construct(array $data = null)
    {
        $this->fields = $this->getFieldsRefl();

        if ($data) {
            $this->import($data);
        }
    }

    public function export()
    {
        $data = [];
        foreach ($this->fields as $field => $attr) {
            if (isset($attr['annotations']) && in_array('extra', $attr['annotations'])) {
                continue;
            }
            $data[] = [
                'name' => $field,
                'value' => $this->toDatabase($field, $attr['type'])
            ];
        }
        return $data;
    }

    private function toDatabase($field, $type)
    {
        switch ($type) {

            case 'array':
                $val = json_encode($this->$field);
                break;

            case 'json':
                $val = json_encode($this->$field);
                break;

            default:
                $val = $this->$field;
        }
        return $val;
    }

    private function fromDatabase($field, $type)
    {
        switch ($type) {

            case 'array':
                $val = $this->parseJson($field);
                break;

            case 'json':
                $val = $this->parseJson($field);
                break;

            default:
                $val = $field;
        }
        return $val;
    }

    private function parseJson($field)
    {
        if (!isset($field) || $field == '') {
            return [];
        }

        if (is_array($field)) {
            return $field;
        }

        return json_decode($field, true);
    }

    private function toSphinx($field, $type, $default)
    {
        switch ($type) {

            case 'array':
                if (isset($this->$field) && is_array($this->$field)) {
                    $val = '(' . implode(',', (array) $this->$field) . ')';
                } else if(!isset($default) || $default == 'empty') {
                    $val = '()';
                } else {
                    $val = '(' . $default . ')';
                }
                break;
            case 'int':
                if(isset($this->$field)) {
                    $val = $this->$field;
                }
                else if(isset($default)) {
                    $val = intval($default);
                } else {
                    $val = null;
                }
                break;
            case 'string':
                if(isset($this->$field)) {
                    $val = $this->$field;
                }
                else if(!isset($default) || $default == 'empty') {
                    $val = '';
                } else {
                    $val = $default;
                }
                break;
            default:
                $val = $this->$field;
        }

        return $val;
    }

    public function exportSphinx()
    {
        $data_with_id = [];
        $data = [];

        foreach ($this->fields as $field => $attr) {
            if (!isset($attr['annotations']) || !in_array('sphinx', $attr['annotations'])) {
                continue;
            }
            if ($field == 'id') {
                $data_with_id[] = [
                    'name' => $field,
                    'value' => $this->toSphinx($field, $attr['type'], $attr['default_value']),
                    'default_value' => $attr['default_value'],
                    'type' => $attr['type']
                ];
                continue;
            }
            $data[] = [
                'name' => $field,
                'value' => $this->toSphinx($field, $attr['type'], $attr['default_value']),
                'default_value' => $attr['default_value'],
                'type' => $attr['type']
            ];
        }

        $data = array_merge($data_with_id, $data);

        return $data;
    }

    public function objectToJson()
    {
        $data = [];
        foreach ($this->fields as $field => $attr) {
            $data[$field] = $this->$field;
        }
        return $data;
    }

    public function import(array $data)
    {
        if (!isset($data) || !is_array($data)) {
            return;
        }

        if (!$this->fields) {
            $this->fields = $this->getFieldsRefl();
        }

        foreach ($data as $fieldName => $value) {
            if (isset($this->fields[$fieldName])) {
                $this->$fieldName = $this->fromDatabase($value, $this->fields[$fieldName]['type']);
            }
        }
    }

    private function getFieldsRefl()
    {
        $sClass = get_called_class(); // unavailable in PHP < 5.3.0
        $rflClass = new \ReflectionClass($sClass);

        $mapFields = [];

        foreach ($rflClass->getProperties() as $rflProperty) {

            $name = $rflProperty->getName();
            if ($name == 'fields') {
                continue;
            }

            $sComment = $rflProperty->getDocComment();
            $annotations = [];

            if (preg_match_all('%^\s*\*\s*@extra\s*.*$%im', $sComment, $result, PREG_PATTERN_ORDER)) {
                $annotations[] = 'extra';
            }

            if (preg_match_all('%^\s*\*\s*@sphinx\s*.*$%im', $sComment, $result, PREG_PATTERN_ORDER)) {
                $annotations[] = 'sphinx';
            }

            $type = null;
            if (preg_match_all('%^\s*\*\s*@type\s*=\s*([A-Za-z]+)\s*.*$%im', $sComment, $result, PREG_PATTERN_ORDER)) {
                $type = $result[1][0];
            }

            $default_value = null;
            if (preg_match_all('%^\s*\*\s*@default_value\s*=\s*(.+)\s*.*$%im', $sComment, $result, PREG_PATTERN_ORDER)) {
                $default_value = $result[1][0];
            }

            $mapFields[$name] = [
                'name' => $name,
                'type' => $type,
                'default_value' => $default_value,
                'annotations' => $annotations
            ];
        }

        return $mapFields;
    }

    private function getFieldsSet(array $fields)
    {
        $names = [];
        foreach ($fields as $field) {
            $names[] = $field['name'];
        }
        return $names;
    }

    /**
     * Получаем доступ к protected полям класса
     * Поидее тут можно делать преобразование типов если такое требуется 
     * и описано в соответствующем аттрибуте 
     * 
     * @param string $name
     * @return mix
     */
    public function __get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->$name;
        }
        return null;
    }

    /**
     * Устанавливает значение private и protected полям класса
     * Поидее тут можно делать преобразование типов если такое требуется 
     * и описано в соответствующем аттрибуте 
     * 
     * @param string $name
     * @param mix $value
     */
    public function __set($name, $value)
    {
        if (isset($this->fields[$name])) {
            $this->$name = $value;
        }
    }

}
