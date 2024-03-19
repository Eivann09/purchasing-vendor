<?php

namespace Server;

class Handler
{
    public function __construct()
    {
    }

    public function handleRequest()
    {
        $this->setCSRF();

        $URI = $this->parseUrl();

        $function = $this->parseHttpRequest();

        $payload = $this->parseContentReceived();

        $result = $this->executeApiRequest($URI, $function, $payload);

        json_decode($result);

        if (json_last_error() === JSON_ERROR_NONE) {
            header('Content-type: application/json');
        }

        return $result;
    }

    public function setCSRF()
    {
        if (!isset($_SERVER["HTTP_ORIGIN"])) {
            $prefix = 'http://';

            $_SERVER['HTTP_ORIGIN'] = $prefix . $_SERVER["REMOTE_ADDR"];
        }

        header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Max-Age: 3600');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }
        
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
        
            exit(0);
        }
    }

    public function parseUrl()
    {
        $script_name = $_SERVER['SCRIPT_NAME'];
        $request_uri = $_SERVER['REQUEST_URI'];

        $exploded_script_name = explode("/", $script_name);
        end($exploded_script_name);

        $endpoint = prev($exploded_script_name);
        $exploded_request_uri = array_values(explode("/", $request_uri));

        $continue = false;
        $identity = $resources = $module = null;

        foreach ($exploded_request_uri as $uri) {
            if ($uri == $endpoint) {
                $continue = true;
            }
            if ($continue) {
                if ($uri == $endpoint) {
                    continue;
                }
                if ($resources != null) {
                    if (is_numeric($uri)) {
                        $identity = $uri;
                    } elseif ($module != null) {
                        $identity = $uri;
                    } else {
                        $module = strtok($uri, "?");
                    }
                } else {
                    $resources = $uri;
                }
            }
        }

        return ['resource' => $resources, 'module' => $module, 'identity' => $identity];
    }

    public function parseHttpRequest()
    {
        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'get':
                $request_method = 'httpGet';
                break;
            case 'post':
                if (strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
                    $request_method = 'httpFileUpload';
                } else {
                    $request_method = 'httpPost';
                }
                break;
            case 'put':
                $request_method = 'httpPut';
                break;
            case 'delete':
                $request_method = 'httpDel';
                break;
            default:
                exit(0);
                break;
        }

        return $request_method;
    }

    public function parseContentReceived()
    {
        $payload = array();

        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $payload);
        }

        $body = file_get_contents("php://input");

        $content_type = false;

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = strtolower($_SERVER['CONTENT_TYPE']);
        }

        switch ($content_type) {
            case "application/json;charset=utf-8":
            case "application/json":
                $json_decode = json_decode($body, true);
                return is_array($json_decode) ? $json_decode : array();
                break;
            case strtok($content_type, ';') == 'multipart/form-data':
                return ['payload' => $_POST, 'form_data' => $_FILES];
                break;
            default:
                exit(0);
                break;
        }
    }


    public function executeApiRequest($URI, $method, $payload)
    {
        $resource = ucfirst($URI['resource']);
        $module = ucfirst($URI['module']);

        $module_path = "Resources\\$resource\\Modules\\$module\\API";

        if (!class_exists($module_path)) {
            exit(0);
        }

        $URIClass = new $module_path;

        if ($method === 'httpPost' || $method === 'httpGet' || $method === 'httpFileUpload') {
            if ($method === 'httpGet') {
                $module_payload = $_GET;
            } else {
                $module_payload = $payload;
            }

            return $URIClass->$method($module_payload);
        }

        if (empty($URI['identity'])) {
            if ($URI['resource'] === 'core' && $URI['module'] === 'authenticate' && $method === 'httpDel') {
                return $URIClass->$method(null, []);
            }

            exit(0);
        }

        return $URIClass->$method($URI['identity'], $payload);
    }
}
