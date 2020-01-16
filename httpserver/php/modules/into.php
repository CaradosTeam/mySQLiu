<?php
require_once "globals.php";
new Session;
if(isset($_POST["request_login"]) && isset($_POST["request_pass"])) {
    //require_once "globals.php";

    $serverAddr = preg_replace('/\:.+/', "", $_SERVER['HTTP_HOST']); //$GLOBALS["serverAddr"];
    if(isset($_POST["serverSelectType"]) && isset($_POST["serverAddr"])) {
        $serverAddr = $_POST["serverAddr"];
    } else if(isset($_POST["noJSserverAddr"]) && $_POST["noJSserverAddr"]!="") {
        $serverAddr = $_POST["noJSserverAddr"];
    }

    var_dump($serverAddr);

    $loginCon = $GLOBALS["mysqliuMng"]->create("custom", "off", $serverAddr, $_POST["request_login"], $_POST["request_pass"]);
    echo var_dump($GLOBALS["mysqliuMng"]).'<hr>';

    if(!$loginCon->ping()) {
        $_SESSION["error_state_onlogin"] = [];
        $_SESSION["error_state_onlogin"]["num"] =  $loginCon->connect_errno;
        $_SESSION["error_state_onlogin"]["name"] = "Server not avaible and reachable"; //$loginCon->error;

        //echo $_SERVER['HTTP_HOST'];
        header("Location:../login.php");
    } else if($loginCon->connect_errno) {
        $_SESSION["error_state_onlogin"] = [];
        $_SESSION["error_state_onlogin"]["num"] =  $loginCon->connect_errno;
        $_SESSION["error_state_onlogin"]["name"] = "Incorrect user name or password"; //$loginCon->connect_error;
        header("Location:../login.php"); //".$_SERVER['HTTP_HOST']."/mysqliu/
    }

    $_SESSION["user"] = new mysqliuUser($_POST["request_login"], $_POST["request_pass"] , $serverAddr);
    $_SESSION["user"]->isLogged = true;
    if(empty($_SESSION["random"])) $_SESSION["random"] = rand(21, 52);
    var_dump($_SESSION["user"]);
    var_dump($GLOBALS["sessionsMenager"]);
    header("Location:../index.php");
} else header("Location:".$_SERVER['HTTP_REFERER']);
?>