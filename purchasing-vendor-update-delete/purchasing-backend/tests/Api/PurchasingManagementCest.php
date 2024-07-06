<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class PurchasingManagementCest{

    public $vendorId;

    //function for Post
    public function iShouldPOSTUser(ApiTester $I){

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('Vendors/',
        [
            "vendor_name" => "Sabs",
            "contact_person"=> "Angelica",
            "country_code" => "098",
            "phone_number" => "09511475223",
            "email" => "angel@gmail.com",
            "address" => "Paulba, Ligao",
            "vendor_status" => "verified",
            "attach" => "attach",
            "payment_term" => "Net 30 days",
            "delivery_schedule" => "Weekly",
            'bank_details' => array([
                "bank_account_name" => "Angelica",
                "bank_account_number" => 123
            ])
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);
        $response = json_decode($I->grabResponse(), TRUE);
        $this->vendorId=$response['data']['id'];

    }

   //function for Get
    public function iShouldGETDetails(ApiTester $I){

        $I->haveHttpHeader('Content-Type', 'application/json');
        $id = $this->vendorId;
        $I->sendGet('Vendors/' . $id);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);
    }

    //for partial match
    public function iShouldGetAllPartialMatch(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('Vendors/?vendor_name=1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);
    }


    //function for Put
    public function iShouldUpdateData(ApiTester $I)
    {
        //test data
        $data = [
            "id" => $this->vendorId,
	        "vendor_name" => "name1edit",
            "contact_person" => "person1edit",
	        "country_code" => "code1edit",
            "phone_number" => 5678,
            "email" => "email1edit",
            "address" => "address1edit",
            "vendor_status" => "verified",
            "attach" => "attach1edit",
            "payment_term" => "term1edit",
            "delivery_schedule" => "schedule1edit",
	        // "vendor_id" => $this->vendorId,
            'bank_details' => array([
                "bank_account_name" => "acc_name1edit",
                "bank_account_number"=> 5678
            ])
        ];

        //setup and execute test
        $I->haveHttpHeader('Content-Type', 'application/json');

        $id = $this->vendorId;

        $I->sendPut('Vendors/' . $id, $data);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);
    }

    //function for Delete
    public function iShouldDeleteUser(ApiTester $I){
        $I->haveHttpHeader('Content-Type', 'application/json');

        $id = $this->vendorId;

        $I->sendDELETE('Vendors/'. $id,
        ['id' => $this->vendorId]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'success']);
    }
}
