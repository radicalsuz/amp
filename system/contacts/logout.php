<?PHP $logout = true; 


require_once("../Connections/freedomrising.php");  
if ($standalone != 1) {
require("../password/secure.php");}
else  { require("password/secure.php");}?>
<?php  header ("Location: index.php");		?>
