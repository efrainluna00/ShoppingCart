<?php 
#aqui defines la carpeta donde tendras los archivos.#es buena idea que esta carpeta no sea de acceso publico.#sin embargo tampoco es muy necesario.

$carpeta="pedidos/"; 
if ( file_exists($carpeta.basename($_GET['file']))){
$file=$carpeta.$_GET["file"];  
header("Content-Transfer-Encoding: binary");   
header("Content-type: application/force-download");   
header("Content-Disposition: attachment; filename=".basename($file));   
header("Content-Length: ".filesize($file));    
readfile($file);}
else{?>
<div align="center">Usted esta accediendo a un archivo que no existe en la carpeta de descargas.</div>
<?php }?>