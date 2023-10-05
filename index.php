<?php 


// if (!isset($_SERVER["CONTENT_TYPE"]) || $_SERVER["CONTENT_TYPE"] != "application/json") {
//     http_response_code(403);
//     die("Thou shall not pass!!! Forbidden");
// }

// header("Content-Type: application/json; charset=UTF-8");
// include_once "dbtest.php";
// Include necessary files and initialize the application

// Handle routing to the appropriate handler functions based on the URL and HTTP method
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id'])) {
    include 'handler.php';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'], $_POST['items'], $_POST['payment_info'])) {
    include 'handler.php';
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    include 'handler.php';
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['order_id'])) {
    include 'handler.php';
} else {
    // Handle other routes or display a 404 error
}
?>


$clientRequest = $_SERVER["REQUEST_URI"];
$clientRequestArray = explode("/", $clientRequest);
// echo $clientRequest;
echo "<pre>";
print_r($clientRequestArray);
echo "</pre>";

require_once "inc/composer/vendor/autoload.php";
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logFilename = date("Y-m-d") . "_activity_log";
$log = new Logger('AWT');
$log->pushHandler(new StreamHandler("log/$logFilename", Level::Info));

$resource = isset($clientRequestArray[2]) ? $clientRequestArray[2] : -1;
switch ($resource) {
    case 'student':
        // $log->info("request received.");
        echo "request received.";
        break;
    default:
        // $log->info("unknown resource. Error $resource");
        echo "unknown resource. Error $resource";
        break;
}



//add records to the log
$log->info("request received...");

$response["rc"] = 1;
$response["message"] = "Success";

echo json_encode($response);



?>