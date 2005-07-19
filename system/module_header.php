<?php
$mod_name="tools";

require_once("AMP/System/Base.php");
require_once("AMP/System/Page.inc.php");

$page = &new AMPSystem_Page ($dbcon, 'IntroText');
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();

print $page->output( "Intro Text" );

?>
