<?php
namespace Tests\User;

use Tests\TestCase;

class RetrieveOrganisationNameByUENTest extends TestCase{
    public function testInvalidLowerBound(){
        $UEN        = 'T0000000';

        $params     = ["uen"=>$UEN];
        $response = $this->json('GET', '/uen', $params)->response;

        $this->seeStatusCode(422);
    }

    public function testInvalidUpperBound(){
        $UEN        = 'T0000000000';

        $params     = ["uen"=>$UEN];
        $response = $this->json('GET', '/uen', $params)->response;

        $this->seeStatusCode(422);
    }

    public function testValidLowerBound(){
        $UEN        = 'T00000000';

        $params     = ["uen"=>$UEN];
        $response = $this->json('GET', '/uen', $params)->response;

        $this->seeStatusCode(404);
        $this->seeJson(["message"=>"UEN not found or have deregistered."]);
    }

    public function testValidUpperBound(){
        $UEN        = 'T000000000';

        $params     = ["uen"=>$UEN];
        $response = $this->json('GET', '/uen', $params)->response;

        $this->seeStatusCode(404);
        $this->seeJson(["message"=>"UEN not found or have deregistered."]);
    }
}