<?php

namespace Tests\unit;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDODatabaseConnection;
use App\Exceptions\ConfigIsNotValid;
use App\Exceptions\DatabaseConnectionException;
use App\Helpers\Config;
use PDO;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase
{
    public function testPdoDatabaseConnectionImplementsDatabaseConnectionInterface()
    {
        $configs = $this->getConfigs();

        $pdoConnection = new PDODatabaseConnection($configs);
        $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
    }

    public function testConnectMethodShouldReturnValidInstance()
    {
        $configs = $this->getConfigs();
        $pdoConnection = new PDODatabaseConnection($configs);
        $pdoHandler = $pdoConnection->connect();
        $this->assertInstanceOf(PDODatabaseConnection::class, $pdoHandler);
        return $pdoHandler;
    }

    #[Depends('testConnectMethodShouldReturnValidInstance')]
    public function testConnectMethodShouldBeConnectToDatabase($pdoHandler)
    {

        $this->assertInstanceOf(PDO::class, $pdoHandler->getConnection());

    }

    public function testItThrowExceptionIfConfigIsInvalid()
    {

        $this->expectException(DatabaseConnectionException::class);
        $configs = $this->getConfigs();
        $configs['database'] = 'dummy';
        $pdoConnection = new PDODatabaseConnection($configs);
        $pdoConnection->connect();

    }

    public function testReceivedConfigHaveRequiredKey()
    {
        $this->expectException(ConfigIsNotValid::class);
        $configs = $this->getConfigs();
        unset($configs['db_user']);

        $pdoConnection = new PDODatabaseConnection($configs);
        $pdoConnection->connect();
    }

    private function getConfigs()
    {
        return $config = Config::get('database', 'pdo_testing');

    }
}