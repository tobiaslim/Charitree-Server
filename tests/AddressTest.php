<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class DonationTest extends TestCase
{
    /**
     * 
     *
     * @return void
     */
    protected $user = "harrisonwjy@hotmail.com";
    protected $session = "RW81dFNOckROVHFkcmFETWg0RWkydVpPOTd5clZWY2xYOWI0Y28zdA==";

    protected $working_params = ["addresses"=>[["street_name"=>"Merry Christmas","unit"=>'12-32',"zip"=>'750469']]];


    public function testCreateAddressForUserShouldReturn401(){
        $this->json('POST', '/users/addresses', $this->working_params);
        $this->seeStatusCode(401);
    }
    
    public function testCreateAddressForUserShouldReturn422(){
        //Testing No zip
        $params = ["addresses"=>[["street_name"=>"Merry Christmas","unit"=>'12-32',"zip"=>'']]];
        $this->json('POST', '/users/addresses', $params, ['Authorization'=>"Basic ". $this->generateToken()]);
        $this->seeStatusCode(422);

        //testing no street name
        $params = ["addresses"=>[["street_name"=>"","unit"=>'12-32',"zip"=>'750469']]];
        $this->json('POST', '/users/addresses', $params, ['Authorization'=>"Basic ". $this->generateToken()]);
        $this->seeStatusCode(422);

        //testing no short zip
        $params = ["addresses"=>[["street_name"=>"Merry Christmas","unit"=>'12-32',"zip"=>'1234']]];
        $this->json('POST', '/users/addresses', $params, ['Authorization'=>"Basic ". $this->generateToken()]);
        $this->seeStatusCode(422);
    }

    public function testCreateAddressShouldReturn201(){
        //full variables
        $this->json('POST', '/users/addresses', $this->working_params, ['Authorization'=>"Basic ". $this->generateToken()]);
        $this->seeStatusCode(201);

        //without unit
        unset($this->working_params['addresses'][0]['unit']);
        $this->json('POST', '/users/addresses', $this->working_params, ['Authorization'=>"Basic ". $this->generateToken()]);
        $this->seeStatusCode(201);

    }

    public function generateToken(){
        return base64_encode("$this->user:$this->session");
    }
}