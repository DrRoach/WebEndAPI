<?php

namespace WebEndAPI\Database\Engines;

use WebEndAPI\Database\Database;
use \PDO;

class MySql implements Base {
    private static $_CONNECTION = null;

    private static $_ERROR_1 = 1;

    public static function connect($hostname, $username, $password, $database)
    {
        if (is_null(self::$_CONNECTION)) {
            self::$_CONNECTION = new PDO(
                'mysql:host=' . $hostname . ';dbname=' . $database,
                $username,
                $password,
                [
                    PDO::ATTR_PERSISTENT => true
                ]);
        }

        if (isset(self::$_CONNECTION->connect_eror)) {
            throw new \Exception(self::$_CONNECTION->connect_error, self::$_ERROR_1);
        }

        return ['success' => true];
    }

    public static function tablesExist($tables)
    {
        /**
         * Loop through each table and see if it exists in the database
         */
        foreach ($tables as $table) {
            $stmt = self::$_CONNECTION->prepare("SHOW TABLES LIKE :table");
            $stmt->bindParam(":table", $table);
            $stmt->execute();
            if (empty($stmt->fetch())) {
                return false;
            } else {
                return true;
            }
        }
    }
}