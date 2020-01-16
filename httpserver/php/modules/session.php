<?php
$GLOBALS["sessionsMenager"] = new class {
    public $sessions = [];

    function __construct() {

    }
};

class Session {
    protected $name = "";
    protected $path = "";

    //Session Data
    private $lastSessionData = [];

    function __construct($time=3600, $path="/",$name="mysqliuServer") {
        $currentCookieParams = session_get_cookie_params();
        //$currentCookieParams["lifetime"]
        session_set_cookie_params(
            $time,
            $path,
            "localhost", //:3308
            $currentCookieParams["secure"],
            $currentCookieParams["httponly"]
        );
        $path = $currentCookieParams["path"];
        //session_name('mysqliuServer');
        session_name($name); $this->name = $name;
        session_start();
        if(isset($_COOKIE['mysqliuServer'])) setcookie($name, $_COOKIE['mysqliuServer'], time() + $time, $path);
        if(isset($GLOBALS["sessionsMenager"])) array_push($GLOBALS["sessionsMenager"]->sessions, $this);
    }

    function getSessionData() {
        $lastSessionData[date("d-m-Y-H-i-s")] = $_SESSION;
        return $_SESSION;
    }
}
?>