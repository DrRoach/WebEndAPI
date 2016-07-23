<?php

use WebEndAPI\User\Account;

class CreateAccountTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        require __DIR__ . '/../../vendor/autoload.php';

        //Add `tel_number` to the users table
        Account::$_schema['users'][] = [
            'name' => 'tel_number',
            'type' => 'varchar',
            'size' => 50
        ];

        $user = [
            'username' => 'TestAccount',
            'password' => 'p4ssw0rd!',
            'passwordConfirm' => 'p4ssw0rd!',
            'email' => 'testemail@gmail.com',
            'tel_number' => '0123456789'
        ];

        $create = Account::create($user);

        $this->assertTrue($create['success']);

        $data = \WebEndAPI\Database\Database::get([
            'table' => 'users',
            'conditions' => [
                'username' => 'TestAccount'
            ]
        ]);

        $this->assertEquals('TestAccount', $data['username']);
        $this->assertEquals('testemail@gmail.com', $data['email']);
        $this->assertEquals('0123456789', $data['tel_number']);

        /**
         * Test that usernames with special characters in aren't saved
         */
        $user['username'] = 'TestAccount\' OR 1=\'1';
        $falseResult = Account::create($user);
        $this->assertFalse($falseResult['success']);

        /**
         * Test that duplicate account names aren't saved
         */
        $user['username'] = 'TestAccount';
        $usernameInUse = Account::create($user);
        $this->assertEquals('Sorry that username has already been taken.', $usernameInUse['message']);


    }
}
