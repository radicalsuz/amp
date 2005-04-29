<?php
$modid = $_GET['temp'];

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");

include ("header.php");
echo "<xml><item>content</item></xml>";
include ("footer.php");

?>