<?php

require_once("AMP/System/Page.inc.php");
require_once("Modules/Schedule/Item/ComponentMap.inc.php");

$map = &new ComponentMap_ScheduleItem();

$page = &new AMPSystem_Page ($dbcon, $map);
/*
$page->setIncludeFile( "Modules/Schedule/List.inc.php", "list" );
$page->setIncludeFile( "Modules/Schedule/Form.inc.php", "form" );
$page->setIncludeFile( "Modules/Schedule/Item.inc.php", "source" );
$page->setComponentName( "ScheduleItem_Form", "form" );
$page->setComponentName( "Schedule_List", "list" );
$page->setComponentName( "ScheduleItem", "source" );
*/

if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();

print $page->output();
?>
