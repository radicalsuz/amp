<?php
require_once("adodb/toexport2.inc.php");
require_once("AMP/BaseDB.php");

$filename='tookaction.csv';

$sql .= "Select * from tookaction";
$rs = $dbcon->Execute($sql);
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
print rs2csv($rs); # return a string, CSV formatprint '<hr>';

	?>
