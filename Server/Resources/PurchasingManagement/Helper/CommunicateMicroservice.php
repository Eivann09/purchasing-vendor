<?php


namespace Resources\PurchasingManagement\Helpers;

use GuzzleHttp\Client;

// use Wrapper\System\Messages;
use Wrapper\Security\JWToken;

class CommunicateMicroservice
{

    public function __construct()
    {
        //Create class for message
        // $this->Messages = new Messages();

        //Create class for JWToken
        $this->token = new JWToken();
    }

    /**
     * Communicating with other Microservice.
     */
    public function connectOtherMS($base_uri, $path, $http_method, $payload)
    {
        // Create Guzzle client
        $microservice_client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $base_uri,
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        // 'headers' => [
        //     'Authorization' => 'Bearer ' . $token,
        //     'Content-Type' => 'application/json'
        // ]

        if ($http_method === 'GET') {
            // Send request to Accounts microservice
            $microservice_client_response = $microservice_client->request($http_method, $path, [
                'query' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token->getBearerToken(),
                    'Content-Type' => 'application/json'
                ]
            ]);
        } else {
            $microservice_client_response = $microservice_client->request($http_method, $path, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token->getBearerToken(),
                    'Content-Type' => 'application/json'
                ]
            ]);
        }

        // Check if request is successful
        if($microservice_client_response->getStatusCode() !== 200) {
            // $this->Messages->logWarning('Accounts microservice returned status code: ' . $microservice_client_response->getStatusCode());
            // return $this->Messages->jsonInternalError();
            return false;
        }

        // Parse JSON response
        $response = json_decode($microservice_client_response->getBody()->getContents(), true);

        // Check if response is a non-decodable response
        if(is_null($response)) {
            // $this->Messages->logWarning('Accounts microservice returned null response.');
            // return $this->Messages->jsonInternalError();
            return false;
        }


        // Check if response is successful and valid
        if (!array_key_exists('status', $response) || $response['status'] !== 'success' || !array_key_exists('data', $response) || !is_array($response['data'])) {
            // $this->Messages->logWarning('Accounts microservice returned invalid JSON response.', $response);
            // return $this->Messages->jsonInternalError();
            return false;
        }

        return $response['data'];
    }
}
