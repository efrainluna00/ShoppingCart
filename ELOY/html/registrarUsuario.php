<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}


$registrado="";


mysql_select_db($database_conn, $conn);
$query_rsCat = "SELECT cod_cate,categoria FROM CATEGORI where eliminado<>1";
$rsCat = mysql_query($query_rsCat, $conn) or die(mysql_error());
$row_rsCat = mysql_fetch_assoc($rsCat);
$totalRows_rsCat = mysql_num_rows($rsCat);
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


mysql_select_db($database_conn, $conn);
$query_rsProd = "SELECT id,name_empre FROM EMPRESAS ORDER BY name_empre";
if(isset($_GET['indice']))
	$query_rsProd = "SELECT id,name_empre FROM EMPRESAS WHERE name_empre LIKE '". $_GET['indice'] ."%' ORDER BY name_empre";
	
	
	
	





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

function validate_email($str){ 
$str = strtolower($str); 
/* Agrega todas las extensiones que quieras
*/
if(ereg("^([^[:space:]]+)@(.+)\.(com|ar|net|org|cv|cr|sv|uk|mx)$",$str)){ 
return 1; 
} else { 
return 0; 
} 
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
</script>
<title>Carrito de Compras</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style4 {
	color: #000000;
	font-weight: bold;
}
.style5 {color: #FF0000}
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
                                        <br></td></tr>
                              </table>                              </td>
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
										
                                            <td><p><?php if($_POST['registrar']==1){
											foreach($_POST as $key => $value){
											if($value!=""){
											$cad = "\$" . $key ."='" . $value. "';";
											//echo $cad;
											eval($cad);
											}
											}
											
											
											
											?>&nbsp;</p>
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
									    <td width="95%">  
											  <?php if( !isset($txtLogin) || !isset($txtPassword) || !isset($txtCorreoElectronico) || !isset($txtNombre) || !isset($txtApellido) || !(validate_email($txtCorreoElectronico)) || !isset($txtDireccion)  ){  ?>
											  <?php if(!isset($txtLogin)){?> <p class='style5' align="center"> Debe introducir un nombre de usuario</p><?php }?> 										<?php if(!isset($txtPassword)){?> <p align="center" class='style5'> Debe introducir una contraseña</p>
											<?php }?>
											<?php if(!isset($txtCorreoElectronico)){?> <p align="center" class='style5'> Debe introducir un correo electronico</p>
											<?php }?>
											<?php if (isset($txtCorreoElectronico) && !(validate_email($txtCorreoElectronico)) ) { ?>
											<p align="center" class='style5'> Debe introducir un correo electronico valido</p>
											<?php 
											}?>
										<?php if(!isset($txtNombre)){?> <p align="center" class='style5'> Debe introducir un nombre </p>
											<?php }?>
										<?php if(!isset($txtApellido)){?> <p align="center" class='style5'> Debe introducir un apellido</p><?php }?>
										<?php if(!isset($txtDireccion)){?> <p align="center" class='style5'> Debe introducir una direccion</p>
											<?php }?>					
											
											<?php }else{
											mysql_select_db($database_conn, $conn);
											$q = "SELECT id FROM USUARIOS where cod_usuario='$txtLogin'";
											$rsUsuario = mysql_query($q, $conn) or die(mysql_error());
											if(mysql_num_rows($rsUsuario)>0){?>	
											<p align="center" class='style5'> Ya existe un usuario registrado con ese nombre, favor seleccionar otro</p>
																						
											
											  <?php }else{
											mysql_select_db($database_conn, $conn);
											$q= "INSERT INTO USUARIOS VALUES ('$txtLogin','$txtPassword','NULL','2','1',";
											if(isset($txtDireccion)) $q .= "'$txtDireccion',";
											else $q .= "'NULL',";
											$q .=  "'$txtCorreoElectronico','$txtNombre','$txtApellido','NULL','NULL')";
											//echo $q;
											 mysql_query($q, $conn) or die(mysql_error());
											 $txtLogin = "";
											 $txtPassword = "";
											 $txtDireccion = "";
											 $txtNombre = "";
											 $txtApellido = "";
											 $txtCorreoElectronico = "";
											 $registrado=1;
											 
											 
											?><div align="center">
											  <p>Su solicitud de registro ha sido llevada a cabo.</p>
											  <p>Haga click <a href="login.php">aqui</a> para iniciar sesion										            </p>
										  </div>
											    <?php }?>
											    <?php }?>												     
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
                                              </table>
                                              <p>
                                                <?php }?>
                                              </p>
                                              <p><?php if($registrado!=1){?>&nbsp;</p>
                                              <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                <tr>
                                                  <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                          <td style="width:100%" class="cont_body_tall_t"></td>
                                                          <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                </tr>
                                                <tr>
                                                  <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table">
                                                    
                                                    
                                                    <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                     
                                                      <tr>
                                                        <td class="cont_heading_td">formulario de registro </td>
                                                      </tr>
													  
                                                    </table> 
														  
                                                          <table cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                              <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                            </tr>
                                                          </table>
                                                          <p>&nbsp;</p>
                                                          <form name="form1" method="post" action="registrarUsuario.php">
                                                            <table border="0">
                                                              <tr>
                                                                <td width="33%"><div align="left">Login<span class="style5">*</span></div></td>
                                                                <td width="67%">
                                                                  <label></label>
                                                                  <div align="left">
                                                                    <input name="txtLogin" type="text" size="60" >
                                                                  </div></td>
                                                              </tr>
                                                              <tr>
                                                                <td><div align="left">Password<span class="style5">*</span></div></td>
                                                                <td>
                                                                  <div align="left">
                                                                    <input name="txtPassword" type="password" size="60" >
                                                                  </div></td>
                                                              </tr>
                                                              <tr>
                                                                <td>Nombre<span class="style5">*</span></td>
                                                                <td><input name="txtNombre" type="text" size="60" value="<?php if(isset($txtNombre)) echo $txtNombre?>" ></td>
                                                              </tr>
                                                              <tr>
                                                                <td>Apellido<span class="style5">*</span></td>
                                                                <td><input name="txtApellido" type="text" size="60" value="<?php if(isset($txtApellido)) echo $txtApellido?>" ></td>
                                                              </tr>
                                                              <tr>
                                                                <td><div align="left">Direccion<span class="style5">*</span></div></td>
                                                                <td>                                                                  <label>
                                                                <div align="left">
                                                                    <textarea name="txtDireccion" cols="60"></textarea>
                                                                </div>
                                                                  </label>                                                                </td>
                                                              </tr>
                                                              <tr>
                                                                <td><div align="left">Correo Electronico<span class="style5">*</span></div></td>
                                                                <td>
                                                                  <div align="left">
                                                                    <input name="txtCorreoElectronico" type="text" size="60" value="<?php if(isset($txtCorreoElectronico)) echo $txtCorreoElectronico?>">
                                                                  </div></td>
                                                              </tr>
                                                              <tr>
                                                                <td>&nbsp;</td>
																<td><div align="left"><span class="style5">*Obligatorio</span></div></td>
                                                              </tr>
                                                              <tr>
                                                                <td colspan="2"><div align="center">
                                                                  <label>
                                                                  <input type="hidden" name="registrar" value="1">
                                                                  <input type="submit" name="Submit" value="ok">
                                                                  </label>
                                                                </div></td>
                                                              </tr>
                                                            </table>
                                                    </form>
                                                  </td>
                                                </tr>
                                                <tr>
                                                  <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                                          <td style="width:100%" class="cont_body_tall_b"></td>
                                                          <td><img src="images/cont_corn_br.gif" alt=""></td>
                                                </tr>
												
                                          </table>     <?php }?>                                   </td>
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
mysql_free_result($rsCliente);

mysql_free_result($rsCat2);
?>









