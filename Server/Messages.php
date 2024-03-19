<?php


namespace Server;

/**
 * Class Messages
 * @package Core\System
 */
class Messages
{
    /**
     * ---------------------------------------
     * Fail Responses
     * ---------------------------------------
     */

    /**
     * @param string $props
     * @return false|string
     */
    public function jsonErrorInvalidParameters($props = "variable not set")
    {
        return json_encode([
            'status' => 'fail',
            'message' => "Invalid Payload Properties: $props"
        ]);
    }

    /**
     * @return false|string
     */
    public function jsonErrorRequiredFieldsNotFilled()
    {
        return json_encode([
            'status' => 'fail',
            'message' => 'Required fields was not filled.'
        ]);
    }


    /**
     * @param $URI
     * @return false|string
     */
    public function jsonErrorModuleNotExist($URI)
    {
        return json_encode([
            'status' => 'fail',
            'message' => 'Requested module not found.',
            'data' => [
                'uri' => $URI
            ]
        ]);
    }


    /**
     * @param $duplicate_value
     * @return false|string
     */
    public function jsonErrorDataAlreadyExists($duplicate_value)
    {
        return json_encode([
            'status' => 'fail',
            'message' => 'Data already exists in the database.',
            'data' => [
                'duplicate_value' => $duplicate_value
            ]
        ]);
    }


    /**
     * @param $message
     * @return false|string
     */
    public function jsonDatabaseError($message = 'Please try again.')
    {
        return json_encode([
            'status' => 'fail',
            'message' => "Database Error: $message"
        ]);
    }

    /**
     * @param $data
     * @return false|string
     */
    public function jsonFailResponse($data)
    {
        return json_encode([
            'status' => 'fail',
            'message' => $data,
            'method' => $_SERVER["REQUEST_METHOD"]
        ], JSON_NUMERIC_CHECK);
    }


    /**
     * ---------------------------------------
     * Success Responses
     * ---------------------------------------
     */


    /**
     * @param $data
     * @return false|string
     */
    public function jsonSuccessResponse($data)
    {
        return json_encode([
            'status' => 'success',
            'data' => $data,
            'method' => $_SERVER["REQUEST_METHOD"]
        ], JSON_NUMERIC_CHECK);
    }
}
