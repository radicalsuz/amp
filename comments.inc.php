<?php
#to do: turn layout into divs

function display_comment($id) {
	global $dbcon;
	$R=$dbcon->CacheExecute("SELECT * FROM comments WHERE publish=1 and articleid = $id order by date desc") or DIE($dbcon->ErrorMsg()); 
	echo '<br><p><a href="comment.php?cid=' . $id . '">add a comment</a></p>';
	while  (!$R->EOF){ 
		echo "<hr><p><b>" . $R->Fields("title") . "</b><br>";
		echo '<i>by  ';
		if ($R->Fields("email")) { 
			echo '<a href="mailto: ' . $R->Fields("email") . '">';
			}
		echo $R->Fields("author") ;
		echo '</a>,  ' . DoDateTime($R->Fields("date"),"l, M j, Y g:iA") . '</i></p>';
		echo '<p>' . converttext($R->Fields("comment")) . '</p>';
		$R->MoveNext();
	}
}
display_comment($MM_id);
?>