<?php
$artcmts=$dbcon->CacheExecute("SELECT * FROM comments WHERE publish=1 and articleid = $MM_id order by date desc") or DIE($dbcon->ErrorMsg()); 
?>
<br>
<p><a href="comment.php?cid=<?php echo $MM_id ; ?>">add a comment</a></p>
<?php

while  (!$artcmts->EOF)
   { ?>
    <hr>
   <p>
   <b><?php echo $artcmts->Fields("title") ;?></b><br>
 <i>by  <?php if ($artcmts->Fields("email")) { ;?><a href="mailto: <?php echo $artcmts->Fields("email") ;?>"><?php }echo $artcmts->Fields("author") ;?></a>,  <?php echo DoDateTime($artcmts->Fields("date"),"l, M j, Y g:iA") ;?>
  </i>  </p>
   <p><?php echo converttext($artcmts->Fields("comment")) ;?></p>
  
 <?php  $artcmts->MoveNext();
}
?>