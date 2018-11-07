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
        $password   = "Pa\$\$w0rd!";
        $firstName  = "xxx";
        $lastName   = "xxx";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["email"=>["The email must be a valid email address."]]);

        $email      = "xxxx.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "xxx";
        $lastName   = "xxxx";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["email"=>["The email must be a valid email address."]]);
    }

    public function testInvalidPassword(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Password";
        $firstName  = "Tobias";
        $lastName   = "Lim";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["password"=>["The password format is invalid."]]);

        $email      = "tobiaslkj@gmail.com";
        $password   = "Password";
        $firstName  = "Tobias";
        $lastName   = "Lim";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["password"=>["The password format is invalid."]]);
    }

    public function testInvalidFirstName(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "!@#$%";
        $lastName   = "tobias";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["first_name"=>["The first name may only contain letters."]]);

        $email      = "tobiaslkj@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "123";
        $lastName   = "Tobias";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["first_name"=>["The first name may only contain letters."]]);
    }

    public function testMissingFirstName(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $lastName   = "Mei Ling";

        $params = ["email"=>$email, "password"=>$password, "last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["first_name"=>["The first name field is required."]]);
    }

    public function testInvalidLastName(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "Tan";
        $lastName   = "123";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, 'last_name'=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["last_name"=>["The last name may only contain letters."]]);

        $email      = "tobiaslkj@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "Tan";
        $lastName   = "!@#$%^";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, 'last_name'=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["last_name"=>["The last name may only contain letters."]]);
    }

    public function testMissingLastName(){
        $email      = "tobiaslkj@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "Tan";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(422);
        $this->seeJson(["last_name"=>["The last name field is required."]]);
    }

    public function testValidRequest(){
        //test should pass unless database already has the user record.
        $email      = "tobias111@gmail.com";
        $password   = "Pa\$\$w0rd!";
        $firstName  = "Lim";
        $lastName   = "Tob";

        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName,"last_name"=>$lastName];

        $this->json('POST', '/users', $params);
        $this->seeStatusCode(201);
        $this->seeInDatabase('User', ['email' => $email]);
    }
}
