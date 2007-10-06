<?php

require_once( 'AMP/Display/Cloud.php');
require_once( 'AMP/Content/Tag/Tag.php');

function amp_badge_tag_cloud( $options = array( )) {
    $qty_set = ( isset( $options['qty_set']) && $options['qty_set']) 
                    ? $options['qty_set'] 
                    : AMP_lookup( 'tag_totals_articles_by_section_live', AMP_current_section_id( ));
    if ( !$qty_set ) return false;

    $display_url = ( isset( $options['display_url']) && $options['display_url']) ? $options['display_url'] : false; 
    $source_item = new AMP_Content_Tag( AMP_Registry::getDbcon( ));
    $source = $source_item->find( array( 'displayable' => 1 ));
    
    $display = new AMP_Display_Cloud( $source, $qty_set );
    if ( $display_url ) {
        $display->set_url_method( $display_url );
    }
    $renderer = AMP_get_renderer( );
    return $renderer->div( $display->execute( ), array( 'class' => 'tagcloud_badge')) ;
}

?>
