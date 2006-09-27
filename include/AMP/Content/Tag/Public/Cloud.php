<?php
require_once( 'AMP/Display/Cloud.php');
require_once( 'AMP/Content/Tag/Tag.php');

function AMP_display_nav_tag_cloud( ) {

    $qty_set = AMPSystem_Lookup::instance( 'tagTotals' );
    if ( !$qty_set ) return false;

    $source_item = &new AMP_Content_Tag( AMP_Registry::getDbcon( ));
    $source = $source_item->find( array( 'live' => AMP_CONTENT_STATUS_LIVE ));

    $display = new AMP_Display_Cloud( $source, $qty_set );
    return $display->execute( );

}

?>
