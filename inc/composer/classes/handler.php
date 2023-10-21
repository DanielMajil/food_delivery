<?php

define("DBHOST", isset($_ENV["DBHOST"]) ? $_ENV["DBHOST"] : "172.17.0.1");
define("DBUSER", isset($_ENV["DBUSER"]) ? $_ENV["DBUSER"] : "root");
define("DBPWD", isset($_ENV["DBPWD"]) ? $_ENV["DBPWD"] : "awtpassword");
define("DBNAME", isset($_ENV["DBNAME"]) ? $_ENV["DBNAME"] : "FOODAPP");

define("RLHOST", isset($_ENV["RLHOST"]) ? $_ENV["RLHOST"] : "10.0.2.15");
define("RLPORT", isset($_ENV["RLPORT"]) ? $_ENV["RLPORT"] : "6379");
define("RLPWD", isset($_ENV["RLPWD"]) ? $_ENV["RLPWD"] : "danielub2023");
define("RL_MAX", isset($_ENV["RL_MAX"]) ? $_ENV["RL_MAX"] : 3); //maximum requests
define("RL_SECS", isset($_ENV["RL_SECS"]) ? $_ENV["RL_SECS"] : 10); //maximum 

define("API_SECRET", isset($_ENV["API_SECRET"]) ? $_ENV["API_SECRET"] : "Daniel"); //maximum 

require_once "./inc/composer/vendor/autoload.php";

use Monolog\Level;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class DBHandler
{
    public $sqlDB = NULL;
    public $log = NULL;
    public $redisDB = NULL; //rate limit. It is redis also

    function __construct()
    {
        // INITIALIZE LOGGER
        $logFilename = date("Y-m-d") . "_activity.log";

        $this->log = new Logger("FOODAPP");
        $handler = new StreamHandler("log/$logFilename");
        $handler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n"));
        $this->log->pushHandler($handler);

        //CONNECTING TO MYSQL ----------------------------------
        $this->sqlDB = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
        if (mysqli_connect_errno()) {
            $this->log->error("Error connecting to mysql. Error:[" . mysqli_connect_error() . "]");
            $this->sqlDB = null;
        }
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


        //CONNECTING TO REDIS ----------------------------------
        try {
            $this->redisDB = new Redis();
            if (!$this->redisDB->connect(RLHOST, RLPORT)) {
                $this->log->error("Unable to connect to Redis");
                $this->redisDB = null;
            }
            if (!$this->redisDB->auth(RLPWD)) {
                $this->log->error("Redis auth failed");
                $this->redisDB = null;
            }

        } catch (RedisException $e) {
            $this->log->error("Redis Object Creation Failed.");
        }
    }

    function __destruct()
    {
        if ($this->sqlDB !== null) {
            $this->sqlDB->close();
        }
        if ($this->redisDB !== null) {
            $this->redisDB->close();
        }
    }
}
