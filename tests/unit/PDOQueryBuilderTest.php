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
        $this->queryBuilder->beginTransaction();
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
            ->where('user', 'Amin')
            ->update(['email' => 'Aminjalili123312@gmail.com']);


        $this->assertEquals(1, $result);
    }

    public function testItCanFetchData()
    {
        $this->multipleInsertIntoDb(10);
        $this->multipleInsertIntoDb(10, ['user' => 'morteza ahmadi']);

        $result = $this->queryBuilder->table('bugs')->where('user', 'morteza ahmadi')->get();

        $this->assertIsArray($result);
        $this->assertCount(10, $result);
    }

    public function testItCanFetchSpecificColumn()
    {
        $this->multipleInsertIntoDb(10);
        $this->multipleInsertIntoDb(10, ['user' => 'morteza ahmadi']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'morteza ahmadi')
            ->get(['name', 'user']);

        $this->assertIsArray($result);
        $this->assertObjectHasProperty('name',$result[0]);
        $this->assertObjectHasProperty('user',$result[0]);


        $result = json_decode(json_encode($result[0]),true);

        $this->assertEquals(['name','user'],array_keys($result));
    }

    public function testItCanGetFirstRow()
    {

        $this->multipleInsertIntoDb(10, ['name' => 'first row']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('name', 'first row')
            ->first();

        $this->assertIsObject($result);
        $this->assertObjectHasProperty('name',$result);
        $this->assertObjectHasProperty('user',$result);
        $this->assertObjectHasProperty('id',$result);
        $this->assertObjectHasProperty('email',$result);
        $this->assertObjectHasProperty('link',$result);
    }

    public function testItCanUpdateWithMultipleWhere()
    {
        $this->insertInToDB();
        $this->insertInToDB(['user' => 'morteza ahmadi']);

        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'Amin')
            ->where('link', 'test')
            ->update(['name' => "after multiple where"]);
        $this->assertEquals(1, $result);
    }


    public function testItCanDeleteRecord()
    {
       $this->multipleInsertIntoDb(4);

        $result = $this->queryBuilder->table('bugs')->where('user', 'Amin Jalili')->delete();
        $this->assertEquals(4, $result);

    }

    public function testItCanFindWithId()
    {
        $this->multipleInsertIntoDb(10);
       $id =  $this->insertInToDB(['name'=>'for find']);
       $result =$this->queryBuilder
           ->table('bugs')
           ->find($id);

        $this->assertIsObject($result);
        $this->assertEquals('for find',$result->name);

    }

    public function testItReturnsEmptyArrayWhenRecordNotFound()
    {
        $this->multipleInsertIntoDb(10);

        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user','dummy')
            ->get();

        $this->assertIsArray($result);
        $this->assertEmpty($result);

    }

    public function testItReturnNullWhenFirstRecordNotFound()
    {
        $this->multipleInsertIntoDb(10);

        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user','dummy')
            ->first();

        $this->assertNull($result);
        $this->assertEmpty($result);
    }

    public function testItReturnsZeroWhenRecordNotFoundForUpdate()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user','dummy')
            ->update(['name'=>'test']);

        $this->assertEquals(0,$result);

    }

    private function insertInToDB($options = [])
    {

        $data = array_merge([
            'name' => 'First Bug report',
            'link' => 'http://link.com',
            'user' => 'Amin Jalili',
            'email' => 'amin@jalili137819@gmail.com'
        ], $options);

        return $this->queryBuilder->table('bugs')->create($data);

    }

    private function multipleInsertIntoDb($count, $options = [])
    {
        for ($i = 1; $i <= $count; $i++) {
            $this->insertInToDB($options);
        }
    }

    private function getConfigs()
    {

        return $config = Config::get('database', 'pdo_testing');

    }

    public function tearDown(): void
    {
//        $this->queryBuilder->truncateAllTable();
        $this->queryBuilder->rollback();
        parent::tearDown();
    }
}