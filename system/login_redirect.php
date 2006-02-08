<?php

require_once( 'AMP/System/Page/Urls.inc.php');

if ( !( isset( $_GET['url']) && $_GET['url'] )) header( 'Location:' . AMP_SYSTEM_URL_HOME );
header( 'Location:' . $_GET['url']);

?>
