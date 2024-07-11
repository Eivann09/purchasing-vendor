<?php

namespace Resources\PurchasingManagement\Modules\Vendors;
require_once __DIR__ . "/../../../../ModuleInterface.php";
require_once __DIR__ . "/../../System/Main.php";

use Wrapper\Interfaces\ModuleInterface;

use Exception;

use Resources\PurchasingManagement\System\Main;

class API extends Main implements ModuleInterface
{

    public const GET_PERMISSION_ALIAS = null;

    public const POST_PERMISSION_ALIAS = null;

    public const PUT_PERMISSION_ALIAS = null;

    public const DELETE_PERMISSION_ALIAS = null;

    public const FILE_UPLOAD_PERMISSION_ALIAS = null;

    protected $Logger;

    /**
     * Table name
     *
     * @var string
     */
    protected $table_bank;
    protected $table_vendors;

    /**
     * Acceptable parameters
     *
     * @var array
     */
    protected $accepted_parameters;

    /**
     * Response column
     *
     * @var array
     */
    protected $response_column;

    protected $post_required_fields;

    protected $post_bank_account;


    public function __construct()
    {

        $this->accepted_parameters = [
            "vendor_name",
            "contact_person",
            "country_code",
            "phone_number",
            "email",
            "address",
            "vendor_status",
            "payment_term",
            "delivery_term",
            "shipping_preparation_day",
            "nature_of_business",
            "business_tax_type",
            "courier",
            "document_expiration_date",
            "is_manufacturer",
            "is_distributor",
            "is_dealer",
            "is_reseller",
            "status",
        ];

        $this->response_column = [
            "vendor_name",
            "contact_person",
            "country_code",
            "phone_number",
            "email",
            "address",
            "vendor_status",
            "payment_term",
            "delivery_term",
            "shipping_preparation_day",
            "nature_of_business",
            "business_tax_type",
            "courier",
            "document_expiration_date",
            "is_manufacturer",
            "is_distributor",
            "is_dealer",
            "is_reseller",
            "status",
        ];

        $this->post_required_fields  = [
            "vendor_name",
            "contact_person",
            "country_code",
            "phone_number",
            "email",
            "address",
            "vendor_status",
            "payment_term",
            "delivery_term",
            "shipping_preparation_day",
            "nature_of_business",
            "business_tax_type",
            "courier",
            "document_expiration_date",
            "is_manufacturer",
            "is_distributor",
            "is_dealer",
            "is_reseller",
            "status",
        ];

        $this->post_bank_account = [
            'bank_account_name',
            'bank_account_number'
        ];

        $this->table_bank = 'bank_accounts';
        $this->table_vendors = 'vendors';

        parent::__construct();
    }

    /**
     * HTTP GET handler
     *
     * @param array $params
     * @param bool $api
     * @return array|false|\MysqliDb|string
     * @throws \Exception
     */
    public function httpGet($params = array(), $api = true)
    {
        //check if params is array
        if(!is_array($params)){
            return $this->Messages->jsonErrorInvalidParameters();
        }

        //validate each property
        foreach ($params as $key => $value){
            if(!in_array($key, $this->accepted_parameters)){
                return $this->Messages->jsonErrorInvalidParameters();
            }
        }

        //Check parameters
        if(isset($params['vendor_id']) || isset($params['vendor_name']) || isset($params['bank_account_name']) || isset($params['bank_account_number'])){
            //Join tables
            $this->db->join($this->table_vendors, 'tbl_bank_accounts.vendor_id = tbl_vendors.id', 'RIGHT OUTER');
            //Apply filters and partial match
            foreach($params as $key => $value){
                $this->db->where($key, "%" . $value . "%", 'like');
            }
            //Execute query
            $queryResult = $this->db->get($this->table_bank);
        } else{
            if(isset($params['id'])) $this->db->where('id', $params['id']);
            $queryResult = $this->db->get($this->table_vendors);

            if(isset($params['id']) && !empty($queryResult)) {
                $this->db->where('vendor_id', $params['id']);
                $queryResult[0]['bank_details'] = $this->db->get($this->table_bank);
            }
        }

        // Remove the specific fields if they are null
        // foreach($queryResult as &$row) {
        //     if(is_null($row['vendor_id']) || is_null($row['bank_account_name']) || is_null($row['bank_account_number'])) {
        //         unset($row['vendor_id']);
        //         unset($row['bank_account_name']);
        //         unset($row['bank_account_number']);
        //     }
        // }

        //check if query is successful
        if ($this->db->getLastErrno() > 0) {
            $this->Logger->logError($this->db->getLastError(), $queryResult);
            return $this->Messages->jsonFailResponse("No data found!");
        }

        // If data found in vendors table, return the result
        return $this->buildApiResponse($queryResult, $this->response_column, TRUE);
    }

    /**
     * HTTP POST handler
     *
     * @param $payload
     * @return false|string
     * @throws \Exception
     */
    public function httpPost($payload){

             //checks if params is array
            if(!is_array($payload)) {
                return $this->Messages->jsonErrorInvalidParameters();
            }

            //check is params is empty
            if(empty($payload)){
                return $this->Messages->jsonFailResponse("Invalid or Empty Payload");
            }

            $required_fields = [
                "vendor_name",
                "contact_person",
                "country_code",
                "phone_number",
                "email",
                "address",
                "vendor_status",
                "payment_term",
                "delivery_term",
                "shipping_preparation_day",
                "nature_of_business",
                "business_tax_type",
                "courier",
                "document_expiration_date",
                "is_manufacturer",
                "is_distributor",
                "is_dealer",
                "is_reseller",
                "status",
            ];

             //Check if all fields required are filled
             foreach($required_fields as $field) {
                if(!array_key_exists($field, $payload)) {
                    return $this->Messages->jsonErrorRequiredFieldsNotFilled();
                }
            }


            //Validate each property if correct
            foreach($payload as $key => $value) {
                if(!in_array($key, $this->accepted_parameters)) {
                    return $this->Messages->jsonErrorInvalidParameters();
                }
            }

            //initialize empty array
            $bankAccountPayload = [];

            //validate the property
            // foreach ($payload['bank_details'] as $key => $value) {
            //     if (in_array($key, $this->post_bank_account)) {
            //         $bankAccountPayload[$key] = $value;
            //         unset($payload[$key]);
            //     }
            // }

            $this->db->startTransaction();

            $bank_details = $payload['bank_details'];
            unset($payload['bank_details']);

            // query for inserting data into vendors table database
            $vendorData = $this->db->insert($this->table_vendors, $payload);

            //check if vendorData query is successful
            if ($vendorData) {

                //fetch the newly inserted id
                $vendorId = $this->db->getInsertId();

                $payload['id'] = $vendorId;

                foreach ($bank_details as &$bank) {
                    $bank['vendor_id'] = $vendorId;
                }

                // $bankAccountPayload['vendor_id'] = $vendorId;

                //quer for inserting data into bank account table
                $bankAccountData = $this->db->insertMulti($this->table_bank, $bank_details);
                // $bankAccountData = $this->db->insert($this->table_bank, $bankAccountPayload);

                //check if bankAccountData is successful
                if ($bankAccountData) {
                    $this->db->commit();
                    return $this->buildApiResponse($payload, $this->response_column, true);
                } else {
                    $this->Logger->logError($this->db->getLastError(), $bankAccountData);
                    $this->db->rollback();
                    return $this->Messages->jsonFailResponse("Bank Account Creation Failed");
                }
            } else {
                $this->Logger->logError($this->db->getLastError(), $vendorData);
                $this->db->rollback();
                return $this->Messages->jsonFailResponse("Vendor Account Creation Failed");
            }

    }

    /**
     * HTTP PUT handler
     *
     * @param null|int $id
     * @param $payload
     * @return false|string
     * @throws \Exception
     */
    public function httpPut($id, $payload)
    {
        // PUT THE BASIC VALIDATION BELOW

        //Basic validation
         if(empty($id)  || !array_key_exists('id', $payload)) {
            return $this->Messages->jsonErrorInvalidParameters();
        }

        if(empty($payload)){
            return $this->Messages->jsonErrorInvalidParameters();
        }
        
        if($id != $payload['id']){
            return $this->Messages->jsonFailResponse("ID does not match to Payload");
        }   

        try {
            // PUT THE BASIC VALIDATION (Required Fields) BELOW
            //Required fields
            $required_fields = [
                "id",
                "vendor_name",
                "contact_person",
                "country_code",
                "phone_number",
                "email",
                "address",
                "vendor_status",
                "payment_term",
                "delivery_term",
                "shipping_preparation_day",
                "nature_of_business",
                "business_tax_type",
                "courier",
                "document_expiration_date",
                "is_manufacturer",
                "is_distributor",
                "is_dealer",
                "is_reseller",
                "status",
            ];

            foreach($required_fields as $field) {
                if(!array_key_exists($field, $payload)) {
                    http_response_code(400);
                    return $this->Messages->jsonErrorRequiredFieldsNotFilled();
                }
            }

            // PROCESS IN DFD STARTS HERE!
            $this->db->startTransaction();

            /**
             * If $id and $payload['id'] is valid and equal, $id will be used in a WHERE clause of a SQL query that is 
             * being updated under variable $vendor_record.
            */    

            $this->db->where('id', $id);
            $vendor_record = $this->db->get($this->table_vendors);

            if(!$vendor_record){
                return $this->Messages->jsonFailResponse("No Data Found for ID: $id.");
            }

            $result = $this->db->update($this->table_vendors, $payload);
            if($result){
                $this->db->commit();
                return $this->buildApiResponse($payload,$this->response_column,true);
            }
            else{
                $this->Logger->logError($this->db->getLastErrno(), $payload);

                $this->db->rollback();
                return $this->buildApiResponse($payload,$this->response_column,false);
            }

        } catch (Exception $e) {
            // Log error
            $this->Logger->logError($e, $payload);

            return false;
        }
    }

    /**
     * HTTP DELETE handler
     *
     * @param $id
     * @param $payload
     * @return false|string
     * @throws \Exception
     */
    public function httpDel($id, $payload)
    {
        // Basic validation
        if(empty($id) && !array_key_exists('id', $payload)) {
            return $this->Messages->jsonErrorInvalidParameters();
        }

        //check if payload is empty
        if(empty($payload)){
            return $this->Messages->jsonFailResponse('Payload is required');
        }

        // Validate each property if correct
        foreach ($payload as $key => $value) {
            if(!in_array($key, $this->accepted_parameters)) {
                return $this->Messages->jsonErrorInvalidParameters();
            }
        }

        //check if payload id match the ID
        if ($payload['id'] != $id) {
            return $this->Messages->jsonDatabaseError("ID in payload does not match request ID");
        }

        //check if id is existing in the database
        $this->db->where('id', $id);
        $existingData = $this->db->get($this->table_vendors);

        if (!$existingData) {
            // Return error message if the record does not exist
            return $this->Messages->jsonDatabaseError("ID #" . $payload['id'] . " does not exist");
        }

        $this->db->startTransaction();
        //delete query
        $this->db->where('id', $id);
        $deleted = $this->db->delete($this->table_vendors);

        //check if query is successful or not
        if ($this->db->getLastErrno() > 0) {
            $this->Logger->logError($this->db->getLastError(), $deleted);
            $this->db->rollback();
            return $this->Messages->jsonDatabaseError("Failed to delete user ID #" . $payload['id'] . "!");

        } else {
            $this->db->commit();
            return $this->Messages->jsonSuccessResponse("User ID #" . $payload['id'] . " has been deleted");
        }
    }

    /**
     * HTTP FILE UPLOAD handler
     *
     * @param $id
     * @param $payload
     * @return false|string
     * @throws \Exception
     */
    public function httpFileUpload($identity = null, array $payload)
    {
        // TODO: Implement httpFileUpload() method.
    }
}