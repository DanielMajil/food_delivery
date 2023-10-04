<?php
class Request {
    public static function getRequestBody() {
        return json_decode(file_get_contents("php://input"), true);
    }
}
?>
