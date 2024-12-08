<?php
require_once 'vendor/autoload.php';
use App\Helpers\Config;

$result = Config::get('database','pdo');
var_dump($result);