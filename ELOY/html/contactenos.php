<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php') ?>
<?php


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
<script language="javascript" type="text/javascript">
function vacio(q) {
        for ( i = 0; i < q.length; i++ ) {
                if ( q.charAt(i) != " " ) {
                        return true
                }
        }
        return false
}
function valida(F) {
        
        if((vacio(F.nombre.value) == false) || (vacio(F.asunto.value) == false)|| (vacio(F.correo.value) == false)|| 
		(vacio(F.mensaje.value) == false)) {
                alert("Debe llenar todos los campos para poder enviar su comentario")
                return false
        } else {
                        var b=/^[^@\s]+@[^@\.\s]+(\.[^@\.\s]+)+$/
				        a = b.test(F.correo.value)
						if(a == true){
							return true
						}else{
			                alert("El correo introducido es incorrecto")
							return false
						}
        }
        
}
</script>
<style type="text/css">
<!--
.style6 {
	font-size: 13px;
	font-weight: bold;
}
.style7 {font-size: 13px}
.style8 {color: #000000}
.style9 {font-size: 13px; font-weight: bold; color: #000000; }
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
								
                                    <table cellpadding="0" cellspacing="0" border="0" style="400px" >
									<tr>                                       
										
                                            <td><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table" >
                                                    <tr>
                                                        <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                                        <td style="width:100%" class="cont_body_tall_t"></td>
                                                        <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                                    </tr>
                                                    <tr>
                                                    	<td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table">
                                                        	<table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                                <tr>
																<td class="cont_heading_td">CONTACTENOS</td>
                                                                </tr>
                                                          </table>  
																  <table cellpadding="0" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                                </tr>
                                                          </table>       <form id="form" method="post" action="emailer.php" onSubmit="return valida(this);">
                                                                  <p>&nbsp;</p>
                                                                  <table cellpadding="0" cellspacing="0" border="0" style="height:32px " class="product">
                                                    <tr><td><table width="398" border="0" align="center">
                                                      <tr>
                                                        <td width="136" height="24"><div align="left" class="style6 style8"><font face="trebuchet ms, Arial, Helvetica">Nombre</font></div></td>
                                                        <td><font color="02088D"><strong>
                                                          <input name="nombre" type="text" size="35" maxlength="50" class="pn">
                                                        </strong></font></td>
                                                      </tr>
                                                      <tr>
                                                        <td width="136" height="24"><div align="left" class="style9"><font face="trebuchet ms, Arial, Helvetica">Email</font></div></td>
                                                        <td width="252"><font color="02088D"><strong>
                                                          <input name="correo" type="text" size="35" maxlength="50" class="pn">
                                                        </strong></font></td>
                                                      </tr>
                                                      <tr>
                                                        <td height="24"><p align="left" class="pn style7 style8"><strong><font face="trebuchet ms, Arial, Helvetica">Asunto</font></strong></p></td>
                                                        <td><font color="02088D"><strong>
                                                          <input name="asunto" type="text" size="35" maxlength="50" class="pn">
                                                        </strong></font></td>
                                                      </tr>
                                                      <tr>
                                                        <td height="34"><div align="right" class="style7"></div>
                                                            <div align="left" class="style9"><font face="trebuchet ms, Arial, Helvetica">Comentario</font></div></td>
                                                        <td height="34"><font color="02088D"><strong>
                                                          <textarea name="mensaje" cols="50" rows="5"></textarea>
                                                        </strong></font></td>
                                                      </tr>
                                                      <tr>
                                                        <td height="26"><center>
                                                        </center></td>
                                                        <td height="26"><font color="02088D">
                                                          <input name="Submit" type="submit" class="pnn" value="Enviar">
                                                          <input name="Submit2" type="reset" class="pnn" value="Reestablecer">
                                                          <strong> </strong></font></td>
                                                      </tr>
                                                      <tr>
                                                        <td height="21" colspan="2"><div align="center"><strong><font color="02088D">
                                                            <input type="hidden" name="subject" value="Asphal--Formulario de Pagina Web" />
                                                            <input type="hidden" name="reply_to_field" value="" />
                                                            <input type="hidden" name="required_fields" value="nombre" />
                                                            <input type="hidden" name="required_email_fields" value="" />
                                                            <input type="hidden" name="recipient_group" value="0" />
                                                            <input type="hidden" name="error_page" value="" />
                                                            <input type="hidden" name="thanks_page" value="confirmacion.php" />
                                                            <input type="hidden" name="send_copy" value="no" />
                                                            <input type="hidden" name="copy_subject" value="" />
                                                            <input type="hidden" name="copy_tomail_field" value="" />
                                                            <input type="hidden" name="mail_type" value="vert_table" />
                                                            <input type="hidden" name="mail_priority" value="1" />
                                                            <input type="hidden" name="return_ip" value="1" />
                                                        </font></strong></div></td>
                                                      </tr>
                                                    </table>
                                                    <p>
                                                
    <center>
    </center>
  </form></td>
                                                    </tr>
                                                 </table>
                                               
                                      </td>
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
                                   El Mago Don Eloy <?php echo date("Y")  ?><br>                                 </td>							
                            </tr>
                        </table>                  </td>
                </tr>
</table>
            </p>
      </td>
	</tr>
</body>
</html>
<?php
mysql_free_result($rsCat);

mysql_free_result($rsofaz);
?>









