<?php 

// find out hierarchy for module and assign hierarchy vars
$modhierarchy=$dbcon->CacheExecute("SELECT templateid, name, type FROM moduletext WHERE id = $mod_id")
    or die("Couldn't establish module rank: " . $dbcon->ErrorMsg());  

$MM_type = $modhierarchy->Fields("type");
$mod_name = $modhierarchy->Fields("name");
$mod_template = $modhierarchy->Fields("templateid");

require_once($base_path."Connections/menu.class.php");
$obj = new Menu;

// populate the module instance varaibles
if (isset($modid)) {

    $modinstance = $dbcon->CacheExecute("SELECT * from module_control where modid = $modid")
        or die("Couldn't find module instance data: " . $dbcon->ErrorMsg());

    while (!$modinstance->EOF) {
        $a = $modinstance->Fields("var");
        $$a = $modinstance->Fields("setting");
        $modinstance->MoveNext();
    }
}

?>
