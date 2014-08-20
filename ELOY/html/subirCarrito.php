<?php require_once('../Connections/conn.php'); ?>
<?php   
	include('common.php');
?>

<?php

if(isset($_GET['eliminar'])){
mysql_query("UPDATE PEDIDOS set eliminado=1 WHERE id=" .$_GET['idPedido'], $conn) or die(mysql_error());
}


$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}

if(isset($_GET['eliminar']) && $_GET['eliminar']==1){
mysql_select_db($database_conn, $conn);
$qel = "UPDATE PEDIDOS set eliminado=1 where id=" . $_GET['idPedido'];
mysql_query($qel, $conn) or die(mysql_error());
}

$flagSubirPedido=1;

mysql_select_db($database_conn, $conn);
$query_rsCat = "SELECT cod_cate,categoria FROM CATEGORI where eliminado<>1";
$rsCat = mysql_query($query_rsCat, $conn) or die(mysql_error());
$row_rsCat = mysql_fetch_assoc($rsCat);
$totalRows_rsCat = mysql_num_rows($rsCat);

$maxRows_rsActualizaciones = $pedidosxvendedor;
$pageNum_rsActualizaciones = 0;
if (isset($_GET['pageNum_rsActualizaciones'])) {
  $pageNum_rsActualizaciones = $_GET['pageNum_rsActualizaciones'];
}
$startRow_rsActualizaciones = $pageNum_rsActualizaciones * $maxRows_rsActualizaciones;

mysql_select_db($database_conn, $conn);
$query_rsActualizaciones = "SELECT p.*, u.nombre as cnom FROM PEDIDOS as p, USUARIOS as u  WHERE 
p.idCliente = u.id and p.eliminado<>1 AND u.eliminado<>1 ";
if(!isset($_GET['uploaded'])) $query_rsActualizaciones .= " AND p.uploaded<>1";
else{
if($_GET['uploaded']==1) $query_rsActualizaciones .= " AND p.uploaded=1";
if($_GET['uploaded']=="0,2") $query_rsActualizaciones .= " AND p.uploaded<>1";
}
if(isset($_GET['buscar'])) $query_rsActualizaciones .= " AND u.nombre LIKE '%". strtoupper($_GET['buscar']) ."%'";
$query_rsActualizaciones .= " ORDER BY p.fechaCompra DESC";
//echo $query_rsActualizaciones;
$query_limit_rsActualizaciones = sprintf("%s LIMIT %d, %d", $query_rsActualizaciones, $startRow_rsActualizaciones, $maxRows_rsActualizaciones);
mysql_select_db($database_conn, $conn);
$rsActualizaciones = mysql_query($query_limit_rsActualizaciones, $conn) or die(mysql_error());
$row_rsActualizaciones = mysql_fetch_assoc($rsActualizaciones);
//echo $query_rsActualizaciones;
 $all_rsActualizaciones = mysql_query($query_rsActualizaciones,$conn);
  $totalRows_rsActualizaciones = mysql_num_rows($all_rsActualizaciones);

if (isset($_GET['totalRows_rsActualizaciones'])) {
  $totalRows_rsActualizaciones = $_GET['totalRows_rsActualizaciones'];
} else {
  $all_rsActualizaciones = mysql_query($query_rsActualizaciones,$conn);
  $totalRows_rsActualizaciones = mysql_num_rows($all_rsActualizaciones);
}
$totalPages_rsActualizaciones = ceil($totalRows_rsActualizaciones/$maxRows_rsActualizaciones)-1;

$queryString_rsActualizaciones = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsActualizaciones") == false && 
        stristr($param, "totalRows_rsActualizaciones") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsActualizaciones = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsActualizaciones = sprintf("&totalRows_rsActualizaciones=%d%s", $totalRows_rsActualizaciones, $queryString_rsActualizaciones);

mysql_select_db($database_conn, $conn);
																 switch($_SESSION['tipo_pre']){
																		 	case 1:  $cadPrecio='precio';
																						break;
																			case 2:  $cadPrecio='precio2';
																						break;
																			case 3:  $cadPrecio='precio3';
																						break;																					
																			case 4:  $cadPrecio='precio4';
																						break;
																			default: $cadPrecio='precio';
																						break;
																		 }
$query_rsofaz = "SELECT i.cod_item, i.des_item, i.$cadPrecio, i.imagen FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item=ie.cod_item AND ie.estado=1
 AND ie.eliminado<>1 ORDER BY RAND() LIMIT 0,1";
$rsofaz = mysql_query($query_rsofaz, $conn) or die(mysql_error());
$row_rsofaz = mysql_fetch_assoc($rsofaz);
$totalRows_rsofaz = mysql_num_rows($rsofaz);

//SINCRONIZACION DE CARRITOS
if(isset($_POST['chkAccion']) && isset($_POST['rp']) && $_POST['rp']==1 ){

include('../Connections/conn2.php');
mysql_select_db($database_conn2, $conn2);
$arr = implode(',', $_POST['chkAccion']);
$qvi = "SELECT cod_item,cant_stock,des_item FROM ITEMS WHERE cod_item IN ($arr)";
$rsqvi = mysql_query($qvi, $conn2) or die(mysql_error());
$tam = mysql_num_rows($rsqvi);


mysql_select_db($database_conn, $conn);
$qdes = "SELECT * FROM PEDIDOS WHERE UPLOADED<>1 AND id IN ($arr)";
$rsPed = mysql_query($qdes, $conn) or die(mysql_error());
$row_rsPed= mysql_fetch_assoc($rsPed);

$i=0;

$affected = 0;
$wished = count($_POST['chkAccion']);
$arr=$_POST['chkAccion'];
$arr = implode(",",$arr);


do{
$flagPedido = 1;

mysql_select_db($database_conn, $conn);
$qdetc = "SELECT * FROM DETALLE_CARRITO WHERE idCarrito=" . $row_rsPed['idIndexCarrito'];
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
mysql_select_db($database_conn2, $conn2);
$qit = "SELECT cod_item, cant_stock FROM ITEMS where cod_item IN ($parrCod)";
$rsqit = mysql_query($qit, $conn2) or die(mysql_error());
$tam = mysql_num_rows($rsqit);
for($i=0; $i<$tam; $i++)
	//$qarrCod[]="'". mysql_result($rsDetc,$i,cod_item). "'";
	$iarrIdCant[mysql_result($rsqit,$i,cod_item)] =  mysql_result($rsqit,$i,cant_stock);

//DESPUES LOS COMPARO...
//print_r($iarrIdCant);
$arrval[$row_rsPed['id']]=1;
foreach($iarrIdCant as $key=>$value){
	if ($parrIdCant[$key]>$value){
		$flagPedido=0;
		$arrval[$row_rsPed['id']] = 0;
		mysql_select_db($database_conn, $conn);
		$upd = "UPDATE PEDIDOS set uploaded=3 WHERE id =" . $row_rsPed['id'];
		mysql_query($upd,$conn) or die(mysql_error());
		}
		
}


if($flagPedido==1){

mysql_select_db($database_conn2, $conn2);
mysql_query("INSERT INTO INDEX_CARRITO VALUES ('NULL')",$conn2) or die(mysql_error());
$idCarrito = mysql_insert_id();
$ins1 = "INSERT INTO PEDIDOS VALUES ('NULL','".  $row_rsPed['idVendedor'] . "','" .   $row_rsPed['idCliente']  .   "','".  $row_rsPed['fechaCompra']  . 
"','$idCarrito',0,'NULL','NULL','";
if(isset($_SESSION['nombreNuevoCliente'])) $ins1 .=  "Nombre: " . $_SESSION['nombreNuevoCliente'] . ". " .  $_SESSION['infoNuevoCliente'] . "')";
	else
		$ins1 .= "NULL')";
mysql_query($ins1,$conn2) or die(mysql_error());
$row_rsDetc = mysql_fetch_assoc($rsDetc);

mysql_select_db($database_conn, $conn);
$qdetcaux = "SELECT * FROM DETALLE_CARRITO WHERE idCarrito=" . $row_rsPed['idIndexCarrito'];
$rsDetcaux = mysql_query($qdetcaux, $conn) or die(mysql_error());
$row_rsDetcaux = mysql_fetch_assoc($rsDetcaux);

do{
mysql_select_db($database_conn2, $conn2);
$ins2 = "INSERT INTO DETALLE_CARRITO VALUES ('NULL','" . $row_rsDetcaux['cod_item'] . "','" . $row_rsDetcaux['precioUnitario'] . "','" . $row_rsDetcaux['cantidad'] .
"','$idCarrito')" ;
//echo $ins2;
mysql_query($ins2,$conn2) or die(mysql_error());
}while ($row_rsDetcaux = mysql_fetch_assoc($rsDetcaux));
mysql_select_db($database_conn, $conn);
$upd = "UPDATE PEDIDOS set uploaded=1, fechaUploaded=TIMESTAMP('" .date("Y-m-d H:i:s") ."') WHERE id =" . $row_rsPed['id'];
mysql_query($upd,$conn) or die(mysql_error());
//$i++;
$affected++;
}

} while ($row_rsPed = mysql_fetch_assoc($rsPed));
//echo "<br>ARRVAL";
//print_r($arrval);
}


/*if (isset($_GET['cat']))
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE id=" . $_GET['cat'];
else
$query_rsCat2 = "SELECT * FROM CATEGORI ORDER BY RAND()";
$rsCat2 = mysql_query($query_rsCat2, $conn) or die(mysql_error());
$row_rsCat2 = mysql_fetch_assoc($rsCat2);
$totalRows_rsCat2 = mysql_num_rows($rsCat2);
$catLocal = mysql_result($rsCat2,0,id);
*/

//echo "<br>ARRVAL";
//print_r($arrval);

if(isset($_GET['detalles']) && $_GET['detalles']==1){
mysql_select_db($database_conn, $conn);

$qdet1 = "SELECT u.nombre as cnom, s.*, u.cod_usuario as cid, s.nombre as vnom
FROM (

SELECT u.nombre ,p. *, u.cod_usuario as vid
FROM PEDIDOS AS p, USUARIOS AS u
WHERE p.idVendedor = u.id
AND u.eliminado <> 1
AND p.eliminado<>1
) AS s, USUARIOS AS u
WHERE s.idCliente = u.id
AND u.eliminado <> 1
 AND s.id=" . $_GET['idPedido'] ;
// echo "<BR>QDET1: " . $qdet1;
$rsdet1 = mysql_query($qdet1, $conn) or die(mysql_error());
$row_rsdet1 = mysql_fetch_assoc($rsdet1);

$qdet2 = "SELECT i.cod_item, i.des_item, i.cant_stock, dc.precioUnitario, dc.cantidad FROM
ITEMS as i, DETALLE_CARRITO as dc WHERE i.cod_item=dc.cod_item and dc.idCarrito=" . $_GET['idIndexCarrito']. " ORDER BY i.cod_item" ;
// echo "<BR>QDET2: " . $qdet2;
$rsdet2 = mysql_query($qdet2, $conn) or die(mysql_error());
$row_rsdet2 = mysql_fetch_assoc($rsdet2);
$totalRows_rsdet2 = mysql_num_rows($rsdet2);

//echo "QDET1 $qdet1 <br>";
//echo "QDET2 $qdet2 <br>";
}

//print_r($arrval);

function getExclusivo($matriz){
$ce = "";
	foreach($_GET as $key => $value){
		$cont =0;
		$i=0;
		for($i=0; $matriz[$i]; $i++){
		if ($key==$matriz[$i])
		$flag=1;		

			}		
			if($flag!=1)
			{ if($cont==0) 
			$ce .= $key . "=" . $value;
				else
				$ce .= "&". $key . "=" . $value;
			}
			$cont++;
			$flag=0;
	} 
	return $ce;	
}




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script language="javascript">
function conslink(enlac,opc,id) {
	if(opc==1){
	var vari1 = "f" + id;
	var vari2 = "s" + id;
	var cuantos = document.getElementById(vari1).selectedIndex;
	
	document.getElementById('link1').href = enlac + "&numProd=" + document.getElementById(vari2).s1.options[cuantos].value;
	
	}
	else{
	var vari1 = "f" + id;
	var vari2 = "s" + id;
	var cuantos = document.getElementById(vari1).selectedIndex;
	document.getElementById('link2').href = enlac + "&numProd=" + document.getElementById(vari1).s2.options[cuantos].value;
	
	}
	
}
		
		
function ef(url){
	if(confirm('¿Desea eliminar estas fechas?')) {
		document.location.href=  url;
	} 
}

function seleccionar_todo(){ 
   for (i=0;i<document.form1.elements.length;i++) 
      if(document.form1.elements[i].type == "checkbox") 
         document.form1.elements[i].checked=1 
} 

function deseleccionar_todo(){ 
   for (i=0;i<document.form1.elements.length;i++) 
      if(document.form1.elements[i].type == "checkbox") 
         document.form1.elements[i].checked=0 
} 
</script>
<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style5 {color: #FF0000}
.style10 {font-size: 13px}
.style11 {color: #00FF00}
-->
</style>
<script language="javascript">
	
function conslink2(){
	var string;
	string = "subirCarrito.php?buscar=" + document.form2.txtBuscar.value;
	document.getElementById('linkBuscar').href = string;
}		
</script>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" class="w">
	<tr>
	  <td style="width:100%"><table cellpadding="0" cellspacing="0" border="0" style="width:716px" align="center"> 
                <tr>
                    <td align="center"  >
                        <table cellpadding="0" cellspacing="0" border="0" >
                            <tr>
                                <td height="222" align="left"><table cellpadding="0" cellspacing="0" border="0" >
                                      
                                  </table> 
                                  
                                  <table border="0">
                                    <tr>
                                      <td><table border="0" style="width:200px">
                                        <tr>
                                          <td rowspan="2"><img src="images/z1.gif" alt=""></td>
                                          <td><a href="carrito.php" ><u>Carrito de compras:</u></a></td>
                                        </tr>
                                        <tr>
                                          <td><?php if ($_SESSION['logged']==1){ ?>
                                            <span><?php echo $_SESSION['cci']?> productos en su carrito</a><br>
                                            <?php } else echo "&nbsp";?></td>
                                        </tr>
                                      </table>
                                        <p><a href="carrito.php" ></a><br>
                                        </p>
                                        <div align="center" class="header">
                                        <div align="left"></div>
                                      </div></td>
                                      <td><div align="right"  class="header">
                                        <?php if ($_SESSION['logged']==1){ ?>
                                              [Buen dia <?php echo $_SESSION['nombreUsuario']; ?> <a href="login.php?logout=ok">Logout]</a> <br>
                                              Estatus:
                                              <?php if ($_SESSION['conexion']==0) echo "ONLINE"; else echo "OFFLINE"?>
                                      </div><?php }?></td>
                                    </tr>
                                  </table>
                                  <p><img src="images/logoeloytranparente3.gif" alt="" width="295" height="77" >                                  </p>
                              </td>
                            </tr>
                        </table>                  </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" style="height:583px ">
                            <tr>
                              <td class="col_left"><table cellpadding="0" cellspacing="0" border="0" class="box_width_left">
									   <tr>
									<td>
									<a href="index.php"><img src="images/esp/m1.jpg" alt="" border="0"></a><br>
                                          <a href="index.php?estado=0"><img src="images/esp/m2.jpg" alt="" border="0"></a><br>
                                          <a href="index.php?estado=1"><img src="images/esp/m3.jpg" alt="" border="0"></a><br>
                                          <a href="login.php<?php if (isset($_SESSION['logged'])) echo "?logout=1" ?>"><img src="images/esp/m4.jpg" alt="" border="0"></a><br>
                                           <a href="contactenos.php"><img src="images/esp/m5.jpg" alt="" border="0"></a><br>
                                          <?php if($_SESSION['tipoUsuario']==3){?>
                                                <img alt="" src="images/line1.gif"><br> 
												                                         
                                               <table cellpadding="0" cellspacing="0" border="0" class="box_heading_table">
                                                  <tr>
                                                    <td style="width:100%" class="box_heading_td">administrador </td>
                                                  </tr>
                                      </table>
                                                <table cellpadding="0" cellspacing="0" border="0" class="box_body_table">
                                                  <tr>
                                                    <td style="width:100%" class="box_body">
													<ul>
													<li class="bg_list"><a href="adminSetEstadoProd.php">Estado de productos</a><br style="line-height:9px">
													  </li>
													  <li class="bg_list"><a href="verPedidos.php?uploaded=0,2">Listado de pedidos</a><br style="line-height:9px">
													  </li>
													  <li class="bg_list"><a href="verUsuarios.php">Listado de usuarios</a><br style="line-height:9px">
													  </li>
													</ul>	
													
                                                    </td></tr></table>
                                                <img alt="" src="images/line2.gif"></td>
									</tr><?php }?>
									<?php if ( $_SESSION['tipoUsuario']==1){ ?>   <tr>
									<td>
									
                                                <img alt="" src="images/line1.gif"><br> 
												                                         
                                               <table cellpadding="0" cellspacing="0" border="0" class="box_heading_table">
                                                  <tr>
                                                    <td style="width:100%" class="box_heading_td"> agente </td>
                                                  </tr>
                                                </table>
                                                <table cellpadding="0" cellspacing="0" border="0" class="box_body_table">
                                                  <tr>
                                                    <td style="width:100%" class="box_body">
													<ul><li class="bg_list"><a href="agenteSelectCliente.php">Seleccionar cliente </a><br style="line-height:9px">
													  </li>
													   <?php if($_SESSION['conexion']==1){ ?>
													 <li class="bg_list"><a href="subirCarrito.php?conexion=0">Ver pedidos pendientes</a></li>
													  <?php } ?>
													<li class="bg_list"><a href="carritoForm.php">Formulario carrito</a></li>
													  <?php if($_SESSION['conexion']==1){ ?> <li class="bg_list"><a href="actualizarBD.php">Cargar base de datos</a></li><?php }?>
													 <?php if($_SESSION['conexion']==0){ ?> <li class="bg_list"><a href="<?php echo $ftpserver ?>">Descargar archivos </a></li><?php }?>
													  <li class="bg_list"><br style="line-height:9px">
												      </li>
													  <div align="center">
													    <?php if($_SESSION['idClienAgen']!=-1){?>
												      </div>
													  <li class="bg_list">
													    <div align="left">Cliente:<br> 
										                <?php echo $_SESSION['nombreClienAgen']?> <br style="line-height:9px">
											            </div>
													  </li>
													  <div align="center">
													    <?php }?>
                                                      </div>
													</ul>	
                                                    </td></tr></table>
                                                <img alt="" src="images/line2.gif"></td>
									</tr><?php }?>
                                       <tr>
                                        <td><img alt="" src="images/line1.gif"><br>
<table cellpadding="0" cellspacing="0" border="0" class="box_heading_table">
                                                    <tr>
                                                        <td style="width:100%" class="box_heading_td">CATEGORIAS</td>
                                                    </tr>
                                          </table>
                                                <table cellpadding="0" cellspacing="0" border="0" class="box_body_table">
                                                    <tr>
                                                      <td style="width:100%" class="box_body">
                                                           
                                                           <ul>
														    <li class="bg_list"><a href="index.php">Todos</a></li>
                                                                 <?php do { ?>
															         <li class="bg_list"><a href="index.php?cat=<?php echo $row_rsCat['cod_cate']; ?>"><?php echo $row_rsCat['categoria']; ?></a></li>
																	  <?php } while ($row_rsCat = mysql_fetch_assoc($rsCat)); ?>
                                                           </ul>
                                                          <br style="line-height:12px">                                                      </td>
                                                    </tr>
                                                </table>
                                           <img alt="" src="images/line2.gif">
										   <?php if ($totalRows_rsofaz>0){ ?>
                                                <img alt="" src="images/line3.gif"><br>
                                                <table cellpadding="0" cellspacing="0" border="0" class="box_heading_table_2">
                                                    <tr>
                                                        <td style="width:100%" class="box_heading_td_2">ofertas especiales </td>
                                                    </tr>
                                                </table>
                                                <table cellpadding="0" cellspacing="0" border="0" class="box_body_table_2">
                                                    <tr>
                                                        <td class="box_body box_body_tall_b_2">
                                                            <table cellpadding="0" cellspacing="0" border="0">
                                                               <tr>
                                                                 <td style="height:28px " align="center" class="vam"><span><a href="index.php?idProducto=<?php echo $row_rsofaz['cod_item']; ?>"><?php echo $row_rsofaz['des_item']; ?></a></span><br></td>
                                                               </tr>
                                                               <tr>
                                                                 <td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:111px">
                                                                     <tr>
                                                                       <td><img src="images/pic_corn_tl.gif" alt="" border="0"></td>
                                                                       <td class="pic_corn_t"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                       <td><img src="images/pic_corn_tr.gif" alt="" border="0"></td>
                                                                     </tr>
                                                                     <tr>
                                                                       <td class="pic_corn_l"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                       <td class="image"><a href="#"><img src="<?php $foto=  $rutaFotos . $row_rsofaz['imagen']; 
								$foto = substr($row_rsofaz['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" alt="" border="0" height="150" width="130"></a></td>
                                                                       <td class="pic_corn_r"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                     </tr>
                                                                     <tr>
                                                                       <td><img src="images/pic_corn_bl.gif" alt="" border="0"></td>
                                                                       <td class="pic_corn_b"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                       <td><img src="images/pic_corn_br.gif" alt="" border="0"></td>
                                                                     </tr>
                                                                 </table></td>
                                                               </tr>
                                                               <tr>
                                                                 <td style="height:28px " align="center" class="vam"><div align="center"><?php if ($_SESSION['tipo_pre']){ ?><span class="productSpecialPrice">₵<?php 
																		 switch($_SESSION['tipo_pre']){
																		 	case 1:  echo number_format($row_rsofaz['precio']);
																						break;
																			case 2:  echo number_format($row_rsofaz['precio2']);
																						break;
																			case 3:  echo number_format($row_rsofaz['precio3']);
																						break;																					
																			case 4:  echo number_format($row_rsofaz['precio4']);
																						break;
																		 }?> </span></div><?php }?></td>
                                                               </tr> 
                                                            </table>                                                      </td>
                                                    </tr>
                                                </table>
                                         <img alt="" src="images/line4.gif"><br>                                            <?php }?></td>
                                      </tr>
                                    </table>&nbsp;</td>
                                <td><img alt="" src="images/spacer.gif" width="11" height="1"></td>
                                <td class="col_center">
								
                                    <table cellpadding="0" cellspacing="0" border="0" style="400px" >
									<tr>                                       
										
                                            <td>
								 <?php 	if(!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario']!=1) {?>
								<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
													<tr>
                                                    	<td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table"><table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table>   <br style="line-height:9px">
                                                    	 
														 
                                                 <table cellpadding="0" cellspacing="0" border="0" style="height:32px " class="product">
                                                    <tr><td>
													  <table border="0" class="product">
 <tr>
											  <td width="95%" bgcolor="#FFFFFF"> <p align="center">Usted no tiene permisos para acceder a esta area. <a href="registrarUsuario.php"></a></p>
															     
																  
							      </td>
																
																
                                                        </tr>
																 
</table>
<table>
   <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td width="1%" class="cont_body_tall_b" style="width:100%"></td>
                                                        <td width="4%"><img src="images/cont_corn_br.gif" alt=""></td>
                                      </tr></table></td>
                                                    </tr>
                                                 </table>
                                                 </td>
                                                    </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                              </table><?php exit;} ?> <?php if (!isset($_GET['detalles'])){ 
											  $_SESSION['postback'] = $_SERVER['QUERY_STRING'];
											  ?>
								
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr>
										
                                            <td><br style="line-height:9px">
                                                <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" style="width:700px">
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
                                                    <tr>
                                                   	  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table">
												

                                                        	<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
												
                                                                <tr>
																  <td class="cont_heading_td"><p>actualizacion de base de datos </p>
															      </td>
                                                                </tr>
                                                        </table> 
													        <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                                  <tr>
                                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                                  </tr>
                                                        </table>                                                   
													<p align="center"><?php if(isset($_GET['eliminar']) && $_GET['eliminar']==1){?>
													<span class="style5">El pedido seleccionado ha sido eliminado</span>                                                  <?php }?>  </p>
													<p align="center">
													  <?php if(isset($_POST['chkAccion']) && isset($_POST['rp']) && $_POST['rp']==1 ){?>
													  <span class="style5">Las solicitudes de compra han sido cargadas en el servidor (<?php echo $affected ?>/<?php echo $wished ?>)<br>
													  </span>
													  <?php }?>
													  <form name="form2" method="post" action="javascript:
															document.location.href = 'subircarrito.php?buscar=' + document.form2.txtBuscar.value;">
													    <div align="right">
                                                        <table border="0">
                                                          <tr>
                                                            <td><table border="0">
                                                                <tr>
                                                                  <td><div align="left"><a href="subirCarrito.php?uploaded=alle">Todos</a></div></td>
                                                                </tr>
                                                                <tr>
                                                                  <td><div align="left"><a href="subirCarrito.php?uploaded=1">Realizados</a></div></td>
                                                                </tr>
                                                                <tr>
                                                                  <td><div align="left"><a href="subirCarrito.php?uploaded=0,2">Pendientes y fallidos </a></div></td>
                                                                </tr>
                                                            </table></td>
                                                            <td><p align="right"><strong>
                                                                <input name="buscar" type="hidden" id="buscar" value="1">
                                                              Buscar cliente:</strong>
                                                                    <input name="txtBuscar" type="text" id="txtBuscar">
                                                              &nbsp; &nbsp; <a href="#" id="linkBuscar" onClick="javascript:conslink2()"><img src="images/k.gif" width="32" height="18"></a></p>
                                                                <p align="right">
                                                              
                                                              <p align="right">
                                                                  <label></label>
                                                              </p></td>
                                                          </tr>
                                                        </table>
													    <p>
                                                        <label>
                                                        <div align="left">
                                                        <?php if (isset($_GET['buscar'])){?>
                                                          Resultados de la busqueda: <?php echo $_GET['buscar'] ?>
  <?php }?>
                                                        </label>
                                                        <br>
                                                        <label></label>
                                                      </form>
													  <p align="center"> <?php if( mysql_num_rows($rsActualizaciones) <=0 ){?>
													  <div align="center">
                                                                    <div align="center">Su búsqueda no produjo ningún resultado.
                                                                    </div>
                                                                    <p>
																  	    <?php }else{ ?>
										                <p align="center">Para realizar los pedidos,debe contar con conexion al internet.
										                
										                <table border="0" width="50%" align="center" class="result">
                                                      <tr>
                                                        <td width="23%" align="center"><?php if ($pageNum_rsActualizaciones > 0) { // Show if not first page ?>
                                                              <a href="<?php printf("%s?pageNum_rsActualizaciones=%d%s", $currentPage, 0, $queryString_rsActualizaciones); ?>">Primero</a>
                                                              <?php } // Show if not first page ?>
                                                        </td>
                                                        <td width="31%" align="center"><?php if ($pageNum_rsActualizaciones > 0) { // Show if not first page ?>
                                                              <a href="<?php printf("%s?pageNum_rsActualizaciones=%d%s", $currentPage, max(0, $pageNum_rsActualizaciones - 1), $queryString_rsActualizaciones); ?>">Anterior</a>
                                                              <?php } // Show if not first page ?>
                                                        </td>
                                                        <td width="23%" align="center"><?php if ($pageNum_rsActualizaciones < $totalPages_rsActualizaciones) { // Show if not last page ?>
                                                              <a href="<?php printf("%s?pageNum_rsActualizaciones=%d%s", $currentPage, min($totalPages_rsActualizaciones, $pageNum_rsActualizaciones + 1), $queryString_rsActualizaciones); ?>">Siguiente</a>
                                                              <?php } // Show if not last page ?>
                                                        </td>
                                                        <td width="23%" align="center"><?php if ($pageNum_rsActualizaciones < $totalPages_rsActualizaciones) { // Show if not last page ?>
                                                              <a href="<?php printf("%s?pageNum_rsActualizaciones=%d%s", $currentPage, $totalPages_rsActualizaciones, $queryString_rsActualizaciones); ?>">Ultimo</a>
                                                              <?php } // Show if not last page ?>
                                                        </td>
                                                      </tr>
                                                    </table>
                                                      <form name="form1" method="post" action="subirCarrito.php?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                                        <table BORDER=1 FRAME=BOX RULES=NONE> 
                                                                <tr>
                                                                  <th>Cliente</th>
                                                                  <th>Fecha de solicitud</th>
                                                                  <th>Estado</th>
                                                                  <td></td>
                                                                  <td></td>
                                                                  <td></td>
                                                                  <td></td>
                                        </tr>
                                                                <?php $ac=0;
																do { ?>
                                                                <tr>
                                                                  <td <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>><div align="center"><?php echo $row_rsActualizaciones['cnom']; ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php echo date("D d M Y H:i:s" , strtotime($row_rsActualizaciones['fechaCompra'])); ?></div></td>
                                                                  <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
                                                                    <?php 
																	$uploaded = $row_rsActualizaciones['uploaded'];
						if( isset($_POST['rp']) && isset($_POST['chkAccion'][$row_rsActualizaciones['id']]) && $arrval[$row_rsActualizaciones['id']]==1) 
																	$uploaded=1;
																	
																  		  switch($uploaded){
																		  case 0: echo "Pendiente"; break;
    																	  case 1: echo '<span class="style11">Realizado</span>'; break;
																		  case 3: echo '<span class="style5">Fallido</span>'; break; 
																		  } ?>
                                                                  </div></td>
                                                                  <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><a href="subirCarrito.php?detalles=1&idPedido=<?php echo $row_rsActualizaciones['id'] ?>&idIndexCarrito=<?php echo $row_rsActualizaciones['idIndexCarrito'] ?>"><img src="images/esp/button_details.gif" width="63" height="19"></a></div></td>
                                                                  <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php if ($uploaded!=1){ ?>
																  <a href="subirCarrito.php?<?php   $arr=array('indice','pageNum_rsActualizaciones','totalRows_rsActualizaciones','eliminar','idPedido','detalles'); 
		$cad = getExclusivo($arr);
		echo $cad . "&" ;  ?>eliminar=1&idPedido=<?php echo  $row_rsActualizaciones['id'] ?>" onClick="javascript:
		
		if(confirm('¿Esta seguro que desea eliminar el pedido?'))
			return true;
			else
				return false;">
				<img src="images/esp/button_delete.gif" width="64" height="19"></a><?php }?></div></td>
                                                                  <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><label>
                                                                    
                                                                  <div align="center">
                                                                    <?php if ($uploaded!=1 ){ ?><input name="chkAccion[<?php echo $row_rsActualizaciones['id'] ?>]" type="checkbox" id="chkAccion[<?php echo $row_rsActualizaciones['id'] ?>]" value="<?php echo $row_rsActualizaciones['id'] ?>"><?php }?>
                                                                  </div>
                                                                  </label></td>
                                                                  <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><?php if( isset($_POST['rp']) && isset($_POST['chkAccion'][$row_rsActualizaciones['id']])){?>
																   <div align="center">
																     <?php 
																  if ($arrval[$row_rsActualizaciones['id']]==1){		  
																  ?>
																     <img src="images/ok.png" width="16" height="16"><?php }else{?>
																   <img src="images/cancel.png" width="16" height="16"><?php }?>																	  </div>
																   <?php }?></td>
                                                                </tr>
                                                                <?php } while ($row_rsActualizaciones = mysql_fetch_assoc($rsActualizaciones)); ?>
                                </table>
                                  <p align="center">
                                    <label>
                                    <input name="rp" type="hidden" id="rp" value="1">
                                    <input name="realp" type="submit" id="realp" value="Realizar pedidos">
									</label>
									</p>
                                  <p align="center">
                                    <?php if ( !isset($_GET['uploaded']) ||  $_GET['uploaded']!=1) {?>
                                    <input name="marcar" type="button" id="marcar" value="Marcar todos" onClick="seleccionar_todo()">
                                    <input name="marcar" type="button" id="marcar" value="Desmarcar todos" onClick="deseleccionar_todo()">
                                    
                                     <?php }?>
                                  </p>
                                  <p align="left"><?php }?>&nbsp; </p>
                                  <p align="center">&nbsp;</p>
                                                      </form>
                                  <p align="center"><br>
                                  </p>
                                               	  </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>

                                          </table>
										  
	                                      </td>
                                        </tr>
										
										<tr>
										  <td><p><?php }?>
										    <?php if (isset($_GET['detalles']) && $_GET['detalles']==1){ ?>
											
										      <br style="line-height:9px">
										  <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" style="width:700px">
                                                <tr>
                                                  <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                  <td style="width:100%" class="cont_body_tall_t"></td>
                                                  <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                </tr>
                                                <tr>
                                                  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table"><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                                                            <tr>
                                                        <td ><p class="cont_heading_td">detalle de compras 
                                                          <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                                  <tr>
                                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                                  </tr>
                                                          </table>
                                                        </td>
                                                      </tr>
                                                    </table>
                                                      <br style="line-height:9px">
                                                     
                                                    <table border="0" cellspacing="0" cellpadding="0" class="cont_heading_table">
                                                        <tr>
                                                          <td><p><a href="<?php 
														 echo "subirCarrito.php?" . $_SESSION['postback'] ?>">Regresar</a>
                                                          
                                                            <table border="0" width="50%" align="center" class="result">
                                                                <tr>
                                                                  <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                      <?php } // Show if not first page ?>                                                                  </td>
                                                                  <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                      <?php } // Show if not first page ?>                                                                  </td>
                                                                  <td width="23%" align="center" class="result"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults" >Siguiente</a>
                                                                      <?php } // Show if not last page ?>                                                                  </td>
                                                                  <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                      <?php } // Show if not last page ?>                                                                  </td>
                                                                </tr>
                                                            </table>
                                                            <p>
                                                              <?php if ($_SESSION['tipoUsuario']==1) {
															 
														 ?>
														  <table border="0">
  <tr>
    <td width="29%">Nombre del vendedor: </td>
    <td width="71%"><?php echo $row_rsdet1['vnom'] ?></td>
  </tr>
  <tr>
    <td><p>Id del vendedor: </p>      </td>
    <td><?php echo $row_rsdet1['vid'] ?></td>
  </tr>
  <tr>
    <td>Nombre dei cliente: </td>
    <td><?php echo $row_rsdet1['cnom'] ?></td>
  </tr>
  <tr>
    <td>Id del cliente: </td>
    <td><?php echo $row_rsdet1['cid'] ?></td>
  </tr>
  <?php if ( $row_rsdet1['idCliente']==$usuarioNuevoCliente){ ?>
  <tr>
    <td>Informacion del nuevo cliente:</td>
    <td><?php echo $row_rsdet1['nuevoCliente']  ?></td>
  </tr><?php }?>
  <tr>
    <td>Fecha y hora del pedido </td>
    <td><?php echo  date("D d M Y H:i:s" ,strtotime($row_rsdet1['fechaCompra'])) ?></td>
  </tr>
</table>

                                                              <p>
                                                              
                                                              <table BORDER=1 FRAME=BOX RULES=NONE>
                                                                <tr>
                                                                  <th width="14%">Id del producto </th>
                                                                  <th width="14%">Producto</th>
                                                                  <th width="17%">Cantidad en existencia </th>
                                                                  <th width="18%"><div align="center">Precio unitario </div></th>
                                                                  <th width="15%">Cantidad deseada </th>
                                                                  <th width="19%">Subtotal</th>
                                                                </tr>
                                                                <?php 
																$ac=0;
																$total=0;
																do { ?>
                                                                <tr>
                                                                  <td   <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>><div align="center"><?php echo $row_rsdet2['cod_item']?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php echo $row_rsdet2['des_item'] ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php 
																  if ($row_rsdet2['cantidad'] > $row_rsdet2['cant_stock']){
																  $flagSubirPedido=0;
																  echo "<span class='style5'>" .$row_rsdet2['cant_stock']. "</span>";
																  }
																  else echo $row_rsdet2['cant_stock'];
																  	  ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo number_format($row_rsdet2['precioUnitario'])  ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php 
																   if ($row_rsdet2['cantidad'] > $row_rsdet2['cant_stock'])
																  echo "<span class='style5'>".$row_rsdet2['cantidad']. "</span>";
																  else
																  	echo $row_rsdet2['cantidad'];?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo number_format($row_rsdet2['precioUnitario'] * $row_rsdet2['cantidad']); $total +=  $row_rsdet2['precioUnitario'] * $row_rsdet2['cantidad'] ?>&nbsp;</div></td>
                                                                </tr>
                                                                <?php } while ($row_rsdet2 = mysql_fetch_assoc($rsdet2)); ?>
                                                                <?php }?>
                                                            </table>
                                                              <label></label><br>
                                                              <table border="0">
                                                                <tr>
                                                                  <td width="24%">&nbsp;</td>
                                                                  <td width="76%"><table border="0">
                                                                      <tr>
                                                                        <td width="46%">&nbsp;</td>
                                                                        <td width="54%"><div align="left">
                                                                            <table border="0">
                                                                              <tr>
                                                                                <td><div align="right" class="style10">Subtotal:</div></td>
                                                                                <td><div align="right" class="style10">₵<?php echo number_format($total) ?></div></td>
                                                                              </tr>
                                                                              <tr>
                                                                                <td><div align="right" class="style10">Impuestos:</div></td>
                                                                                <td><div align="right" class="style10">₵<?php echo number_format($total*$impuesto) ?></div></td>
                                                                              </tr>
                                                                              <tr>
                                                                                <td><span class="style10"></span></td>
                                                                                <td><hr style="height:1px"></td>
                                                                              </tr>
                                                                              <tr>
                                                                                <td><div align="right" class="style10">Total:</div></td>
                                                                                <td><div align="right" class="style10">₵<?php echo number_format($total+($total*$impuesto)) ?></div></td>
                                                                              </tr>
                                                                            </table>
                                                                        </div></td>
                                                                      </tr>
                                                                  </table></td>
                                                                </tr>
                                                              </table>
                                                              <p>Mostrando <?php echo mysql_num_rows($rsdet2) ?> (de <?php echo $totalRows_rsdet2 ?>)</p>
                                                              <table border="0" width="50%" align="center" class="result">
                                                                <tr>
                                                                  <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                      <?php } // Show if not first page ?>                                                                  </td>
                                                                  <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                      <?php } // Show if not first page ?>                                                                  </td>
                                                                  <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults" >Siguiente</a>
                                                                      <?php } // Show if not last page ?>                                                                  </td>
                                                                  <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                      <?php } // Show if not last page ?>                                                                  </td>
                                                                </tr>
                                                            </table>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td><p>&nbsp;</p>
                                                          <p align="center"><?php if ($flagSubirPedido==0){ ?>
                                                            <span class="style5">El pedido no pudo ser llevado a cabo. La cantidad de productos requeridos excede a la existente.</span>
                                                          <?php }?></p>
                                                          <p align="center">&nbsp;</p></td>
                                                        </tr>
                                                    </table>
                                                    <br>
                                                      <table cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                          <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                        </tr>
                                                      </table>
                                                    <table border="0" cellspacing="0" cellpadding="0" class="result">
                                                        <tr>
                                                          <td class="result_right">                                                
                                                      </tr>
                                                    </table></td>
                                                </tr>
                                                <tr>
                                                  <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                  <td style="width:100%" class="cont_body_tall_b"></td>
                                                  <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                </tr>
                                          </table><br style="line-height:9px">
								          <?php }?></td>
										</tr>
                                    </table>                           </td>
									</tr><tr>
                                          <td>&nbsp;</td>
                                        </tr>
                                    </table>                              </td>
                            </tr>
                        </table>                  </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" style="height:115px;" class="footer">
                            <tr>
                                <td><br style="line-height:66px"><a href="#"></a><br></td>
                                <td style="width:100%" align="right">
                                    <br style="line-height:62px">
                                   <span><a href="index.php?estado=0">Productos Nuevos &nbsp; </a></span><span>|</span><span><a href="index.php?estado=1">&nbsp;&nbsp;Ofertas especiales&nbsp;&nbsp;</a></span>|<span>&nbsp;&nbsp;<a href="contactenos.php">Contáctenos</a></span><br>
                                    <br style="line-height:5px">
                                   El Mago Don Eloy <?php echo date("Y")  ?><br>                                </td>							
                            </tr>
                        </table>                  </td>
                </tr>
            </table>
      </td>
	</tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsActualizaciones);

mysql_free_result($rsofaz);

//mysql_free_result($rsCliente);

//mysql_free_result($rsCat2);
?>









