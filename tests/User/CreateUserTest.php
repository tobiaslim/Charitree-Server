<?php
namespace Tests\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    // Include the statement directly below this to rollback the creation of a resource after the test
    use DatabaseTransactions;

    public function testInvalidEmail(){
        $email      = "xxxx@xxxx";
        $password   = "Password1!";
        $firstName  = "Tan";
        $lastName   = "Thomas";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["email"=>["The email must be a valid email address."]]);
    }

    public function testInvalidPassword(){
        $email      = "xxx@xxx.xxx";
        $password   = "password1";
        $firstName  = "Tan";
        $lastName   = "Thomas";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["password"=>["The password format is invalid."]]);
    }

    public function testInvalidFirstName(){
        $email      = "xxx@xxx.xxx";
        $password   = "Password1!";
        $firstName  = "123";
        $lastName   = "Thomas";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["first_name"=>["The first name may only contain letters."]]);
    }

    public function testMissingFirstName(){
        $email      = "xxx@xxx.xxx";
        $password   = "Password1!";
        $lastName   = "Thomas";

        $params = ["email"=>$email, "password"=>$password, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["first_name"=>["The first name field is required."]]);
    }

    public function testInvalidLastName(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Password1!";
        $firstName  = "Tan";
        $lastName   = "321";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, 'last_name'=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["last_name"=>["The last name may only contain letters."]]);
    }

    public function testMissingLastName(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Password1!";
        $firstName  = "Tan";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["last_name"=>["The last name field is required."]]);
    }

    public function testValidRequest(){
        //test should pass unless database already has the user record.
        $email      = "xxx@xxx.xxx";
        $password   = "Password1!";
        $firstName  = "Tan";
        $lastName   = "Thomas";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName,"last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(201);
        $this->seeInDatabase('User', ['email' => $email]);
    }
}
