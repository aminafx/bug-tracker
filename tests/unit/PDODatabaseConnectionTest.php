<?php

namespace Tests\unit;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase
{
    public function testPdoDatabaseConnectionImplementsDatabaseConnectionInterface()
    {
        $configs = $this->getConfigs();
        $pdoConnetion = new PDODatabaseConnection();
    }

    private function getConfigs()
    {
        $config = Config::get('database','pdo');
        var_dump($config);
    }
}