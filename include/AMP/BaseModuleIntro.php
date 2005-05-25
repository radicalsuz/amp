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
if (isset($mod_id) || isset($intro_id)) {
	if (!$intro_id) { $intro_id = $mod_id; }
	$module=$dbcon->CacheExecute("SELECT * FROM moduletext WHERE id = $intro_id") or DIE("Couldn't retrieve module text: " . $dbcon->ErrorMsg());
	if (isset($moduleintroreplace) && $moduleintroreplace != NULL) { 
		include ("$moduleintroreplace"); 
	}
	else {
		echo '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td class="text">';
		echo '<p class="title">'  .  $module->Fields("title")  .  '</p>';
		if ($module->Fields("subtitile") != (NULL)) {
			echo '<p class="subtitle">'  .  $module->Fields("subtitile")  .  '</p>';
		}
        $AMP_Module_Intro = $module->Fields("test");
		if ($AMP_Module_Intro != (NULL)) {
            print 'heh - intro no work';
            $AMP_Module_Intro = eval_includes ( $AMP_Module_Intro );
			echo '<p class="text">';
			if  ($module->Fields("html")) {
				echo $AMP_Module_Intro;
			}
			else {
				echo converttext($AMP_Module_Intro);
			}
			echo '</p>';
		}
		echo '</td></tr></table>';
	}
}
?>
