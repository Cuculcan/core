<?php

require_once 'vendor/autoload.php';

require_once 'config/config.php';
Logger::configure('config/log4php-config.xml');

use PHPUnit\Framework\TestCase;
use \Mockery as m;
use Example\Model\User;

class UserTest extends TestCase
{

    public function testUserExportArray()
    {
        $user = new User(0, "Vasia", "email@email.bu", "+79245555577");

        $userData = $user->export();
        $this->assertNotNull($userData);
        $this->assertEquals(count($userData), 4);
    }

    public function testUserGetField()
    {
        $user = new User(100500, "Vasia", "email@email.bu", "+79245555577");

        $id = $user->get("id");
        $this->assertEquals($id, 100500);
    }

    public function testUserSetField()
    {
        $user = new User(100500, "Vasia", "email@email.bu", "+79245555577");

        $user->set("name", "Qwerty");

        $this->assertEquals("Qwerty", $user->get("name"));
    }
    
    public function testUserImport()
    {
        $user = new User(100500, "Vasia", "email@email.bu", "+79245555577");

        $data = [
            "email"=>"test@test.com",
            "phone"=>"22322322"
        ];
        
        $user->import($data);
        
        $this->assertEquals("test@test.com", $user->get("email"));
        $this->assertEquals("22322322", $user->get("phone"));
       
    }

    public function testInternalException()
    {
        $this->expectException(Cuculcan\Core\Errors\InternalException::class);
        $user = new User(100500, "Vasia", "email@email.bu", "+79245555577");

        $id = $user->get("no_such_field");
    }
    
    public function testInternalExceptionWhileImport()
    {
        $this->expectException(Cuculcan\Core\Errors\InternalException::class);
        $user = new User(100500, "Vasia", "email@email.bu", "+79245555577");

        $data = [
            "email"=>"test@test.com",
            "phone"=>"22322322",
            "no_such_field"=>"bzzzzzzzzzzz"
        ];
        
        $user->import($data);
    }

}
