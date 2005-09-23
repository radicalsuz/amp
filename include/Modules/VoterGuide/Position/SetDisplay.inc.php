<?php

require_once(  'AMP/Content/Display/List.inc.php' );
require_once(  'AMP/Content/VoterGuide/Position.php' );

class VoterGuidePositionSet_Display extends AMPContent_DisplayList_HTML {

    var $_voterguide;
    var $_sourceItem_class = "VoterGuide_Position";

    function VoterGuidePositionSet_Display( &$dbcon, $guide_id =  null ) {
        $source = &new VoterGuidePositionSet( $dbcon, $guide_id );
        $this->init( $source );
    }
    function _HTML_listItemDescription( &$position ) {
        return
            $this->_HTML_listItemTitle( $position ) . 
            $this->_HTML_listItemSubtitle( $position->getSubtitle() ) .
            $this->_HTML_listItemBlurb( $position->getBlurb() );
    }
    function _HTML_listItemSubtitle( $subtitle ) {
        if ( !$subtitle ) return false;
        return $this->_HTML_inSpan( $subtitle, $this->_css_class_subtitle ) . $this->_HTML_newline( );
    }
}
?>
