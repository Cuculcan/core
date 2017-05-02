<?php

//require "src/Cuculcan/Core/autoload.php";
require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Cuculcan\Core\Request;


class RequestTest extends TestCase{
    
    
    public function testGetAllHeadersReturnArray(){
       $request = new Request();

       $headers = $request->getAllHeaders();
       $this->assertTrue(is_array($headers));
    }
    
    
}
