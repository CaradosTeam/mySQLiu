<?php
var_dump($_SERVER["DOCUMENT_ROOT"].'/mysqliu/execute/users.sh');
var_dump(shell_exec('whoami'));
var_dump(shell_exec('sudo -S '.$_SERVER["DOCUMENT_ROOT"].'/mysqliu/execute/users.sh 2>&1 >> '.$_SERVER["DOCUMENT_ROOT"].'/mysqliu/logs/errors.log &'));
?>
