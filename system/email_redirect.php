<?php
require_once("Connections/freedomrising.php");


if (AMP_MODULE_BLAST == 'PHPlist') {
    $sql ="select password from phplist where loginname ='admin' ";
    $R=$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());
    $location = "../phplist/admin/?login=admin&password=".$R->Fields("password");
    ampredirect($location);
} elseif (AMP_MODULE_BLAST == 'DIA') {

} else {
    include ("header.php");
    echo "<b>You do not have a email list program defined</b>";
    include ("footer.php");
}


?>