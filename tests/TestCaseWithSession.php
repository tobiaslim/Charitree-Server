<?php
namespace Tests;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TestCaseWithSession extends TestCase{
    use DatabaseTransactions;
    protected $sessionToken;

    public function createUserAndSessionForTest(String $email, String $password, String $firstName, String $lastName){
        $params = ["email"=>$email, "password"=>$password, "first_name"=>$firstName, "last_name"=>$lastName];
        $this->json('POST', '/users', $params);
        $this->createSessionTokenForTest($email, $password);
    }
    
    public function createSessionTokenForTest(String $email, String $password){
        $params             = ["email"=>$email, "password"=>$password];
        $response           = $this->json('POST', '/sessions', $params)->response->getData(true);
        $token              = $response['user_token'];
        $this->sessionToken = base64_encode("$email:$token");
    }

    public function createCampaignManagerWithSessionForTest(){
        $organizationName   = "test organization";
        $UEN                = 'T00000000';
        $params             = ["UEN"=>$UEN, "organization_name"=>$organizationName];
        $headers            = ['Authorization'=>"Basic: $this->sessionToken"];
        $response           = $this->json('POST', '/users/campaignmanagers', $params, $headers)->response->getData(true); 
    }
}