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


//INSERTAR UN ITEM EN EL CARRITO
if ( $_POST['inscc']==1 && isset($_SESSION['tipoUsuario']) ){ 
	if(isset($_POST['s2']) && myIsInt($_POST['s2']) &&  $_POST['s2']>0 && ($_POST['stock'] >= $_POST['s2'])){
	if(!isset($_SESSION['ccIdProdCant'][ $_POST['idItem']]))
		$_SESSION['cci'] += 1 ;
		$_SESSION['ccIdProd'][ $_POST['idItem']] =  "'" . $_POST['idItem'] . "'";		
		$_SESSION['ccIdProdCant'][$_POST['idItem']] = $_POST['s2'] ;
		}
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
$query_rsofaz = "SELECT i.cod_item, i.des_item, i.$cadPrecio, i.imagen FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item=ie.cod_item AND ie.estado=1 and  ie.eliminado<>1 ORDER BY RAND() LIMIT 0,1";
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
if(!isset($_GET['buscar'])){
if(isset($_GET['estado'])){
$query_rsProd = "SELECT * FROM (SELECT * FROM ITEMS) as s, ITEMS_ESTADO as ie WHERE s.cod_item=ie.cod_item  AND ie.eliminado<>1 AND ie.estado=" . $_GET['estado'];
if(isset($_GET['cat'])) $query_rsProd .= " AND s.cod_cate = '". $_GET['cat'] ."'" ;
if(isset($_GET['indice']))
$query_rsProd .= " AND  s.des_item LIKE '". $_GET['indice'] ."%'";
$query_rsProd .= " ORDER BY s.des_item ";

}
else{
$query_rsProd = "SELECT * FROM (SELECT * FROM ITEMS as i) as s, ITEMS_ESTADO as ie WHERE s.cod_item=ie.cod_item AND ie.eliminado<>1 " ;
if(isset($_GET['cat'])) $query_rsProd .= " AND s.cod_cate = '". $_GET['cat'] . "'";
if(isset($_GET['indice']))
$query_rsProd .= " AND  s.des_item LIKE '". $_GET['indice'] ."%'";
$query_rsProd .= " ORDER BY s.des_item ";
//echo $query_rsProd;
}
}
else
{
$querybus = "SELECT s.cod_item FROM (SELECT cod_item FROM ITEMS WHERE des_item LIKE '%". strtoupper($_GET['buscar']) ."%') as s, ITEMS_ESTADO as ie WHERE s.cod_item = ie.cod_item AND ie.eliminado<>1";
//echo $querybus;
$busqueda = mysql_query($querybus,$conn) or die(mysql_error());
if(mysql_num_rows($busqueda)<=0)
$query_rsProd = "SELECT * FROM ITEMS WHERE cod_item=-1";
else{
$rowBusqueda = mysql_fetch_row($busqueda);
$arrBusqueda="";
do{
$arrBusqueda[]= "'" . $rowBusqueda[0] . "'";
} while ($rowBusqueda = mysql_fetch_row($busqueda));
$arrBusqueda = implode(",",$arrBusqueda);
$query_rsProd = "SELECT * FROM ITEMS WHERE cod_item IN ($arrBusqueda) ORDER BY des_item";
//echo $query_rsProd;
}
}
//echo $query_rsProd;
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

mysql_select_db($database_conn, $conn);
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE eliminado<>1";
if(isset($_GET['cat']))
$query_rsCat2 .= " AND cod_cate=" . $_GET['cat'];
$rsCat2 = mysql_query($query_rsCat2, $conn) or die(mysql_error());
$row_rsCat2 = mysql_fetch_assoc($rsCat2);
$totalRows_rsCat2 = mysql_num_rows($rsCat2);
$catLocal = mysql_result($rsCat2,0,categoria);
if(!isset($_GET['cat'])) $catLocal = "Todos";

/*if($_SESSION['tipoUsuario']==1){
include('../Connections/conn2.php');
mysql_select_db($database_conn2, $conn2);
$qact = "SELECT count(*) FROM PEDIDOS WHERE uploaded=0 and eliminado<>1 GROUP BY id";
$rsact = mysql_query($qact, $conn2) or die(mysql_error());
$actualizaciones = mysql_num_rows($rsact);
}*/
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

	clearCookies();
	setCookie('linkRed','<?php echo  'index.php?' . $_SERVER['QUERY_STRING']?> ');
	//var bubu = getCookie('linkRed'); 
	//alert(bubu);



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
		
function conslink2(){
	document.getElementById('linkBuscar').href = "index.php?buscar=" + document.form1.txtBuscar.value;
}		
		
function ef(url){
	if(confirm('¿Desea eliminar estas fechas?')) {
		document.location.href=  url;
	} 
}
</script>
<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style5 {color: #FF0000}
.style7 {font-size: 10}
.style8 {font-size: 9px}
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
                                  <p><img src="images/logoeloytranparente3.gif" alt="" width="295" height="77" ></p>
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
                              </table> </td>
                            </tr>
                        </table>                  </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" style="height:583px ">
                            <tr>
                                <td class="col_left"> 
								
                                    <table cellpadding="0" cellspacing="0" border="0" class="box_width_left">
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
													<li class="bg_list"><a href="carritoForm.php">Formulario carrito</a></li><?php if($_SESSION['conexion']==1){ ?> <li class="bg_list"><a href="actualizarBD.php">Cargar base de datos</a></li><?php }?>
													 <?php if($_SESSION['conexion']==0){ ?>
													  <li class="bg_list"><a href="<?php echo $ftpserver ?>">Descargar archivos </a></li>
													  <?php }?>
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
                                    </table>                                </td>
                                <td><img alt="" src="images/spacer.gif" width="11" height="1"></td>
                                <td class="col_center">
								
                                    <table cellpadding="0" cellspacing="0" border="0" style="400px" >
									<tr>                                       
										
                                            <td><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" style="width:<?php echo $pancho?>">
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
                                                    <tr>
                                                   	  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table">
												

                                                        	<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
														<tr><td>
														</td></tr>
                                                                <tr>
																  <td class="cont_heading_td"><?php 
																  if(isset($_GET['estado'])){
																  	switch($row_rsProd['estado']){
																	case 0: echo 'Productos Nuevos'; break;
																	case 1: echo 'Productos En oferta'; break;	
																	case 2: echo 'Precio regular'; break;
																	case 3: echo 'Productos No disponibles'; break;
																	case 4: echo 'Productos Descontinuados'; break;				
																	case 5: echo 'Productos en General'; break;															
																	
																	}
																  }
																  else
																  		if(isset($_GET['busqueda']))
																		echo "Resultados de la busqueda";
																		else
																		  echo $catLocal; ?></td>
                                                                </tr>
                                                        </table> 
														    <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                        </table>   
														    <form name="form1" method="post" action="javascript:
															document.location.href = 'index.php?buscar=' + document.form1.txtBuscar.value;
															" >
														  <div align="right"><strong>
														    <input name="buscar" type="hidden" id="buscar" value="1">
													      Buscar:</strong>
													        <input name="txtBuscar" type="text" id="txtBuscar">
&nbsp;&nbsp;<a href="#" id="linkBuscar" onClick="javascript:conslink2()"><img src="images/k.gif" width="32" height="18"></a> </div>
														</form>
														<p class="result"><?php if(isset($_GET['buscar'])){ ?>Resultados de búsqueda: <?php echo $_GET['buscar'] ?><?php }?></p>
														<p class="result">
														  <?php if (mysql_num_rows($rsProd)>0) {?>
														  Mostrando de  
														  <?php  echo ($pageNum_rsProd) * $regsxPag + 1?> 
														  a <?php echo (($pageNum_rsProd) * $regsxPag) + mysql_num_rows($rsProd)  ?> 
                                                                  (de <b><?php echo $totalRows_rsProd ?>)</b>
                                                                  <?php }?>
														</p>
														<p align="center" class="result">&nbsp;<?php 
																$alfabeto=array(
																'A','B','C','D','E','F','G','H',
																'I','J','K','L','M','N','Ñ','O',
																'P','Q','R','S','T','U','V','W',
																'X','Y','Z'
															);
															
																?>
                                                                 <?php for($i=0; $alfabeto[$i]; $i++){?>
																	  <a href="index.php?<?php 
																	  if ($_SERVER['QUERY_STRING']!="") {
																	  $arr=array('indice','pageNum_rsProd','totalRows_rsProd','nombreCliente','idCliente','precioCliente','buscar'); 
																	  $cad = getExclusivo($arr);
																	  echo $cad . "&" ; 
																	};?>indice=<?php echo $alfabeto[$i]  ?>" >
												
																	  <?php  echo $alfabeto[$i]  ?> </a> <?php echo " | " ?> 
																	  <?php }?></p>
														<p align="center">
														  <?php if( mysql_num_rows($rsProd) <=0 ){ ?>
														<p align="center">&nbsp;</p>
													    
														<p align="center" >Su búsqueda no produjo ningún resultado</p>
														<p align="center" >&nbsp;</p>
														<p align="center" ><?php }else{?>
														<table border="0" width="50%" align="center" class="result">
                                                                    <tr>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, 0, $queryString_rsProd); ?>" class="pageResults" >Primero</a>
                                                                          <?php } // Show if not first page ?>                                                                      </td>
                                                                      <td width="31%" align="center"><?php if ($pageNum_rsProd > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, max(0, $pageNum_rsProd - 1), $queryString_rsProd); ?>" class="pageResults" >Anterior</a>
                                                                          <?php } // Show if not first page ?>                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                          <a href="<?php  printf("%s?pageNum_rsProd=%d%s", $currentPage, min($totalPages_rsProd, $pageNum_rsProd + 1), $queryString_rsProd); ?>" class="pageResults">Siguiente</a>
                                                                          <?php } // Show if not last page ?>                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsProd < $totalPages_rsProd) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsProd=%d%s", $currentPage, $totalPages_rsProd, $queryString_rsProd); ?>" class="pageResults">Ultimo</a>
                                                                          <?php } // Show if not last page ?>                                                                      </td>
                                                                    </tr>
                                                        </table>  
                                                              
																
                                                                  <p>
                                                                    <?php if (!isset($_SESSION['logged'])) {
																	$col=0;
																	echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="product" style="width:33%; height:194px">';
																	?>
                                                                  </p>
                                                                  <p>
                                                                    
                                                                    
                                                                    <?php do { 
														 
														 
														 if($col==0)
														 echo '<tr> ';
														 echo '<td bordercolor="#CCCCCC" bgcolor="#FFFFFF">';
														 
														 ?>
                                                                        </p>
																		  <div id="resultados">
                                                                  <table cellpadding="0" cellspacing="0" border="0" >
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                            </table> 
                                                            <table cellpadding="0" cellspacing="0" border="0" class="product" style="width:<?php echo $colancho ?>">
                                                                <tr>
                                                                     <td style="width:42%; height:194px" align="center">
                                                                   	   <br style="line-height:9px">
																	   <table>
																	   <tr> <td style="height:34px" class="vam" align="center"><span class="vam" style="height:52px"><span><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank"><?php echo $row_rsProd['des_item']; ?></a></span></span></td></tr>
																	   </table>
                                                                    	<table cellpadding="0" cellspacing="0" border="0" style="width:156px">
                                                                            <tr>
                                                                              <td><img src="images/pic_corn_tl.gif" alt="" border="0"></td>
                                                                              <td class="pic_corn_t"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
                                                                              <td><img src="images/pic_corn_tr.gif" alt="" border="0"></td>
                                                                            </tr>
																																					
                                                                            <tr>
                                                                              <td class="pic_corn_l"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
																			  
                                                                              <td class="image"><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
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
																	                                                         
                                                                    	<p><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
								$foto = substr($row_rsProd['imagen'],11);
								$foto = str_replace('\\','/',$foto);
								echo $foto;
																			  
																			  ?>" target="_blank">Click para agrandar</a></p></td>	
                                                                </tr>
                                                            </table>  </div>
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
<?php 
	if ( isset($_SESSION['tipoUsuario']) && ($_SESSION['tipoUsuario']==0 || $_SESSION['tipoUsuario']==2 )){
																		$col=0;
			echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="product" style="width:33%; height:194px">';
																		?>
																												 
																<?php do { 
																	 if($col==0)
																	 echo '<tr> ';	
																	 echo '<td bordercolor="#CCCCCC" bgcolor="#FFFFFF">';
																		?>
															<a name="goTo<?php echo $row_rsProd['cod_item'] ?>"></a>
                                                            <table cellpadding="0" cellspacing="0" border="0" class="product" style="width:<?php echo $colancho ?>">
                                                              <tr>
                                                                <td style="width:42%; height:194px" align="center"><br style="line-height:9px">
                                                                    <table>
                                                                      <tr>
                                                                        <td style="height:34px" class="vam" align="center"><span class="vam" style="height:52px"><span><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
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
                                                                        <td class="image"><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
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
                                                                        <td style="height:19px" class="vam" align="center"> <form name="f2" method="post" action="index.php?<?php if ($_SERVER['QUERY_STRING']!="") echo $_SERVER['QUERY_STRING'] ?>#goTo<?php echo $row_rsProd['cod_item'] ?>" >
                                                                          <p><?php if ($row_rsProd['estado']!=3 && $row_rsProd['estado']!=4 && $row_rsProd['cant_stock']>0 ){ ?><input name="inscc" type="hidden" id="inscc" value="1">
																		  <input name="stock" type="hidden" value="<?php echo $row_rsProd['cant_stock'] ?>">
                                                                            <input name="idItem" type="hidden" value="<?php echo $row_rsProd['cod_item'] ?>">
                                                                            <strong>                                                                            Cantidad</strong>
                                                                            <input name="s2" type="text" value="1" size="2">
                                                                            <label></label>
                                                                            <input type="image" name="imageField" src="images/esp/button_add_to_cart1.gif">
                                                                          <?php }else{?>
                                                                          <img src="images/icon-not-allowed.png" width="16" height="16">Producto 
																		  <?php if ($row_rsProd['estado']==3 || $row_rsProd['cant_stock']<=0)
																		  echo "no disponible";
																		  	else
																				echo "descontinuado"; ?><?php }?></p>
                                                                          </form>                                               <p>&nbsp;</p></td>
                                                                      </tr>
																	   <tr><td>
															  <?php if ( isset($_POST['inscc']) && $_POST['inscc']==1  && $_POST['idItem']==$row_rsProd['cod_item']){
	/*
		$_SESSION['ccIdProd'][] = $_GET['idItem'];
		$_SESSION['ccNumProd'][] = $_GET['numProd'];
		$_SESSION['cci'] += 1 ; */
	?>
	
	<?php if(!myIsInt($_POST['s2'])){?>
	 <p align="center" class="style5">El producto no pudo ser agregado, favor ingresar una cantidad correcta.</p>	
	<?php }?>
	<?php if(myIsInt($_POST['s2']) && $_POST['s2']<=0){?>
	 <p align="center" class="style5">El producto no pudo ser agregado, favor ingresar una cantidad entera mayor que cero.</p>	
	<?php } ?>
	<?php if( myIsInt($_POST['s2']) && ($_POST['stock'] < $_POST['s2'])){?>
	 <p align="center" class="style5">La cantidad de producto deseada excede la del inventario, pruebe con una cantidad mas baja.</p>	
	<?php }?>
	<?php  if ( myIsInt($_POST['s2']) && $_POST['s2']>0 && ($_POST['stock'] >= $_POST['s2']) ){?>
	    <p align="center" class="style5">El producto ha sido agregado al carrito de compras.</p>
	    <p align="center"><a href="carrito.php" class="style9">Ver carrito</a></p>
	   
	    <?php }?>
		
	    <?php }?>
                                                          </td>
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
<?php 
																	if (isset($_SESSION['tipoUsuario']) && ( $_SESSION['tipoUsuario']==1 || $_SESSION['tipoUsuario']==3 ) ){
																	$col=0;
																	echo '<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="product" style="width:33%; height:194px">';
																	?>
																	 
													<?php do { 
																	 if($col==0)
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
                                                                <td style="width:42%; height:194px" align="center"><br style="line-height:9px">
                                                                    <table>
                                                                      <tr>
                                                                        <td style="height:34px" class="vam" align="center"><span class="vam" style="height:52px"><span><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
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
                                                                        <td class="image"><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd['imagen']; 
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
                                                                  <a href="agrandar.php?imagen=<?php echo $row_rsProd['imagen']; ?>" target="_blank"></a>
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
																		 ?>
                                                                         </span><img alt="" src="images/spacer.gif" width="10" height="1"><a href="#"></a></td>
                                                                      </tr>
                                                                      <tr>
                                                                        <td style="height:19px" class="vam" align="center"><p>Cantidad disponible: <span class="vam" style="height:19px"><?php echo $row_rsProd['cant_stock'] ?></span></p>                                                                        </td>
                                                                      </tr>
                                                                      <tr><?php if ($row_rsProd['expiracion']!="0000-00-00"  && $row_rsProd['expiracion']!=null) {?>
                                                                        <td style="height:19px" class="vam" align="center"><p>Fecha de expiración: </p>
                                                                        <?php echo date("l d F Y", strtotime($row_rsProd['expiracion'])) ?></td> <?php }?>
                                                                      </tr>
                                                                      <tr>
                                                                        <td style="height:19px" class="vam" align="center"><form name="f2" method="post" action="index.php?<?php if ($_SERVER['QUERY_STRING']!="") echo $_SERVER['QUERY_STRING']?>#goTo<?php echo $row_rsProd['cod_item'] ?>">
                                                                          <label></label>
                                                                          <p>
                                                                            <?php if ($row_rsProd['estado']!=3 && $row_rsProd['estado']!=4 && $row_rsProd['cant_stock']>0 ){ ?>
                                                                            <input name="inscc" type="hidden" id="inscc" value="1">
																			 <input name="stock" type="hidden" value="<?php echo $row_rsProd['cant_stock'] ?>">                                                                            <input name="idItem" type="hidden" value="<?php echo $row_rsProd['cod_item'] ?>">
																			<strong>Cantidad</strong>
                                                                          <input name="s2" type="text" value="1" size="2">
                                                                           <input type="image" name="imageField" src="images/esp/button_add_to_cart1.gif"></a>
                                                                            <?php }else{?>
                                                                            <img src="images/icon-not-allowed.png" width="16" height="16">Producto
                                                                            <?php if ($row_rsProd['estado']==3 || $row_rsProd['cant_stock']<=0)
																		  echo "no disponible";
																		  	else
																				echo "descontinuado"; ?>
                                                                            <?php }?>
                                                                          </p>
                                                                        </form>                                                                        </td>
                                                                      </tr>
                                                                  </table></td>
                                                              </tr>
															  <tr><td>
															  <?php if ( isset($_POST['inscc']) && $_POST['inscc']==1  && $_POST['idItem']==$row_rsProd['cod_item']){
	/*
		$_SESSION['ccIdProd'][] = $_GET['idItem'];
		$_SESSION['ccNumProd'][] = $_GET['numProd'];
		$_SESSION['cci'] += 1 ; */
	?>
	<?php if(!myIsInt($_POST['s2'])){?>
	 <p align="center" class="style5">El producto no pudo ser agregado, favor ingresar una cantidad correcta.</p>	
	<?php }?>
	<?php if(myIsInt($_POST['s2']) && $_POST['s2']<=0){?>
	 <p align="center" class="style5">El producto no pudo ser agregado, favor ingresar una cantidad entera mayor que cero.</p>	
	<?php } ?>
	<?php if( myIsInt($_POST['s2']) && ($_POST['stock'] < $_POST['s2'])){?>
	 <p align="center" class="style5">La cantidad de producto deseada excede la del inventario, pruebe con una cantidad mas baja.</p>	
	<?php }?>
	<?php if ( myIsInt($_POST['s2']) && $_POST['s2']>0 && ($_POST['stock'] >= $_POST['s2']) ){?>
	    <p align="center" class="style5">El producto ha sido agregado al carrito de compras.</p>
	    <p align="center"><a href="carrito.php" class="style9">Ver carrito</a></p>
	   
	    <?php }?>
		
	    <?php }?>
                                                          </td>
															  </tr>  </table>
                                                                                                                     <?php 
																
																echo "</td>";																
																$col++;
																if($col==$colsxPag){
																echo '</tr>';
																$col=0;
																}
																
																} while ($row_rsProd = mysql_fetch_assoc($rsProd) ); 
																
																echo "</table>";
																} 
																?><br>
                                                   <?php }?>
<table border="0" cellspacing="0" cellpadding="0" class="result">
                                                              <tr>
                                                                <td><p class="result">&nbsp;</p>
                                                                  <p class="result"><?php if (mysql_num_rows($rsProd)>0){?>Mostrando de  
                                                                    <?php  echo ($pageNum_rsProd) * $regsxPag + 1?> 
                                                                    a <?php echo (($pageNum_rsProd) * $regsxPag) + mysql_num_rows($rsProd)  ?> 
                                                                  (de <b><?php echo $totalRows_rsProd ?>)</b><?php }?></p>
                                                                  <p align="center" class="result"><?php for($i=0; $alfabeto[$i]; $i++){?>
																	  <a href="index.php?<?php 
																	  if ($_SERVER['QUERY_STRING']!="") {
																	$arr=array('indice','pageNum_rsProd','totalRows_rsProd','nombreCliente','idCliente','precioCliente','buscar'); 
																	  $cad = getExclusivo($arr);
																	  echo $cad . "&" ; 
																	};?>indice=<?php echo $alfabeto[$i]  ?>" >
																	  
																	  <?php  echo $alfabeto[$i]  ?> </a> <?php echo " | " ?> 															  																	  <?php }?></p>
                                                                  <table border="0" width="50%" align="center">
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
                                                              </tr>
                                                    </table>                                                    </tr>
                                                     <tr>
                                                        <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_b"></td>
                                                        <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                      </table>                                          </td>
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
            </p>
      </td>
	</tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsProd);

mysql_free_result($rsCat2);
?>









