<?
require_once("Modules/Podcast/ComponentMap.inc.php");
require_once("AMP/System/Page.inc.php");
$modid = 46;
$map = &new ComponentMap_Podcast();

$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();

print $page->output( );

?>