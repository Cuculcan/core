<?php

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Cuculcan\Core\Request;


class RequestTest extends TestCase{
    
    public function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI']="";
        parent::setUp();
    }

    public function testRequestConstructShouldSetMethod(){
    
        $_SERVER['REQUEST_METHOD'] = "GET";

        $request = new Request();
        $this->assertEquals($request->method, "GET");
    }
    
    public function testRequestConstructShouldParseUrl(){
    
        $_SERVER['REQUEST_METHOD'] = "GET";
        $_GET['handler']="/test1/test2";

        $request = new Request();
        $urlElements = $request->getUrlElements();

        $this->assertTrue(is_array($urlElements)); 
        $this->assertEquals(count($urlElements), 3); 
        $this->assertEquals($urlElements[0], ""); 
        $this->assertEquals($urlElements[1], "test1"); 
        $this->assertEquals($urlElements[2], "test2"); 
        
    }
    
    public function testRequestConstructShouldParseQueryParamsFromGET(){
    
        $_SERVER['QUERY_STRING']="handler=index.php&handler=&param=value";
                
        $request = new Request();
        $parameters = $request->getQueryParameters();
        
        $this->assertTrue(is_array($parameters)); 
        $this->assertEquals(count($parameters), 1); 
        $this->assertEquals($parameters["param"], "value"); 
        
    }
    
    public function testRequestConstructShouldParseQueryParamsFromPOST_JSON(){
    
        $_SERVER['REQUEST_METHOD'] = "POST";
        $_SERVER['CONTENT_TYPE'] = "application/json";
                
        $request = new Request(__DIR__.'/request_input_json.dat');
        $parameters = $request->getQueryParameters();
        
        $this->assertTrue(is_array($parameters)); 
        $this->assertEquals(count($parameters), 1); 
        $this->assertEquals($parameters["param"], "value"); 
        
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
    
    
    
    
    public function testGetAllHeadersReturnArray(){
       $request = new Request();

       $headers = $request->getAllHeaders();
       $this->assertTrue(is_array($headers));
    }
    
    
}
