<?php

namespace Cuculcan\Core\Repositories;

class Criteria
{
    private $name;
    private $operator;
    private $value;
    
    public function __construct($name, $operator, $value)
    {
        $this->name = $name;
        $this->operator = trim($operator);
        $this->value = $value;
    }
    
    public function build()
    {
        if(strtolower($this->operator) === 'in') {
            $in_values = implode(',', array_fill(0, count($this->value), '?'));
            $criteria = "`".$this->name."` ".$this->operator." (".$in_values.")";
        }
        else if(strtolower($this->operator) === 'not in') {
            $not_in_values = implode(',', array_fill(0, count($this->value), '?'));
            $criteria = "`".$this->name."` ".$this->operator." (".$not_in_values.")";
        }
        else {
            $criteria = "`".$this->name."` ".$this->operator." ?";
            $this->value = [$this->value];
        }
        return [
            'sql' => $criteria,
            'value' => $this->value
        ];
    }
}