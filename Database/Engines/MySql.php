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

    public static function createSchema($tables)
    {
        /**
         * Loop through each table
         */
        foreach ($tables as $name => $columns) {
            /**
             * 1. Create table `$key`
             * 2. Foreach `$column` create column
             */
            $stmtString = "CREATE TABLE " . $name . "(";

            foreach ($columns as $key => $column) {
                $stmtString .= $column['name'] . " " . $column['type'];

                //Check to see if a size has been set in the schema
                if (!empty($column['size'])) {
                    $stmtString .= "(" . $column['size'] . ")";
                }

                $stmtString .= (!empty($column['extra']) ? ' ' . $column['extra'] : '');

                //Keep appending `,` unless we're on the last column
                if (($key + 1) != sizeof($columns)) {
                    $stmtString .= ',';
                } else {
                    //Close our brackets
                    $stmtString .= ')';
                }
            }

            //Create table
            $stmt = self::$_CONNECTION->prepare($stmtString);
            $stmt->execute();
        }
    }

    public static function get($query)
    {
        $stmt = "SELECT ";

        /**
         * Select fields logic
         */
        if (empty($query['fields'])) {
            $stmt .= "*";
        } else {
            foreach ($query['fields'] as $key => $field) {
                $stmt .= " $field";
                if (($key + 1) != sizeof($query['fields'])) {
                    $stmt .= ",";
                }
            }
        }

        /**
         * From table logic
         */
        if (empty($query['table'])) {
            throw new Exception("You can't make a database call without giving a table");
        } else {
            $stmt .= " FROM " . $query['table'];
        }

        /**
         * Set where logic
         */
        if (!empty($query['conditions'])) {
            $stmt .= " WHERE ";
            foreach ($query['conditions'] as $field => $value) {
                $stmt .= $field . "='" . addslashes($value) . "'";
                if (($key + 1) != sizeof($query['fields'])) {
                    $stmt .= ",";
                }
            }
        }

        $stmt = self::$_CONNECTION->prepare($stmt);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function insert($query)
    {
        $stmt = "INSERT INTO " . $query['table'] . " (";
        $count = 0;

        /**
         * Insert the column names into the query
         */
        foreach ($query['data'] as $field => $value) {
            $stmt .= $field;
            if (($count + 1) != sizeof($query['data'])) {
                //Add comma between each column name except for last column
                $stmt .= ",";
            } else {
                //Add closing bracket at end of columns
                $stmt .= ")";
            }
            $count++;
        }

        $stmt .= " VALUES (";

        /**
         * Insert values into the query
         */
        $count = 0;
        foreach ($query['data'] as $value) {
            //Call `addslashes()` as this is user inputted
            $stmt .= "'" . addslashes($value) . "'";

            if (($count + 1) != sizeof($query['data'])) {
                //Add comma between each column name except for last column
                $stmt .= ",";
            } else {
                //Add closing bracket at end of columns
                $stmt .= ")";
            }
            $count++;
        }

        $stmt = self::$_CONNECTION->prepare($stmt);
        return $stmt->execute();
    }
}
