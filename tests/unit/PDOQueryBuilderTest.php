<?php

namespace Tests\unit;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class PDOQueryBuilderTest extends TestCase
{
    private $queryBuilder;

    public function setup(): void
    {
        $configs = $this->getConfigs();
        $pdoConnection = new PDODatabaseConnection($configs);
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->connect());
        parent::setup();

    }

    public function testItCanCreateData()
    {
        $result = $this->insertInToDB();

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdateData()
    {

        $result = $this->queryBuilder->table('bugs')
            ->where('user', 'Amin Jalili')
            ->update(['email' => 'Aminjalili123312@gmail.com']);


        $this->assertEquals(0, $result);
    }

    public function testItCanDeleteRecord()
    {
        $this->insertInToDB();
        $this->insertInToDB();
        $this->insertInToDB();
        $this->insertInToDB();

        $result = $this->queryBuilder->table('bugs')->where('user','Amin Jalili')->delete();
        $this->assertEquals(4, $result);

    }

    private function insertInToDB()
    {

        $data = [
            'name' => 'First Bug report',
            'link' => 'http://link.com',
            'user' => 'Amin Jalili',
            'email' => 'amin@jalili137819@gmail.com'
        ];
        return $this->queryBuilder->table('bugs')->create($data);

    }

    private function getConfigs()
    {

        return $config = Config::get('database', 'pdo_testing');

    }

    public function tearDown(): void
    {
        $this->queryBuilder->truncateAllTable();
        parent::tearDown();
    }
}