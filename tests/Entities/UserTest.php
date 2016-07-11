<?php

namespace Videouri\Tests\Entities;

use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Videouri\Tests\Entities
 */
class UserTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testModelFactory()
    {
        $user = factory(User::class)->create();
        $userFromDB = User::where('username', $user->username)->first();

        $this->assertEquals($user->username, $userFromDB->username);
    }
}