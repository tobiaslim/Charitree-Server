<?php
namespace Tests\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

use Tests\TestCaseWithSession;


class GetCurrentCampaignManagerTest extends TestCaseWithSession{

    // use DatabaseTransactions;
    public function testInvalidSessionToken(){
        $this->createUserAndSessionForTest("xxxx@xxxxx.xxx", "P@ssword1", "xxx", "xxx");
        $this->createCampaignManagerWithSessionForTest();

        $invalidSessionToken    = $this->sessionToken; //. "sdsa";

        $headers                = ['Authorization'=>"Basic: $this->sessionToken"];
        $response = $this->json('GET', '/users/campaignmanagers', [], $headers); 
        $this->seeStatusCode(401);
    }


    public function testValidSessionToken(){
        $this->createUserAndSessionForTest("xxxx@xxxxx.xxx", "P@ssword1", "xxx", "xxx");
        $this->createCampaignManagerWithSessionForTest();
        // $this->seeInDatabase('CampaignManager', ['UEN' => "T00000000"]);

        $headers            = ['Authorization'=>"Basic: $this->sessionToken"];
        $response = $this->json('GET', '/users/campaignmanagers', array(), $headers); 
        $this->seeStatusCode(200);
    }
}