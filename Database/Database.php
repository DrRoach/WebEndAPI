<?php

namespace WebEndAPI\Database;

use WebEndAPI\Database\Engines\MySql;

class Database {
    /**
     * Your database login details
     *
     * TODO:
     * Move this into each individual engine class or put in some sort of JSON file.
     * Not all engines use the same credentials like below.
     */
    public static $_HOSTNAME = '127.0.0.1';
    public static $_USERNAME = 'root';
    public static $_PASSWORD = '';
    public static $_DATABASE = 'discovery';

    /**
     * Each supported database engine.
     * The engine that you want to use should be uncommented.
     */
    private static $_ENGINE = MySql::class;

    public static function __callStatic($function, $params)
    {
        /**
         * Check to see if the `$_schema` tables exist.
         */

        //Get the engine we are using
        $engine = self::$_ENGINE;

        //Get the table names from `$_schema`
        $class = get_called_class();
        //Get the list of tables
        $tables = array_keys($class::$_schema);

        //Connect to database
        $engine::connect(self::$_HOSTNAME, self::$_USERNAME, self::$_PASSWORD, self::$_DATABASE);

        //Check to see if the tables exist
        if ($engine::tablesExist($tables) === false) {
            //Get list of full table
            $fullTable = $class::$_schema;
            //Create the tables
            $engine::createSchema($fullTable);
        }
    }

    public static function get($query)
    {
        $engine = self::$_ENGINE;

        return $engine::get($query);
    }

    /**
     * Insert passed data into database.
     *
     * Take the data passed to it from the calling class and call the matching function in the right engines'
     * class.
     *
     * @param $query
     */
    public static function insert($query)
    {
        $engine = self::$_ENGINE;

        return $engine::insert($query);
    }
}
