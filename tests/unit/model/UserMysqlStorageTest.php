<?php

require_once 'vendor/autoload.php';

require_once 'config/config.php';
Logger::configure('config/log4php-config.xml');

use PHPUnit\Framework\TestCase;
use \Mockery as m;
use Example\Model\User;
use Example\Model\UserMysqlStorage;

class UserMysqlStorageTest extends TestCase
{

    protected static $dbh;

    public static function setUpBeforeClass()
    {
        global $config;
        $host = $config['db']['server'];
        $dbname = $config['db']['name'];
        $user = $config['db']['user'];
        $pass = $config['db']['pass'];
        self::$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    }

    public static function tearDownAfterClass()
    {
        self::$dbh = null;
    }

    public function setUp()
    {
        self::$dbh->beginTransaction();
        parent::setUp();
    }

    public function tearDown()
    {
        self::$dbh->rollBack();
        parent::tearDown();
    }

    public function testStorageAdd()
    {
        $user = new User(0, "Vasia", "email@email.bu", "+79245555577");
        $storage = new UserMysqlStorage(self::$dbh);
        $id = $storage->add($user);
        $this->assertTrue($id > 0);
    }

    public function testGetById()
    {
        $user = new User(0, "Vasia", "email@email.bu", "+79245555577");
        $storage = new UserMysqlStorage(self::$dbh);
        $id = $storage->add($user);
        $this->assertTrue($id > 0);

        $user = $storage->getById($id);
        $this->assertNotEmpty($user);
        $this->assertEquals($user->get("name"), "Vasia");
        $this->assertEquals($user->get("email"), "email@email.bu");
        $this->assertEquals($user->get("phone"), "79245555577");
    }

    public function testGetByIdNonExist()
    {
        $storage = new UserMysqlStorage(self::$dbh);
        $user = $storage->getById(0);
        $this->assertEmpty($user);
    }

    public function testStorageUpdate()
    {
        $user = new User(0, "Vasia", "email@email.bu", "+79245555577");
        $storage = new UserMysqlStorage(self::$dbh);
        $id = $storage->add($user);

        $user->set("id", $id);
        $user->set("name", "Fedia");
        $storage->update($user);

        $user = $storage->getById($id);
        $this->assertEquals($user->get("name"), "Fedia");
    }

}
