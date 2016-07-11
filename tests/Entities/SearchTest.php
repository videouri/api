<?php

namespace Videouri\Tests\Entities;

use App\Entities\Search;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Videouri\Tests\Entities
 */
class SearchTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testModelFactory()
    {
        $search = factory(Search::class)->create();
        $searchFromDB = Search::where('term', $search->term)->first();

        $this->assertEquals($search->term, $searchFromDB->term);
    }
}