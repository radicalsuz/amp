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
   $module=$dbcon->CacheExecute("SELECT * FROM moduletext WHERE id = $mod_id") or DIE($dbcon->ErrorMsg());
	if ($moduleintroreplace !=NULL) { include ("$moduleintroreplace"); }
	else {
?><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td class="text">
		   <p class="title"><?php echo $module->Fields("title")?></p>
		   <?php if ($module->Fields("subtitile") != ($null)) {?><p class="subtitle"><?php echo $module->Fields("subtitile")?></p><?php } ?>
		   <?php if ($module->Fields("test") != ($null)) {?><p class="text"><?php if  ($module->Fields("html")) {echo $module->Fields("test");} else {echo converttext($module->Fields("test"));}?></p><?php } ?>
		   <?php $module->Close(); ?></td></tr></table>
<?php } ?>