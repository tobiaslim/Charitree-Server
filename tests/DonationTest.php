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
    public function testGetDonationsForUserShouldReturn200(){
        $user = "tobiaslj@gmail.com";
        $password = "OFE5UUx4T3FNSDh1Y3BlMHY5WWJUNE5YZFd4M052bnhIWk9sclpLRQ==";
        $token = base64_encode("$user:$password");
        $this->json('GET','/users/donations', [], ["Authorization"=>"Basic: $token"]);
        $this->seeStatusCode(200);
    }

    public function testGetDonationsForUserShouldReturn404(){
        $user = "tobiass@gmail.com";
        $password = "WEVRUUo0QUhWWGo3T2Zsb2RQekVmN09vdHQxUUJjem45dkxoZmZmWQ==";
        $token = base64_encode("$user:$password");
        $this->json('GET','/users/donations', [], ["Authorization"=>"Basic: $token"]);
        $this->seeStatusCode(404);
    }
}