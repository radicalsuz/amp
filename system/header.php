<?php 
require_once("AMP/System/BaseTemplate.php");

$template = & AMPSystem_BaseTemplate::instance();

if (isset($modid) && $modid) $template->setTool( $modid );
if (isset($mod_name) && $mod_name) $template->setToolName( $mod_name );

if (!isset($_GET['noHeader'])) {
    ob_start();

    print $template->outputHeader();
}
?>
