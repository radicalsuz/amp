<?php
require_once( 'AMP/User/Data/Data.php');
require_once( 'AMP/User/Data/List.php');

function amp_badge_forms( $options = array( )) {
    $modin = ( isset( $options['modin']) && $options['modin']) ? $options['modin'] : false;
    if ( !$modin ) {
        $modin = ( isset( $options['form_id']) && $options['form_id']) ? $options['form_id'] : false;
    }
    $sort = ( isset( $options['sort']) && $options['sort'] ) ? $options['sort'] : "created_timestamp DESC";

    $criteria = array( );
    $criteria['live'] = 1;
    if ( $modin ) $criteria['modin'] = $modin;
    $limit =   ( isset( $options['limit']) && $options['limit']) ? $options['limit'] : AMP_CONTENT_LIST_DISPLAY_MAX;
    $display = ( isset( $options['display']) && $options['display']) ? $options['display'] : false;
    $display_header = ( isset( $options['display_header']) && $options['display_header']) ? $options['display_header'] : false;
    $display_subheader = ( isset( $options['display_subheader']) && $options['display_subheader'] ) ? $options['display_subheader'] : false;

    $finder = new AMP_User_Data( AMP_Registry::getDbcon( ));
    $search = &$finder->getSearchSource( $finder->makeCriteria($criteria ));
    $search->addSort( $sort );
    $search->setLimit( $limit );
    $source = $finder->find( );

    $list = &new AMP_User_Data_List( $source );#, $criteria, $limit );
    $list->_pager_active = false;
    if ( $display ) $list->set_display_method( $display );
    if ( $display_header ) $list->set_display_header_method( $display_header );
    if ( $display_subheader ) $list->set_display_subheader_method( $display_subheader );
    return $list->execute( );
}
?>
