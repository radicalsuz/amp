<?php 
// find out hierarchy for module and assign hierarchy vars
$modhierarchy=$dbcon->CacheExecute("SELECT templateid, name, type FROM moduletext WHERE id = $mod_id") or DIE($dbcon->ErrorMsg());  
	$MM_type = $modhierarchy->Fields("type");
	$mod_name = $modhierarchy->Fields("name");
	$mod_template = $modhierarchy->Fields("templateid");
include($base_path."Connections/menu.class.php");
$obj = new Menu;
// populate the module instance varaibles
if (isset($modid)){
$modinstance = $dbcon->CacheExecute("SELECT * from module_control where modid = $modid") or DIE($dbcon->ErrorMsg());
while (!$modinstance->EOF) {
$a = $modinstance->Fields("var");
$$a = $modinstance->Fields("setting");
$modinstance->MoveNext();} }
?>