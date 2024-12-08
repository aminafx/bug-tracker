<?php

namespace Tests\unit;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDODatabaseConnection;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase
{
    public function testPdoDatabaseConnectionImplementsDatabaseConnectionInterface()
    {
        $configs = $this->getConfigs();
        $pdoConnection = new PDODatabaseConnection();
        $this->assertInstanceOf(DatabaseConnectionInterface::class,$pdoConnection);
    }

    private function getConfigs()
    {
       return $config = Config::get('database','pdo_testing');

    }
}