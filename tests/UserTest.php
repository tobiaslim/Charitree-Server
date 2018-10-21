<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * 
     *
     * @return void
     */
    public function testShouldReturn422IfNoParameters(){
        $this->post('/users',array(),['Content-type'=>'application/json']);

        $this->assertEquals(422, $this->response->getStatusCode());
    }

    public function testShouldReturn422IfNotEmailFormat(){
        $this->post('/users',["email"=>"tobias"],['Content-type'=>'application/json']);

        $this->assertEquals(422, $this->response->getStatusCode());
    }

    public function testShouldReceived201Code(){
        $params = ["email"=>"tobias@gmail.com", "first_name"=>"tobias", "last_name"=>"lim","password"=>"tobias"];
        // $this->post('/users', $params, ['Content-type'=>'application/json']);
        $this->json('POST', '/users', $params);
        $this->seeStatusCode(201);
    }

}
