<?php

namespace Cuculcan\Core\Interfaces;

interface EntityInterface {

    /**
     * @return array
     */
    public function export();
    
    public function import(array $data);
}
