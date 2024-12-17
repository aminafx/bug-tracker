<?php

namespace Tests\Functional;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;
use App\Helpers\HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class CrudTest extends TestCase
{
    private $httpClient;
    protected $queryBuilder;

    public function setUp(): void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfigs());

        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->connect());

        $this->httpClient = new HttpClient();

        parent::setUp();
    }

    /**
     * @throws GuzzleException
     * @throws GuzzleException
     */
    public function testItCanCreateDataWithApi()
    {
        $data = [
            'json' => [
                'name' => 'API',
                'user' => 'Ahmad',
                'email' => 'api@gmail.com',
                'link' => 'api.com'
            ]
        ];

        $response = $this->httpClient->request('POST', 'index.php', $data);
        $this->assertEquals(200, $response->getStatusCode());
        $bug = $this->queryBuilder
            ->table('bugs')
            ->where('name', 'API')
            ->where('user', 'Ahmad')
            ->first();
        $this->assertNotNull($bug);

        return $bug;
    }

    #[Depends('testItCanCreateDataWithApi')]
    public function testItCanUpdateDataWithApi($bug)
    {

        $data = [
            'json' => [
                'name' => 'API for update',
                'id' => $bug->id

            ]
        ];
        $response = $this->httpClient->request('PUT', 'index.php', $data);
        $this->assertEquals(200, $response->getStatusCode());

        $bug = $this->queryBuilder->table('bugs')
            ->find($bug->id);

        $this->assertNotNull($bug);
        $this->assertEquals('API for update', $bug->name);
        return $bug;
    }

    #[Depends('testItCanUpdateDataWithApi')]
    public function testItCanFetchDataWithApi($bug)
    {
        $data = [
            'json' => [
                'id' => $bug->id
            ]
        ];
        $response = $this->httpClient->request('GET', 'index.php',$data);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('id',(array)json_decode($response->getBody()),true);
        return $bug;

    }

    #[Depends('testItCanCreateDataWithApi')]
    public function testItCanDeleteDataWithApi($bug)
    {
        $data = [
            'json'=>[
                'id'=>$bug->id
            ]
        ];
        $response = $this->httpClient->request('DELETE','index.php',$data);
        $this->assertEquals(204,$response->getStatusCode());
        $bug = $this->queryBuilder->table('bugs')
            ->find($bug->id);
        $this->assertNull($bug);

    }

    public function tearDown(): void
    {
        $this->httpClient = null;
        parent::tearDown();
    }

    private function getConfigs()
    {
        return $config = Config::get('database', 'pdo_testing');
    }

}