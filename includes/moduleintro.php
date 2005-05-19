 <?php
 /*********************
11-26-2002  v3.01
Module:  Module System
Description:  calls module headers
CSS: subtitle, text, title
functions: converttext
Called by: all modules
To Do:  

*********************/ 

$module=$dbcon->CacheExecute("SELECT * FROM moduletext WHERE id = $mod_id") or DIE("Couldn't retrieve module text: " . $dbcon->ErrorMsg());
if (isset($moduleintroreplace) && $moduleintroreplace != NULL) { 
	include ("$moduleintroreplace"); 
}
else {
	echo '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td class="text">';
	echo '<p class="title">'  .  $module->Fields("title")  .  '</p>';
	if ($module->Fields("subtitile") != (NULL)) {
		echo '<p class="subtitle">'  .  $module->Fields("subtitile")  .  '</p>';
	}
	if ($module->Fields("test") != (NULL)) {
		echo '<p class="text">';
		if  ($module->Fields("html")) {
			echo $module->Fields("test");
		}
		else {
			echo converttext($module->Fields("test"));
		}
		echo '</p>';
	}
	echo '</td></tr></table>';
}
?>
