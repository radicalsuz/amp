<?
require_once("AMP/Geo/FlashMap/ComponentMap.inc.php");
require_once("AMP/System/Page.inc.php");

$map = &new ComponentMap_FlashMap();
$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();

print $page->output( );

?>

