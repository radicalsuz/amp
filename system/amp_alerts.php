<?php
#alerts: gets the alerts from the central amp db and displays them

require("Connections/freedomrising.php");

function display_alerts($q) {
	echo "<table width=\"100%\" cellpadding=3><tr><td>";
	while (!$q->EOF) {
		echo "<h3>".$q->Fields("alert_title")."</h3>";
		echo "<p class=\"name\"><b>".$q->Fields("date")."</b><br>".$q->Fields("alert_text")."</p><br>";
		
		$q->MoveNext();
	}
	echo "</td></tr></table>";
}
$sql="select * from alerts where publish =1 order by date desc";
$alerts_txt=$ampdbcon->Execute($sql) or DIE("15".$ampdbcon->ErrorMsg());
include ("header.php");
echo "<h2>AMP SYSTEM UPDATES & NEWS</h2>";	
display_alerts($alerts_txt);
include ("amp_alerts_emails.php");
include ("footer.php");

?>