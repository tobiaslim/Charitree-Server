<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class DonationTest extends TestCase
{
    /**
     * 
     *
     * @return void
     */
    public function testShouldHaveAName(){
        $user = "harrisonwjy@mail.com";
        $password = "U0JiZnM0UlRrU20wakljNzkySTJmd2U3SnVvMWdBMHpIRGhXeERrYg==";
        $token = base64_encode("$user:$password");
        $this->json('GET','/donations', [], ["Authorization"=>"Basic: $token"]);
        $this->seeStatusCode(200);
    }

}