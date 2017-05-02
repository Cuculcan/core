<?php

require 'vendor/autoload.php';

require 'config/config.php';
require 'third-party/log4php-2.3.0/php/Logger.php';
Logger::configure('config/log4php-config.xml');

use PHPUnit\Framework\TestCase;
use \Mockery as m;
use Cuculcan\Core\Application;


class ApplicationTest extends TestCase{
    
    
    public function testShouldExistApplicationObject(){
        $app = new Application("Example");
        $this->assertNotNull($app);
    }
    
     public function testShouldRun(){
         
        //$requestMoc = m::mock('overload:Cuculcan\Core\Aview', function($mock){$mock->shouldIgnoreMissing();});
        $requestMoc = m::mock('overload:Cuculcan\Core\Aview');
        $requestMoc->shouldReceive('render')
            ->never()
            ->andReturn('rendered!');
        
    }
    
}
