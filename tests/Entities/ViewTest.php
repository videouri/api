<?php

namespace Videouri\Tests\Entities;

use App\Entities\View;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Videouri\Tests\Entities
 */
class ViewTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testModelFactory()
    {
        $view = factory(View::class)->create();
        $viewFromDB = View::where('user_id', $view->user_id)->first();

        $this->assertEquals($view->user_id, $viewFromDB->user_id);
    }
}