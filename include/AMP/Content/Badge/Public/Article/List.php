<?php

require_once( 'AMP/Content/Article/Public/List.php');

class AMP_Content_Badge_Public_Article_List extends Article_Public_List {

    var $_class_pager = 'AMP_Display_Pager_Morelink';
    var $_path_pager = 'AMP/Display/Pager/Morelink.php';

    function AMP_Content_Badge_Public_Article_List( $source = false, $criteria=array( ), $limit = null ) {
		$source = false;
		$this->__construct($source, $criteria, $limit );
    }

    function _init_criteria( ) {
        if ( !isset( $this->_pager )) return;
        $section = ( isset( $this->_source_criteria['section']) && $this->_source_criteria['section']) ? $this->_source_criteria['section'] : false; 
        $class = ( isset( $this->_source_criteria['class']) && $this->_source_criteria['class']) ? $this->_source_criteria['class'] : false; 
        if( $section && $class ) {
            $pager_target = AMP_url_update( AMP_CONTENT_URL_LIST_CLASS, array( 'type' => $section, 'class' => $class ));
        } elseif ( $section ) {
            $pager_target = AMP_url_update( AMP_CONTENT_URL_LIST_SECTION, array( 'type' => $section ));
        } elseif ( $class ) {
            $pager_target = AMP_url_update( AMP_CONTENT_URL_LIST_CLASS, array( 'class' => $class ));
        }
        $this->_pager->set_target( $pager_target );
    }

}

?>
