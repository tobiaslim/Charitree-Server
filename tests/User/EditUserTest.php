<?php
namespace Tests\User;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCaseWithSession;

class EditUserTest extends TestCaseWithSession{
    use DatabaseTransactions;

    public function testInvalidEmail(){
        $this->createSessionTokenForTest("tobiaslkj@gmail.com", "password");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];
        
        //First Invalid EC
        $newEmail   = "xxxx@xxxx";
        $firstName  = "Tan";
        $lastName   = "Tobias";
        $params     = ["email"=>$newEmail, "first_name"=>$firstName, "last_name"=>$lastName];
        $this->json('PUT', '/users', $params, $headers);
        $this->seeStatusCode(422);
        $this->seeJson(["email"=>["The email must be a valid email address."]]);
    }

    public function testInvalidFirstName(){
        $this->createSessionTokenForTest("tobiaslkj@gmail.com", "password");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];
        
        //First Invalid EC
        $newEmail   = "xxxx@xxxx";
        $firstName  = "321";
        $lastName   = "Tobias";
        $params     = ["email"=>$newEmail, "first_name"=>$firstName, "last_name"=>$lastName];
        $this->json('PUT', '/users', $params, $headers);
        $this->seeStatusCode(422);
        $this->seeJson(["first_name"=>["The first name may only contain letters."]]);
    }

    public function testInvalidLastName(){
        $this->createSessionTokenForTest("tobiaslkj@gmail.com", "password");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];
        
        //First Invalid EC
        $newEmail   = "xxxx@xxxx";
        $firstName  = "Tan";
        $lastName   = "321";
        $params     = ["email"=>$newEmail, "first_name"=>$firstName, "last_name"=>$lastName];
        $this->json('PUT', '/users', $params, $headers);
        $this->seeStatusCode(422);
        $this->seeJson(["last_name"=>["The last name may only contain letters."]]);
    }

    public function testValidEditUser(){
        $this->createSessionTokenForTest("tobiaslkj@gmail.com", "password");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];
        
        // valid EC
        $newEmail   = "xxxx@xxxx.com";
        $firstName  = "Tan";
        $lastName   = "Tobias";
        $params     = ["email"=>$newEmail, "first_name"=>$firstName, "last_name"=>$lastName];
        $this->json('PUT', '/users', $params, $headers);
        $this->seeStatusCode(201);
    }


    
}