<?php

require_once(  'AMP/Content/Display/List.inc.php' );
require_once(  'Modules/VoterGuide/Position.php' );

class VoterGuidePositionSet_Display extends AMPContent_DisplayList_HTML {

    var $_voterguide;
    var $_sourceItem_class = "VoterGuide_Position";
    var $_pager_active = false;

    function VoterGuidePositionSet_Display( &$dbcon, $guide_id =  null ) {
        $source = &new VoterGuidePositionSet( $dbcon, $guide_id );
        $this->init( $source );
    }

    function _HTML_listItemDescription( &$position ) {
        return
            $this->_HTML_listItemTitle( $position ) . 
            $this->_HTML_listItemVote( $position ) .
            $this->_HTML_listItemBlurb( $position->getBlurb() );
    }

    function _HTML_listItemVote( &$position ) {
        if ( !$issue = $position->getName( ) ) return false;
        $output = "Issue:" . $issue;
        if ( $vote = $position->getPosition( )) $output .= $this->_HTML_newline() . "Vote: " . $vote ;
        return $this->_HTML_inSpan( $output, $this->_css_class_subtitle ) . $this->_HTML_newline( );
    }
}
?>
