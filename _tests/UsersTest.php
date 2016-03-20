<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Faker\Factory as Faker;

class UsersTest extends TestCase
{
    use DatabaseTransactions;

    public $users;
    public $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
        $this->users = new App\Repositories\UserRepository;
    }

    // /**
    //  * [createRegularUser description]
    //  * @return void
    //  */
    // public function testCreateSocialUser()
    // {
    //     // $user = factory(User::class)->create();

    //     $userData = new stdClass;

    //     $userData->id       = $this->faker->uuid();
    //     $userData->nickname = $this->faker->name();
    //     $userData->email    = $this->faker->email();
    //     $userData->password = bcrypt(str_random(10));
    //     $userData->avatar   = null;


    //     $user = $this->users->findByUserNameOrCreate($userData, 'laravel');

    //     // dd($user);


    //     // $this->visit('/')
    //     //      ->see('Laravel 5');
    // }

    // public function testCreateRegularUser()
    // {
    //     $user = factory(User::class)->create();

    //     // $this->visit('/')
    //     //      ->see('Laravel 5');
    // }

    public function testRegistrationForm()
    {
        return true;
        // $this->visit('join');
    }
}
