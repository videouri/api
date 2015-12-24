<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost:8000';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        // $app->loadEnvironmentFrom('.env.testing');

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Create mock
     *
     * @param $class
     */
    protected function mock($class)
    {
        $this->mock = \Mockery::mock($class);
        $this->app->instance($class, $this->mock);
    }

    /**
     * Mocking Route Model Binding
     *
     * @param $mock
     * @param $model
     * @param string $key
     */
    public function mockRouteModelBinding($mock, $model, $key = 'id')
    {
        $mock->shouldReceive('getRouteKeyName')->once()
            ->andReturn($key);
        $mock->shouldReceive('where')->once()
            ->with('id', 1)
            ->andReturn(\Mockery::self())
            ->shouldReceive('first')->once()
            ->andReturn($model);
    }

    /**
     * Dump Session Error Message for Debug
     */
    protected function dumpErrorMessages()
    {
        $errors = $this->app['session.store']->get('errors');
        echo "\n";
        if ($errors) {
            dd($errors->getMessages());
        } else {
            echo "Error Message does not exist.\n";
            die;
        }
    }

    // public function setUp()
    // {
    //     parent::setUp();
    //     Artisan::call('migrate');
    // }

    // public function tearDown()
    // {
    //     Artisan::call('migrate:reset');
    //     parent::tearDown();
    // }
}
