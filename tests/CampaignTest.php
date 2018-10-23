<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CampaignTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     * 
     */
    public function testGetCampaignsWithNoParamsShouldGet200(){
        $this->json('GET', '/campaigns');
        $this->seeStatusCode(200);
    }

    public function testGetCampaignsWithWrongParamsShouldGet422(){
        $this->json('GET', '/campaigns', ['max'=>"not a number"]);
        $this->seeStatusCode(422);
    }

    public function testReturnCampaignsShouldBeLowerOrEqualToMax(){
        $max = 5;   //max number of result
        $response = $this->json('GET', '/campaigns', ['max'=>$max])->response->content();
        $response = json_decode($response, true);
        $size = $response['campaigns'] == null ? 0 : count($response['campaigns']);
        $this->seeStatusCode(200);
        $this->assertLessThanOrEqual($max, $size);
    }
}
