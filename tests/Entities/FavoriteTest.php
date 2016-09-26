<?php

namespace Videouri\Tests\Entities;

use Videouri\Entities\Favorite;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Videouri\Tests\Entities
 */
class FavoriteTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testModelFactory()
    {
        $favourite = factory(Favorite::class)->create();
        $favouriteFromDB = Favorite::where('user_id', $favourite->user_id)->first();

        $this->assertEquals($favourite->user_id, $favouriteFromDB->user_id);
    }
}
