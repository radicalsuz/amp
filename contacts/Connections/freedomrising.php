<?php 
require("../adodb/adodb.inc.php");
ob_start();

$subdir=1;
require("../Connections/freedomrising.php");

if ($security == "inactive") {
	$userLevel = 1 ;
	$ID = 1 ;
} else {
	require("../password/secure.php");
}
		

	$valper=$dbcon->Execute("SELECT perid FROM permission WHERE groupid = $userLevel") or DIE($dbcon->ErrorMsg());
  	 $userper = array();
	 while (!$valper->EOF)    { 
				$perin = $valper->Fields("perid");
				$userper["$perin"] = 1;
  	$valper->MoveNext();  }
	 if ($userper[72] != 1){ header ("Location: index.php"); } 
	
	 ?>
