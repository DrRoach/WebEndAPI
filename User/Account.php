<?php

namespace WebEndAPI\User;

use WebEndAPI\Database\Database;

class Account extends Database {
    protected static $_schema = [
        'users' => [
            [
                'name' => 'id',
                'type' => 'int',
                'length' => 11,
                'extras' => [
                    'auto_increment'
                ]
            ],
            [
                'name' => 'username',
                'type' => 'varchar',
                'length' => 30
            ]
        ]
    ];

    public static function create($user)
    {
        echo 'a';
    }
}
