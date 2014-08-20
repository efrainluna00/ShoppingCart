<script type="text/javascript" src="funciones.js"></script>

<?php 
//header('Content-Type: text/html; charset=ISO-8859-1');
if($_REQUEST['v']>=0){
$hostname_conn = "LOCALHOST";
$database_conn = "ELOY";
$username_conn = "root";
$password_conn = "";

$conn = mysql_pconnect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); ?>
<?php 
//echo $query_rsProd;
//echo $query_rsProd;

mysql_select_db($database_conn, $conn);
$query_rsProd = "SELECT * FROM ITEMS ORDER BY RAND() LIMIT 0,1";
$rsProd = mysql_query($query_rsProd, $conn) or die(mysql_error());
$row_rsProd = mysql_fetch_assoc($rsProd);
?>
<div align="center">
  <table border="0">
    <tr>
      <td colspan="2" class="vam" style="height:19px" align="center"><p id="des"><?php echo $row_rsProd["des_item"]; ?></p><img src="<?php $foto=  $rutaFotos . $row_rsProd["imagen"]; 
								$foto = substr($row_rsProd["imagen"],11);
								$foto = str_replace("\\","/",$foto);
								echo $foto;
																			  
																			  ?>" alt="" border="0" height="150" width="130" align="middle"><div align="center"><a href="agrandar.php?imagen=<?php $foto=  $rutaFotos . $row_rsProd["imagen"]; 
								$foto = substr($row_rsProd["imagen"],11);
								$foto = str_replace("\\","/",$foto);
								echo $foto;
																			  
																			  ?>" target="_blank">Click para agrandar</a></div></td>
    </tr>
    <tr>
      <td style="height:19px" class="vam" align="center"><span class="productSpecialPrice"><span class="vam" style="height:19px">&#8373;
            <?php 
															echo number_format($row_rsProd["precio"]);
																	
																		 ?>
      </span><img alt="" src="images/spacer.gif" width="10" height="1"><a href="#"></a></td>
    </tr>
    <tr>
      <td style="height:19px" class="vam" align="center"><p>Cantidad disponible: <span class="vam" style="height:19px"><?php echo $row_rsProd["cant_stock"] ?></span></p></td>
    </tr>
    <tr>
      <?php if ($row_rsProd["expiracion"]!="0000-00-00"  && $row_rsProd["expiracion"]!=null) {?>
      <td style="height:19px" class="vam" align="center"><p>Fecha de expiraci&oacute;n: </p>
          <?php echo date("l d F Y", strtotime($row_rsProd["expiracion"])) ?></td>
      <?php }?>
    </tr>
    <tr>
      <td style="height:19px" class="vam" align="center">
          <label></label>
          <p><strong>Cantidad</strong>
            <input name="s2" type="text" value="1" size="2">
            <input type="image" name="imageField" src="images/esp/button_add_to_cart1.gif" id="ac">
            </a></p>
     </td>
    </tr>
  </table>
  
</div>
<?php }?>
