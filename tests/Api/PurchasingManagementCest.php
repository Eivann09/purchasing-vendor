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
           "vendor_name"=> "name1edittest",
            "contact_person"=> "person1edittest",
            "country_code"=> "code1edittest",
            "phone_number"=> "5678111",
            "email"=> "email1edittest",
            "address"=> "address1edittest",
            "vendor_status"=> "not_verified",
            "payment_term"=> "term1edittest",
            "delivery_term"=> "schedule1edittest",
            "shipping_preparation_day"=> "3_to_5_business_days",
            "nature_of_business"=> "corporation",
            "business_tax_type"=> "non_vat",
            "courier"=> "lbc",
            "document_expiration_date"=> "2024-07-08T05:34:39.699Z",
            "is_manufacturer"=> 1,
            "is_distributor"=> 0,
            "is_dealer"=> 0,
            "is_reseller"=> 1,
            "status"=> "inactive",
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
            "id" => 1,
            "vendor_name"=> "name1edit",
            "contact_person"=> "person1edit",
            "country_code"=> "code1edit",
            "phone_number"=> 5678,
            "email"=> "email1edit",
            "address"=> "address1edit",
            "vendor_status"=> "verified",
            "payment_term"=> "term1edit",
            "delivery_term"=> "schedule1edit",
            "shipping_preparation_day"=> "2_to_3_business_days",
            "nature_of_business"=> "partnership",
            "business_tax_type"=> "vat",
            "courier"=> "lazada",
            "document_expiration_date"=> "2024-07-08T05:34:39.699Z",
            "is_manufacturer"=> 1,
            "is_distributor"=> 1,
            "is_dealer"=> 1,
            "is_reseller"=> 1,
            "status"=> "active",
        ];

        //setup and execute test
        $I->haveHttpHeader('Content-Type', 'application/json');

        $id = 1;

        $I->sendPut('Vendors/Api.php/' . $id, $data);
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
