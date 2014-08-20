<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}
?>
<?php 
//PARA INSERTAR EN LA TABLA DE ESTIMADOS
if(isset($_GET['generarArchivo']) && $_GET['generarArchivo']==1 ){

mysql_select_db($database_conn, $conn);
//$arr = implode(',', $_POST['chkAccion']);
/*
$qvi = "SELECT cod_item,cant_stock,des_item FROM ITEMS WHERE cod_item='" . $_GET['idPedido'] . "'";
$rsqvi = mysql_query($qvi, $conn) or die(mysql_error());
$tam = mysql_num_rows($rsqvi);
*/

mysql_select_db($database_conn, $conn);
$qdes = "SELECT s.*, s.cod_usuario as vnom, u.cod_usuario as cnom FROM (SELECT p.*, u.cod_usuario FROM PEDIDOS as p, USUARIOS as u WHERE "; 
if(!isset($_GET['again']))
$qdes.= "UPLOADED<>1 AND ";
$qdes.=  "p.id=". $_GET['idPedido'] ."  AND p.idVendedor=u.id) as s, USUARIOS as u WHERE s.idCliente=u.id";
$rsPed = mysql_query($qdes, $conn) or die(mysql_error());

if(mysql_num_rows($rsPed)<=0)
	echo "EL PEDIDO SOLICITADO YA HA SIDO REALIZADO";
//echo "<BR>QDES $qdes" ;
else{

$i=0;

$affected = 0;
//$wished = count($_POST['chkAccion']);
//$arr=$_POST['chkAccion'];
//$arr = implode(",",$arr);

$row_rsPed= mysql_fetch_assoc($rsPed);
do{
$flagPedido = 1;

mysql_select_db($database_conn, $conn);
$qdetc = "SELECT * FROM DETALLE_CARRITO WHERE idCarrito=" . $row_rsPed['idIndexCarrito'];
//echo "<BR>QDETC $qdetc" ;
$rsDetc = mysql_query($qdetc, $conn) or die(mysql_error());

$parrCod="";
$parrIdCant="";
$iarrIdCant="";
//PRIMERO ALMACENO LOS ITEMS Y LA CANTIDAD DESEADA
$tam = mysql_num_rows($rsDetc);
for($i=0; $i<$tam; $i++){
	$parrCod[]="'". mysql_result($rsDetc,$i,cod_item). "'";
	$parrIdCant[mysql_result($rsDetc,$i,cod_item)] =  mysql_result($rsDetc,$i,cantidad);
	}
//echo "<br> ARR DE COD ITEMS";
//print_r($parrCod);
//echo "<br> ARR DE COD ITEMS Y CANTIDAD";
//print_r($parrIdCant);

//DESPUES ALMACENO LOS ITEMS Y LA CANTIDAD EXISTENTE
$parrCod = implode(",",$parrCod);
mysql_select_db($database_conn, $conn);
$qit = "SELECT cod_item, cant_stock FROM ITEMS where cod_item IN ($parrCod)";
//echo "<BR>QIT $qit" ;
$rsqit = mysql_query($qit, $conn) or die(mysql_error());
$tam = mysql_num_rows($rsqit);
for($i=0; $i<$tam; $i++)
	//$qarrCod[]="'". mysql_result($rsDetc,$i,cod_item). "'";
	$iarrIdCant[mysql_result($rsqit,$i,cod_item)] =  mysql_result($rsqit,$i,cant_stock);

//DESPUES LOS COMPARO...
//print_r($iarrIdCant);
foreach($iarrIdCant as $key=>$value){
	if ($parrIdCant[$key]>$value){
		$flagPedido=0;
		$arrval[$key] = 0;
		mysql_select_db($database_conn, $conn);
		$upd = "UPDATE PEDIDOS set uploaded=3 WHERE id =" . $row_rsPed['id'];
		//echo "<BR>UPD $upd" ;
		mysql_query($upd,$conn) or die(mysql_error());
		}
		else
			$arrval[$key]=1;
}

if($flagPedido==1){

mysql_select_db($database_conn, $conn);
$qdetc = "SELECT dc.*, i.des_item FROM DETALLE_CARRITO as dc, ITEMS as i WHERE idCarrito=" . $row_rsPed['idIndexCarrito'] . " AND dc.cod_item=i.cod_item";
//echo "<BR>QDETC $qdetc" ;
$rsDetc2 = mysql_query($qdetc, $conn) or die(mysql_error());

mysql_select_db($database_conn, $conn);
$tam= mysql_num_rows($rsDetc2);
$nombreArchivo = mysql_query("SELECT cod_usuario,cod_monica FROM USUARIOS WHERE id=" . $_GET['idCliente'], $conn) or die(mysql_error());
if( mysql_result($nombreArchivo,0,cod_monica)=='NULL' || mysql_result($nombreArchivo,0,cod_monica)=='')
	$archivo = mysql_result($nombreArchivo,0,cod_usuario);
		else
			$archivo = mysql_result($nombreArchivo,0,cod_monica);
$archivo .= ".txt";
$fp = fopen("pedidos/".$archivo,"w+b"); 

for($i=0; $i < $tam; $i++){
$string = mysql_result($rsDetc2,$i,cod_item) . " " .  mysql_result($rsDetc2,$i,cantidad). "\r\n";
fwrite($fp, $string); 




/*
$db = dbase_open($rutaEstimados, 2);

if ($db) {
//echo "si me conecte";
$ins = array('5',"'".date("d/m/Y")."'","'".$row_rsPed['cnom'] . "'", "'". mysql_result($rsDetc2,$i,cod_item) . "'", "'". mysql_result($rsDetc2,$i,cantidad) . "'", "'". mysql_result($rsDetc2,$i,precioUnitario) . "'",'0','0','150');
echo "<BR>INS";
print_r($ins);
dbase_add_record($db,$ins);
dbase_close($db);

}
 else
  echo "<p align='center'>Problemas al abrir la bd, intente nuevamente</p>";
  */
  }
  fclose($fp); 


 

mysql_select_db($database_conn, $conn);
$upd = "UPDATE PEDIDOS set uploaded=1, fechaUploaded=TIMESTAMP('" .date("Y-m-d H:i:s") ."') WHERE id =" . $row_rsPed['id'];
//echo "<BR>UPD $upd" ;
mysql_query($upd,$conn) or die(mysql_error());
//$i++;
$affected++;
}

} while ($row_rsPed = mysql_fetch_assoc($rsPed));
}

}//END ELSE
?>

<?php if(mysql_num_rows($rsPed)>0) {?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<div align="center">
  <p>
    <?php if($flagPedido==1){?>
    <a href="descargar.php?file=<?php echo  $archivo ?>">Descargar el archivo</a>
    <?php }else{?> 
    La orden de compra no pudo ser efecutada. Haga click <a href="verPedidos.php?detalles=1&idIndexCarrito=<?php echo $_GET['idIndexCarrito']?>&idPedido=<?php echo $_GET['idPedido']?>" target="_parent">aqui </a>para ver los detalles del pedido. 
    
    <?php }?>
</p>
  <p><a href="#" onclick="javascript:window.close()">Cerrar ventana</a> </p>
</div>
</body>
</html>
<?php }?>