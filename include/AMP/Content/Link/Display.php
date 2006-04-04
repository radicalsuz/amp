<?php
require_once( 'AMP/Content/Display/List.inc.php');
require_once( 'AMP/Content/Link/Set.inc.php');

class AMP_Content_Link_Display extends AMPContent_DisplayList_HTML {
    var $_subheader = 'LinkTypeName';
    var $_css_class_container_listentry = "links";
    var $_css_class_title    = "";
    var $_pager_active = false;

    function AMP_Content_Link_Display( &$dbcon ) {
        $source = &new AMP_Content_Link_Set( $dbcon, false );
        $source->addCriteriaLive( );
        $this->init( $source );
        $this->setLayoutCSS( true );
    }

    function _HTML_listItemDescription( &$link_item ) {
        return
            $this->_HTML_listItemSubheading( $link_item ) .
            $this->_HTML_listItemTitle( $link_item ) . 
            $this->_HTML_listItemBlurb( $link_item->getBlurb() );
    }

}

?>
