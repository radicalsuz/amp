<?php

require_once("Modules/Quiz/ComponentMap.inc.php");
require_once("AMP/System/Page.inc.php");
$modid = 46;
$map = &new ComponentMap_Quiz();

$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();

print $page->output( );

?>
