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
        $favorite = factory(Favorite::class)->create();
        $favoriteFromDB = Favorite::where('user_id', $favorite->user_id)->first();

        $this->assertEquals($favorite->user_id, $favoriteFromDB->user_id);
    }
}
