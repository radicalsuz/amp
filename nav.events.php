<?php
$newevents=$dbcon->Execute("SELECT id, event, date from calendar where  publish=1 and fpevent=1 and date >=  CURDATE() order by date asc Limit 6") or DIE($dbcon->ErrorMsg());
?>
       <?php 	while (!$newevents->EOF)   {?>
 <span class="newsbody">
   <?php echo dodate($newevents->Fields("date"),("n/j"))?>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="calendar.php?calid=<?php echo $newevents->Fields("id"); ?>" ><?php echo $newevents->Fields("event");?></a><br>
	      <?php  
		  $newevents->MoveNext();} 
		 ?>
	  </span>
    
