<?php
if(empty($_POST["action"])) exit;
require_once "../modules/dbconfig.php";
require_once "../modules/mysqliu.php";
switch($_POST["action"]) {
    case "changeDB":
    
    break;
    case "fetchQuery":
    $conn = new mysqliu;
    if($conn->query()) {

    }
    break;
    case "fetchMultipleQuery":
    $conn = new mysqliu;
    if($conn->multi_query($querychain)) {
        do {
            if($res = $conn->store_result()) {
                if($res->num_rows>0) {
                while($row = $res->fetch_row()) {

                }
                }
                $res->free();
            }
            

            if($conn->more_results()) {
                printf("-----------------\n");
            }
        } while($conn->next_result());
    }
    break;
}
var_dump($_POST);
?>