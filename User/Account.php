<?php
/**
 * Account class that lets your quickly and easily create,
 * edit and store information about accounts.
 *
 * @category User
 * @package
 */
namespace WebEndAPI\User;

use phpDocumentor\Reflection\DocBlock\Tags\ParamTest;
use WebEndAPI\Database\Database;

class Account extends Database
{
    /**
     * Change this salt value, it is used to make your passwords more secure
     */
    private static $_SALT = "aFROj3nf9fJFoifkor303j2";

    /**
     * @var array
     *
     * TODO:
     * When `$_schema` is updated in a file, this should be recognised and the table should be updated.
     * Should it? Would this put data in danger of being accidentally deleted?
     */
    protected static $_schema = [
        'users' => [
            [
                'name' => 'id',
                'type' => 'int',
                'size' => 11,
                'extra' => 'PRIMARY KEY'
            ],
            [
                'name' => 'username',
                'type' => 'varchar',
                'size' => 30
            ],
            [
                'name' => 'password',
                'type' => 'varchar',
                'size' => 128
            ],
            [
                'name' => 'salt',
                'type' => 'varchar',
                'size' => 128
            ],
            [
                'name' => 'email',
                'type' => 'varchar',
                'size' => 255
            ]
        ]
    ];

    public static function create($user)
    {
        //Call parent class to create DB from schema
        self::__callStatic('create', []);
        
        //Check to make sure all required fields have been passed
        $message = self::checkRequiredFields($user);
        if ($message !== true) {
            return [
                'success' => false,
                'message' => $message
            ];
        }

        //Check to see if the username is available
        $userCheck = Database::Get([
            'table' => 'users',
            'fields' => ['id'],
            'conditions' => [
                'username' => $user['username']
            ]
        ]);

        //The username has already been taken
        if ($userCheck !== false) {
            return [
                'success' => false,
                'message' => 'Sorry that username has already been taken.'
            ];
        }

        $salt = self::generateSalt();

        /**
         * Hash password with salt
         */
        $password = hash('sha512', self::$_SALT . $user['password'] . $salt);

        /**
         * Create the new user.
         * Make sure that we pass all custom data, but first unset the password.
         */
        unset($user['password']);
        $customData = [];
        foreach ($user as $column => $value) {
            $customData[$column] = $value;
        }

        /**
         * Merge our custom data with our hardcoded required values
         */
        $userCreate = Database::insert([
            'table' => 'users',
            'data' => array_merge($customData, [
                'password' => $password,
                'salt' => $salt,
            ])
        ]);

        switch ($userCreate) {
            case true:
                $message = 'Account Created.';
                break;
            case false:
                $message = 'I\'m sorry, your account could not be created.';
        }

        return [
            'success' => $userCreate,
            'message' => $message
        ];
    }

    private static function checkRequiredFields($user)
    {
        //Check required data
        if (empty($user['username'])) {
            return 'You must give a username to create an account.';
        }

        if (empty($user['password'])) {
            return 'You must give a password to create an account.';
        }
        
        return true;
    }

    private static function generateSalt()
    {
        //Generate random salt
        $salt = microtime(true) * rand(1, 100);

        $salt = hash('sha512', $salt);

        return $salt;
    }
}
