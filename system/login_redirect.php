<?php

require_once( 'AMP/System/Page/Urls.inc.php');

if ( !( isset( $_GET['url']) && $_GET['url'] )) header( 'Location:' . AMP_SYSTEM_URL_HOME );
trigger_error ( 'hitting redirect page with url' . $_GET['url']);
header( 'Location:' . $_GET['url']);

?>
