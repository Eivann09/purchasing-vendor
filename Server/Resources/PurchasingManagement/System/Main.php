<?php
namespace Resources\PurchasingManagement\System;

use Resources\PurchasingManagement\System\Config;
use MysqliDb;
use Server\Messages;

/**
 * Class Main
 *
 * Bootstrapper class for PolanguiTreasury resource
 *
 * @package Resources\PolanguiTreasury
 */
class Main
{
    /**
     * MySQL DB class
     *
     * @var MysqliDb
     */
    protected MysqliDb $project_management_db;

    protected $db;

    protected $Config;

    protected $Messages;

    /**
     * Main constructor.
     * @param bool $initialize_db
     */
    public function __construct($initialize_db = false)
    {
        $this->Config = new Config();

        // Create Instance of Query Builder
        $this->db = new MysqliDb($this->Config->dbConnection());

        //Create class for message
        $this->Messages = new Messages();

        // parent::__construct($initialize_db);
    }

    /**
     * Build GlobalHelpers response
     *
     * @param array $data Array of data to build
     * @param array $response_column Array of allowed columns in response
     * @param bool $status Flag for sending success or fail response
     * @return false|string JSON-encoded response or array of values
     */
    public function buildApiResponse(array $data = array(), array $response_column = array(), bool $status = true)
    {
        // Check if data is a multi-dimensional array
        if (count($data) === count($data, COUNT_RECURSIVE)) {
            // Not multi-dimensional
            $filtered = array_intersect_key($data, array_flip($response_column));
        } else {
            // Check if array has a normal multi-dimensional indexing
            if (array_key_exists(0, $data)) {
                $filtered = array_map(function ($arr) use ($response_column) {
                    return array_intersect_key($arr, array_flip($response_column));
                }, $data);
            } else {
                // AKO BUDOY!
                $filtered = array_intersect_key($data, array_flip($response_column));
            }
        }

        return $status
            ? $this->Messages->jsonSuccessResponse($filtered)
            : $this->Messages->jsonFailResponse($data);
    }

    /**
     * Trim payload
     *
     * @param $payload
     * @return array|string
     */
    public function trimPayload($payload)
    {
        return is_array($payload) ? array_map(array($this, 'trimPayload'), $payload) : trim($payload);
    }
}
