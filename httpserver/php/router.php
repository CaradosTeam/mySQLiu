<?php
//HTTP Router Request API <=> MySQLiu v.0.1

function get_mime_type($filename) {
    $idx = explode( '.', $filename );
    $count_explode = count($idx);
    $idx = strtolower($idx[$count_explode-1]);

    $mimet = array( 
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    if (isset( $mimet[$idx] )) {
     return $mimet[$idx];
    } else {
     return 'application/octet-stream';
    }
 }

class Router {
    function __construct() {

    }

    function run($url) {

        $mimeType = get_mime_type($url);
        if (preg_match('/\.(?:php|html)$/', $url)) {
            header("HTTP/1.1 200 OK");
            header("Host: localhost:3308");
            header("Date: ".date("D, m M Y G:i:s ")."GMT"); //Tue, 03 Sep 2019 19:33:20 GMT

            echo "<!-- HTML Rendered File -->";
            (include_once $url) or die('<font color="red">Server is not avaible now, for details read <strong>logs/errors.log</strong> file</red>');
        } else if(preg_match('/\.(?:css)$/', $url)) {
            header("Content-Type: $mimeType; charset=UTF-8");
            $tempData = file_get_contents($url);
            echo preg_replace('/\s+/', " ", $tempData);
            return true;
        } else {
            header("Content-Type: $mimeType; charset=UTF-8");
            $tempData = file_get_contents($url);
            echo $tempData;
            return true;
        }
    }


}


$addr = $_SERVER["REQUEST_URI"]=="" || $_SERVER["REQUEST_URI"]=="/" ? __DIR__."/index.php" : (file_exists(__DIR__.$_SERVER["REQUEST_URI"]) ? __DIR__.$_SERVER["REQUEST_URI"] : __DIR__."/index.php");
$router = new Router();
$router->run($addr);

//$addr = $_SERVER["REQUEST_URI"]=="/" ? "index.php" : __DIR__.$_SERVER["REQUEST_URI"];
/*echo $addr;*/

//include_once 'router/Request.php';
//include_once 'router/Router.php';


/*if (preg_match('/\.(?:css|js)$/', $_SERVER["REQUEST_URI"], $ext)) {
    $tempData = file_get_contents(__DIR__.$_SERVER["REQUEST_URI"]);
    HTTP/1.1 200 OK
    Host: localhost:3308
    Date: Tue, 03 Sep 2019 19:33:20 GMT
    Connection: close
    Content-Type: text/css; charset=UTF-8
    Content-Length: 3781
    //$mimeSecondPart = str_replace(".", "", $ext[0]);
    header("Content-Type: text/css; charset=UTF-8");
    //header("Date: Tue, 03 Sep 2019 19:33:20 GMT");
    //echo mime_content_type(__DIR__.$_SERVER["REQUEST_URI"]);
    
    echo preg_replace('/\s+/', " ", $tempData);
    return true;
    
} else if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    $tempData = file_get_contents(__DIR__.$_SERVER["REQUEST_URI"]);
    echo $tempData;
    return false;
} else { 
    $addr = $_SERVER["REQUEST_URI"]=="" || $_SERVER["REQUEST_URI"]=="/" ? "index.php" : (file_exists(__DIR__.$_SERVER["REQUEST_URI"]) ? __DIR__.$_SERVER["REQUEST_URI"] : "index.php");
    //echo $addr;
    echo "<!-- HTML Rendered File -->";
    //echo '<style type="text/css">html, body {margin:0;}</style>';
    (include_once $addr) or die('<font color="red">Server is not avaible now, for details read <strong>logs/errors.log</strong> file</red>');
}*/

//include_once $addr;



//var_dump(file_get_contents("php://input"));
//var_dump(file_get_contents('php://temp'));
//var_dump(stream_get_contents(STDIN));
//var_dump($_POST);

//var_dump(stream_get_contents(detectRequestBody()));


?>