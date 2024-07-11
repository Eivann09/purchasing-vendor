<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

class PurchasingManagementFailCest
{
    //function for httpPost
    public function iShouldFailPostEmptyPayload(ApiTester $I) {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('Vendors/', []);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'fail']);
    }

    //function for httpPost
    public function iShouldFailPostNonArray(ApiTester $I){
        $I->haveHttpHeader('content-Type', 'application/json');
        $I->sendPOST('Vendors/', 'This is a string');
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'fail']);

    }

    //function for httpGet
    public function iShouldFailGETDetails(ApiTester $I){
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('Vendors/', [
            'age' => 25
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'fail']);
    }

    //function for Update/Put
    public function iShouldUpdateDataFail(ApiTester $I)
    {
        //test data
        $data = [
            "id" => 1,
        ];

        //setup and execute test
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPut('/Vendors/Api.php/1', $data);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'fail']);
    }
    
    //function for Delete
    function iShouldFailDeleteUser(ApiTester $I){
        $id = 123;
        $payload = ['id' => $id];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDelete('Vendors/1', $payload);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status'=> 'fail']);
    }

}
