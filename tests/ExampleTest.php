<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->json('GET','/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testJsonResponse(){
        $this->json('POST','/');

        $this->assertEquals(404, $this->response->getStatusCode());
    }
}
