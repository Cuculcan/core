<?php

namespace Cuculcan\Core\Form;

//use Cuculcan\Core\Errors\InternalException;

class Form
{
  
    public function fields()
    {
        $sClass = get_called_class(); // unavailable in PHP < 5.3.0
        $rflClass = new \ReflectionClass($sClass);

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
