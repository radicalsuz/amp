<?php
$modid =101;
$mod_id = 101 ;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 

$R=$dbcon->Execute("SELECT id, question FROM neip  ") or DIE($dbcon->ErrorMsg());
echo "<br><br>";
while (!$R->EOF) {
	echo "<a href =\"indicators.php?id=".$R->Fields("id")."\">".$R->Fields("question")."</a><br>";
	$R->MoveNext();
	
}

include("AMP/BaseFooter.php");?>
