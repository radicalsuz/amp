<?php

require_once("AMP/BaseDB.php");
require_once("adodb/toexport2.inc.php");

$filename='emailexport.csv';

$id = preg_replace( "/(^\d+\)/", "\$1", $_REQUEST['id'] );

$sql .= "SELECT e.*, l.name
         FROM email e, subscription s, lists l
         WHERE l.id = s.listid AND s.userid = e.id AND s.listid=" . $dbcon->qstr($id);
$rs = $dbcon->Execute($sql);

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");
print rs2csv($rs); # return a string, CSV formatprint '<hr>';

?>
