<?php

require_once("AMP/System/Page.inc.php");
require_once("Modules/Schedule/Item/ComponentMap.inc.php");

$map = &new ComponentMap_ScheduleItem();
$page = &new AMPSystem_Page ($dbcon, $map);

if (isset($_GET['action']) && $_GET['action'] == "list")   $page->showList( true );
if (isset($_GET['schedule_id']) && $_GET['schedule_id'] )  {
    $page->addCallback( 'list', 'addCriteria', array('schedule_id=' . $_GET['schedule_id'], true) );
    $page->addCallback( 'form', 'setDefaultValue', array('schedule_id', $_GET['schedule_id']) );
}
if (isset($_GET['owner_id']) && $_GET['owner_id'] )  {
    $page->addCallback( 'list', 'addCriteria', array('owner_id=' . $_GET['owner_id'], true) );
    $page->addCallback( 'form', 'setDefaultValue', array('owner_id', $_GET['owner_id']) );
}

$page->execute();
print $page->output();
?>
