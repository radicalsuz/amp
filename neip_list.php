<?php
$modid =101;
$mod_id = 101 ;
include("sysfiles.php");
include("header.php"); 

$R=$dbcon->Execute("SELECT id, question FROM neip  ") or DIE($dbcon->ErrorMsg());
echo "<br><br>";
while (!$R->EOF) {
	echo "<a href =\"indicators.php?id=".$R->Fields("id")."\">".$R->Fields("question")."</a><br>";
	$R->MoveNext();
	
}

include("footer.php");?>
