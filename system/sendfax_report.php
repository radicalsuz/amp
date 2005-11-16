<?php
$modid = "21";
$mod_name = 'actions';

require("Connections/freedomrising.php");
include ("header.php");
echo "<h2>Action Center Report</h2>";

$sql = "select count(id) as number from action_history where actionid = ".$_REQUEST['report']	;
$R= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
$sql = "select title from action_text where id = ".$_REQUEST['report']	;
$N= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());

#$sql = "select distinct MONTH(date) from action history where actionid = ".$_REQUEST['report']	
#$M= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
echo "<p>".$N->Fields("title")."</p>";
echo "Actions Taken: ".$R->Fields("number");



include ("footer.php");
?>
	
	
	
			
