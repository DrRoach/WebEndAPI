<?php

namespace WebEndAPI\Database\Engines;

interface Base
{
    public static function connect($hostname, $username, $password, $database);
    public static function tablesExist($tables);
    public static function createSchema($tables);
    public static function get($query);
}
