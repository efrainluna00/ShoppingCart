<?php if (!isset($_SESSION)) {
  session_start();
}?>
<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"

error_reporting(E_ALL & ~E_NOTICE); 
//CONEXION A INTERNET
$hostname_conn = "LOCALHOST";
$database_conn = "ELOY";
$username_conn = "root";
$password_conn = "";

$conn = mysql_pconnect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
if(!isset($_SESSION['conexion'] ))
$_SESSION['conexion'] = 0;

//echo $_SESSION['conexion'];

?>