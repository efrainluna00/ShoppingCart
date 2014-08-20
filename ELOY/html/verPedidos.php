<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}


if(isset($_GET['eliminar'])){
mysql_query("UPDATE PEDIDOS set eliminado=1 WHERE id=" .$_GET['idPedido'], $conn) or die(mysql_error());
}


/*
if ( $_POST['cambiarEstado']==1 && isset($_SESSION['tipoUsuario']) && $_POST['chkEstado']!="") //NOTA CAMBIAR EL TIPO USUAARIO POR EL DEL ADMINISTRADOR
														{ 
														$arr = $_POST['chkEstado'];
														$arr = implode(',',$arr);
														$updq = "UPDATE ITEMS_ESTADO SET ESTADO=" .$_POST['selEstado'] . " WHERE id_item IN ($arr)";
														mysql_select_db($database_conn, $conn);
   														 mysql_query($updq, $conn) or die(mysql_error());
	}*/
/*
if ( $_GET['inscc']==1 && isset($_SESSION['tipoUsuario']) ){ 
	if(!isset($_SESSION['ccIdProdCant'][ $_POST['idItem']]))
		$_SESSION['cci'] += 1 ;
		$_SESSION['ccIdProd'][ $_POST['idItem']] =  $_POST['idItem'];		
		$_SESSION['ccIdProdCant'][$_POST['idItem']] =  $_POST['s2'];
		
		}*/

$flagSubirPedido=1;
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

$maxRows_rsProd = $pedidosxvendedor;
$pageNum_rsProd = 0;
if (isset($_GET['pageNum_rsProd'])) {
  $pageNum_rsProd = $_GET['pageNum_rsProd'];
}
$startRow_rsProd = $pageNum_rsProd * $maxRows_rsProd;

mysql_select_db($database_conn, $conn);
if(!isset($_GET['buscar'])){
	$query_rsProd = "SELECT u.nombre as cnom, s.*, u.cod_usuario as cid, s.nombre as vnom
FROM (

SELECT u.nombre ,p. *, u.cod_usuario as vid
FROM PEDIDOS AS p, USUARIOS AS u
WHERE p.idVendedor = u.id
AND u.eliminado<>1
AND p.eliminado<>1
";
if(isset($_GET['uploaded'])){
 if($_GET['uploaded']==1) $query_rsProd .= " AND p.uploaded=1 ";
 else $query_rsProd .= " AND p.uploaded <> 1 ";
 }

$query_rsProd .= ") AS s, USUARIOS AS u
WHERE s.idCliente = u.id
AND u.eliminado<>1
ORDER BY s.fechaCompra DESC
";
//echo $query_rsProd;
}
else
	{
	
if(isset($_GET['rd1']) && $_GET['rd1']=="vendedor"){
	$qaux = "SELECT u.nombre as cnom, s.*, u.cod_usuario as cid, s.nombre as vnom
FROM (

SELECT u.nombre ,p. *, u.cod_usuario as vid
FROM PEDIDOS AS p, USUARIOS AS u
WHERE p.idVendedor = u.id  AND u.eliminado<>1 AND p.eliminado<>1 AND u.nombre LIKE '%" . strtoupper($_GET['buscar']) . "%'" ;
if(isset($_GET['uploaded'])){
 if($_GET['uploaded']==1) $qaux .= " AND p.uploaded=1 ";
 else $qaux .= " AND p.uploaded<>1 ";
 }


$qaux .= ") AS s, USUARIOS AS u
WHERE s.idCliente = u.id
AND u.eliminado<>1";
$qaux .= " ORDER BY s.fechaCompra DESC";
}
	else{
	$qaux = "SELECT u.nombre as vnom, s.*, u.cod_usuario as cid, s.nombre as cnom
FROM (

SELECT u.nombre ,p. *, u.cod_usuario as cid
FROM PEDIDOS AS p, USUARIOS AS u
WHERE p.idCliente = u.id   AND u.eliminado<>1 AND p.eliminado<>1 AND u.nombre LIKE '%" . strtoupper($_GET['buscar']) . "%'" ;
if(isset($_GET['uploaded'])){
 if($_GET['uploaded']==1) $qaux .= " AND p.uploaded=1 ";
 else $qaux .= " AND p.uploaded<>1 ";
 }

$qaux .= ") AS s, USUARIOS AS u
WHERE s.idVendedor = u.id
AND u.eliminado<>1";
$qaux .= " ORDER BY s.fechaCompra DESC";
	}
	
	$raux = mysql_query($qaux, $conn) or die(mysql_error());
	if(mysql_num_rows($raux)<=0) $arraux=-1;
	else{
	$rowaux = mysql_fetch_assoc($raux);
	do{
		$arraux[]= $rowaux['id']; 
	} while ($rowaux= mysql_fetch_assoc($raux));
	$arraux = implode(',',$arraux);
	}
	//echo $qaux;
	$query_rsProd= "SELECT s.*, u.nombre as cnom, u.cod_usuario as cid, s.nombre as vnom, s.cod_usuario as vid FROM (SELECT p.*, u.nombre, u.cod_usuario FROM PEDIDOS as p, USUARIOS as u WHERE p.idVendedor=u.id AND p.id IN ($arraux)) as s,
	USUARIOS as u WHERE s.idCliente=u.id";


	
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

mysql_select_db($database_conn, $conn);
if (isset($_GET['cat']))
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE id=" . $_GET['cat'];
else
$query_rsCat2 = "SELECT * FROM CATEGORI ORDER BY RAND()";
$rsCat2 = mysql_query($query_rsCat2, $conn) or die(mysql_error());
$row_rsCat2 = mysql_fetch_assoc($rsCat2);
$totalRows_rsCat2 = mysql_num_rows($rsCat2);
$catLocal = mysql_result($rsCat2,0,id);


if(isset($_GET['detalles']) && $_GET['detalles']==1){
mysql_select_db($database_conn, $conn);
$rsNota = mysql_query("SELECT tipoUsuario from USUARIOS where cod_usuario='" . $_GET['cod_cliente'] . "'", $conn) or die(mysql_error());
$row_rsNota = mysql_fetch_assoc($rsNota);


$qdet1 = "SELECT u.nombre as cnom, s.*, u.cod_usuario as cid, s.nombre as vnom
FROM (

SELECT u.nombre ,p. *, u.cod_usuario as vid
FROM PEDIDOS AS p, USUARIOS AS u
WHERE p.idVendedor = u.id
AND u.eliminado<>1
AND p.eliminado<>1
) AS s, USUARIOS AS u
WHERE s.idCliente = u.id
AND u.eliminado<>1 
 AND s.id=" . $_GET['idPedido'];
$rsdet1 = mysql_query($qdet1, $conn) or die(mysql_error());
$row_rsdet1 = mysql_fetch_assoc($rsdet1);

$qdet2 = "SELECT i.cod_item, i.cant_stock, i.des_item, dc.precioUnitario, dc.cantidad FROM
ITEMS as i, DETALLE_CARRITO as dc WHERE i.cod_item=dc.cod_item and dc.idCarrito=" . $_GET['idIndexCarrito']. " ORDER BY i.des_item" ;
$rsdet2 = mysql_query($qdet2, $conn) or die(mysql_error());
$row_rsdet2 = mysql_fetch_assoc($rsdet2);
$totalRows_rsdet2 = mysql_num_rows($rsdet2);

//echo "QDET1 $qdet1 <br>";
//echo "QDET2 $qdet2 <br>";
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
<script language="javascript">
	
		
function confirmar(pregunta){
	if(confirm(pregunta)) {
		document.location.href=  url;
	} 
}
</script>
<script language="javascript">
	
function conslink2(){
	var string;
	string = "verPedidos.php?buscar=" + document.form1.txtBuscar.value + "&rd1=";
	if(document.form1.rd1[0].checked)  
	string += document.form1.rd1[0].value;
		else
				string += document.form1.rd1[1].value;
	document.getElementById('linkBuscar').href = string;
}		
		

</script>
<script> 
function marcar(fila) { 
     document.getElementById(fila).style.backgroundColor='F7FEC0' ;
    }
function desmarcar(fila,color) { 
	var col = "'" + color + "'";
     document.getElementById(fila).style.backgroundColor=color ;
    }
</script> 

<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style8 {color: #FF0000}
.style9 {font-size: 13px}
.style10 {color: #00FF00}
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
                              <td height="254" align="left"><table cellpadding="0" cellspacing="0" border="0" >
                                      
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
                                        <p>&nbsp;</p>
                                        <p><img src="images/logoeloytranparente3.gif" alt="" width="295" height="77" >
                                        </p>
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
                                          <a href="contactenos.php"><img src="images/esp/m5.jpg" alt="" border="0"></a><br style="line-height:12px">
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
												 <?php if($_SESSION['conexion']==1){ ?>	 <li class="bg_list"><a href="subirCarrito.php">Ver pedidos pendientes</a></li><?php }?>
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
										                <?php echo $_SESSION['nombreClienAgen']?><br style="line-height:9px">
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
								
                                    <table cellpadding="0" cellspacing="0" border="0" style="width:800px" >
									<tr>                                       
										
                                            <td>
											<?php 	if(!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario']!=3) {?>
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
											<td>
											</td>
											</tr>
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
                                                    <tr>
                                                   	  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table">
												

                                                        	<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
														<tr>
														</tr>
                                                                <tr>
																  <td ><p class="cont_heading_td">Listado de pedidos 
																    
																    <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table> 
														            <div align="center" class="style8">
														    <?php if (isset($_GET['eliminar']) && $_GET['eliminar']==1){ ?>
														  El pedido ha sido removido de la lista.
														  <?php }?>
																    </div>
														            <form name="form1" method="post"  action="javascript: var string;
			string = 'verPedidos.php?buscar=' + document.form1.txtBuscar.value + '&rd1=';
	if(document.form1.rd1[0].checked)  
	string += document.form1.rd1[0].value;
		else
				string += document.form1.rd1[1].value;
			document.location.href = string;
	" onSubmit="javascript:
																	var string;
			string = 'verPedidos.php?buscar=' + document.form1.txtBuscar.value + '&rd1=';
	if(document.form1.rd1[0].checked)  
	string += document.form1.rd1[0].value;
		else
				string += document.form1.rd1[1].value;
			document.location.href = string;
	"> <div align="right">
                                                            <table border="0">
                                                              <tr>
                                                                <td><table border="0">
                                                                  <tr>
                                                                    <td><div align="left"><a href="verPedidos.php">Todos</a></div></td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><div align="left"><a href="verPedidos.php?uploaded=1">Realizados</a></div></td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><div align="left"><a href="verPedidos.php?uploaded=0,2">Pendientes y fallidos </a></div></td>
                                                                  </tr>
                                                                </table></td>
                                                                <td><p align="right"><strong>
                                                                  <input name="buscar" type="hidden" id="buscar" value="1">
                                                                  Buscar</strong>
                                                                      <input name="txtBuscar" type="text" id="txtBuscar">
  &nbsp; &nbsp; <a href="#" id="linkBuscar" onClick="javascript:conslink2()"><img src="images/k.gif" width="32" height="18"></a></p>
                                                                  <p align="right">
                                                                    <input type="radio" name="rd1" value="cliente" checked="checked">
                                                                    Cliente
  <input type="radio" name="rd1" value="vendedor">
                                                                    Vendedor</p>
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
															      </td>
                                                                </tr>
                                                            </table> 
														
                                                          <br style="line-height:9px">
                                                    
                                                            <table border="0" cellspacing="0" cellpadding="0" class="cont_heading_table">
                                                              <tr>
                                                                <td><p>
                                                                
                                                                <p>Mostrando <?php echo mysql_num_rows($rsProd) ?> 
                                                                  (de <?php echo $totalRows_rsProd ?>)
                                                                  <p>
                                                                    <?php if( mysql_num_rows($rsProd) <=0 ){?>
                                                                  <div align="center">
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
                                                               
                                                                  
                                                              
                                                                    <p><?php if ($_SESSION['tipoUsuario']==3) {
																	
																	
														  $ac=0;?>
                                                                  <form name="form2" method="post" action="">
                                                                      <table BORDER=1 FRAME=BOX RULES=NONE>
                                                                        <tr>
                                                                          <th>No</th>
                                                                          <th><div align="center">Id vendedor </div></th>
                                                                          <th>Nombre  vendedor </th>
                                                                          <th>Id cliente </th>
                                                                          <th>Nombre  cliente </th>
                                                                          <th>Fecha compra </th>
                                                                          <th>Estatus</th>
                                                                          <th></th>
                                                                          <th></th>
                                                                          <th></th>
                                                                        </tr>
                                                                        <?php do { ?>
                                                                        <tr id="fila<?php echo $row_rsProd['id']  ?>" <?php  if(isset($_GET['detalles']) && $_GET['idPedido']== $row_rsProd['id']) echo "bgcolor='#F7FEC0'"; else
																		{if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;}} ?>>
                                                                          <td><div align="center"><?php echo $row_rsProd['id']?></div></td>
                                                                          <td><div align="center"><?php echo $row_rsProd['vid']?></div></td>
                                                                          <td ><div align="center"><?php echo $row_rsProd['vnom']?></div></td>
                                                                          <td ><div align="center"><?php echo $row_rsProd['cid'] ?></div></td>
                                                                          <td><div align="center"><?php echo $row_rsProd['cnom'] ?></div></td>
                                                                          <td><div align="center"><?php echo  date("D d M Y h:i:s" ,strtotime($row_rsProd['fechaCompra'])) ?></div></td>
                                                                          <td><div align="center">
																		  <?php switch($row_rsProd['uploaded']){
																		  case 0: echo "Pendiente"; break;
    																	  case 1: echo '<span class="style10">Realizado</span>'; break;
																		  case 3: echo '<span class="style8">Fallido</span>'; break;												  
																		  } ?>
																		  
																		  
																		  </div></td>
                                                                          <td><div align="center"><a href="verPedidos.php?<?php $arr = array('detalles','idIndexCarrito','idPedido','cod_cliente','idCliente','idIndexCarrito','eliminar','generarArchivo','again' ); $cad = getExclusivo($arr); echo $cad; ?>&detalles=1&idIndexCarrito=<?php echo $row_rsProd['idIndexCarrito']?>&idPedido=<?php  echo $row_rsProd['id'] ?>&cod_cliente=<?php echo $row_rsProd['cid'] ?>"><img src="images/esp/button_details.gif" width="63" height="19"></a></div></td>
                                                                          <td ><div align="center"><a href="verPedidos.php?<?php   $arr=array('detalles','idIndexCarrito','idPedido','cod_cliente','idCliente','idIndexCarrito','eliminar','generarArchivo','again' ); 
		$cad = getExclusivo($arr);
		echo $cad . "&" ;  ?>eliminar=1&idPedido=<?php echo  $row_rsProd['id'] ?>" onClick="javascript:
		
		if(confirm('¿Esta seguro que desea eliminar el pedido?'))
			return true;
			else
				return false;">
                                                                            <?php if($row_rsProd['uploaded']!=1) { ?>
                                                                          <img src="images/esp/button_delete.gif"></a> 
                                                                              <?php }?>
                                                                          </div></td>
                                                                          <td><div align="center">
																		  
																		  <?php if($row_rsProd['uploaded']!=1) { ?><div><a href="verPedidos2.php?<?php 
																		  $arr = array('detalles','idIndexCarrito','idPedido','cod_cliente','idCliente','idIndexCarrito','eliminar','generarArchivo','again' ); $cad = getExclusivo($arr); echo $cad?>&idIndexCarrito=<?php echo $row_rsProd['idIndexCarrito']?>&generarArchivo=1&idPedido=<?php echo $row_rsProd['id']?>&idCliente=<?php 													  echo $row_rsProd['idCliente'];?>" target="_blank" id="lnk[<?php echo $row_rsProd['id']?>]"><img src="images/esp/button_checkout.gif" width="106" height="19" onClick="javascript: document.getElementById('fila<?php echo $row_rsProd['id']  ?>').style.backgroundColor='#66f32d'"></a><?php }else{?>
																		  <a href="verPedidos2.php?<?php  $arr = array('detalles','idIndexCarrito','idPedido','cod_cliente','idCliente','idIndexCarrito','eliminar','generarArchivo','again' ); $cad = getExclusivo($arr); echo $cad?>&idIndexCarrito=<?php echo $row_rsProd['idIndexCarrito']?>&generarArchivo=1&idPedido=<?php echo $row_rsProd['id']?>&idCliente=<?php 													  echo $row_rsProd['idCliente'];?>&again=1" target="_blank" id="lnk[<?php echo $row_rsProd['id']?>]">Volver a realizar pedido</a>
																		  <?php }?></div>
                                                                            <label></label></div></td>
                                                                        </tr>
                                                                        <?php } while ($row_rsProd = mysql_fetch_assoc($rsProd)); ?>
                                                                        <?php }?>
                                                                    </table>
                                                                
                                                                      <div align="center">
  <label></label>
</div>  </form>
<p><?php }?>                                                                  </p>
                                                                 <p align="left" class="result">Mostrando  <?php echo mysql_num_rows($rsProd) ?>
                                                                  (de <b><?php echo $totalRows_rsProd ?>)</b></p>
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
                                          </table>        
										      <p><?php if (isset($_GET['detalles']) && $_GET['detalles']==1){ ?>
					</p>
										      <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
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
                                                          <td><p>
                                                          
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
                                                              <?php if ($_SESSION['tipoUsuario']==3) {
															 
														 ?>
														  <?php if( $row_rsNota['tipoUsuario']==2 || $row_rsNota['tipoUsuario']==4) {?>  <p align="center">
																<?php if($row_rsNota['tipoUsuario']==4){?>
																	<strong>El comprador es nuevo, debe ser ingresado en MONICA</strong>
																    <?php }else{?>
																	<strong>Comprador por internet, verificar si se ha ingresado en MONICA</strong>
															<?php }?></p>
														  <?php }?>
															
															
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
    <td><?php echo  date("D d M Y h:i:s" ,strtotime($row_rsdet1['fechaCompra'])) ?></td>
  </tr>
</table>

                                                              <p>
                                                           
                                                              <table BORDER=1 FRAME=BOX RULES=NONE>
                                                                <tr>
                                                                  <th>Id producto </th>
                                                                  <th>Producto</th>
                                                                  <th>Precio unitario </th>
                                                                  <th>Cantidad deseada </th>
                                                                  <th><div align="center">Cantidad en existencia </div></th>
                                                                  <th>Subtotal</th>
                                                                </tr>
                                                                <?php 
																$ac=0;
																$total=0;
															do { ?>
                                                                <tr>
                                                                  <td   <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>><div align="center"><?php echo $row_rsdet2['cod_item']?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php echo $row_rsdet2['des_item'] ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo number_format($row_rsdet2['precioUnitario'])  ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php 
																  if ( $row_rsdet2['cantidad']>$row_rsdet2['cant_stock']){
																  echo "<span class='style8'>". $row_rsdet2['cantidad'] ."</span>";
																  $flagSubirPedido=0;
																  }
																  else echo $row_rsdet2['cantidad'] ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php 
																   if ( $row_rsdet2['cantidad']>$row_rsdet2['cant_stock'])
																   echo "<span class='style8'>" . $row_rsdet2['cant_stock'] . "</span>";
																   else echo $row_rsdet2['cant_stock'] ?></div></td>
                                                                  <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">₵<?php echo number_format($row_rsdet2['precioUnitario'] * $row_rsdet2['cantidad']); $total +=  $row_rsdet2['precioUnitario'] * $row_rsdet2['cantidad'] ?>&nbsp;</div></td>
                                                                </tr>
                                                                <?php } while ($row_rsdet2 = mysql_fetch_assoc($rsdet2)); ?>
                                                                <?php }?>
                                                            </table>
															  <label></label>
															  <?php if ($flagSubirPedido==1){ ?>
                                                              <table border="0">
                                                                <tr>
                                                                  <td width="24%">&nbsp;</td>
                                                                  <td width="76%"><table border="0">
                                                                    <tr>
                                                                      <td width="46%">&nbsp;</td>
                                                                      <td width="54%"><div align="left"><br></div>
                                                                        <table border="0">
                                                                          <tr>
                                                                            <td><div align="right" class="style9">Subtotal:</div></td>
                                                                            <td><div align="right" class="style9">₵<?php echo number_format($total) ?></div></td>
                                                                          </tr>
                                                                          <tr>
                                                                            <td><div align="right" class="style9">Impuestos:</div></td>
                                                                            <td><div align="right" class="style9">₵<?php echo number_format($total*$impuesto) ?></div></td>
                                                                          </tr>
                                                                          <tr>
                                                                            <td><span class="style9"></span></td>
                                                                            <td><hr style="height:1px"></td>
                                                                          </tr>
                                                                          <tr>
                                                                            <td><div align="right" class="style9">Total:</div></td>
                                                                            <td><div align="right" class="style9">₵<?php echo number_format($total+($total*$impuesto)) ?></div></td>
                                                                          </tr>
                                                                        </table></td>
                                                                    </tr>
                                                                  </table></td>
                                                                </tr>
                                                              </table>
                                                            <?php }?>
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
                                                            <span class="style8">El pedido no pudo ser llevado a cabo. La cantidad de productos requeridos excede a la existente.</span>&nbsp;<?php }?></p>
                                                          <p>&nbsp;</p></td>
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
                                              </table>
										      <p>					        
							          <?php }?></td>
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
                                    El Mago Don Eloy <?php echo date("Y")  ?><br>                                  </td>							
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
mysql_free_result($rsofaz);

mysql_free_result($rsProd);

mysql_free_result($rsCat2);
?>









