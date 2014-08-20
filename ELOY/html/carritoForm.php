<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php
$flagShowForm=1;
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}

//CUANDO EL AGENTE SELECCIONA UN CLIENTE
if ( isset($_GET['idCliente']) && $_GET['agenteSelectCliente']==1 && isset($_SESSION['tipoUsuario']) ){ 
	$_SESSION['idClienAgen'] = $_GET['idCliente'];
	$_SESSION['nombreClienAgen'] = $_GET['nombreCliente'];
	$_SESSION['tipo_pre'] = $_GET['tipo_pre'];

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



if ( $_POST['realizarPedido']==1 && isset($_SESSION['tipoUsuario']) && isset($_POST['txtCodigo']) && !estaVacio($_POST['txtCodigo']) ){
mysql_select_db($database_conn, $conn);

$arr="";
//0 si no ha escrito nada, 1 si lo escrito no es un numero valido
$arrImposibleNumero="";
$arrImposibleCodigo="";
$flagRealizarPedido=1;

$_SESSION['cfcodigo']= $_POST['txtCodigo'];
$_SESSION['cfcantidad']=$_POST['txtCantidad'];

$arrIdCant="";

foreach($_POST['txtCodigo'] as $key=>$value){
	
	if(trim($value)!=""){ 
	$value = strtoupper(trim($value));
	$arr2[]= "'" . $value . "'";
	$arrIdCant[$value] = $_POST['txtCantidad'][$key];
	if($_POST['txtCantidad'][$key]==""  ){ $arrImposibleNumero[$key] = 0; $flagRealizarPedido=0;}
	
		if($_POST['txtCantidad'][$key]<=0 || !myIsInt($_POST['txtCantidad'][$key])){$arrImposibleNumero[$key] = 1; $flagRealizarPedido=0;}	
	
	}
}
//print_r($arrIdCant);
$arr = implode(',', $arr2);

mysql_select_db($database_conn, $conn);
$query_rsProd = "SELECT * FROM ITEMS  where cod_item IN ($arr) ORDER BY des_item";

$rsProd = mysql_query($query_rsProd, $conn) or die(mysql_error());

mysql_select_db($database_conn, $conn);
$qcf = "SELECT cod_item, cant_stock FROM ITEMS where cod_item IN ($arr) ORDER BY des_item";
$rscf = mysql_query($qcf, $conn) or die(mysql_error());


foreach($arr2 as $key=>$value){
	if(!existeCodigo($rscf,$value)){ $arrImposibleCodigo[$key]=$value; $flagRealizarPedido=0;}
}


 

if($flagRealizarPedido!=0){
switch($_SESSION['tipo_pre']){
	case 1: $cadPrecio= "precio"; break;
	case 2:  $cadPrecio= "precio2"; break;
	case 3:  $cadPrecio= "precio3"; break;
	case 4:  $cadPrecio= "precio4"; break;
}

$qvi = "SELECT cod_item,cant_stock,des_item, $cadPrecio as prize FROM ITEMS WHERE cod_item IN ($arr) ORDER BY des_item";
$rsqvi = mysql_query($qvi, $conn) or die(mysql_error());

$rsSuma = mysql_query("SELECT $cadPrecio as precioUnitario, cod_item FROM ITEMS WHERE cod_item IN ($arr) ORDER BY des_item", $conn) or die(mysql_error());

$tam = mysql_num_rows($rsqvi);
$i=0;
$flagPedido = 1;
   do{ 
  if (mysql_result($rsqvi,$i,cant_stock) < $arrIdCant[mysql_result($rsqvi,$i,cod_item)])
													$flagPedido = 0;
													//$arrCant[$i] =  $_POST['txtCantidad'][$i];
													
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
			if($_SESSION['conexion']==0){
			mysql_select_db($database_conn, $conn);
			mysql_query("INSERT INTO INDEX_CARRITO VALUES ('NULL')") or die(mysql_error());
			$idCarrito = mysql_insert_id();
			
	$tam = mysql_num_rows($rsProd);
	for($i=0; $i < $tam; $i++)
	{
	$idItem = mysql_result($rsProd,$i,cod_item);
	$precioU = mysql_result($rsSuma,$i,precioUnitario);
	$cantidad = $arrIdCant[mysql_result($rsProd,$i,cod_item)];
		$idClienAgen = $_SESSION['idClienAgen'];
	
	mysql_select_db($database_conn, $conn);
	$ins = "INSERT INTO DETALLE_CARRITO VALUES ('NULL', '$idItem', '$precioU', '$cantidad', '$idCarrito')";
		mysql_query($ins, $conn) or die(mysql_error());
	
	}
	if(isset($_SESSION['nombreNuevoCliente']))  $idClienAgen = $usuarioNuevoCliente;
	$ins = "INSERT INTO PEDIDOS VALUES ('NULL','$VENDEDOR','$idClienAgen',TIMESTAMP('". date("Y-m-d H:i:s") ."'),'$idCarrito','0','NULL','NULL','";
	if(isset($_SESSION['nombreNuevoCliente'])) $ins .= "Nombre: " . $_SESSION['nombreNuevoCliente'] . ". " .  $_SESSION['infoNuevoCliente'] . "')";
	else
		$ins .= "NULL')";
	mysql_query($ins, $conn) or die(mysql_error());
	
	
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
	$cantidad = $arrIdCant[mysql_result($rsProd,$i,cod_item)];
	$idClienAgen = $_SESSION['idClienAgen'];
	
	
	mysql_select_db($database_conn, $conn);
	$ins = "INSERT INTO DETALLE_CARRITO VALUES ('NULL', '$idItem', '$precioU', '$cantidad', '$idCarrito')";
		mysql_query($ins, $conn) or die(mysql_error());
	
	}
	if(isset($_SESSION['nombreNuevoCliente']))  $idClienAgen = $usuarioNuevoCliente;
	$ins = "INSERT INTO PEDIDOS VALUES ('NULL','$VENDEDOR','$idClienAgen',TIMESTAMP('". date("Y-m-d H:i:s") ."'),'$idCarrito','0','NULL','NULL','";
		if(isset($_SESSION['nombreNuevoCliente'])) $ins .=  "Nombre: " . $_SESSION['nombreNuevoCliente'] . ". " .  $_SESSION['infoNuevoCliente'] . "')";
	else
		$ins .= "NULL')";
	
		mysql_query($ins, $conn) or die(mysql_error());
		//$_SESSION['cci'] = 0;
	
	
}
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

function existeCodigo($r,$v){
	$tam = mysql_num_rows($r);
	for($i=0;$i < $tam; $i++){
		$codleido = "'". mysql_result($r,$i,cod_item). "'";
		if($codleido==$v)
			return 1;
	}
	return 0;
}

function myIsInt ($x) {
    return (is_numeric($x) ? intval($x) == $x : false);
}

function estaVacio($m){
	foreach($m as $value){
		if($value!="") return 0; }
	return 1;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script language="javascript">
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
                                  <p><img src="images/logoeloytranparente3.gif" alt="" width="295" height="77" >
                                  </p>
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
													 <?php if($_SESSION['conexion']==1){ ?> <li class="bg_list"><a href="subirCarrito.php">Ver pedidos pendientes</a></li><?php }?>
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
										
                                            <td><p><?php  if( !isset($_SESSION['tipoUsuario']) && !isset($_POST['realizarPedido']) ) {?>
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
															     
																  <?php }?>
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
                                         <?php if(isset($_SESSION['tipoUsuario']) && isset($_POST['realizarPedido']) && isset($_POST['txtCodigo']) && $flagRealizarPedido!=0 && !estaVacio($_POST['txtCodigo'])){
										  $flagPedido=1;
										  ?>
                                              <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" style="width:550px">
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
                                                    	  <table BORDER=1 FRAME=BOX RULES=NONE> 
                                                            <tr>
                                                              <th><div align="center">Id producto </div></th>
                                                              <th>Producto</th>
															  <th>Cantidad en existencia </th>
                                                              <th>Cantidad deseada </th>
                                                              <th><div align="center">Precio</div></th>
                                                              <th>Estatus</th>
                                                              <th>Subtotal</th>
                                                            </tr>
                                                           <?php $ac=0;
														   $subtotal = 0;
														   $total =0;
														   $tam = mysql_num_rows($rsqvi);
														   $i=0;
														   //print_r($arrIdCant);
														   
														   do{ ?> 
                                                             <tr>
                                                               <td <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>><div align="center"><?php echo mysql_result($rsqvi,$i,cod_item)?></div></td>
                                                              <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php echo mysql_result($rsqvi,$i,des_item)?></div></td>
															  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php 
															  if( mysql_result($rsqvi,$i,cant_stock) < $arrIdCant[mysql_result($rsqvi,$i,cod_item)])
															   echo "<div class='style5'>".mysql_result($rsqvi,$i,cant_stock)."</div>";
															  else
															  echo mysql_result($rsqvi,$i,cant_stock); ?></div></td>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php 
															    if( mysql_result($rsqvi,$i,cant_stock) < $arrIdCant[mysql_result($rsqvi,$i,cod_item)] )
																  echo "<div class='style5'>".$arrIdCant[mysql_result($rsqvi,$i,cod_item)]."</div>";
																else
															   echo $arrIdCant[mysql_result($rsqvi,$i,cod_item)]; ?></div></td>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo  number_format(mysql_result($rsqvi,$i,prize))?></div></td>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
															     <?php 
															   if (mysql_result($rsqvi,$i,cant_stock) < $arrIdCant[mysql_result($rsqvi,$i,cod_item)] ){
															   echo "<div class='style5'><img src='images/cancel.png'>La cantidad solicitada excede a la del inventario</div>";						
															   $flagPedido = 0;
															   } else{
															   	echo "<img src='images/ok.png'>Ok";
																
																}
															
																?>
														       </div></td>
															   <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo number_format($arrIdCant[mysql_result($rsqvi,$i,cod_item)] * mysql_result($rsqvi,$i,prize)); $subtotal = $subtotal + ($arrIdCant[mysql_result($rsqvi,$i,cod_item)]* mysql_result($rsqvi,$i,prize))  ?></div></td>
                                                             </tr>
                                                            <?php 	$i++;
															}while ($i < $tam);  ?>
                                                          </table>
														  <?php if ($flagPedido==1){?>
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
                                                    	    <?php if ($flagPedido==1) {
															unset($_SESSION['cfcodigo']);
															unset($_SESSION['cfcantidad']);
															?>
                                                    	    Para realizar mas compras haga click <a href="carritoForm.php">aqui</a> 
                                                    	    <?php } else{?>
                                                    	    Para regresar al carrito de compras haga click <a href="carritoForm.php">aqui</a>
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
                                              <p><?php 
											  if($flagRealizarPedido==0) $flagShowForm=1;
											  	else $flagShowForm=0;
											  
											  }?>
                                                <?php if(isset($_SESSION['tipoUsuario']) && $flagShowForm==1) {?>
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
																  <td><div align="center"></div>            </td>
																</tr>
                                                                <tr>
																  <td class="cont_heading_td">Pedidos </td>
                                                                </tr>
                                                        </table> 
														  <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> 
														  <p align="center">Favor digite el codigo del producto y la cantidad que desea ordenar. Para ver el listado de productos y sus codigos haga click <a href="productosxCodigo.php" target="_blank">aqui </a></p>
														  <p align="center"><?php if (isset($_POST['txtCodigo']) && estaVacio($_POST['txtCodigo'])) {?>
														    <span class="style5">Debe ingresar al menos un producto y su cantidad</span>
													      <?php }?></p>
														  <form action="carritoForm.php" method="post">
                                                         <div align="center">
                                                           <table width="165%" BORDER="1" FRAME=BOX RULES=NONE <?php  $style = (isset($_POST['realizarPedido']) && isset( $flagRealizarPedido) && $flagRealizarPedido==0) ? 	"style='width:60%'" : "style='width:30%'"; echo $style ?> >
                                                             <tr>
                                                               <th width="33%">Codigo</th>
                                                               <th width="32%">Cantidad</th>
                                                             </tr>
                                                             <?php 
															
															for($i=0; $i<44; $i++){?>
                                                             <tr  <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>>
                                                               <td>
                                                              
                                                                   <input name="txtCodigo[<?php echo $i ?>]" type="text" size="8" value="<?php  if (isset($_SESSION['cfcodigo'])) echo $_SESSION['cfcodigo'][$i] ?>">
                                                                   <?php if (isset($arrImposibleCodigo[$i])){ ?>
                                                                   <table >
                                                                     <tr>
                                                                       <td class="style5"><img src="images/cancel.png">
                                                                         El codigo digitado no ha sido encontrado, favor verificar en el listado de productos y codigos.                               
                                                                       </td>
                                                                     </tr>
                                                                   </table>
                                                               <?php }?></td>
                                                               <td>

                                                                
                                                                   <input name="txtCantidad[<?php echo $i?>]" type="text" size="4" value="<?php if (isset($_SESSION['cfcantidad'])) echo $_SESSION['cfcantidad'][$i] ?>">
                                                           
															 														    
														   
															    <?php if (isset($arrImposibleNumero[$i])){ ?>
															<table ><tr>
															  <td class="style5">
																<img src="images/cancel.png">
															   	<?php if ($arrImposibleNumero[$i]==0){?>
																		Debe introducir un numero.
																	<?php }else{?>
																		Debe introducir un numero entero mayor que cero.
																	<?php }?>
															  </td>
															</tr></table>
																<?php }?>
                                                             <?php }?>  </tr>
                                                           </table>
                                                         </div>
                                                        <div align="center">
														    <p>
															<?php if  ($_SESSION['tipoUsuario']==1 && $_SESSION['idClienAgen']==-1 ){ ?>Debe seleccionar un cliente para poder realizar el pedido. Para hacerlo haga 
																   
															      click <a href="agenteSelectCliente.php?regresar=carritoForm">aqui </a><?php }else{?>
														      <input name="realizarPedido" type="hidden" id="realizarPedido" value="1">
														      <input name="real" type="image" src="images/esp/button_checkout.gif">
															  <?php }?>
													        </p>
													      </div>
														</form>
                                                          <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> 
                                                            <p>
                                                      </td>
                                                    </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                      </table>
                                              </p><?php }?></td>
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

if(isset($rsProd))
mysql_free_result($rsProd);

//mysql_free_result($rsCat2);
?>









