<?php

//validation docs at https://respect-validation.readthedocs.io/en/latest/
use Respect\Validation\Validator as v; //use this wherever the library is used.

class User extends DBHandler
{

    private $permissions;

    function __construct($permissions)
    {
        parent::__construct();
        $this->permissions = $permissions;
    }

    function __destruct()
    {
    }

    public function test()
    {
        $this->log->info("test fx ran...");
        $response["rc"] = 100;
        $response["message"] = "Success";
        return $response;
    }
    //done
    public function getAllUsers()
    {
        $this->log->info("checking ". __METHOD__);
        $response["rc"] = -15;
        $response["message"] = "Users' Details Not Found";

        try {
            $query = "SELECT * FROM users;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting users";
                $this->log->error("query execution error for getting all users");
                http_response_code(403);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading user records";
                $this->log->debug("error: no results received");
                http_response_code(403);
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 50;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Got all users.");

        http_response_code(200);
        return $response;
    }
    //done
    public function getUsersDetails($requestID)
    {
        $this->log->info("checking ". __METHOD__);
        $response["rc"] = -16;
        $response["message"] = "User Details Not Found for id $requestID";

        if (!v::numericVal()->positive()->validate($requestID)) {
            $response["rc"] = "yet to count";
            $response["message"] = "Invalid ID. Expected INT value.";
            http_response_code(403);
            return $response;
        }

        try {
            $query = "SELECT * FROM users WHERE user_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $requestID);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting user with id $requestID";
                $this->log->error("query execution error for getting user with id");
                http_response_code(403);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading user record of provided ID: $requestID";
                $this->log->debug("error: no results received for id $requestID");
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 52;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Got user with provided id.");
        http_response_code(200);
        return $response;
    }
    //done
    public function getAllEmployees()
    {
        $response["rc"] = -17;
        $response["message"] = "Employee Details Not Found";

        try {
            $query = "SELECT * FROM users WHERE role_id = 1;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting employees";
                $this->log->error("query execution error for getting all employees");
                http_response_code(500);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading record of employee";
                $this->log->debug("Error. No results received");
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 54;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Retrieved all employees");
        http_response_code(200);
        return $response;
    }
    //done
    public function getAllCustomers()
    {
        $response["rc"] = -18;
        $response["message"] = "Customer Details Not Found";

        try {
            $query = "SELECT * FROM users WHERE role_id = 2;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting customers";
                $this->log->error("query execution error for getting all customers");
                http_response_code(500);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading record of customer";
                $this->log->debug("error: no results retreived");
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 55;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Retrieved all customers");
        http_response_code(200);
        return $response;
    }

    public function checkValidMethod($parentResource, $subResource, $method)
    {
        $result = false;
        if (is_numeric($subResource) || $subResource == "") {
            $subResource = "/";
        }

        $methodsArray = $this->permissions["1"][$parentResource][$subResource];

        for ($i = 0; $i < sizeof($methodsArray); $i++) {
            if ($methodsArray[$i] == $method) {
                $this->log->debug("granted access for request using method: $method");
                $result = true;
                return $result;
            }
        }
        http_response_code(403);
        $this->log->debug("No permissions to access parent: $parentResource, subresource: $subResource, method: $method");

        return $result;
    }
    //done
    public function GET($requestParameters)
    {
        $response["rc"] = -11;
        $response["message"] = "Invalid Request";

        $this->log->info("processing GET fx request");

        $subResource = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $parentResource = $requestParameters[1];

        switch ($subResource) {
            case 'employees':
                $this->log->info("subrequest received --" . $subResource . '--');
                $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
                if ($validRequest) {
                    $response = $this->getAllEmployees();
                } else {
                    $response["rc"] = -12;
                    $this->log->error("no access to $subResource using method --GET--");
                }
                break;
            case 'customers':
                $this->log->info("subrequest received --" . $subResource . '--');
                $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
                if ($validRequest) {
                    $response = $this->getAllCustomers();
                } else {
                    $response["rc"] = -13;
                    $this->log->error("no access to $subResource using method --GET--");
                }
                break;
            default:
                $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
                if ($validRequest) {
                    $response = $subResource < 1 ? $this->getAllUsers() : $this->getUsersDetails($subResource);
                } else {
                    $response["rc"] = -14;
                    $this->log->error("no access to $subResource using method --GET--");
                }
                //If no ID was provided then return all users: NO MATTER IF THE OTHER PARAMETERS ARE spelt incorrectly
                break;
        }

        return $response;
    }

    //-----------------------------------------------

    public function generateRandomString($length = 24)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#%*()+=[]{};:,.<>?';
        $randomString = '';
        $characterCount = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $characterCount - 1)];
        }

        $this->log->info("random string generate for new user key");

        return $randomString;
    }

    public function addUserApiKey($newlyInsertedUserID, $role_id)
    {
        $randomString = $this->generateRandomString();
        $key = "awt_" . $randomString . "_" . crc32($randomString . API_SECRET);
        $this->log->info("assembled new user key");

        try {
            $query = "INSERT INTO `user_keys` (`key_id`, `user_id`, `key`, `expired`, `created_at`, `status`)
            VALUES
            (NULL, ?, ?, 0, current_timestamp(), 1);";

            $stmt = $this->sqlDB->prepare($query);
            $stmt->bind_param("is", $newlyInsertedUserID, $key);
            $stmt->execute();
            $newlyInsertedKeyID = "";
            if ($this->sqlDB != null) {
                $newlyInsertedKeyID = $this->sqlDB->insert_id;
                $this->log->info("new user key inserted");
            }

            if ($role_id == 1) { //is an employee so give them all permissions

                $query = "INSERT INTO `key_permissions` (`id`, `key_id`, `permission_id`, `method_id`, `created_at`, `status`)
                VALUES
                -- user with role 1 can perform GET requests for all collections
                (NULL, $newlyInsertedKeyID, 1, 1, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 2, 1, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 3, 1, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 4, 1, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 1, 2, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 1, 3, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 1, 4, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 4, 2, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 4, 3, current_timestamp(), 1),
                (NULL, $newlyInsertedKeyID, 4, 4, current_timestamp(), 1);";
                $stmt = $this->sqlDB->prepare($query);
                $stmt->execute();
                $this->log->info("permission added to key for user with role 1(employee)");
            } else if ($role_id == 2) { //is a customer so give them limited permissions
                $query = "INSERT INTO key_permissions (id, key_id, permission_id, method_id, created_at, status) 
                VALUES (NULL, $newlyInsertedKeyID, 4, 1, current_timestamp(), 1);";
                $stmt = $this->sqlDB->prepare($query);
                $stmt->execute();
                $this->log->info("permission added to key for user with role 2(customer)");
            }
            $stmt->close();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $this->log->error("Error adding api key for new user");
        }
    }
    //done
    public function POST($requestParameters, $postData)
    {

        $response["rc"] = -23;
        $response["message"] = "Invalid Request";

        $this->log->info("processing POST fx request");

        $parentResource = $requestParameters[1];
        $subResource = $requestParameters[2];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
        if (!$validRequest) {
            $response["rc"] = -24;
            $this->log->error("no access to resource $parentResource using method --POST--");
            return $response;
        }

        $role_id = array_key_exists('role_id', $postData) ? $postData["role_id"] : 2; //make new user a customer by default
        $firstname = array_key_exists('firstname', $postData) ? $postData["firstname"] : "";
        $lastname = array_key_exists('lastname', $postData) ? $postData["lastname"] : "";
        $username = array_key_exists('username', $postData) ? $postData["username"] : "";
        $email = array_key_exists('email', $postData) ? $postData["email"] : "";
        $address = array_key_exists('address', $postData) ? $postData["address"] : NULL;
        $phone = array_key_exists('phone', $postData) ? $postData["phone"] : NULL;
        $age = array_key_exists('age', $postData) ? $postData["age"] : NULL;
        $password = array_key_exists('password', $postData) ? $postData["password"] : "password";
        $member = array_key_exists('member', $postData) ? $postData["member"] : 0; //not a member by default
        $status = 1;

        try {
            $query = "INSERT INTO users (`role_id`, `firstname`, `lastname`, `username`, `email`, `address`, `phone`, `age`, `password`, `member`, `status`, `created_at`)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW());";

            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("issssssisii", $role_id, $firstname, $lastname, $username, $email, $address, $phone, $age, $password, $member, $status,);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error creating new user";
                $this->log->error("query execution error for inserting a user.");
                http_response_code(500);
                return $response;
            }

            $newlyInsertedID = "";
            if ($this->sqlDB != null) {
                $newlyInsertedID = $this->sqlDB->insert_id;
                $response["rc"] = 56;
                $response["message"] = "Successful user creation";
                $response["new_user_id"] = $newlyInsertedID;
            }
            $stmt->close();
            //generate and add the user's api key
            $this->addUserApiKey($newlyInsertedID, $role_id);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }


        http_response_code(200);
        return $response;
    }

    //-----------------------------------------------

    public function getUpdatedDetails($requestID, $response)
    {
        $this->log->debug("getting updated user details...");
        try {
            $query = "SELECT * FROM users WHERE user_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $requestID);
            } else {
                $response["error_getting_updated_user"] = "Sorry...lost database connection";
                $this->log->error("lost database connection. Could not get udpated user details");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["error"] = "";
                $response["error_getting_updated_user"] = "STMT Error. Couldn't get updated user's new details";
                $this->log->error("query execution error for getting update user details");
                http_response_code(500);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["error_getting_updated_user"] = "Sorry. Something went wrong...Updated details not Found for user with id $requestID";
                $this->log->error("could not find udpated user details using ID");
                http_response_code(500);
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["newdata"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 51;
        $response["message"] = "Success";
        $this->log->info("Retrieved updated details");
        http_response_code(200);
        return $response;
    }
    //done
    public function PUT($requestParameters, $putData)
    {
        $response["rc"] = -27;
        $response["message"] = "Invalid Request";

        $request = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $this->log->info("processing PUT fx request for id $request");

        $subResource = $request; //sorry for repeating it, but time
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
        if (!$validRequest) {
            $response["rc"] = -28;
            $this->log->error("no access to resource $parentResource using method --PUT--");
            http_response_code(403);
            return $response;
        }

        //checks if user does not exist
        $response = $this->getUsersDetails($request);
        if ($response["message"] != "Success") {
            $this->log->error("Could not find user with the id provided");
            return $response;
        }

        try {

            $allowedKeys = ["role_id", "firstname", "lastname", "username", "email", "address", "phone", "age", "member", "status"];
            $query = "UPDATE users SET ";
            $bindParams = [];

            foreach ($allowedKeys as $key) {
                // $this->log->debug('');
                if (isset($putData[$key])) {
                    $query .= "$key = ?, ";
                    $bindParams[] = $putData[$key];
                }
            }

            // Remove the trailing comma and space
            $query = rtrim($query, ", ");

            // Add a WHERE clause to specify the record to update
            $query .= " WHERE user_id = ?"; // Assuming you're updating based on an 'id' value
            $bindParams[] = $request; // Set the user ID here    

            // $this->log->info($query);

            // Prepare and bind the SQL statement
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                return $response;
            }

            // Determine the data types for binding parameters (assuming they are all strings in this example)
            $types = str_repeat('s', count($bindParams));

            // Bind the parameters
            $stmt->bind_param($types, ...$bindParams);

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error updating user";
                $this->log->error("query execution error for updating a user");
                http_response_code(403);
            } else {
                $this->log->info("Successful request execution. User updated");
                $response = $this->getUpdatedDetails($request, $response);
                http_response_code(200);
            }
            $stmt->close();
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 57;
        $response["message"] = "Success. User Updated";
        return $response;
    }

    //-----------------------------------------------

    public function DELETE($requestParameters)
    {
        $response["rc"] = -31;
        $response["message"] = "Invalid Request";

        $request = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $this->log->info("processing DELETE fx request");

        $subResource = $request; //sorry again for repeating it
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
        if (!$validRequest) {
            $response["rc"] = -32;
            $this->log->error("no access to resource $parentResource using method --DELETE--");
            return $response;
        }

        //checks if user does not exist
        $response = $this->getUsersDetails($request);
        if ($response["message"] != "Success") {
            $this->log->info("User with provided ID does not exist to delete");
            return $response;
        }

        try {
            $query = "DELETE FROM users WHERE user_id = ?";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $request);

                if ($stmt->execute()) {
                    $stmt->close();
                    $response["rc"] = 59;
                    $response["message"] = "Success. User deleted with id $request";
                    $this->log->info("user deleted. request complete");
                    http_response_code(200);
                } else {
                    $stmt->close();
                    $response["rc"] = -5;
                    $response["message"] = "Error deleting user with id $request";
                    return $response;
                    $this->log->error("query execution error for deleting a user with given ID");
                    http_response_code(500);
                }
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        return $response;
    }
}
