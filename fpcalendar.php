<?php 
/*********************
01-14-2003  v3.01
Module:  Template Include
Description:  include file for  tagged calendar  events on front page
CSS: homeeventname,  homeeventname, homebody
To Do:  write better sql
				replace  more image with text or include image in library 

*********************/ 
$event=$dbcon->CacheExecute("SELECT calendar.*, states.statename  FROM calendar Inner Join states on calendar.lstate=states.id  WHERE fpevent = 1 and publish=1 and calendar.date >=  CURDATE()  ORDER BY fporder, id asc") or DIE($dbcon->ErrorMsg());

while (!$event->EOF) { 
	echo '<span class="homeeventname"><a href="calendar.php?calid='. $event->Fields("id") . '">' . $event->Fields("event") . '</a></span><br>';
	echo '<span class="homeeventdate">'  .  $event->Fields("lcity")  .  ',&nbsp;' . $event->Fields("statename")  .  ', ' . DoDate( $event->Fields("date"), 'F, j');
	if   ($event->Fields("time") != NULL) {
		echo ",&nbsp;".$event->Fields("time");
	} 
	echo '</span><br>';
	echo '<span class="homebody">' . converttext($event->Fields("shortdesc")) . '&nbsp;&nbsp;<a href="calendar.php?calid=' .  $event->Fields("id")  .  '">More<b>&#187</b></a><br><br>';
	$event->MoveNext();
}
echo '<a href="calendar.php"><img src="img/more_events.gif" alt="more events" border="0"></a><br><br><br>';
?>