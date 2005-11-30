<?php

require_once("Modules/Podcast/Item/ComponentMap.inc.php");
require_once("AMP/System/Page.inc.php");

$map = &new ComponentMap_PodcastItem();
$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();
print $page->output( );

?>
