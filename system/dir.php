<?php
$base_path = "/var/www/freedomrising/endtheoccupation";
$dir = $base_path."test";
//chown ($base_path, "www-data")or die("Couldn't chown");
chmod ($base_path, 0777)or die("Couldn't chomd"); 
mkdir ($dir,0777) or die("Couldn't create dir");
chmod ($base_path, 0755); 
echo "done";
?>
