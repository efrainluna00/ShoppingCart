<?php require_once('../Connections/conn.php'); ?><?php require_once('../Connections/conn.php'); 
$currentPage = $_SERVER["PHP_SELF"];
?>
<?php include('common.php') ?>
<?php


if(isset($_POST['idUsuario'])){
mysql_select_db($database_conn, $conn);
$uq = "UPDATE USUARIOS set cod_monica='". $_POST['txtcm'] . "' WHERE id=" . $_POST['idUsuario'];
mysql_query($uq, $conn) or die(mysql_error());

}

mysql_select_db($database_conn, $conn);
$query_rsCat = "SELECT cod_cate,categoria FROM CATEGORI where eliminado<>1";
$rsCat = mysql_query($query_rsCat, $conn) or die(mysql_error());
$row_rsCat = mysql_fetch_assoc($rsCat);
$totalRows_rsCat = mysql_num_rows($rsCat);

$maxRows_rsUsuarios = $pedidosxvendedor;
$pageNum_rsUsuarios = 0;
if (isset($_GET['pageNum_rsUsuarios'])) {
  $pageNum_rsUsuarios = $_GET['pageNum_rsUsuarios'];
}
$startRow_rsUsuarios = $pageNum_rsUsuarios * $maxRows_rsUsuarios;

mysql_select_db($database_conn, $conn);
$query_rsUsuarios = "SELECT id,cod_usuario, password, tipoUsuario, email, nombre, apellido,direccion, cod_monica FROM usuarios WHERE eliminado<>1 ";
if(isset($_GET['tipoUsuario'])) $query_rsUsuarios .= " AND tipoUsuario =" . $_GET['tipoUsuario'];
if(isset($_GET['buscar'])){
	if($_GET['rd1']=='nombre')
 $query_rsUsuarios .= " AND nombre LIKE '%" . strtoupper($_GET['buscar']) . "%' ";
 else
 	 $query_rsUsuarios .= " AND cod_usuario LIKE '" . strtoupper($_GET['buscar']) . "' ";
 }
$query_rsUsuarios .= " ORDER BY nombre";
//echo $query_rsUsuarios;
$query_limit_rsUsuarios = sprintf("%s LIMIT %d, %d", $query_rsUsuarios, $startRow_rsUsuarios, $maxRows_rsUsuarios);
$rsUsuarios = mysql_query($query_limit_rsUsuarios, $conn) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);

if (isset($_GET['totalRows_rsUsuarios'])) {
  $totalRows_rsUsuarios = $_GET['totalRows_rsUsuarios'];
} else {
  $all_rsUsuarios = mysql_query($query_rsUsuarios);
  $totalRows_rsUsuarios = mysql_num_rows($all_rsUsuarios);
}
$totalPages_rsUsuarios = ceil($totalRows_rsUsuarios/$maxRows_rsUsuarios)-1;

$queryString_rsUsuarios = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsUsuarios") == false && 
        stristr($param, "totalRows_rsUsuarios") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsUsuarios = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsUsuarios = sprintf("&totalRows_rsUsuarios=%d%s", $totalRows_rsUsuarios, $queryString_rsUsuarios);

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
mysql_select_db($database_conn, $conn);
$query_rsofaz =  "SELECT i.cod_item, i.des_item, i.$cadPrecio, i.imagen FROM ITEMS as i, ITEMS_ESTADO as ie WHERE i.cod_item=ie.cod_item AND ie.estado=1
 AND ie.eliminado<>1 ORDER BY RAND() LIMIT 0,1";
$rsofaz = mysql_query($query_rsofaz, $conn) or die(mysql_error());
$row_rsofaz = mysql_fetch_assoc($rsofaz);
$totalRows_rsofaz = mysql_num_rows($rsofaz);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {color: #FF0000}
-->
</style>
<script language="javascript">
	
function conslink2(){
	var string;
	string = "verUsuarios.php?buscar=" + document.form1.txtBuscar.value + "&rd1=";
	if(document.form1.rd1[0].checked)  
	string += document.form1.rd1[0].value;
		else
				string += document.form1.rd1[1].value;
	document.getElementById('linkBuscar').href = string;
}		
		

</script>	
		

</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" class="w">
	<tr>
	  <td style="width:100%"><table cellpadding="0" cellspacing="0" border="0" style="width:716px" align="center"> 
                <tr>
                    <td align="center">
                        <table cellpadding="0" cellspacing="0" border="0" >
                            <tr>
                                <td height="317" align="left"><table cellpadding="0" cellspacing="0" border="0" >
                                      
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
                                  <p>&nbsp;</p>
                                  <p><img src="images/logoeloytranparente3.gif" alt="" width="295" height="77" >
                                  </p>
                              </td>
                            </tr>
                        </table>                  </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" style="height:583px ">
                            <tr>
                              <td class="col_left"><table cellpadding="0" cellspacing="0" border="0" class="box_width_left">
									<?php if($_SESSION['tipoUsuario']==3){?>   <tr>
									<td>
									<a href="index.php"><img src="images/esp/m1.jpg" alt="" border="0"></a><br>
                                          <a href="index.php?estado=0"><img src="images/esp/m2.jpg" alt="" border="0"></a><br>
                                          <a href="index.php?estado=1"><img src="images/esp/m3.jpg" alt="" border="0"></a><br>
                                          <a href="login.php<?php if (isset($_SESSION['logged'])) echo "?logout=1" ?>"><img src="images/esp/m4.jpg" alt="" border="0"></a><br>
                                         <a href="contactenos.php"><img src="images/esp/m5.jpg" alt="" border="0"></a><br>
									
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
													  <?php if($_SESSION['conexion']==1){ ?><li class="bg_list"><a href="subirCarrito.php">Ver pedidos pendientes</a></li><?php }?>
													<li class="bg_list"><a href="carritoForm.php">Formulario carrito</a></li><?php if($_SESSION['conexion']==1){ ?> <li class="bg_list"><a href="actualizarBD.php">Cargar base de datos</a></li><?php }?>
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
                                         <img alt="" src="images/line4.gif"><br>                                           <?php }?> </td>
                                      </tr>
                                    </table>&nbsp;</td>
                                <td><img alt="" src="images/spacer.gif" width="11" height="1"></td>
                              <td class="col_center"><?php 	if(!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario']!=3) {?>
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
                                                  </table>
                                                    <br style="line-height:9px">
                                                    <table cellpadding="0" cellspacing="0" border="0" style="height:32px " class="product">
                                                      <tr>
                                                        <td><table border="0" class="product">
                                                            <tr>
                                                              <td width="95%"><p align="center">Usted no tiene permisos para acceder a esta area. <a href="registrarUsuario.php"></a></p></td>
                                                            </tr>
                                                          </table>
                                                            <table>
                                                              <tr>
                                                                <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                                <td width="1%" class="cont_body_tall_b" style="width:100%"></td>
                                                                <td width="4%"><img src="images/cont_corn_br.gif" alt=""></td>
                                                              </tr>
                                                            </table></td>
                                                      </tr>
                                                  </table></td>
                                              </tr>
                                              <tr>
                                                <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                <td style="width:100%" class="cont_body_tall_b"></td>
                                                <td><img src="images/cont_corn_br.gif" alt=""></td>
                                              </tr>
                                            </table>
                                            <?php exit;} ?>
                                            <table cellpadding="0" cellspacing="0" border="0" style="400px" >
                                              <tr>
                                                <td><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" style="width:700px" >
                                                    <tr>
                                                      <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                      <td style="width:100%" class="cont_body_tall_t"></td>
                                                      <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
                                                    <tr>
                                                      <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table"><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                          <tr>
                                                            <td class="cont_heading_td">Usuarios</td>
                                                          </tr>
                                                        </table>
                                                          <table cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                              <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                            </tr>
                                                          </table>
                                                        <table cellpadding="0" cellspacing="0" border="0" style="height:32px">
                                                            <tr>
                                                              <td><p align="center">
                                                                <?php if (isset($_POST['idUsuario'])){ ?>
                                                                <span class="style2">
																La modificacion ha sido efectuada con exito.</span>
                                                                <?php }?></p>
                                                                <table border="0">
                                                                  <tr>
                                                                    <td><a href="verUsuarios.php">Todos</a></td>
                                                                    <td rowspan="4"><div align="right">
                                                                        <form name="form1" method="post"action="javascript: var string;
			string = 'verUsuarios.php?buscar=' + document.form1.txtBuscar.value + '&rd1=';
	if(document.form1.rd1[0].checked)  
	string += document.form1.rd1[0].value;
		else
				string += document.form1.rd1[1].value;
			document.location.href = string;
	" onSubmit="javascript:
																	var string;
			string = 'verUsuarios.php?buscar=' + document.form1.txtBuscar.value + '&rd1=';
	if(document.form1.rd1[0].checked)  
	string += document.form1.rd1[0].value;
		else
				string += document.form1.rd1[1].value;
			document.location.href = string;
	">
                                                                          <p><strong>Buscar:</strong>
                                                                            <input name="txtBuscar" type="text" id="txtBuscar">
  &nbsp; &nbsp; <a href="#" id="linkBuscar" onClick="javascript:conslink2()"><img src="images/k.gif" width="32" height="18"></a>                                                                          </p>
                                                                          <p> <input type="radio" name="rd1" value="nombre" checked="checked">
                                                                          Nombre
                                                                            <input type="radio" name="rd1" value="codigo">
                                                                   Codigo</p>  
                                                                        </form>
                                                                    </div></td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><a href="verUsuarios.php?tipoUsuario=0">Clientes</a></td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><a href="verUsuarios.php?tipoUsuario=1">Agentes</a></td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><a href="verUsuarios.php?tipoUsuario=2">Usuarios de internet </a></td>
                                                                  </tr>
                                                                </table>
                                                                  <table border="0" width="50%" align="center" class="result">
                                                                    <tr>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsUsuarios > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, 0, $queryString_rsUsuarios); ?>">Primero</a>
                                                                          <?php } // Show if not first page ?>
                                                                      </td>
                                                                      <td width="31%" align="center"><?php if ($pageNum_rsUsuarios > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, max(0, $pageNum_rsUsuarios - 1), $queryString_rsUsuarios); ?>">Anterior</a>
                                                                          <?php } // Show if not first page ?>
                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsUsuarios < $totalPages_rsUsuarios) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, min($totalPages_rsUsuarios, $pageNum_rsUsuarios + 1), $queryString_rsUsuarios); ?>">Siguiente</a>
                                                                          <?php } // Show if not last page ?>
                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsUsuarios < $totalPages_rsUsuarios) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, $totalPages_rsUsuarios, $queryString_rsUsuarios); ?>">Ultimo</a>
                                                                          <?php } // Show if not last page ?>
                                                                      </td>
                                                                    </tr>
                                                                  </table>
                                                                </p>
                                                              
                                                                    <table BORDER=1 FRAME=BOX RULES=NONE>
                                                                      <tr>
                                                                        <th><div align="center">Codigo usuario </div></th>
                                                                        <th><div align="center">Nombre</div></th>
                                                                        <th><div align="center">Apellido</div></th>
                                                                        <th><div align="center">Contraseña</div></th>
                                                                        <th><div align="center">Clase usuario</div></th>
                                                                        <th><div align="center">Email</div></th>
                                                                        <th><div align="center">Direccion</div></th>
                                                                        <th><div align="center">Codigo Monica  </div></th>
                                                                      </tr>
                                                                      <?php do { ?>
                                                                      <tr>
                                                                        <td <?php if ($ac == 1) $ac=0; else { echo 'bgcolor="#E6E6E6"'; $ac=1;} ?>><div align="center">
                                                                            <div align="center"><?php echo $row_rsUsuarios['cod_usuario']; ?></div></td>
                                                                        <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?> ><div align="center">
                                                                            <?php if($row_rsUsuarios['nombre']=="") echo "N/A"; 
																else 
																	echo $row_rsUsuarios['nombre']; ?>
                                                                        </div></td>
                                                                        <td <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?> ><div align="center">
                                                                            <?php if($row_rsUsuarios['apellido']=="" || $row_rsUsuarios['apellido']=='NULL' ) echo "N/A"; 
																else 
																	echo $row_rsUsuarios['apellido']; ?>
                                                                        </div></td>
                                                                        <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center"><?php echo $row_rsUsuarios['password']; ?></div></td>
                                                                        <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
                                                                            <?php 
																switch($row_rsUsuarios['tipoUsuario']){
																case 0: echo "Cliente";break;
																case 1: echo "Agente";break;
																case 2: echo "Usuario de internet";break;
																case 3: echo "Administrador";break; 
																}
																?>
                                                                        </div></td>
                                                                        <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
                                                                            <?php 
																if($row_rsUsuarios['email']==""|| $row_rsUsuarios['email']=='NULL' ) echo "N/A"; 
																else 
																	echo $row_rsUsuarios['email'];
																?>
                                                                        </div></td>
                                                                        <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
                                                                            <?php 
																if($row_rsUsuarios['direccion']==""|| $row_rsUsuarios['direccion']=='NULL' ) echo "N/A"; 
																else 
																	echo $row_rsUsuarios['direccion'];
																?>
                                                                        </div></td>
                                                                        <td  <?php if ($ac != 0) echo 'bgcolor="#E6E6E6"'; ?>><div align="center">
                                                                          <label>
                                                                          <div align="left">
                                                                            <?php if($row_rsUsuarios['tipoUsuario']==2){?>
																			    <form name="form2" method="post" action="verUsuarios.php?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                                                            <input name="txtcm" type="text" id="txtcm" maxlength="8" value="<?php if ($row_rsUsuarios['cod_monica']!="NULL" && $row_rsUsuarios['cod_monica']!="" ) echo $row_rsUsuarios['cod_monica'];  ?>">
                                                                            </label>
                                                                          <div align="center">
                                                                            <input type="image" name="imageField" src="images/esp/button_update.gif">
                                                                            <input name="idUsuario" type="hidden" id="idUsuario" value="<?php echo $row_rsUsuarios['id'] ?>"> </form>
                                                                            <?php }else{?>&nbsp;<?php }?>
                                                                          </div>
                                                                        </td>
                                                                      </tr>
                                                                      <?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
                                                                    </table>
                                                               
                                                                  <p>&nbsp;</p>
                                                                  <table border="0" width="50%" align="center" class="result">
                                                                    <tr>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsUsuarios > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, 0, $queryString_rsUsuarios); ?>">Primero</a>
                                                                          <?php } // Show if not first page ?>
                                                                      </td>
                                                                      <td width="31%" align="center"><?php if ($pageNum_rsUsuarios > 0) { // Show if not first page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, max(0, $pageNum_rsUsuarios - 1), $queryString_rsUsuarios); ?>">Anterior</a>
                                                                          <?php } // Show if not first page ?>
                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsUsuarios < $totalPages_rsUsuarios) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, min($totalPages_rsUsuarios, $pageNum_rsUsuarios + 1), $queryString_rsUsuarios); ?>">Siguiente</a>
                                                                          <?php } // Show if not last page ?>
                                                                      </td>
                                                                      <td width="23%" align="center"><?php if ($pageNum_rsUsuarios < $totalPages_rsUsuarios) { // Show if not last page ?>
                                                                          <a href="<?php printf("%s?pageNum_rsUsuarios=%d%s", $currentPage, $totalPages_rsUsuarios, $queryString_rsUsuarios); ?>">Ultimo</a>
                                                                          <?php } // Show if not last page ?>
                                                                      </td>
                                                                    </tr>
                                                              </table></td>
                                                            </tr>
                                                          </table>
                                                        <table cellpadding="0" cellspacing="0" border="0">
                                                          <tr>
                                                            <td class=" line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                          </tr>
                                                        </table></td>
                                                    </tr>
                                                    <tr>
                                                      <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                      <td style="width:100%" class="cont_body_tall_b"></td>
                                                      <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                    </tr>
                                                </table></td>
                                              </tr>
                                              <tr>
                                                <td>&nbsp;</td>
                                              </tr>
                                            </table>&nbsp;</td>
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
                                    El Mago Don Eloy <?php echo date("Y")  ?><br>                                 </td>							
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
mysql_free_result($rsCat);

mysql_free_result($rsUsuarios);

mysql_free_result($rsofaz);
?>









