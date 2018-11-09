<?php
namespace Tests\User;

use Tests\TestCaseWithSession;

class RegisterAsCampaignManagerTest extends TestCaseWithSession{
    public function testNullOrganizationName(){
        $this->createSessionTokenForTest("tobiaslkj@gmail.com", "password");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];

        $UEN        = 'T00000000';

        $params     = ["UEN"=>$UEN];
        $this->json('POST', '/users/campaignmanagers', $params, $headers);
        $this->seeStatusCode(422);
        $this->seeJson(["organization_name"=>["The organization name field is required."]]);
    }
}