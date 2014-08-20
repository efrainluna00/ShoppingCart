<?php 
if (!isset($_SESSION)) 
  session_start();
  
 
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());
  
if(isset($_GET['conexion'])) $_SESSION['conexion'] =  $_GET['conexion'];
error_reporting(E_ALL & ~E_NOTICE); 
$rutaFotos = "";
$regsxPag = 9;
$pedidosxvendedor = 50;
$clientesxpag = 30;
$colsxPag= 3;
$limsup = 5;
$pancho="550" . "px";
$colancho="225px";
$maxTamArchi = 30;
$MAXLINEA = 300;
$rutaArchivos = "";
$impuesto = 0.13;
$nombreCompradorNuevo = "nuevoComprador";
$ftpserver = "";

$usuarioInternet =2;
$usuarioNuevoCliente =3;
?>