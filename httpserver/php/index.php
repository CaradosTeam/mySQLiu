<?php
require_once "modules/globals.php";
new Session;
if(empty($_SESSION["user"])) header("Location:login.php");

$dataGetCon = $GLOBALS["mysqliuMng"]->create("custom", "off", $_SESSION["user"]->server, $_SESSION["user"]->name, $_SESSION["user"]->readPassword());

$currPage = "";
if(array_key_exists('page',$_GET)) {
$currPage = $_GET['page'];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Charset -->
<meta charset="utf-8">
<!-- Title -->
<title>Mysqliu Menager</title>
<!-- Styles -->
<link rel="stylesheet" type="text/css" href="static/fontawsome/fontawesome-all.css">
<link rel="stylesheet" type="text/css" href="themes/default_light.css" >
<!-- Libraries -->
<script type="text/javascript" src="scripts/libraries/events.js"></script>
<!-- Scripts -->
<script type="text/javascript" src="scripts/console.js"></script>
<script type="text/javascript" src="scripts/intern_index.js"></script>
</head>
<body>
<div id="base">
<nav>
<ul id="nav">
<li id="openMenunav"><i class="fas fa-bars"></i></li>
<li id="openConsole"><i class="fas fa-terminal"></i></li>
<li class="righty"><i class="fas fa-cog"></i></li>
</ul>
<ul id="menunav">
<a href="?page=summary"><li id="pagesummary"><i class="fas fa-hockey-puck"></i> Summary</li></a>
<a href="?page=databases"><li id="pagedatabases"><i class="fas fa-database"></i> Databases</li></a>
<a href="?page=users"><li id="pageusers"><i class="fas fa-users-cog"></i> Users</li></a>
<a href="?page=logs"><li id="pagelogs"><i class="fas fa-stethoscope"></i> Logs</li></a>
<a href="modules/logout.php"><li id="logout"><i class="fas fa-sign-out-alt"></i> Logout</li></a>
</ul>
</nav>
<script type="text/javascript">
/*Array.from(document.getElementById("menunav").childNodes).forEach(v=>{
    if(v.nodeType == 1) {
        if(v.id=="<?php echo "page".$currPage; ?>" && v.className.indexOf("selected")==-1) {
            v.classList.add("selected");
        } else if(v.className.indexOf("selected")!=-1) {
            v.classList.remove("selected");
        }
    }
});*/
document.getElementById("page<?php echo $currPage!="" ? $currPage : "summary"; ?>").classList.add("selected");
document.getElementsByTagName("script")[document.getElementsByTagName("script").length - 1].remove();
</script>
<div id="container">
<?php


//$test = var_dump($GLOBALS["mysqliuMng"]);

switch($currPage) {
    case "summary":
    default:
echo <<<_PAGE
<header>
<div id="header">
<h1>Summary</h1>
</div>    
</header>
<main>
<div id="main">
<div class="in">
<h2>Witaj {$_SESSION["user"]->name}!<hr>
<img src="images/mysqliu_logo.png">
</div>
</div>    
</main>
_PAGE;
    break;
    case "databases":

$_SESSION["databases"] = [];
$res = $dataGetCon->query("SHOW DATABASES");
while($row = $res->fetch_array()) {
    array_push($_SESSION["databases"], $row);
}

$tableOfResult = '<div class="scrollable centredContainer"><table><tr><th></th><th>Name</th><th>Name</th><th>Actions</th></tr>';
foreach($_SESSION["databases"] as $name=>$val) {
    $tableOfResult .= '<tr><td>'.$name.'</td><td>'.$val[0].'</td><td>'.$val["Database"].'</td><td>Drop | Edit | Export | Backup | Explore</td></tr>';
}
$tableOfResult .= '<tr><td id="addNewDB" colspan="4">Add new</td></tr></table></div>';

$treeView = '<ul class="tree"><li><span class="caret">localhost</span><ul class="nested">';
foreach($_SESSION["databases"] as $val) {
    $treeView .= '<li>'.$val[0].'</li>';
}
$treeView .= '</li></ul>';


echo <<<_PAGE
<div id="aside-stack">
<aside>
<div class="in">
<h1>Aside</h1>

</div>
</aside>
</div> 
<div id="inside">
    <header>
    <div id="header">
    <h1>Databases</h1>
    </div>    
    </header>
    <main>
    <div id="main">
    <div class="in">
    <h2>Bazy danych</h2><hr>
    {$tableOfResult}
    </div>
    </div>    
    </main>
</div>
_PAGE;
    break;
}
?>
<footer>
<div id="footer">
<?php echo '<span class="righty"><strong>Page loaded in:</strong> '.(sprintf("%.2f", (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])*1000)).'ps</span>'; ?>
</div>
</footer>
</div>
</div>
<div id="hiddenConsole"></div>
</body>
</html>