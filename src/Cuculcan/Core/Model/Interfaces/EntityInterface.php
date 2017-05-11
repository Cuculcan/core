<?php

namespace Cuculcan\Core\Model\Interfaces;

interface EntityInterface {

    public function get($field);
    public function set($field, $value);
    public function import(array $data);
    
    /**
     * @return array
     */
    public function export();
}
