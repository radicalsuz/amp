<?php
#css used: newsbody

$newevents=$dbcon->Execute("SELECT id, event, date from calendar where  publish=1 and fpevent=1 and date >=  CURDATE() order by date asc Limit 6") or DIE($dbcon->ErrorMsg());
while (!$newevents->EOF)   {
	echo  '<span class="newsbody">' . dodate($newevents->Fields("date"),("n/j")) . '&nbsp;&nbsp;-&nbsp;&nbsp;<a href="calendar.php?calid=' .  $newevents->Fields("id") . '" >' . $newevents->Fields("event") . '</a></span><br>';
	$newevents->MoveNext();
} 
?>	  