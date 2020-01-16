<?php
require_once "session.php";
require_once "mysqliu.php";
new Session;
if(isset($_SESSION['user']->isLogged) && $_SESSION['user']->isLogged) {
    session_unset();
    session_destroy();
    session_regenerate_id(true);
    $_SESSION["error_state_onlogin"]["name"] = "Logged out successfully!";
}
header("Location:../login.php");
?>