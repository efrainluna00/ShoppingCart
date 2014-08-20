<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}

function myIsInt ($x) {
    return (is_numeric($x) ? intval($x) == $x : false);
}


//CUANDO EL AGENTE SELECCIONA UN CLIENTE
if ( isset($_GET['idCliente']) && $_GET['agenteSelectCliente']==1 && isset($_SESSION['tipoUsuario']) ){ 
	$_SESSION['idClienAgen'] = $_GET['idCliente'];
	$_SESSION['nombreClienAgen'] = $_GET['nombreCliente'];
	$_SESSION['tipo_pre'] = $_GET['tipo_pre'];

		}


if (isset($_SESSION['tipoUsuario'])){
if ($_POST['actCant']==1 && $_POST['txtCantidad'] > 0 && ($_POST['stock'] >= $_POST['txtCantidad'] )  && myIsInt($_POST['txtCantidad']) ){
	$_SESSION['ccIdProdCant'][$_POST['idItem']] =  $_POST['txtCantidad'];
	}
	
if ($_GET['eliminar']==1){
	$pos = $_GET['idItem'];
	unset($_SESSION['ccIdProdCant'][$pos] );
	unset($_SESSION['ccIdProd'][$pos]);
	$_SESSION['cci'] -= 1;
	
}

if ($_GET['vaciarCarrito']==1){
$_SESSION['ccIdProd'] = "";
$_SESSION['ccIdProdCant']="";
$_SESSION['cci'] = 0;
}
}


mysql_select_db($database_conn, $conn);
$query_rsCat = "SELECT cod_cate,categoria FROM CATEGORI where eliminado<>1";
$rsCat = mysql_query($query_rsCat, $conn) or die(mysql_error());
$row_rsCat = mysql_fetch_assoc($rsCat);
$totalRows_rsCat = mysql_num_rows($rsCat);

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
 and ie.eliminado<>1 ORDER BY RAND() LIMIT 0,1";
$rsofaz = mysql_query($query_rsofaz, $conn) or die(mysql_error());
$row_rsofaz = mysql_fetch_assoc($rsofaz);
$totalRows_rsofaz = mysql_num_rows($rsofaz);

$maxRows_rsProd = $regsxPag;
$pageNum_rsProd = 0;
if (isset($_GET['pageNum_rsProd'])) {
  $pageNum_rsProd = $_GET['pageNum_rsProd'];
}
$startRow_rsProd = $pageNum_rsProd * $maxRows_rsProd;


mysql_select_db($database_conn, $conn);
if($_SESSION['cci']>0){
$idsCarr = implode(',', $_SESSION['ccIdProd']);
$query_rsProd = "SELECT * FROM ITEMS WHERE cod_item IN($idsCarr) ORDER BY des_item";


 switch($_SESSION['tipo_pre']){
						 	case 1:  $price = 'precio';
							break;
							case 2:    $price = 'precio2';
							break;
							case 3:    $price = 'precio3';
							break;																					
							case 4:    $price = 'precio4';
							break;
											}
$rsSuma = mysql_query("SELECT $price as precioUnitario, cod_item
FROM ITEMS
WHERE cod_item
IN ($idsCarr) GROUP BY cod_item ORDER BY des_item", $conn) or die(mysql_error());

}
else
$query_rsProd = "SELECT * FROM ITEMS ORDER BY des_item";

$query_limit_rsProd = sprintf("%s LIMIT %d, %d", $query_rsProd, $startRow_rsProd, $maxRows_rsProd);
$rsProd = mysql_query($query_limit_rsProd, $conn) or die(mysql_error());
$row_rsProd = mysql_fetch_assoc($rsProd);
 $all_rsProd = mysql_query($query_rsProd);
  $totalRows_rsProd = mysql_num_rows($all_rsProd);

if (isset($_GET['totalRows_rsProd'])) {
  $totalRows_rsProd = $_GET['totalRows_rsProd'];
} else {
  $all_rsProd = mysql_query($query_rsProd);
  $totalRows_rsProd = mysql_num_rows($all_rsProd);
}
$totalPages_rsProd = ceil($totalRows_rsProd/$maxRows_rsProd)-1;

$queryString_rsProd = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsProd") == false && 
        stristr($param, "totalRows_rsProd") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsProd = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsProd = sprintf("&totalRows_rsProd=%d%s", $totalRows_rsProd, $queryString_rsProd);


/*
mysql_select_db($database_conn, $conn);
if (isset($_GET['cat']))
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE id=" . $_GET['cat'];
else
$query_rsCat2 = "SELECT * FROM CATEGORI ORDER BY RAND()";
$rsCat2 = mysql_query($query_rsCat2, $conn) or die(mysql_error());
$row_rsCat2 = mysql_fetch_assoc($rsCat2);
$totalRows_rsCat2 = mysql_num_rows($rsCat2);
$catLocal = mysql_result($rsCat2,0,id);
*/

function obtenerCotizacion($r1){
$total = 0;
	$tam = mysql_num_rows($r1);
	for($i=0; $i < $tam; $i++)
	{
	$pu = mysql_result($r1,$i,precioUnitario);
	$idP =  mysql_result($r1,$i,cod_item);
		$subtotal = $pu * $_SESSION['ccIdProdCant'][$idP];
		$total+= $subtotal;
	}
	return $total;
}

if (isset($_SESSION['tipoUsuario'])){



if ($_POST['realizarPedido']==1 && isset($_SESSION['tipoUsuario']) ){
mysql_select_db($database_conn, $conn);

$arr = implode(',', $_SESSION['ccIdProd']);
switch($_SESSION['tipo_pre']){
	case 1: $cadPrecio= "precio"; break;
	case 2:  $cadPrecio= "precio2"; break;
	case 3:  $cadPrecio= "precio3"; break;
	case 4:  $cadPrecio= "precio4"; break;
}

$qvi = "SELECT cod_item,cant_stock,des_item, $cadPrecio as prize FROM ITEMS WHERE cod_item IN ($arr) ORDER BY des_item";
$rsqvi = mysql_query($qvi, $conn) or die(mysql_error());

$tam = mysql_num_rows($rsqvi);
$i=0;
$flagPedido = 1;
   do{ 
  if (mysql_result($rsqvi,$i,cant_stock) < $_SESSION['ccIdProdCant'][mysql_result($rsqvi,$i,cod_item)])
													$flagPedido = 0;
													$arrCant[$i] = $_SESSION['ccIdProdCant'][mysql_result($rsqvi,$i,cod_item)];
													
						$i++;										
      }while ($i < $tam); 
	  //echo $qvi;
	  
	 


	if($_SESSION['tipoUsuario']==0 || $_SESSION['tipoUsuario']==2  )
		$VENDEDOR = $usuarioInternet;
		else
			$VENDEDOR = $_SESSION['idUsuario'];
			//Si esta conectado al internet
			// NOTA CAMBIAR EL ID DEL CLIENTE... DESPUES
			if($flagPedido==1){
			$rsProd = mysql_query($query_rsProd, $conn) or die(mysql_error());
			if($_SESSION['conexion']==0){
			mysql_select_db($database_conn, $conn);
			mysql_query("INSERT INTO INDEX_CARRITO VALUES ('NULL')") or die(mysql_error());
			$idCarrito = mysql_insert_id();
			
	$tam = $totalRows_rsProd;
	for($i=0; $i < $tam; $i++)
	{
	$idItem = mysql_result($rsProd,$i,cod_item);
	$precioU = mysql_result($rsSuma,$i,precioUnitario);
	$cantidad = $_SESSION['ccIdProdCant'][$idItem];
		$idClienAgen = $_SESSION['idClienAgen'];
	
	mysql_select_db($database_conn, $conn);
	$ins = "INSERT INTO DETALLE_CARRITO VALUES ('NULL', '$idItem', '$precioU', '$cantidad', '$idCarrito')";
		mysql_query($ins, $conn) or die(mysql_error());
	// NOTA CAMBIAR EL ID DEL CLIENTE... DESPUES
		
	unset($_SESSION['ccIdProdCant'][$idItem] );
	unset($_SESSION['ccIdProd'][$idItem]);
	
	//echo "$ins<br>";
	}
	if(isset($_SESSION['nombreNuevoCliente']))  $idClienAgen = $usuarioNuevoCliente;
	$ins = "INSERT INTO PEDIDOS VALUES ('NULL','$VENDEDOR','$idClienAgen',TIMESTAMP('". date("Y-m-d H:i:s") ."'),'$idCarrito','0','NULL','NULL','";
	if(isset($_SESSION['nombreNuevoCliente'])) $ins .= "Nombre: " . $_SESSION['nombreNuevoCliente'] . ". " .  $_SESSION['infoNuevoCliente'] . "')";
	else
		$ins .= "NULL')";
	mysql_query($ins, $conn) or die(mysql_error());
		$_SESSION['cci'] = 0;
		$_SESSION['ccIdProdCant'] ="";
		$_SESSION['ccIdProd'] = "";

	
}
else{
		//include('../Connections/conn2.php'); 
			mysql_select_db($database_conn, $conn);
			mysql_query("INSERT INTO INDEX_CARRITO VALUES ('NULL')") or die(mysql_error());
			$idCarrito = mysql_insert_id();
			
	$tam = mysql_num_rows($rsProd);
	for($i=0; $i < $tam; $i++)
	{
	$idItem = mysql_result($rsProd,$i,cod_item);
	$precioU = mysql_result($rsSuma,$i,precioUnitario);
	$cantidad = $_SESSION['ccIdProdCant'][$idItem];
	$idClienAgen = $_SESSION['idClienAgen'];
	
	
	mysql_select_db($database_conn, $conn);
	$ins = "INSERT INTO DETALLE_CARRITO VALUES ('NULL', '$idItem', '$precioU', '$cantidad', '$idCarrito')";
		mysql_query($ins, $conn) or die(mysql_error());
	// NOTA CAMBIAR EL ID DEL CLIENTE... DESPUES
	unset($_SESSION['ccIdProdCant'][$idItem] );
	unset($_SESSION['ccIdProd'][$idItem]);
	
	//echo "$ins<br>";
	}
	if(isset($_SESSION['nombreNuevoCliente']))  $idClienAgen = $usuarioNuevoCliente;
	$ins = "INSERT INTO PEDIDOS VALUES ('NULL','$VENDEDOR','$idClienAgen',TIMESTAMP('". date("Y-m-d H:i:s") ."'),'$idCarrito','0','NULL','NULL','";
		if(isset($_SESSION['nombreNuevoCliente'])) $ins .=  "Nombre: " . $_SESSION['nombreNuevoCliente'] . ". " .  $_SESSION['infoNuevoCliente'] . "')";
	else
		$ins .= "NULL')";
	
		mysql_query($ins, $conn) or die(mysql_error());
		$_SESSION['cci'] = 0;
	
	
}
}

}//END REALIZAR PEDIDO


}

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
<script type="text/javascript" src="funciones.js"></script>
<script language="javascript">
var linkRed;
linkRed = getCookie('linkRed');


function conslink(enlac,opc) {
	if(opc==1){
	var cuantos = document.getElementById('s1').selectedIndex;
	document.getElementById('link1').href = enlac + "&numProd=" + document.getElementById('f1').s1.options[cuantos].value;
	
	}
	else{
	var cuantos = document.getElementById('s2').selectedIndex;
	document.getElementById('link2').href = enlac + "&numProd=" + document.getElementById('f2').s2.options[cuantos].value;
	
	}
	
}
		
		
function ef(url){
	if(confirm('¿Desea eliminar este producto del carrito?')) {
		document.location.href=  url;
	} 
		else return false;
	}
function ef2(url){
	if(confirm('¿Desea eliminar el contenido del carrito?')) {
		document.location.href=  url ;
	} 
	else return false;

}

function ef3(url){
	if(confirm('¿Desea llevar a cabo el pedido?')) {
		document.location.href=  url + "?realizarPedido=1";
		}
		else {
		document.location.href=  url + "?realizarPedido=0";
		}
}
</script>
<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style5 {color: #FF0000}
.style7 {font-size: 12; }
.style9 {font-size: 14px; }
.style10 {font-size: 13px; }
-->
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" class="w">
	<tr>
	  <td style="width:100%"><table cellpadding="0" cellspacing="0" border="0" style="width:716px" align="center"> 
                <tr>
                    <td align="center">
                        <table cellpadding="0" cellspacing="0" border="0" >
                            <tr>
                                <td height="353" align="left"><table cellpadding="0" cellspacing="0" border="0" >
                                      
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
                                  <table cellpadding="0" cellspacing="0" border="0">
                                      <tr>
                                        <td width="32%"><a href="index.php"><img src="images/esp/m1.jpg" alt="" border="0"></a><br>
                                          <a href="index.php?estado=0"><img src="images/esp/m2.jpg" alt="" border="0"></a><br>
                                          <a href="index.php?estado=1"><img src="images/esp/m3.jpg" alt="" border="0"></a><br>
                                          <a href="login.php<?php if (isset($_SESSION['logged'])) echo "?logout=1" ?>"><img src="images/esp/m4.jpg" alt="" border="0"></a><br>
                                          <a href="contactenos.php"><img src="images/esp/m5.jpg" alt="" border="0"></a><br><br style="line-height:12px"></td>
									    <td><br></td>
                                        <td width="68%" align="center"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="470" height="235">
                                          <param name="movie" value="images/flash/anima.swf">
                                          <param name="quality" value="high">
                                          <embed src="images/flash/anima.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="470" height="235"></embed>
                                        </object>
                                        <br></td>
                                      </tr>
                              </table>                              </td>
                            </tr>
                        </table>                  </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" style="height:583px ">
                            <tr>
                              <td class="col_left"><table cellpadding="0" cellspacing="0" border="0" class="box_width_left">
									  <tr>
									<td><?php if($_SESSION['tipoUsuario']==3){?> 
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
													  <li class="bg_list"><a href="verPedidos.php">Listado de pedidos</a><br style="line-height:9px">
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
													 <li class="bg_list"><a href="subirCarrito.php">Ver pedidos pendientes</a></li>
													<li class="bg_list"><a href="carritoForm.php">Formulario carrito</a></li>
													  <?php if($_SESSION['conexion']==1){ ?> <li class="bg_list"><a href="actualizarBD.php">Cargar base de datos</a></li><?php }?>
													 <?php if($_SESSION['conexion']==0){ ?> <li class="bg_list"><a href="<?php echo $ftpserver ?>">Descargar archivos </a></li><?php }?>
													  <li class="bg_list"><br style="line-height:9px">
												      </li>
													  <div align="center">
													    <?php if($_SESSION['idClienAgen']!=-1){?>
												      </div>
													  <li class="bg_list">
													    <div align="left"><span class="style7">Cliente:<br> 
										                <?php echo $_SESSION['nombreClienAgen']?> </span><br style="line-height:9px">
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
										
                                            <td><p><?php  if( (!isset($_SESSION['tipoUsuario']) || $_SESSION['cci']<=0) && !isset($_POST['realizarPedido']) ) {?>
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
											  <td width="95%"> <?php 	if(!isset($_SESSION['tipoUsuario'])) {?><p align="center">Favor iniciar sesión. Para iniciar sesion haga click <a href="login.php">aqui</a>.<br>
Registrese <a href="registrarUsuario.php">aqui</a></p>
															     
																  <?php } if(isset($_SESSION['cci']) && $_SESSION['cci'] ==0){?>  
																    <div align="center">
																      <p>&nbsp;</p>
																      <p>No hay ningún item en el carrito. </p>
															    </div>										      </td><?php }?>

                                                        </tr>
</table>
<table>
   <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td width="1%" class="cont_body_tall_b" style="width:100%"></td>
                                                        <td width="4%"><img src="images/cont_corn_br.gif" alt=""></td>
                                      </tr></table>      </td>
                                                    </tr>
                                                 </table
                                                 ></td>
                                                    </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                         </table><?php exit;}?>
                                         <?php if(isset($_SESSION['tipoUsuario']) && isset($_POST['realizarPedido'])  ){
										  $flagPedido=1;
										  ?>
                                              <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" style="width:400px">
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
													<tr>
                                                    	<td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table"><p><span class="cont_heading_td">detalle de compra</span>
                                                    	  <p>
                                                  	    
                                                    	  <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> 
														  <table border="0">
                                                            <tr>
                                                              <td>Cliente:<?php if ($_SESSION['tipoUsuario']==0 || $_SESSION['tipoUsuario']==2){ ?>
															  <?php echo $_SESSION['nombreUsuario'] ?>															  
															  <?php }?>
															  <?php if ($_SESSION['tipoUsuario']==1 || $_SESSION['tipoUsuario']==4){ ?>
															  <?php echo $_SESSION['nombreClienAgen']?>															  															  <?php }?>															  </td>
                                                            </tr>
                                                            <tr>
                                                              <td>Vendedor:<?php if ($_SESSION['tipoUsuario']==0 || $_SESSION['tipoUsuario']==2){ ?>
															  INTERNET													  
															  <?php }?>
															  <?php if ($_SESSION['tipoUsuario']==1 || $_SESSION['tipoUsuario']==4){ ?>
															  <?php echo $_SESSION['nombreUsuario']?>															  															  <?php }?></td>
                                                            </tr>
                                                          </table>
                                                    	  <p>                                          </p>
                                                    	  <table BORDER=1 FRAME=BOX RULES=NONE style="width:400px"> 
                                                            <tr>
															 <?php if ($_SESSION['tipoUsuario']==1){ ?>
															   <th><div align="center">Producto</div></th>
															  <?php }?>
                                                              <th>Codigo producto</th>
															  <th>Cantidad deseada </th>
															  <?php if ($_SESSION['tipoUsuario']==1){ ?>
															  <th>Cantidad existente </th>
															  <?php }?>
                                                              <th><div align="center">Precio</div></th>
                                                              <th>Estatus</th>
                                                              <th>Subtotal</th>
                                                            </tr>
                                                           <?php $ac=0;
														   $subtotal = 0;
														   $total =0;
														   $tam = mysql_num_rows($rsqvi);
														   $i=0;
														   
														   do{ ?> 
                                                             <tr>
															
															    <td <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>>
                                                             <div align="center"><?php echo mysql_result($rsqvi,$i,des_item)?></div></td>
															   <?php if ($_SESSION['tipoUsuario']==1){ ?>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>>
															     <div align="center"><?php echo mysql_result($rsqvi,$i,cod_item)?>
													             </div></td>
															  <?php }?>
															 
															  
															  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php  if (mysql_result($rsqvi,$i,cant_stock) < $arrCant[$i] )
																echo "<div class='style5'>".$arrCant[$i] ."</div>";																			
																else
																echo $arrCant[$i];  ?></div></td>
															  
															    <?php if ($_SESSION['tipoUsuario']==1){ ?>
															    <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
																<?php 
															  if (mysql_result($rsqvi,$i,cant_stock) < $arrCant[$i] )
																echo "<div class='style5'>".mysql_result($rsqvi,$i,cant_stock) ."</div>";																			
																else
																echo mysql_result($rsqvi,$i,cant_stock) ?></div></td>
															   
															   <?php }?>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo  number_format(mysql_result($rsqvi,$i,prize))?></div></td>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
															     <?php 
															   if (mysql_result($rsqvi,$i,cant_stock) < $arrCant[$i] ){
															   echo "<div class='style5'><img src='images/cancel.png'>La cantidad solicitada excede a la del inventario</div>";						
															   $flagPedido = 0;
															   } else{
															   	echo "<img src='images/ok.png'>Ok";
																
																}
															
																?>
														       </div></td>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo number_format($arrCant[$i]* mysql_result($rsqvi,$i,prize)); $subtotal = $subtotal + ($arrCant[$i]* mysql_result($rsqvi,$i,prize))  ?></div></td>
                                                             </tr>
                                                            <?php 	$i++;
															}while ($i < $tam);  ?>
                                                          </table><?php if ($flagPedido==1){?>
                                                    	  <table border="0">
                                                            <tr>
                                                              <td width="24%">&nbsp;</td>
                                                              <td width="76%"><table border="0">
                                                                  <tr>
                                                                    <td width="46%">&nbsp;</td>
                                                                    <td width="54%"><div align="left"><br>
                                                                    </div>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><div align="right" class="style10">Subtotal:</div></td>
                                                                            <td><div align="right" class="style10">₵<?php echo number_format($subtotal) ?></div></td>
                                                                          </tr>
                                                                          <tr>
                                                                            <td><div align="right" class="style10">Impuestos:</div></td>
                                                                            <td><div align="right" class="style10">₵<?php echo number_format($subtotal*$impuesto) ?></div></td>
                                                                          </tr>
                                                                          <tr>
                                                                            <td><span class="style10"></span></td>
                                                                            <td><hr class="style10" style="height:1px"></td>
                                                                          </tr>
                                                                          <tr>
                                                                            <td><div align="right" class="style10">Total:</div></td>
                                                                            <td><div align="right" class="style10">₵<?php echo number_format($subtotal+($subtotal*$impuesto)) ?></div></td>
                                                                          </tr>
                                                                      </table></td>
                                                                  </tr>
                                                              </table></td>
                                                            </tr>
                                                          </table><?php }?>
                                                       	  <p align="center" class="style5"><?php if ($flagPedido==0){?> Su orden de compra no pudo ser efectuada.</p>
                                                       	  <p align="center" class="style5">Recomendacion: Ver en el detalle de compras que la cantidad de producto deseada no exceda a la existente. De ser asi, volver a realizar el pedido, reduciendo la cantidad o eliminando el producto que exceda el existente. </p>
                                                       	  <p align="center" class="style5">
<?php }else{?>
                                                                                    	    
                                                                                    	    La petición de compra ha sido añadida. Gracias por su preferencia!
    <?php }?>
                                                                                  	                                    </p>
                                                                                    	  <p align="center">
                                                    	    <?php if ($flagPedido==1) {?>
                                                    	    Para realizar mas compras haga click <a href="index.php">aqui</a> 
                                                    	    <?php } else{?>
                                                    	    Para regresar al carrito de compras haga click <a href="carrito.php">aqui</a>
                                                    	    <?php } ?>
                                                    	  </p>
                                                    	  <p align="center">&nbsp;</p>
                                                    	  <p><br style="line-height:9px">
                                       	                  </p>
                                                   	  </td>
													</tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                              </table>
                                              <p><?php }?>
                                                <?php if(isset($_SESSION['tipoUsuario']) && $_SESSION['cci']>0 && !isset($_POST['realizarPedido']) ) {?>
                                              </p>
                                              <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table"  style="width:<?php echo $pancho?>">
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
													
												

												
                                                    <tr>
													
                                   
								                   	  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table"><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
																<tr>
																  <td align="center"> <p>
																    <?php if ( isset($_GET['eliminar']) && $_GET['eliminar']==1){?>
																    </p>
																    <p><span class="style5">El producto fue removido de la carretilla exitosamente</span></p>
																    <p>
																      <?php }?>
																    </p>
																    </td>
																</tr>
                                                                <tr>
																  <td class="cont_heading_td">contenido del carrito </td>
                                                                </tr>
                                                        </table> 
														
                                                          <br style="line-height:9px">
                                                          <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> 
                                                            <table border="0" cellspacing="0" cellpadding="0" class="result">
                                                              <tr>
                                                                <td><p align="right" >
																
<a href="#" onClick="javascript:document.location.href = linkRed">
Regresar al carrito de compras</a></p>
                                                                  <p>Mostrando <?php echo mysql_num_rows($rsProd) ?> (de <b><?php echo $totalRows_rsProd ?></b> productos)</p></td></tr>
																
																<tr>
                                                                <td class="result_right">
                                                                    <table border="0" width="50%" align="center">
                                                                                                                              <tr>
                                                                                                                                <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                                                                                      <?php } // Show if not first page ?>                                                                                                                                </td>
                                                                                                                                <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                                                                                      <?php } // Show if not first page ?>                                                                                                                                </td>
                                                                                                                                <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults" >Siguiente</a>
                                                                                                                                      <?php } // Show if not last page ?>                                                                                                                                </td>
                                                                                                                                <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                                                                                      <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                                                                                      <?php } // Show if not last page ?>                                                                                                                                </td>
                                                                                                                              </tr>
                                                                  </table>                                                                </td>
                                                          </tr>
                                                        </table> 
												            <p> <?php  /*}
															  else {*/
																	if (isset($_SESSION['tipoUsuario']) && $_SESSION['tipoUsuario']!=1){
																		$col=0;
			echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="product" style="width:33%; height:194px">';
																	do {  if($col==0)
																	 echo '<tr> ';	
																	 echo '<td bordercolor="#CCCCCC" bgcolor="#FFFFFF">';
																		?>
																		<a name="goTo<?php echo $row_rsProd['cod_item'] ?>"></a>
                                                        <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                        </table> 
                                                            <table cellpadding="0" cellspacing="0" border="0" class="product" style="width:<?php echo $colancho ?>">
                                                              <tr>
                                                                <td style="height:194px" align="center"><br style="line-height:9px" >
                                                                    <table>
                                                                      <tr>
                                                                        <td style="height:34px" class="vam" align="center"><span class="vam" style="height:52px"><span><a href="<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank"><?php echo $row_rsProd['des_item']; ?></a></span></span></td>
                                                                      </tr>
                                                                    </table>
                                                                  <table cellpadding="0" cellspacing="0" border="0" style="width:156px">
                                                                            <tr>
                                                                              <td><img src="images/pic_corn_tl.gif" alt="" border="0"></td>
                                                                              <td class="pic_corn_t"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                              <td><img src="images/pic_corn_tr.gif" alt="" border="0"></td>
                                                                            </tr>
																																					
                                                                            <tr>
                                                                              <td class="pic_corn_l"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
																			  
                                                                              <td class="image"><a href="<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank"><img src="<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
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
                                                                  </table>
                                                                  <table border="0">
                                                                      <tr>
                                                                        <td colspan="2" class="vam" style="height:19px"><div align="center"><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank">Click para agrandar</a></div></td>
                                                                      </tr>
                                                                      <tr>
                                                                    
                                                                         <td style="height:19px" class="vam" align="center"><span class="productSpecialPrice"><span class="vam" style="height:19px">₵
                                                                           <?php 
																		 switch($_SESSION['tipo_pre']){
																		 	case 1:  echo number_format($row_rsProd['precio']);
																						break;
																			case 2:  echo number_format($row_rsProd['precio2']);
																						break;
																			case 3:  echo number_format($row_rsProd['precio3']);
																						break;																					
																			case 4:  echo number_format($row_rsProd['precio4']);
																						break;
																		 }
																		 ?></span><img alt="" src="images/spacer.gif" width="10" height="1"><a href="#"></a></td>
                                                                      </tr>
                                                                      <tr>
                                                                        <td style="height:19px" class="vam" align="center"><form name="f3" method="post" action="carrito.php?<?php $arr = array('eliminar','vaciarCarrito');
							$cad = getExclusivo($arr);
							echo $cad;?>&actCant=1#goTo<?php echo $row_rsProd['cod_item'] ?>" id="f1">
                                                                          <label> Cantidad
                                                                          <input name="txtCantidad" type="text" size="2" id="txtCantidad" value="<?php echo $_SESSION['ccIdProdCant'][$row_rsProd['cod_item']] ?>">
																		
                                                                          <br>
                                                                          </label> 
																		   <input type="hidden" name="actCant" value="1">
																		    <input type="hidden" name="stock" value="<?php echo $row_rsProd['cant_stock']  ?>">
                                                                          <input type="hidden" name="idItem" value="<?php echo $row_rsProd['cod_item']  ?>">
                                                                          <label>
                                                                          <input type="image" name="imageField3" src="images/esp/button_update_cart1.gif" >
                                                                          </label>
                                                                          <a href="#" onclick="javascript:ef('carrito.php?eliminar=1&idItem=<?php echo  $row_rsProd['cod_item']?>');return false;" id="link1"><img src="images/esp/button_delete1.gif"></a>
                                                                        </form>                                                                                                                   <p>
                                                                          <?php if ($_POST['actCant']==1 && $_POST['idItem']==$row_rsProd['cod_item'] ){ ?>
                                                                          <?php if ($_POST['txtCantidad']>0 && ($_POST['txtCantidad'] <= $_POST['stock'])  && myIsInt($_POST['txtCantidad']) ){?>
</p>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                                <p align="center" class="style5">La modificacion ha sido llevada a cabo con exito </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <?php } if(  myIsInt($_POST['txtCantidad']) && $_POST['txtCantidad']<=0){?>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                                <p align="center" class="style5">No se hizo la modificacion, la cantidad debe ser mayor que cero </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <div align="center">
                                                                        <div align="center">
                                                                <?php }?>
																<?php  if ( !myIsInt($_POST['txtCantidad']) ){?>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                               <p align="center" class="style5">No se hizo la modificacion, la cantidad debe ser un numero entero mayor que cero </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <div align="center">
                                                                       
                                                                        <?php }?>
																		<?php if ( myIsInt($_POST['txtCantidad']) && ($_POST['stock'] < $_POST['txtCantidad']) ){?>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                               <p align="center" class="style5">No se hizo la modificacion,la cantidad deseada excede la del inventario, pruebe con una cantidad menor </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <div align="center">
                                                                       
                                                                        <?php }?>
																		   <?php }?>
																</td>
                                                                      </tr>
                                                                  </table></td>
                                                              </tr>
                                                            </table>
                                                            <p>
															       <?php 
																
																echo "</td>";																
																$col++;
																if($col==$colsxPag){
																echo '</tr>';
																$col=0;
																}
																
															} while ($row_rsProd = mysql_fetch_assoc($rsProd) ); 
															echo "</table>";
															 } ?></p>
																<?php if (isset($_SESSION['tipoUsuario']) && $_SESSION['tipoUsuario']==1){
																$col=0;
																	echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="product" style="width:33%; height:194px">'; ?>
																<?php do { 
																	 if($col==0)
																	 echo '<tr> ';	
																	 echo '<td bordercolor="#CCCCCC" bgcolor="#FFFFFF">';?>
																	 <a name="goTo<?php echo $row_rsProd['cod_item'] ?>"></a>
                                                            <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                            </table> 
                                                            <table cellpadding="0" cellspacing="0" border="0" class="product" style="width:<?php echo $colancho ?>">
                                                              <tr>
                                                                <td style="height:194px" align="center"><br style="line-height:9px">
                                                                    <table>
                                                                      <tr>
                                                                        <td style="height:34px" class="vam" align="center"><span class="vam" style="height:52px"><span><a href="<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank"><?php echo $row_rsProd['des_item']; ?></a></span></span></td>
                                                                      </tr>
                                                                    </table><table cellpadding="0" cellspacing="0" border="0" style="width:156px">
                                                                            <tr>
                                                                              <td><img src="images/pic_corn_tl.gif" alt="" border="0"></td>
                                                                              <td class="pic_corn_t"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                              <td><img src="images/pic_corn_tr.gif" alt="" border="0"></td>
                                                                            </tr>
																																					
                                                                            <tr>
                                                                              <td class="pic_corn_l"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
																			  
                                                                              <td class="image"><a href="<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank"><img src="<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
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
                                                                        </table>
                                                                    <table border="0">
                                                                      <tr>
                                                                        <td colspan="2" class="vam" style="height:19px"><div align="center"><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank">Click para agrandar</a></div></td>
                                                                      </tr>
                                                                      <tr>
                                                                    
                                                                         <td style="height:19px" class="vam" align="center"><span class="productSpecialPrice"><span class="vam" style="height:19px">₵
                                                                           <?php 
																		 switch($_SESSION['tipo_pre']){
																		 	case 1:  echo number_format($row_rsProd['precio']);
																						break;
																			case 2:  echo number_format($row_rsProd['precio2']);
																						break;
																			case 3:  echo number_format($row_rsProd['precio3']);
																						break;																					
																			case 4:  echo number_format($row_rsProd['precio4']);
																						break;
																		 }
																		 ?></span><img alt="" src="images/spacer.gif" width="10" height="1"><a href="#"></a></td>
                                                                      </tr>
                                                                      <tr>
                                                                        <td style="height:19px" class="vam" align="center">Cantidad disponible: <?php echo $row_rsProd['cant_stock'] ?></td>
                                                                      </tr>
                                                                      <tr><?php if ($row_rsProd['expiracion']!="0000-00-00"  && $row_rsProd['expiracion']!=null) {?>
                                                                        <td style="height:19px" class="vam" align="center">Fecha de expiración: <?php echo date("l d F Y", strtotime($row_rsProd['expiracion'])) ?></td> <?php }?>
                                                                      </tr>
                                                                      <tr>
                                                                        <td style="height:19px" class="vam" align="center"><form name="f3" method="post" action="carrito.php?<?php $arr = array('eliminar','vaciarCarrito');
							$cad = getExclusivo($arr);
							echo $cad;?>&actCant=1#goTo<?php echo $row_rsProd['cod_item'] ?>" id="f1">
                                                                          <label> Cantidad
                                                                          <input name="txtCantidad" type="text" size="2" id="txtCantidad" value="<?php echo $_SESSION['ccIdProdCant'][$row_rsProd['cod_item']] ?>">
                                                                          <br>
                                                                          </label>
																		   <input type="hidden" name="actCant" value="1">
																		    <input type="hidden" name="stock" value="<?php echo $row_rsProd['cant_stock']  ?>">
                                                                          <input type="hidden" name="idItem" value="<?php echo $row_rsProd['cod_item']  ?>">
                                                                          <label>
                                                                          <input type="image" name="imageField" src="images/esp/button_update_cart1.gif">
                                                                          </label>
                                                                          <a href="#" onclick="javascript:ef('carrito.php?eliminar=1&idItem=<?php echo  $row_rsProd['cod_item']?>');return false;"><img src="images/esp/button_delete1.gif"></a>
                                                                        </form>                                                                          <p>
                                                                          <?php if ($_POST['actCant']==1 && $_POST['idItem']==$row_rsProd['cod_item']){ ?>
                                                                          <?php if ($_POST['txtCantidad']>0 && myIsInt($_POST['txtCantidad']) && ($_POST['txtCantidad'] <= $_POST['stock']) ){?>
</p>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                                <p align="center" class="style5">La modificacion ha sido llevada a cabo con exito </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <?php } if( myIsInt($_POST['txtCantidad']) && $_POST['txtCantidad']<=0){?>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                                <p align="center" class="style5">No se hizo la modificacion, la cantidad debe ser mayor que cero </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <div align="center">
                                                                       
                                                                        <?php }?>
																		     <?php  if ( !myIsInt($_POST['txtCantidad']) ){?>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                               <p align="center" class="style5">No se hizo la modificacion, la cantidad debe ser un numero entero mayor que cero </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <div align="center">
                                                                       
                                                                        <?php }?>
																		<?php if ( myIsInt($_POST['txtCantidad']) && ($_POST['stock'] < $_POST['txtCantidad'])  ){?>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><p>&nbsp;</p>
                                                                               <p align="center" class="style5">No se hizo la modificacion, la cantidad deseada excede la del inventario, pruebe con una cantidad menor </p>
                                                                              <p>&nbsp;</p></td>
                                                                          </tr>
                                                                        </table>
                                                                        <div align="center">
                                                                       
                                                                        <?php }?>										
																		<?php }?></td>
                                                                      </tr>
                                                                  </table></td>
                                                              </tr>
                                                            </table>
                                                             <?php 
																
																echo "</td>";																
																$col++;
																if($col==$colsxPag){
																echo '</tr>';
																$col=0;
																}
																
																} while ($row_rsProd = mysql_fetch_assoc($rsProd) ); 
																echo "</table>";
																} ?>
													      <p>&nbsp;
									                    </p>
														    <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                        </table> 
<table border="0" cellspacing="0" cellpadding="0">
                                                              <tr>
                                                                <td><p>&nbsp;</p>
                                                                  <p class="result" align="right"><a href="#" onClick="javascript:document.location.href = linkRed">Regresar al carrito de compras</a></p>
                                                                  <p class="result">Mostrando de 
                                                                    <?php echo mysql_num_rows($rsProd) ?>
                                                                  (de <b><?php echo $_GET['totalRows_rsProd'] ?></b> productos)</p>
                                                                  <table border="0" width="50%" align="center" class="result">
                                                                    <tr>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                          <?php } // Show if not first page ?>
                                                                      </td>
                                                                      <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                          <?php } // Show if not first page ?>
                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults" >Siguiente</a>
                                                                          <?php } // Show if not last page ?>
                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                          <?php } // Show if not last page ?>
                                                                      </td>
                                                                    </tr>
                                                                  </table>   
																                                                                 
                                                                  <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
																  <tr> <td class="line_x1" ><img alt="" src="images/spacer.gif" width="1" height="1"></td></tr>
                                                               	<tr> <td style="height:19px" class="vam" align="center"><div align="center">
																  <p>&nbsp;</p>
																  <table border="0">
                                                                    <tr>
                                                                      <td width="24%">&nbsp;</td>
                                                                      <td width="40%"><table width="111%" border="0">
                                                                        <tr>
                                                                          <td width="54%"><div align="right" class="style10">Subtotal:</div></td>
                                                                          <td width="46%"><div align="center" class="style10">₵
                                                                            <?php $cotizacion = obtenerCotizacion($rsSuma);
																echo number_format($cotizacion); ?>
                                                                          </div></td>
                                                                        </tr>
                                                                        <tr>
                                                                          <td><div align="right" class="style10">IVA:</div></td>
                                                                          <td><div align="center" class="style10">₵<?php echo number_format($cotizacion*$impuesto); ?></div></td>
                                                                        </tr>
                                                                        <tr>
                                                                          <td><div align="right"></div></td>
                                                                          <td><hr align="center" style="height:1px"></td>
                                                                        </tr>
                                                                        <tr>
                                                                          <td><div align="right" class="style9"><span class="style10">Total</span>:</div><br></td>
                                                                          <td><div align="center"><span class="productSpecialPrice">₵ <?php echo number_format($cotizacion + ($cotizacion*$impuesto)) ?></span>
                                                                          </div><br></td>
                                                                        </tr>
                                                                      </table></td>
                                                                      <td width="36%">&nbsp;</td>
                                                                    </tr>
                                                                  </table>
																  <p>
																    <?php if(isset($_SESSION['tipoUsuario'])){?>
															      </p>
																  <?php if ($_SESSION['tipoUsuario']==3  ) {?> <p>Usted no puede realizar un pedido.</p>
																 <p>
																   <?php }?>
															      </p>
																 <p><?php if  ($_SESSION['tipoUsuario']==1 && $_SESSION['idClienAgen']==-1 ){ ?>Debe seleccionar un cliente para poder realizar el pedido. Para hacerlo haga 
																   
															      click <a href="agenteSelectCliente.php?regresar=carrito">aqui </a><?php }?></p>
																 <?php if  ($_SESSION['tipoUsuario']!=3 && isset($_SESSION['idClienAgen']) && !isset($_POST['realizarPedido']) && $_SESSION['cci']>0 && $_SESSION['idClienAgen']!=-1)  { ?>
																 <form name="formPedido" method="post" action="carrito.php" onSubmit="javascript:
																 if(confirm('¿Esta seguro que desea realizar el pedido?'))
																 	return true;
																	else
																		return false;
																 ">
															   
                                                                  <label>
                                                                  <input type="image" name="imageField2" src="images/esp/button_checkout.gif">
						                                           </label>
                                                                                                                                                                                                                             <input name="realizarPedido" type="hidden" id="realizarPedido" value="1">
															                                                                                                                                                                 <?php if(!isset($_POST['realizarPedido']) && $_SESSION['cci']>0 && isset($_SESSION['tipoUsuario'])){?>
																                                                                                                                                                             <a href="#" onClick="javascript:ef2('carrito.php?vaciarCarrito=1');return false;"><img src="images/esp/button_delete.gif" width="64" height="19"></a>
																                                                                                                                                                             <?php }?>
																 </form>
																<?php }?><?php }?>
																 </div>
																
																  </td>
																 </tr>
                                                            </table> 
														
                                                          
                                                            
                                                              </tr>
                                                      </table>                                                      </td>
														
                                                    </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                      </table></p></td>
									</tr><tr>
                                          <td> <?php }?></td>
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
                                   El Mago Don Eloy <?php echo date("Y")  ?><br>                      </td>							
                            </tr>
                        </table>                  </td>
                </tr>
            </table>
            </p>
      </td>
	</tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsofaz);

mysql_free_result($rsProd);

//mysql_free_result($rsCat2);
?>









