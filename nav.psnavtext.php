<?php
global $MM_id;
if ($MM_id) {
$getpsnav=$dbcon->CacheExecute("Select navtext from articles where id = $MM_id");
if ($getpsnav->Fields("navtext")) {
echo converttext($getpsnav->Fields("navtext"));
}
}
?>