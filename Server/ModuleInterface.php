<?php


namespace Wrapper\Interfaces;

/**
 * Interface ModuleInterface
 * @package Wrapper\Interfaces
 */
interface ModuleInterface
{
    /**
     * HTTP GET handler
     *
     * @param array $payload Query payload
     * @param bool $api Flag for internal querying
     * @return false|string JSON-encoded response
     */
    public function httpGet(array $payload, bool $api = true);

    /**
     * HTTP POST handler
     *
     * @param array $payload Query payload
     * @return false|string JSON-encoded response
     */
    public function httpPost(array $payload);

    /**
     * HTTP PUT handler
     *
     * @param int $identity Unique identifier
     * @param array $payload Query payload
     * @return false|string JSON-encoded response
     */
    public function httpPut(int $identity, array $payload);

    /**
     * HTTP DELETE handler
     *
     * @param array|string $identity Unique identifier
     * @param array $payload Query payload
     * @return false|string JSON-encoded response
     */
    public function httpDel($identity, array $payload);

    /**
     * HTTP File Upload handler
     *
     * @param int $identity Unique identifier
     * @param array $payload Query payload
     * @return false|string JSON-encoded response
     */
    public function httpFileUpload(array $payload);
}
