<?php
namespace Tests\User;

use Tests\TestCaseWithSession;

class RegisterAsCampaignManagerTest extends TestCaseWithSession{
    public function testInvalidNullOrganizationName(){
        $this->createUserAndSessionForTest("xxxx@xxxx.xxx", "P@ssword1", "tobias", "lim");

        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];

        $UEN        = 'T00000000';

        $params     = ["UEN"=>$UEN];
        $response = $this->json('POST', '/users/campaignmanagers', $params, $headers)->response;

        $this->seeStatusCode(422);
        $this->seeJson(["organization_name"=>["The organization name field is required."]]);
    }

    public function testInvalidLowerBoundaryUEN(){
        $this->createUserAndSessionForTest("xxxx@xxxx.xxx", "P@ssword1", "xxx", "xxx");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];

        $orgName    = "Organization X";
        $UEN        = 'T1234567';       // 8 Characters

        $params     = ["UEN"=>$UEN, "organization_name"=>$orgName];

        $response = $this->json('POST', '/users/campaignmanagers', $params, $headers)->response;
        $this->seeStatusCode(422);
        $this->seeJson(["UEN"=>["The u e n must be between 9 and 10 characters."]]);
    }

    public function testInvalidUpperBoundaryUEN(){
        $this->createUserAndSessionForTest("xxxx@xxxx.xxx", "P@ssword1", "xxx", "xxx");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];

        $orgName    = "Organization X";
        $UEN        = 'T1234567890';       // 11 Characters

        $params     = ["UEN"=>$UEN, "organization_name"=>$orgName];

        $response = $this->json('POST', '/users/campaignmanagers', $params, $headers)->response;
        $this->seeStatusCode(422);
        $this->seeJson(["UEN"=>["The u e n must be between 9 and 10 characters."]]);
    }

    public function testValidLowerBoundaryUEN(){
        $this->createUserAndSessionForTest("xxxx@xxxx.xxx", "P@ssword1", "xxx", "xxx");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];

        $orgName    = "Organization X";
        $UEN        = 'T12345678';       // 9 Characters

        $params     = ["UEN"=>$UEN, "organization_name"=>$orgName];

        $response = $this->json('POST', '/users/campaignmanagers', $params, $headers)->response;
        $this->seeStatusCode(201);
    }

    public function testValidUpperBoundaryUEN(){
        $this->createUserAndSessionForTest("xxxx@xxxx.xxx", "P@ssword1", "xxx", "xxx");
        $headers    = ['Authorization'=>"Basic: $this->sessionToken"];

        $orgName    = "Organization X";
        $UEN        = 'T123456789';       // 10 Characters

        $params     = ["UEN"=>$UEN, "organization_name"=>$orgName];

        $response = $this->json('POST', '/users/campaignmanagers', $params, $headers)->response;
        $this->seeStatusCode(201);
    }
}