<?php
if ($MM_type) {
$getimg=$dbcon->CacheExecute("Select * from gallery where   publish =1 order by rand()");
if ($getimg->Fields("img")) {
echo "<img src=\"img/pic/".$getimg->Fields("img")."\">";
if ($getimg->Fields("caption")) { echo "<p class= imgsidecap>Above: ".$getimg->Fields("caption");
if ($getimg->Fields("photoby")) {echo " Photo by: ".$getimg->Fields("photoby"); }
echo "</p>";
}
}
}
?>