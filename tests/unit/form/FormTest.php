<?php

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Cuculcan\Core\Request;


class FormTest extends TestCase{
    
    public function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI']="";
        parent::setUp();
    }

    
    public function testRequestConstructShouldParseQueryParamsFromPOST_FORM(){
    
        $_SERVER['REQUEST_METHOD'] = "POST";
        $_SERVER['CONTENT_TYPE'] = "application/x-www-form-urlencoded";
                
        $request = new Request(__DIR__.'/request_input_form.dat');
        $parameters = $request->getQueryParameters();
        $this->assertTrue(is_array($parameters)); 
        $this->assertEquals(count($parameters), 1); 
        $this->assertEquals($parameters["param"], "value"); 
        
    }
    
}
