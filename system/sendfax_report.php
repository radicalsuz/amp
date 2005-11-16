<?php
$modid = "21";
$mod_name = 'actions';

require("Connections/freedomrising.php");
include ("header.php");
#require_once("AMP/Charts/charts.php");
echo "<h2>Action Center Report</h2>";

$sql = "select count(id) as number from action_history where actionid = ".$_REQUEST['report']	;
$R= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
$sql = "select title from action_text where id = ".$_REQUEST['report']	;
$N= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());


echo "<h3>".$N->Fields("title")."</h3>";
echo '<table>';
echo "<tr><td><br>Total Actions Taken</b> </td><td>".$R->Fields("number")."</td></tr>";

$sql = "select distinct MONTH(date) as month,  YEAR(date) as year from action_history where actionid = ".$_REQUEST['report']	;
$M= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
while (!$M->EOF) {
	$sql = 'select count(id) as number from action_history where actionid = '.$_REQUEST['report'] .' and MONTH(date) = '. $M->Fields('month')	;
	$C= $dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
	
	echo '<tr><td>'.date("F Y", mktime(0, 0, 0, $M->Fields('month'), 1, $M->Fields('year'))) ."</td><td> ".$C->Fields("number").'</td></tr>';
	

	
	$M->MoveNext();
}
echo '</table>';
include ("footer.php");
?>
	
	
	
			
