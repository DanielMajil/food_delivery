<?php

use Respect\Validation\Rules\Exists;

require_once "handler.php";
require_once "users.php";
require_once "helper.php";

class Request extends DBHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    public function rateLimitCheck($server_data)
    {
        $total_user_calls = 0;
        $max_calls_limit = RL_MAX;
        $time_period = RL_SECS;

        $client_key = isset($server_data["HTTP_API_KEY"]) ? $server_data["HTTP_API_KEY"] : NULL;

        if (!$client_key) {
            $this->log->error("API key is missing in the request.");
            return -1;
        }

        try {
            if (!$this->redisDB->exists($client_key)) {
                $this->redisDB->set($client_key, 1);
                $this->redisDB->expire($client_key, $time_period);
                $total_user_calls = 1;
            } else {
                $this->redisDB->INCR($client_key);
                $total_user_calls = $this->redisDB->get($client_key);
                if ($total_user_calls > $max_calls_limit) {
                    $this->log->info("API key usage limit exceeded.");
                    return -2;
                }
            }
        } catch (RedisException $e) {
            $this->log->error("Error with rate limiting.");
        }

        header("X-Rate-Limit-Limit: " . $max_calls_limit);
        header("X-Rate-Limit-Remaining: " . ($max_calls_limit - $total_user_calls));
        header("X-Rate-Limit-Used: " . $total_user_calls);
        header("X-Rate-Limit-Reset: " . (time() + $this->redisDB->ttl($client_key)));

        return 1;
    }

    public function checkApiKey($server_data)
    {
        $response = [
            "key_id" => -1,
            "permissions" => []
        ];

        $key = isset($server_data["HTTP_API_KEY"]) ? $server_data["HTTP_API_KEY"] : NULL;
        if (!$key) {
            $this->log->error("API key is missing in the request.");
            return $response;
        }

        $key_parts = explode("_", $key);
        if (count($key_parts) != 3) {
            $this->log->error("Invalid API key format.");
            return $response;
        }

        $checksum = crc32($key_parts[1] . API_SECRET);
        if ($checksum != $key_parts[2]) {
            $this->log->error("Invalid API key checksum.");
            return $response;
        }

        $info = $this->apiKeyInfo($key);
        if ($info["key_id"] == -1) {
            $this->log->error("API key [$key] not found in the database.");
            return $response;
        }

        if (empty($info["permissions"])) {
            $this->log->error("API key has no permissions.");
            return $response;
        }

        return $info;
    }

    public function apiKeyInfo($key)
    {
        $response = [
            "key_id" => -1,
            "permissions" => null
        ];

        try {
            $query = "SELECT uk.key_id, COALESCE(pr.parent, -1) AS parent, pr.resource, GROUP_CONCAT(m.method) AS methods
                      FROM user_keys AS uk
                      INNER JOIN users AS u ON u.user_id = uk.user_id
                      INNER JOIN key_permissions AS kp ON uk.key_id = kp.key_id AND kp.status = 1
                      INNER JOIN permissions AS pr ON kp.permission_id = pr.permission_id AND pr.status = 1
                      INNER JOIN methods AS m ON kp.method_id = m.method_id
                      WHERE uk.key = ? AND uk.status = 1 AND u.status = 1
                      GROUP BY uk.key_id, pr.parent, pr.resource
                      ORDER BY uk.key_id, pr.parent, pr.resource;";

            $stmt = $this->sqlDB->prepare($query);
            $stmt->bind_param("s", $key);

            if (!$stmt->execute()) {
                $this->log->error("SQL error when checking for key permissions.");
                throw new Exception("Error Processing Request", 1);
            }

            $result = $stmt->get_result();

            if ($result->num_rows < 1) {
                $this->log->error("No permissions found for the provided API key.");
                throw new Exception("Information not found for API key\n", 1);
            }

            $associativeArray = array();
            $currentKeyID = null;

            foreach ($result as $row) {
                $response["key_id"] = $row['key_id'];
                $keyID = 1;
                $parent = $row['parent'];
                $resource = $row['resource'];
                $methods = explode(',', $row['methods']);

                if ($keyID !== $currentKeyID) {
                    $associativeArray[$keyID] = [
                        "key_id" => $row['key_id']
                    ];
                    $currentKeyID = $keyID;
                }

                if (!isset($associativeArray[$keyID][$parent])) {
                    $associativeArray[$keyID][$parent] = [];
                }

                if (!isset($associativeArray[$keyID][$parent][$resource])) {
                    $associativeArray[$keyID][$parent][$resource] = [];
                }

                $associativeArray[$keyID][$parent][$resource] = $methods;
            }

            $response["permissions"] = $associativeArray;

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        return $response;
    }

    private function generateAssocArray($postData)
    {
        $array = array();
        foreach ($postData as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    private function parsePutFormData($putData)
    {
        $formData = [];
        list($boundary, $data) = explode("\r\n", $putData, 2);
        $parts = explode($boundary, $data);

        foreach ($parts as $part) {
            if (empty($part)) continue;
            if (preg_match('/Content-Disposition: form-data; name="([^"]*)"\s*\r\n\r\n(.*)\r\n/', $part, $matches)) {
                $name = $matches[1];
                $value = $matches[2];
                $formData[$name] = $value;
            }
        }

        return $formData;
    }

    public function process($data, $permissions)
    {
        $response = [
            "rc" => -1,
            "message" => "Invalid Request"
        ];
        $errorFound = 0;

        $clientRequest = $data["REQUEST_URI"];
        $clientRequestArray = explode("/", ltrim($clientRequest, "/"));
        $requestMethod = isset($data["REQUEST_METHOD"]) ? $data["REQUEST_METHOD"] : "GET";
        $parentResource = isset($clientRequestArray[1]) ? $clientRequestArray[1] : -1;

        $this->log->info("Request received: " . $requestMethod);

        $service = null;

        if (!isset($permissions["1"][$parentResource])) {
            $this->log->debug("No access for parent resource: $parentResource");
            $response["rc"] = -2;
            $response["message"] = "No permissions to access parent resource: $parentResource";
            $errorFound = 1;
        } else {
            $this->log->debug("Granted access for parent resource: $parentResource");
        }

        if ($errorFound == 0) {
            switch ($parentResource) {
                case 'users':
                    $service = new User($permissions);
                    break;
                case 'orders':
                    break;
                case 'feedback':
                    break;
                case 'FOODAPP':
                    $service = new foodorder($permissions);
                    break;
                default:
                    $this->log->info("Unknown resource requested...");
                    break;
            }

            if ($service) {
                switch ($requestMethod) {
                    case 'GET':
                        $response = $service->GET($clientRequestArray);
                        break;
                    case 'POST':
                        $postData = $this->generateAssocArray($_POST);
                        $response = $service->POST($clientRequestArray, $postData);
                        break;
                    case 'PUT':
                        $rawPutData = file_get_contents("php://input");
                        $parsedData = $this->parsePutFormData($rawPutData);
                        $response = $service->PUT($clientRequestArray, $parsedData);
                        break;
                    case 'DELETE':
                        $response = $service->DELETE($clientRequestArray);
                        break;
                    default:
                        $response["rc"] = -3;
                        $response["message"] = "Unsupported Request Method";
                        $this->log->info("Unsupported request method used...");
                        break;
                }
            }
        }

        echo json_encode($response);
    }
}
