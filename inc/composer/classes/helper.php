<?php

use Respect\Validation\Validator as v;

class FoodApp extends DBHandler
{
    private $permissions;

    function __construct($permissions)
    {
        parent::__construct();
        $this->permissions = $permissions;
    }

    public function getAllOrders()
    {
        $response["rc"] = -21;
        $response["message"] = "Orders Not Found";

        try {
            $query = "SELECT * FROM orders;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting orders";
                $this->log->error("Query execution error for getting all orders");
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $response["rc"] = -6;
                $response["message"] = "Error reading order records";
                $this->log->debug("Error: no results retrieved");
                $stmt = null;
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 51;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Got all orders.");

        http_response_code(200);
        return $response;
    }

    public function getOrderDetails($orderId)
    {
        $response["rc"] = -22;
        $response["message"] = "Order Details Not Found for ID $orderId";

        if (!v::numericVal()->positive()->validate($orderId)) {
            $response["rc"] = "yet to count";
            $response["message"] = "Invalid ID. Expected INT value.";
            return $response;
        }

        try {
            $query = "SELECT * FROM orders WHERE order_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $orderId);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting order with ID $orderId";
                $this->log->error("Query execution error for getting order with ID");
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading order record of provided ID";
                $this->log->debug("Error: no results received");
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 53;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Got order with provided ID.");
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
                $this->log->debug("Granted access for request using method: $method");
                $result = true;
                return $result;
            }
        }

        $this->log->debug("No permissions to access parent: $parentResource, subresource: $subResource, method: $method");

        return $result;
    }

    public function GET($requestParameters)
    {
        $response["rc"] = -19;
        $response["message"] = "Invalid Request";

        $this->log->info("Processing GET request");

        $subResource = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION);
        if ($validRequest) {
            $response = $subResource < 1 ? $this->getAllOrders() : $this->getOrderDetails($subResource);
        } else {
            $response["rc"] = -20;
            $response["message"] = "No permission to access the resource using method GET";
            $this->log->error("No access to $subResource using method GET");
        }

        return $response;
    }

    public function POST($requestParameters, $postData)
    {
        $response["rc"] = 0;
        $response["message"] = "Update coming soon";
        return $response;
    }

    public function getUpdatedDetails($orderId, $response)
    {
        try {
            $query = "SELECT * FROM orders WHERE order_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $orderId);
            } else {
                $response["error_getting_updated_order"] = "Sorry...lost database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["error_getting_updated_order"] = "STMT Error. Couldn't get updated order's new details";
                $this->log->error("Query execution error for getting updated order details");
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["error_getting_updated_order"] = "Sorry. Something went wrong...Updated details not found for order with ID $orderId";
                $this->log->error("Could not find updated order details using ID");
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

    public function PUT($requestParameters, $putData)
    {
        $response["rc"] = -29;
        $response["message"] = "Invalid Request";

        $request = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $this->log->info("Processing PUT request for ID $request");

        $subResource = $request;
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION);
        if (!$validRequest) {
            $response["rc"] = -30;
            $this->log->error("No access to resource $parentResource using method PUT");
            return $response;
        }

        $response = $this->getOrderDetails($request);
        if ($response["message"] != "Success") {
            $this->log->error("Could not find order with the ID provided");
            return $response;
        }

        try {
            $allowedKeys = ["order_id", "customer_id", "food_item", "quantity", "total_price", "status"];
            $query = "UPDATE orders SET ";
            $bindParams = [];

            foreach ($allowedKeys as $key) {
                if (isset($putData[$key])) {
                    $query .= "$key = ?, ";
                    $bindParams[] = $putData[$key];
                }
            }

            $query = rtrim($query, ", ");
            $query .= " WHERE order_id = ?";
            $bindParams[] = $request;

            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            $types = str_repeat('s', count($bindParams));
            $stmt->bind_param($types, ...$bindParams);

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error updating order";
                $this->log->error("Query execution error for updating an order");
                http_response_code(500);
            } else {
                $this->log->info("Successful request execution. Order updated");
                $response = $this->getUpdatedDetails($request, $response);
            }
            $stmt->close();
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 58;
        $response["message"] = "Success. Order Updated";
        return $response;
    }

    public function DELETE($requestParameters)
    {
        $response["rc"] = -33;
        $response["message"] = "Invalid Request";

        $request = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $this->log->info("Processing DELETE request");

        $subResource = $request;
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, );
        if (!$validRequest) {
            $response["rc"] = -34;
            $this->log->error("No access to resource $parentResource using method DELETE");
            return $response;
        }

        $response = $this->getOrderDetails($request);
        if ($response["message"] != "Success") {
            $this->log->info("Order with provided ID does not exist to delete");
            return $response;
        }

        try {
            $query = "DELETE FROM orders WHERE order_id = ?";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $request);

                if ($stmt->execute()) {
                    $stmt->close();
                    $response["rc"] = 60;
                    $response["message"] = "Success. Order deleted with ID $request";
                    $this->log->info("Order deleted. Request complete");
                    http_response_code(200);
                } else {
                    $stmt->close();
                    $response["rc"] = -1;
                    $response["message"] = "Error deleting order with ID $request";
                    $this->log->error("Query execution error for deleting an order with given ID");
                    http_response_code(500);
                    return $response;
                }
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        return $response;
    }
}
