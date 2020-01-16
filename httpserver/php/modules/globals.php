<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "config.php";
if((isset($require_session) && $require_session===true) || empty($require_session)) require_once "session.php";

define("MYSQLIU_DIR_ABSOLUTE", str_replace("modules".DIRECTORY_SEPARATOR."globals.php", "", __FILE__)); //$GLOBALS["serverAbsolutePath"] = 

define("MYSQLIU_MODULES_DIR", MYSQLIU_DIR_ABSOLUTE."modules".DIRECTORY_SEPARATOR); //ABSOLUTE, PATH

function autoloadModule($modulePath) {
    if(file_exists(MYSQLIU_MODULES_DIR.$modulePath.'.php')) {
        require_once MYSQLIU_MODULES_DIR.$modulePath.'.php';
        return true;
    } else throw new Exception("Error during loading Module Set $modulePath.php (Path: ".MYSQLIU_MODULES_DIR.$modulePath.".php), file not exist");
    throw new Exception("Error during loading Module Set $modulePath.php, unexpected error");
    return false;
} 

function autoloadModules($arrOfModules) {
    foreach($arrOfModules as $modulePath) autoloadModule($modulePath);
}

function __autoloader() { autoloadModules(["mysqliu", "engine"]); }
spl_autoload_register("__autoloader");



function transltionIPType($ip) {
    if(strpos($ip, ":")!==false) {
        hexdec(substr($ip, 0, 2)). "." . hexdec(substr($ip, 2, 2)). "." . hexdec(substr($ip, 5, 2)). "." . hexdec(substr($ip, 7, 2));
    } else return false;
}

#Print Error
function print_e($error, $tag="font", $attr="color", $attrVal="red") {
    if(is_array($error)) {
        $errAll = '<'.$tag.' '.$attr.'="'.$attrVal.'">';
        foreach($error as $errPart) {
            $errAll .= $errPart.PHP_EOL;
        }
        $errAll .= '</'.$tag.'>';
        echo $errAll;
    } else if(is_string($error)) {
        echo '<'.$tag.' '.$attr.'="'.$attrVal.'">'.$error.'</'.$tag.'>';
    } else return false;
}

$GLOBALS["serverAddr"] = gethostbyname(gethostname());
$GLOBALS["serverAddrIP"] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "";

$GLOBALS["mysqliuMng"] = new mysqliuMenager();

?>