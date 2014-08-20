<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}



if ( $_POST['cambiarEstado']==1 && isset($_SESSION['tipoUsuario']) && $_POST['chkEstado']!="") //NOTA CAMBIAR EL TIPO USUAARIO POR EL DEL ADMINISTRADOR
														{ 
														foreach($_POST['chkEstado'] as $v)
														$arr[] = "'" . $v  . "'";
//														$arr = $_POST['chkEstado'];
														$arr = implode(',',$arr);
														$updq = "UPDATE ITEMS_ESTADO SET ESTADO=" .$_POST['selEstado'] . " WHERE cod_item IN ($arr)";
														//echo $updq;
														mysql_select_db($database_conn, $conn);
   														 mysql_query($updq, $conn) or die(mysql_error());
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
$query_rsofaz = "SELECT i.cod_item, i.des_item, i.$cadPrecio, i.imagen FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item=ie.cod_item AND ie.estado=1 AND ie.eliminado <>1 ORDER BY RAND() LIMIT 0,1";
$rsofaz = mysql_query($query_rsofaz, $conn) or die(mysql_error());
$row_rsofaz = mysql_fetch_assoc($rsofaz);
$totalRows_rsofaz = mysql_num_rows($rsofaz);

$maxRows_rsProd = $pedidosxvendedor;
$pageNum_rsProd = 0;
if (isset($_GET['pageNum_rsProd'])) {
  $pageNum_rsProd = $_GET['pageNum_rsProd'];
}
$startRow_rsProd = $pageNum_rsProd * $maxRows_rsProd;

mysql_select_db($database_conn, $conn);
if(!isset($_GET['estado'])){
$query_rsProd = "SELECT i.cod_item,i.des_item,ie.estado FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item = ie.cod_item AND ie.eliminado<>1 ORDER BY i.des_item ";
if(isset($_GET['indice']))
	$query_rsProd = "SELECT i.cod_item,i.des_item,ie.estado FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item = ie.cod_item AND ie.eliminado<>1 AND i.des_item LIKE '". $_GET['indice'] ."%' ORDER BY i.des_item";
	if(isset($_GET['buscar']))
	$query_rsProd = "SELECT i.cod_item,i.des_item,ie.estado FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item = ie.cod_item AND ie.eliminado<> 1 AND i.des_item LIKE '%". strtoupper($_GET['buscar']) ."%' ORDER BY i.des_item";
	}
	
	else
	{
	$query_rsProd = "SELECT i.cod_item,i.des_item,ie.estado FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item = ie.cod_item AND ie.eliminado<>1 AND ie.estado=". $_GET['estado'] . " ORDER BY i.des_item";
if(isset($_GET['indice']))
	$query_rsProd = "SELECT i.cod_item,i.des_item,ie.estado FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item = ie.cod_item AND ie.eliminado<>1  AND ie.estado=". $_GET['estado']   ." AND i.des_item LIKE '". $_GET['indice'] ."%' ORDER BY i.des_item";
		if(isset($_GET['buscar']))
		$query_rsProd = "SELECT i.cod_item,i.des_item,ie.estado FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item = ie.cod_item  AND ie.eliminado<>1 AND ie.estado=". $_GET['estado']   ." AND i.des_item LIKE '%". strtoupper($_GET['buscar']) ."%') ORDER BY i.des_item";
	}


	
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
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE id=" . $_GET['cat']. "";
else
$query_rsCat2 = "SELECT * FROM CATEGORI ORDER BY RAND()";
$rsCat2 = mysql_query($query_rsCat2, $conn) or die(mysql_error());
$row_rsCat2 = mysql_fetch_assoc($rsCat2);
$totalRows_rsCat2 = mysql_num_rows($rsCat2);
$catLocal = mysql_result($rsCat2,0,id);*/

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
	
		
function confirmar(pregunta){
	if(confirm(pregunta)) {
		document.location.href=  url;
	} 
}
</script>
<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript">
	
function conslink2(){
	document.getElementById('linkBuscar').href = "adminSetEstadoProd.php?buscar=" + document.form2.txtBuscar.value;
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
<style type="text/css">
<!--
.style4 {
	color: #000000;
	font-weight: bold;
}
.style5 {color: #FF0000}
.style6 {color: #000000}
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
                                <td height="248" align="left"><table cellpadding="0" cellspacing="0" border="0" >
                                      
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
													
                                                    </td>
                                                  </tr></table>
                                                <img alt="" src="images/line2.gif"><?php }?></td>
									</tr>
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
													 <?php if($_SESSION['conexion']==1){ ?> <li class="bg_list"><a href="subirCarrito.php">Ver pedidos pendientes</a></li><?php } ?>
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
										
                                            <td>
											<?php 	if(!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario']!=3 ) {?>
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
											  <td width="95%"> <p align="center">Usted no tiene permisos para acceder a esta area. <a href="registrarUsuario.php"></a></p>
															     
																  
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
                                              </table><?php exit;} ?>
											<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
											  <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
                                                    <tr>
                                                   	  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table">
												

                                                        	<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
														<tr><td><?php if ( $_POST['cambiarEstado']==1 && isset($_SESSION['tipoUsuario'])==3 ) //NOTA CAMBIAR EL TIPO USUAARIO POR EL DEL ADMINISTRADOR
														{ 
													
	/*
		$_SESSION['ccIdProd'][] = $_GET['idItem'];
		$_SESSION['ccNumProd'][] = $_GET['numProd'];
		$_SESSION['cci'] += 1 ; */
	?>
	                                                        <p>&nbsp;</p>
	                                                        <p align="center" class="style5">El estado de los productos ha sido modificado con exito.</p>
	    <p class="style5">&nbsp;</p>
	    <?php }?>
														</td></tr>
                                                                <tr>
																  <td ><p class="cont_heading_td">Modificar Estado <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> </p>
                                                          <form name="form2" method="post" action="">
                                                            <label>
                                                            <div align="right">Buscar producto: 
                                                              <input name="txtBuscar" type="text" id="txtBuscar">
                                                            <a href="#" id="linkBuscar" onClick="javascript:conslink2()"><img src="images/k.gif" width="32" height="18"></a>                                                           </div>
                                                            </label>
                                                                    </form>
                                                          <p>Ver productos:                                                          </p>
                                                          <table border="0">
                                                                    <tr>
                                                                       <td><a href="adminSetEstadoProd.php?estado=0">Nuevo</a></td>
                                                                      <td><a href="adminSetEstadoProd.php?estado=1">En oferta </a></td>
                                                                    </tr>
                                                                    <tr>
                                                                      <td><a href="adminSetEstadoProd.php?estado=2">Precio regular</a></td>
                                                                  <td><a href="adminSetEstadoProd.php?estado=3">No disponible </a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><a href="adminSetEstadoProd.php?estado=4">Descontinuado</a></td>
                                                                     <td><a href="adminSetEstadoProd.php?estado=5">Normal</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                      <td><a href="adminSetEstadoProd.php">Todos</a></td>
                                                                      <td>&nbsp;</td>
                                                                    </tr>
                                                                  </table>
															   <br>
															      </td>
                                                                </tr>
                                                            </table> 
														
                                                          <br style="line-height:9px">
                                                          <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> 
                                                            <table border="0" cellspacing="0" cellpadding="0" class="cont_heading_table">
                                                              <tr>
                                                                <td><p>
                                                                  <p align="left" class="result">Mostrando <?php echo mysql_num_rows($rsProd) ?> 
                                                                  (de <b><?php echo $totalRows_rsProd ?>)</b></p>
                                                                  <p align="center" class="result">
                                                                    <?php 
																$alfabeto=array(
																'A','B','C','D','E','F','G','H',
																'I','J','K','L','M','N','Ñ','O',
																'P','Q','R','S','T','U','V','W',
																'X','Y','Z'
															);
															
																?>
                                                                    <?php for($i=0; $alfabeto[$i]; $i++){?>
                                                                      <a href="adminSetEstadoProd.php?<?php 
																	  if ($_SERVER['QUERY_STRING']!="") {
																	 $arr=array('indice','pageNum_rsProd','totalRows_rsProd','buscar'); 
																	 $cad = getExclusivo($arr);
																	  echo $cad . "&" ; 
																	};?>indice=<?php echo $alfabeto[$i]  ?>" >
                                                                      <?php  echo $alfabeto[$i]  ?>
                                                                      </a> <?php echo " | " ?>
                                                                      <?php }?>
                                                                  </p>
                                                                  <p align="center" class="result"> <?php if( mysql_num_rows($rsProd) <=0 ){?></p>
																  	<div align="center">
																  	  <p>&nbsp;</p>
																  	  <p>Su búsqueda no produjo ningún resultado.</p>
																  	  <p>
																  	    <?php }else{ ?>
													  	              </p>
															  	  </div>
																  	<table border="0" width="50%" align="center" class="result">
                                                                      <tr>
                                                                        <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                            <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                            <?php } // Show if not first page ?>                                                                        </td>
                                                                        <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                            <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                            <?php } // Show if not first page ?>                                                                        </td>
                                                                        <td width="23%" align="center" class="result"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                            <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults" >Siguiente</a>
                                                                            <?php } // Show if not last page ?>                                                                        </td>
                                                                        <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                            <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                            <?php } // Show if not last page ?>                                                                        </td>
                                                                      </tr>
                                                                  </table>
                                                               
                                                                  
                                                                  <form name="form1" method="post" action="adminSetEstadoProd.php">
                                                                    <label>
                                                                    <div align="center">Cambiar a: 
                                                                      <select name="selEstado">
                                                                        <option value="0">Nuevo</option>
                                                                        <option value="1">En oferta</option>
                                                                        <option value="2">Precio regular</option>
                                                                        <option value="3">No disponible</option>
                                                                        <option value="4">Descontinuado</option>
                                                                        <option value="5">Normal</option>
                                                                      </select>
                                                                    </div>
                                                                    <p><?php if (isset($_SESSION['tipoUsuario']) && $_SESSION['tipoUsuario']==3) {
														  $ac=0;?>
														
																	
																	
                                                                   <br>
                                                                    <table BORDER=1 FRAME=BOX RULES=NONE> 
																	<tr>
																	<th>Nombre del producto</th>
																	<th>Estado actual</th>
																	<th>&nbsp;</th>
																	</tr>
															 	 <?php do { ?>
  <tr>
    <td <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>><div align="center"><?php echo $row_rsProd['des_item']?></div></td>
	<td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>> 
	    <div align="center"><?php 
			switch($row_rsProd['estado']){
				case 0: echo "Nuevo"; break;
				case 1: echo "En oferta"; break;
				case 2: echo "Precio regular"; break;
				case 3: echo "No disponible"; break;
				case 4: echo "Descontinuado"; break;
				case 5: echo "Normal"; break;
			}
		?></div></td>
	<td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><label>	 
	    <div align="center">
	      <input type="checkbox" name="chkEstado[<?php echo $row_rsProd['cod_item']?>]" value="<?php echo $row_rsProd['cod_item']?>" >
	       </div>
	</label></td>
  </tr>


                                                             
                                                             <?php } while ($row_rsProd = mysql_fetch_assoc($rsProd)); ?>
															  <?php }?></table>
                                                                    </p>
                                                                    <div align="right"><a name="sel" href="#sel" onClick="seleccionar_todo()">[Seleccionar todos]|</a>|<a href="#sel" onClick="deseleccionar_todo()">[Deseleccionar todos]</a></div>
                                                                    <p align="center">
                                                                      <label></label>
                                                                      <input type="hidden" name="cambiarEstado" value="1">
																	   <input type="image" name="imageField" src="images/esp/button_confirm_order.gif" onclick="javascript:confirmar('¿Esta seguro de desea modificar el estado de estos productos?')">
                                                                    </p>
                                                                  </form>
                                                                  <p>                                                                   
                                                                  <p><?php }?>                                                                  </p>
                                                                 <p align="left" class="result">Mostrando  <?php echo mysql_num_rows($rsProd) ?>
                                                                  (de <b><?php echo $totalRows_rsProd ?>)</b></p>
                                                                  <p align="center" class="result">
                                                                    <?php for($i=0; $alfabeto[$i]; $i++){?>
                                                                    <a href="adminSetEstadoProd.php?<?php 
																	  if ($_SERVER['QUERY_STRING']!="") {
																	 $arr=array('indice','pageNum_rsProd','totalRows_rsProd','buscar'); 
																	 $cad = getExclusivo($arr);
																	  echo $cad . "&" ; 
																	};?>indice=<?php echo $alfabeto[$i]  ?>" >
                                                                    <?php  echo $alfabeto[$i]  ?>
                                                                    </a> <?php echo " | " ?>
                                                                    <?php }?>
                                                                  </p>
                                                                  <p align="center">&nbsp;</p>
                                                                  <table border="0" width="50%" align="center" class="result">
                                                                    <tr>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                          <?php } // Show if not first page ?>                                                                      </td>
                                                                      <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                          <?php } // Show if not first page ?>                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults" >Siguiente</a>
                                                                          <?php } // Show if not last page ?>                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                          <?php } // Show if not last page ?>                                                                      </td>
                                                                    </tr>
                                                                  </table>                                                                  <p>&nbsp;</p></td>
                                                                <td class="result_right">&nbsp;</td>
                                                              </tr>
															  <tr>
															    <td>&nbsp;</td>
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
                                                              <td class="result_right">                                                              </tr>
                                                        </table>                                                      </td>
                                                    </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                          </table></td>
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
                                     El Mago Don Eloy <?php echo date("Y")  ?><br>                           </td>							
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









