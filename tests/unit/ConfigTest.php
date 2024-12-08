<?php

namespace Tests\unit;

use App\Exceptions\ConfigFileNotFoundException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetFileContentsReturnsArray()
    {
        $config = Config::getFileContent('database');
        $this->assertIsArray($config);
    }

    public function testItThrowsExceptionIfFileNotFound()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        $config = Config::getFileContent('database2');

    }

    public function testGetMethodReturnsValidData()
    {
        $config = Config::get('database','pdo');
        $expectedData = [
            'driver'=>'mysql',
            'host'=>'127.0.0.1',
            'database'=>'bug_tracker',
            'db_user'=>'root',
            'db_password'=>''
        ];
        $this->assertEquals($expectedData,$config);
    }
}