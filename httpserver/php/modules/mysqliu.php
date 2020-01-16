<?php //Mysql Improved Upgraded Library

class mysqliuMenager {
    //Mysqliu workflow
    public $openedConnections = [];

    function create($connectType="global", $errReport="on", $db_host="", $db_user="", $db_password="", $db_name=false, $charset="utf8", $connAutoclose=false) {
      $temp = new mysqliu($connectType, $errReport, $db_host, $db_user, $db_password, $db_name, $charset, $connAutoclose);
      $temp->assocWithMenager($this);
      return $temp;
    }

    function closeAll() {

    }
}

class mysqliuUser {
    public $name;
    public $fullname;
    protected $password;
    public $server;
    //State
    public $isLogged = false;

    function __construct($username, $pass, $server) {
       $this->name = $username;
       $this->password = base64_encode($pass);
       $this->server = $server;
       $this->fullname = $username."@".$server;
    }

    function readPassword() {
       return base64_decode($this->password);
    }

    function setPassword($newpass) {

    }
}

class mysqliu extends mysqli {
	//User Config Data
	protected $dbhost;
	protected $dbuser;
	protected $dbpassword;
	protected $dbname;
	//Mysqliu Connect Params
	public $dbHost; 
  public $dbUser;
  public $dbPassword;
  public $dbName;
  //Mysqliu Config
  private $connAutoclose;
  private $connRuntime = 0;
	//Mysqliu Params
  public $isActive;
  public $menager = false;
  private $id = 0;
	
	public $customErrInput;
	public $customErrExt = false;
	//Mysqliu Private
	protected $conn;
	
	function __construct($connectType="global", $errReport="on", $db_host="", $db_user="", $db_password="", $db_name=false, $charset="utf8", $connAutoclose=false) {
		 if($errReport!="on") mysqli_report(MYSQLI_REPORT_STRICT);  
		
        try{
        
        switch($connectType) {
            case "global":
            $this->dbHost = $GLOBALS['database']['host']; 
            $this->dbUser = $GLOBALS['database']['user'];
            $this->dbPassword = $GLOBALS['database']['password'];
            $this->dbName = $GLOBALS['database']['name'];
            break; 
            case "globalnodb":
            $this->dbHost = $GLOBALS['database']['host']; 
            $this->dbUser = $GLOBALS['database']['user'];
            $this->dbPassword = $GLOBALS['database']['password'];  
            $this->dbName = false;
            break;
            case "custom":
			       $this->dbHost = $db_host; 
             $this->dbUser  =  $db_user;
             $this->dbPassword  = $db_password;
            $this->dbName =  $db_name;
            break;
        }
		
        $this->connAutoclose = $connAutoclose;
	   
		//$obj = get_object_vars($this);

      if(($this->dbName ? parent::__construct($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbName) : parent::__construct($this->dbHost, $this->dbUser, $this->dbPassword))===false) {
		   $this->isActive = false; throw new Exception("constructor error");
	   } else {  $this->isActive = true;
		  $this->set_charset("utf8"); } 

            
        } catch(/*Exceptionmysqli_sql_exception*/Exception $e) {
            switch($errReport) {
                case "on":
                //echo MYSQL 
                echo '<div class="infobox error"><span>Błąd: '.$this->errno.' Opis: '.$this->error.'Zarządzaj połączeniem z bazą danych <a href="acp.php?page=memorymng">Menadżer pamięci</a></span></div>';  
                //exit;
                break;
                case "publish":
                echo '<div><div style="margin: 5px auto;width:90%;background-color:#c44c4c;padding:10px;color: #fff;" class="errorBox"><h2>Wystąpił problem techniczny</h2>Wystąpił błąd po stronie serwera, za utrudnienia przepraszamy.</div></div>';
				        exit;
                break;
                case "script":
                header("HTTP/1.1 500 Internal Server Error");    
                break;
				        case "requestUncompleted":
                echo '<infobox class="neg"><span>Wystąpiły problemy po stronie serwera, zaloguj się jako <a href="login.php">Root</a></span></infobox>';
				        exit;
                break;
                case "custom":
                $this->custom_error();
                break;
                case "off":
                default:

                break;
            }
			
        }
	}
	
	function custom_error($input=null, $ext=null) {
		if($input===null) $input = $this->customErrInput;
		if($ext===null) $ext = $this->customErrExt;
		echo $input;
		if($ext) exit;
	}
	
	function check_exist($table, $row,$keyword) {
		if($results = $this->query("SELECT $row FROM $table WHERE $row='$keyword'")) {
			return ($results->num_rows<=0) ? false : true;
		} else return false;
  }
  
  function assocWithMenager($menager) {
      $this->menager = $menager;
      $this->id = count($this->menager->openedConnections);
      array_push($this->menager->openedConnections, $this);
  }
	
	/* Database SQL Backup Module */
	function backup_tables($sqlfile=null, $return=false, $tables = '*', $log=false){
  $data = "\n/*---------------------------------------------------------------".
          "\n  SQL DB BACKUP ".date("d.m.Y H:i")." ".
          "\n  HOST: {$this->dbHost}".
          "\n  DATABASE: {$this->dbName}".
          "\n  TABLES: {$tables}".
          "\n  ---------------------------------------------------------------*/\n";
  //$link = new mysqli($host,$user,$pass,$name);
         
  $this->query( "SET NAMES `utf8` COLLATE `utf8_general_ci`"); // Unicode

  if($tables == '*'){ //get all of the tables
    $tables = array();
    $result = $this->query("SHOW TABLES");
    while($row = $result->fetch_row()){
      $tables[] = $row[0];
    }
  }else{
    $tables = is_array($tables) ? $tables : explode(',',$tables);
  }

  foreach($tables as $table){
    $data.= "\n/*---------------------------------------------------------------".
            "\n  TABLE: `{$table}`".
            "\n  ---------------------------------------------------------------*/\n";           
    $data.= "DROP TABLE IF EXISTS `{$table}`;\n";
    $res = $this->query("SHOW CREATE TABLE `{$table}`");
    $row = $res->fetch_row();
    $data.= $row[1].";\n";

   if( $result = $this->query("SELECT * FROM `{$table}`")){
      
$num_rows = $result->num_rows;    

   if($num_rows>0){
      $vals = Array(); $z=0;
      for($i=0; $i<$num_rows; $i++){
        $items = $result->fetch_row();
        $vals[$z]="(";
        for($j=0; $j<count($items); $j++){
            
          if (isset($items[$j])) { $vals[$z].= "'".$this->real_escape_string($items[$j])."'"; } else { $vals[$z].= "NULL"; }
          if ($j<(count($items)-1)){ $vals[$z].= ","; }
        }
        $vals[$z].= ")"; $z++;
      }
      $data.= "INSERT INTO `{$table}` VALUES ";      
      $data .= "  ".implode(";\nINSERT INTO `{$table}` VALUES ", $vals).";\n";
    }
    }
  }
  //$link->close();
		$backupFile = $this->dbName."-".date("dmYHi").".sql";
         $backupPath = $GLOBALS['globalDir']."db/backups/".$backupFile;
      if($sqlfile==1) {
            $sqlbackupfile = fopen($backupPath , "w");
          fputs( $sqlbackupfile, $data);
          /*return '<font color="green">Zapis do pliku zakończył się powodzeniem</font>';*/
          fclose( $sqlbackupfile);
		  if($return)  return [$data, $backupPath, $backupFile];
        } else if ($sqlfile==2) {
             header('Content-Disposition: attachment; filename="'.$backupFile.'"');
            header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
            header('Content-Length: ' . strlen($data));
            header('Connection: close');
          echo $data;
        } else if($sqlfile==3) {
		      $sqlbackupfile = fopen( $backupPath , "w");
          fputs( $sqlbackupfile, $data);
          /*return '<font color="green">Zapis do pliku zakończył się powodzeniem</font>';*/
          fclose( $sqlbackupfile);
		  header('Content-Disposition: attachment; filename="'.$backupFile.'"');
            header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
            header('Content-Length: ' . strlen($data));
            header('Connection: close');
		    if($return)  return [/*$data $backupPath,,*/ $backupFile];

	  } else {
		    if($return) return $data;
	  }
 
}
	
  /* END Dtaabse SQL Backup Module */
  
  /* Standard Method Replacement */

  function queryServal() {
    $start = microtime(true);
    return [parent::query(), microtime(true) - $start];
  }

  function close() {
    if($this->menager) $this->menager->close($this->id);
    return parent::close();
  }
	
	function __destruct() {
		if($this->connAutoclose) $this->close();
	}
}


?>