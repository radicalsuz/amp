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

 while (!$event->EOF) 
   { ?>
<span class="homeeventname"><a href="calendar.php?calid=<?php echo $event->Fields("id"); ?>"><?php echo $event->Fields("event")?></a></span><br>
              <span class="homeeventdate"><?php echo $event->Fields("lcity")?>,&nbsp;<?php echo $event->Fields("statename")?>, <?php echo DoDate( $event->Fields("date"), 'F, j') ?>
			  <?php if  ($event->Fields("time") != NULL) {echo ",&nbsp;".$event->Fields("time");} ?>  </span><br>
              <span class="homebody"> <?php echo converttext($event->Fields("shortdesc"));?>&nbsp;&nbsp;<a href="calendar.php?calid=<?php echo $event->Fields("id"); ?>">More 
              <b>&#187</b></a> <br>
              <br>
			  <?php
    $event->MoveNext();}?>

<a href="calendar.php"><img src="images/more_events.gif" alt="more events" width="75" height="16" border="0"></a> 
<br>
              <br><br>	
 <?php
$event->Close();
?>
