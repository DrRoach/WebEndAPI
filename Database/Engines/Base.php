<?php

namespace WebEndAPI\Database\Engines;

interface Base
{
    public static function connect($hostname, $username, $password, $database);
    public static function tablesExist($table);
}