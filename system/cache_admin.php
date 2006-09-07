<?php
require_once("AMP/System/Base.php");
require_once("AMP/System/BaseTemplate.php");
trigger_error( 'actually hitting the flush');

$template = &new AMPSystem_BaseTemplate();

$script = "
<script type = 'text/javascript'>
//<!--
history.go(-1);
alert( '". AMP_TEXT_CACHE_RESET ."' );
//-->
</script>";

print $template->outputHeader();

if ( isset( $_GET['action']) && $_GET['action'] == 'flush') {
    $dbcon = & AMP_Registry::getDbcon( );
    trigger_error( 'actually hitting the global flush');
    $dbcon->Execute( 'RESET QUERY CACHE' );

    print AMP_TEXT_CACHE_RESET;
    print $script;
}

print $template->outputFooter();
?>
