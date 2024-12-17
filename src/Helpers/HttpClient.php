<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class HttpClient extends Client
{
public function __construct()
{
    $config= Config::get('app');
    parent::__construct(['base_uri'=>$config['base_uri']]);
}
}