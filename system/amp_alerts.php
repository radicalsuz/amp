<?php
#alerts: gets the alerts from the central amp db and displays them

require_once("Connections/freedomrising.php");

function display_alerts($q) {

	while (!$q->EOF) {
        echo "<h3>".$q->Fields("alert_title")."</h3>";
        echo "<p class=\"name\"><b>".$q->Fields("date")."</b><br>".$q->Fields("alert_text")."</p>";		
        $q->MoveNext();
	}

}

include ("header.php");

if (isset($ampdbcon)) {

    $sql="SELECT * FROM alerts WHERE publish = 1 ORDER BY date DESC";
    $alerts_txt=$ampdbcon->Execute($sql) or DIE("15".$ampdbcon->ErrorMsg());
    echo "<table width=\"100%\" cellpadding=3><tr><td>";
    echo "<h2>AMP SYSTEM UPDATES & NEWS</h2>";	
    display_alerts($alerts_txt);
    include ("amp_alerts_emails.php");
    echo "</td></tr></table>";

} else {

    print "No Alerts.";

}

include ("footer.php");

?>
