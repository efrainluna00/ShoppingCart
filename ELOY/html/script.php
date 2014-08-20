<?php require_once('../Connections/conn.php'); ?>
<?php 
mysql_select_db($database_conn, $conn);
$query = "SELECT id FROM ITEMS";
$rs = mysql_query($query, $conn) or die(mysql_error());
$row = mysql_fetch_row($rs);
do{
	$ins = "INSERT INTO ITEMS_ESTADO (id_item,estado) VALUES ('". $row[0] . "','5')";
	mysql_query($ins, $conn) or die(mysql_error());
} while ($row = mysql_fetch_row($rs));
echo "baaaaaaa";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
