<?php
  require("Connections/freedomrising.php");
 $port=$dbcon->Execute("SELECT * FROM template ") or DIE($dbcon->ErrorMsg());
 $MM_update=1;
 while ((!$port->EOF)){

 $id = $port->Fields("id");
$MM_editTable  = "template";
    $MM_editColumn = "id";
    $MM_recordId = $id;
	$header2 =  $port->Fields("header1")."  [-left nav-]  ".$port->Fields("header4")."  [-body-]  ".$port->Fields("header5")."  [-right nav-]  ".$port->Fields("footer");
    $MM_fieldsStr ="header2|value";
    $MM_columnsStr = "header2|',none,''";
	
	  require ("../Connections/insetstuff.php");
require ("../Connections/dataactions.php");
	

echo  $header2;
$port->MoveNext();}
echo "done";
?>
