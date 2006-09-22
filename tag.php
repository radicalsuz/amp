<?php

require_once( 'AMP/BaseDB.php' );

/**
 * Check for a cached copy of this request
 */
if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

require_once( 'AMP/Content/Tag/Public/Controller.php');

$controller = &new AMP_Content_Tag_Public_Controller( );
$controller->execute( );

require_once( "AMP/BaseTemplate.php");
require_once( "AMP/BaseFooter.php");

?>
