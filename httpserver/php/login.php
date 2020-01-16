<?php
include_once "modules/globals.php";
new Session;
if(isset($_SESSION["user"]->isLogged) && $_SESSION["user"]->isLogged) header("Location:index.php");
?>
<!doctype html>
<html lang="en">
<head>
<!-- Charset -->
<meta charset="utf-8">
<!-- Title -->
<title>Mysqliu Menager</title>
<!-- Styles -->
<link rel="stylesheet" type="text/css" href="themes/login_light.css" >
<!-- Favicon -->
<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#00a300">
<meta name="theme-color" content="#ffffff">
<!-- Scripts -->
<script type="application/javascript" src="scripts/intern.js"></script>
<script type="application/javascript">
    var thisServer = {
        addr: "<?php echo $GLOBALS["serverAddr"]; ?>"
    }
window.addEventListener("load", evt=>{


    //document.getElementsByTagName("noscript")[0].parentNode.replaceChild(customInput, document.getElementsByTagName("noscript")[0]);
    //customInput.outerHTML += '<span class="selectionwrapper"><select id="serverSelectType"><option value="thisServer">Ten Serwer</option><option value="outsideServer">Inny serwer</option></select></span>';

    document.getElementById("serverSelectType").onchange = ev=>{
        let selectNode = document.getElementById("serverSelectType");
        console.log(selectNode.selectedIndex);
        if(selectNode.selectedIndex==1) { //"outsideServer"
        document.getElementById("serverAddr").value = "";
        document.getElementById("serverAddr").removeAttribute("disabled");
        } else if(selectNode.selectedIndex==0) { //"thisServer"
        document.getElementById("serverAddr").value = thisServer.addr;
        document.getElementById("serverAddr").setAttribute("disabled", "");
        }

        //<input type="text" name="serverAddr" id="serverAddr" value="<?php echo $GLOBALS["serverAddr"]; ?>" placeholder="Specify addr" disabled>

    }


});
</script>
</head>
<body>
<div id="container">
<div class="centredinside">
<form id="mysqliuLoginBox" method="post" action="modules/into.php" autocomplete="off">
<header>
<div id="mysqliuLoginBox-header">
<img src="images/mysqliu_logo.png">
</div>
</header>
<div id="mysqliuLoginBox-main">
<div class="in">
<noscript><input type="text" name="noJSserverAddr" id="noJSserverAddr" placeholder="Specify server addres if is a outside"></noscript>
<script type="text/javascript">
<!--
let customInput = document.createElement("input");
customInput.type = "text";
customInput.id = "serverAddr";
customInput.value = thisServer.addr;
customInput.setAttribute("name", "serverAddr");
customInput.setAttribute("placeholder", "Specify addr");
customInput.setAttribute("disabled", "");
document.getElementsByClassName("in")[0].appendChild(customInput);
document.write('<span class="selectionwrapper"><select id="serverSelectType" name="serverSelectType"><option value="thisServer">Ten Serwer</option><option value="outsideServer">Inny serwer</option></select></span>');
document.getElementsByTagName("script")[document.getElementsByTagName("script").length-1].remove();
//-->
</script>
<input type="text" name="request_login">
<label>Login</label>
<input type="password" name="request_pass">
<label>Hasło</label>
<input type="submit" value="Zaloguj">
<?php
//if(isset($_SESSION["error_state_onlogin"])) { echo echo $_SESSION["error_state_onlogin"]["name"]; unset($_SESSION["error_state_onlogin"]); }
if(isset($_SESSION["error_state_onlogin"])) { print_e($_SESSION["error_state_onlogin"]["name"], "span", "class", "error"); unset($_SESSION["error_state_onlogin"]); }
?>
</div>
</div>
<div id="mysqliuLoginBox-footer">
<strong>Wersja:</strong> 0.1 <span class="righty"><a href="">Odzyskiwanie konta</a></span>
</form>
</div>
</div>  
</body>
</html>