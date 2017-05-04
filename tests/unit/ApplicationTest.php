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
         
        $controllerMoc = m::mock(\Cuculcan\Core\AController::class); 
        $controllerMoc->shouldReceive('processRequest')
            ->once()
            ->andReturn('done!');
         
         
        $routerMoc = m::mock('overload:Cuculcan\Core\Router');
        $routerMoc->shouldReceive('getController')
            ->once()
            ->andReturn($controllerMoc);

       (new Application("Example"))->run();
         
    }
    
}
