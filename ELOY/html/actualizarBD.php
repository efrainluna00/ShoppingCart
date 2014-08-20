<?php require_once('../Connections/conn.php'); ?>
<?php include('common.php');
?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

if (!isset($_SESSION)) {
  session_start();
}



mysql_select_db($database_conn, $conn);
$query_rsCat = "SELECT cod_cate,categoria FROM CATEGORI where eliminado<>1";
$rsCat = mysql_query($query_rsCat, $conn) or die(mysql_error());
$row_rsCat = mysql_fetch_assoc($rsCat);
$totalRows_rsCat = mysql_num_rows($rsCat);

/*mysql_select_db($database_conn, $conn);
if (isset($_GET['cat']))
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE id=" . $_GET['cat'] . "AND eliminado <> 1";
else
$query_rsCat2 = "SELECT * FROM CATEGORI WHERE eliminado <> 1 ORDER BY RAND()";
$rsCat2 = mysql_query($query_rsCat2, $conn) or die(mysql_error());
$row_rsCat2 = mysql_fetch_assoc($rsCat2);
$totalRows_rsCat2 = mysql_num_rows($rsCat2);
$catLocal = mysql_result($rsCat2,0,id);*/

mysql_select_db($database_conn, $conn);

/*
$q = mysql_query("SELECT CURRENT_USER() as sel", $conn) or die(mysql_error());
echo mysql_result($q,0,sel);*/

//PRIMERO INSERTO LOS DATOS EN LA TABLA CATEOGORI
$qact = "LOAD DATA INFILE 'C:/ELOY/BD/VENDEDOR.txt' REPLACE INTO TABLE VENDEDOR
 FIELDS TERMINATED BY '!#!'";
 mysql_unbuffered_query($qact, $conn) or die(mysql_error());
 
 $qact = "LOAD DATA INFILE 'C:/ELOY/BD/ITEMS.txt' REPLACE INTO TABLE ITEMS
 FIELDS TERMINATED BY '!#!'";
 mysql_unbuffered_query($qact, $conn) or die(mysql_error());

$qact = "LOAD DATA INFILE 'C:/ELOY/BD/CATEGORI.txt' REPLACE INTO TABLE CATEGORI
 FIELDS TERMINATED BY '!#!'";
 mysql_unbuffered_query($qact, $conn) or die(mysql_error());

$qact = "LOAD DATA INFILE 'C:/ELOY/BD/ITEMS_ESTADO.txt' REPLACE INTO TABLE ITEMS_ESTADO
 FIELDS TERMINATED BY '!#!'";
 mysql_unbuffered_query($qact, $conn) or die(mysql_error());

$qact = "LOAD DATA INFILE 'C:/ELOY/BD/EMPRESAS.txt' REPLACE INTO TABLE EMPRESAS
 FIELDS TERMINATED BY '!#!'";
 mysql_unbuffered_query($qact, $conn) or die(mysql_error());

$qact = "LOAD DATA INFILE 'C:/ELOY/BD/USUARIOS.txt' REPLACE INTO TABLE USUARIOS
 FIELDS TERMINATED BY '!#!'";
 mysql_unbuffered_query($qact, $conn) or die(mysql_error());


 
 /*
;
;
;
;
";*/


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
<title>EL MAGO DON ELOY</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link href="style.css" rel="stylesheet" type="text/css">
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
<body><table cellpadding="0" cellspacing="0" border="0" class="w">
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
										<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td width="68%" align="center"><br>
                                          <table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                            <tr>
                                              <td><img src="images/cont_corn_tl.gif" alt=""></td>
                                              <td style="width:100%" class="cont_body_tall_t"></td>
                                              <td><img src="images/cont_corn_tr.gif" alt=""></td>
                                            </tr>
                                            <tr>
                                              <td colspan="3" style="width:100%; border:1px solid #FFFFFF; border-width:0 16px 0 15px" class="cont_body_table"><table cellpadding="0" cellspacing="0" border="0" class="cont_heading_table">
                                                  <tr>
                                                    <td class="cont_heading_td">actualizacion de base de datos </td>
                                                  </tr>
                                                </table>
                                                  <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                      <td class="line_x"><img alt="" src="images/spacer.gif" width="1" height="1"></td>
                                                    </tr>
                                                  </table>
                                                <?php 	if(!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario']!=1) {?>
                                                  <p class="cont_heading_table" align="center">Usted no tiene permisos para acceder aqui.</p>
                                                <p>
                                                    <?php }else{ ?>
                                                </p>
                                                <p align="center">Al acceder a esta pagina se cargan los datos guardados en su computadora a la base de datos local. Cada vez que vaya a trabajar sin conexion, recuerde antes descargar los archivos del servidor.</p>
                                                <p align="center">Los datos han sido actualizados exitosamente. </p>
                                                <p>
                                                  <?php }?>
                                                </p>
                                            </tr>
                                            <tr>
                                              <td><img src="images/cont_corn_bl.gif" alt=""></td>
                                              <td style="width:100%" class="cont_body_tall_b"></td>
                                              <td><img src="images/cont_corn_br.gif" alt=""></td>
                                            </tr>
                                          </table></td></tr>
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
													  </li> <?php if($_SESSION['conexion']==1){ ?>
													 <li class="bg_list"><a href="subirCarrito.php">Ver pedidos pendientes</a></li><?php }?>
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
                                         <img alt="" src="images/line2.gif"></td>
                                      </tr>
                                    </table>&nbsp;</td>
                                <td><img alt="" src="images/spacer.gif" width="11" height="1"></td>
                                <td class="col_center">
								
                                    <table cellpadding="0" cellspacing="0" border="0" style="400px" >
									<tr>                                       
										
                                            <td>&nbsp;</td>
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
                                  El Mago Don Eloy <?php echo date("Y")  ?><br>                            </td>							
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
//mysql_free_result($rsCliente);

//mysql_free_result($rsCat2);
?>









