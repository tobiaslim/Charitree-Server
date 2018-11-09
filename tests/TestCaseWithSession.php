<?php
namespace Tests;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TestCaseWithSession extends TestCase{
    use DatabaseTransactions;
    protected $sessionToken;
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
        $this->json('POST', '/campaignmanagers', $params); 
    }
}