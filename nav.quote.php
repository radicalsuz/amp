<?php
//if ($MM_type) {
$getq=$dbcon->CacheExecute("Select * from quotes where type=$MM_type and publish =1 ");
if ($getq->Fields("quote")) {
echo "<p class=quote>\"".$getq->Fields("quote")."\"</p>";
if ($getq->Fields("source")) { echo "<p class=quoteby>-".$getq->Fields("source")."</p>";}
}
//}
?>
