<?php
  require_once("../adodb/toexport2.inc.php");
  require_once("../adodb/adodb.inc.php");
  require_once("Connections/freedomrising.php");
  require_once("$ConfigPath2");

$filename='registration.csv';

$sql .= "Select * from registration";
$db = &NewADOConnection('mysql');
$db->Connect($MM_HOSTNAME, $MM_USERNAME, $MM_PASSWORD, $MM_DATABASE);
$rs = $db->Execute($sql);
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=$filename");
print rs2csv($rs); # return a string, CSV formatprint '<hr>';

	?>