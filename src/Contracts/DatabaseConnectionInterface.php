<?php

namespace App\Contracts;


interface DatabaseConnectionInterface
{
    public function conncet();
    public function getConncetion();
}