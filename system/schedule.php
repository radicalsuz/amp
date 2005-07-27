<?php

require_once ('AMP/System/Page.inc.php');
require_once ('Modules/Schedule/ComponentMap.inc.php');

$map = &new ComponentMap_Schedule();
$page = &new AMPSystem_Page( $dbcon, $map );

if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();
print $page->output();

?>
