<?php
if ($_REQUEST['id']) {
	$getpsnav=$dbcon->CacheExecute("Select navtext from articles where id =".$_REQUEST['id']);
	if ($getpsnav->Fields("navtext")) {
		echo converttext($getpsnav->Fields("navtext"));
	}
}
?>