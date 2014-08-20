<?php if (!isset($_SESSION)) {
  session_start();
}?>
<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"

error_reporting(~E_ALL & ~E_NOTICE); 

//CONEXION LOCAL
$hostname_conn2 = "www.elmagodoneloy.com:3307";//"192.168.2.2";
$database_conn2 = "ELOY";
$username_conn2 = "diagcomp";
$password_conn2 = "diagcomp";

$conn2 = mysql_pconnect($hostname_conn2, $username_conn2, $password_conn2);
if(!$conn2) echo "NO HAY CONEXION AL INTERNET, FAVOR ASEGURESE DE ESTAR CONECTADO<BR>PARA REALIZAR LOS PEDIDOS<br><a href='subirCarrito.php'>Regresar</a>";

//echo $_SESSION['conexion'];
?>