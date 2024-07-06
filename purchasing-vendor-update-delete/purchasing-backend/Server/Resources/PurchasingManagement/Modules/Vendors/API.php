<?php

namespace Resources\PurchasingManagement\Modules\Vendors;

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

    /** protected $Logger */

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
            'id',
            'vendor_name',
            'contact_person',
            'country_code',
            'phone_number',
            'email',
            'address',
            'vendor_status',
            'attach',
            'payment_term',
            'delivery_schedule',

            'vendor_id',
            'bank_details',
            'bank_account_name',
            'bank_account_number'
        ];

        $this->response_column = [
            'id',
            'vendor_name',
            'contact_person',
            'country_code',
            'phone_number',
            'email',
            'address',
            'vendor_status',
            'attach',
            'payment_term',
            'delivery_schedule',

            'bank_details',
            'vendor_id',
            'bank_account_name',
            'bank_account_number'
        ];

        $this->post_required_fields  = [
            'vendor_name',
            'contact_person',
            'country_code',
            'phone_number',
            'email',
            'address',
            'vendor_status',
            'attach',
            'payment_term',
            'delivery_schedule',
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
                'vendor_name',
                'contact_person',
                'phone_number',
                'email',
                'address',
                'vendor_status',
                // 'payment_term',
                'delivery_schedule',
                'bank_details'
                // 'bank_account_name',
                // 'bank_account_number'
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
        //Check if id exists and is not empty
        if(empty($id)  && !array_key_exists('id', $payload)) {
            return $this->Messages->jsonErrorInvalidParameters();
        }

        //Check if payload is not empty
        if(empty($payload)){
            return $this->Messages->jsonFailResponse("Payload is required for update");
        }

        //Check if id is a number and greater than 0
        if(gettype($id) === 'integer' || $id <= 0) {
            return $this->Messages->jsonFailResponse("Invalid ID Format");
        }

        //Check if value of id is not equal to payload[id]
        if($id != $payload['id'])
        {
            return $this->Messages->jsonFailResponse("ID does not match to Payload");
        }

        $existingRecord = $this->db->where('id', $id)->get($this->table_vendors);
        if(!$existingRecord){
            return $this->Messages->jsonFailResponse("Data not found for ID: $id.");
        }

        /** Validate each property if correct */
        foreach ($payload as $key => $value) {
            if(!in_array($key, $this->accepted_parameters)) {
                return $this->Messages->jsonErrorInvalidParameters();
            }
        }

        //Required fields
        $required_fields = [
            'vendor_name',
            'contact_person',
            'phone_number',
            'email',
            'address',
            'vendor_status',
            // 'payment_term',
            'delivery_schedule',
            // 'vendor_id',
            'bank_details'
        ];

        //For extracting bank data from payload
        $bank_extract_fields = [
            'vendor_id',
            'bank_account_name',
            'bank_account_number'
        ];

        /** Check if all fields required are filled */
        foreach($required_fields as $field) {
            if(!array_key_exists($field, $payload)) {
                http_response_code(400);
                return $this->Messages->jsonErrorRequiredFieldsNotFilled();
            }
        }

        //Initialize array for bank data
        $bank_details = $payload['bank_details'];
        unset($payload['bank_details']);

        //Extract bank account data from payload
        // foreach ($payload as $key => $value) {
        //     if (in_array($key, $bank_extract_fields)) {
        //         $data_bank[$key] = $value;
        //         unset($payload[$key]);
        //     }
        // }

        $this->db->startTransaction();

        //Execute query for vendor
        $this->db->where('id', $id);
        $vendor_query = $this->db->update($this->table_vendors, $payload);

        //Execute query for bank account
        $this->db->where('vendor_id', $id);
        // $bank_query = $this->db->update($this->table_bank, $data_bank);
        if(!$this->db->delete($this->table_bank)) {
            $this->Logger->logError($this->db->getLastErrno(), $payload);
            $this->db->rollback();
            return $this->buildApiResponse([$this->db->getLastError()], 'message', false);
        }
        foreach ($bank_details as &$bank) {
            $bank['vendor_id'] = $id;
        }
        $bank_query = $this->db->insertMulti($this->table_bank, $bank_details);

        //Check if queries are successful or not
        if($vendor_query && $bank_query && $this->db->getLastErrno() === 0) {
            $this->db->commit();
            return $this->buildApiResponse($payload, $this->response_column, true);
        } else {
            $this->Logger->logError($this->db->getLastErrno(), $payload);
            $this->db->rollback();
            return $this->buildApiResponse([$this->db->getLastError()], $this->response_column, false);
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