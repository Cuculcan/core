<?php
require "src/Cuculcan/Core/autoload.php";
require "src/Cuculcan/Example/autoload.php";

require_once 'third-party/log4php-2.3.0/php/Logger.php';
Logger::configure('config/log4php-config.xml');

use PHPUnit\Framework\TestCase;
use \Mockery as m;
use Cuculcan\Core\Application;


class ApplicationTest extends TestCase{
    
    
    public function testShouldExistApplicationObject(){
        $app = new Application("Cuculcan\Example");
        $this->assertNotNull($app);
    }
    
     public function testShouldRun(){
         
//        $externalMock = m::mock('overload:Cuculcan\Core\Request', function($mock){$mock->shouldIgnoreMissing();});
//        $externalMock->shouldReceive('getController')
//            ->once()
//            ->andReturn('Tested!');
        
        
        $app = new Application("Cuculcan\Example");
        $this->assertNotNull($app);
        
        
        
        $app->run();
        
        
        //$this->assertTrue(false);
        
    }
    
}
