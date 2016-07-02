<?php

namespace WebEndAPI\Database;

use WebEndAPI\Database\Engines\MySql;

class Database {
    /**
     * Your database login details
     */
    public static $_HOSTNAME = '127.0.0.1';
    public static $_USERNAME = 'root';
    public static $_PASSWORD = 'root';
    public static $_DATABASE = 'discovery';

    /**
     * Each supported database engine.
     * The engine that you want to use should be uncommented.
     */
    private static $_ENGINE = MySql::class;

    public function __construct()
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
        $engine::tablesExist($tables);
    }
}