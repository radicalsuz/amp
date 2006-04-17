<?php

require_once(  'AMP/Content/Display/List.inc.php' );
require_once(  'Modules/VoterGuide/Position.php' );

class VoterGuidePositionSet_Display extends AMPContent_DisplayList_HTML {

    var $_voterguide;
    var $_sourceItem_class = "VoterGuide_Position";
    var $_pager_active = false;

	var $_css_class_title = 'voter_item_title';

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

	function _HTML_listItemTitle( &$source ) {
		return  "<h2 class=\"{$this->_css_class_title}\">".$source->getTitle()."</h2>";
//		                $this->_HTML_newline();
	}  

    function _HTML_listItemVote( &$position ) {
        if ( !$issue = $position->getName( ) ) return false;
        $output = '<h4 class="listname"><span class="name_label">Candidate/Issue: </span><span class="name_text">'.$issue.'</span></h4>';
        if ( $vote = $position->getPosition( )) $output .= '<h4 class="position"><span class="position_label">Endorsed Vote: </span><span class="position_text">' . $vote.'</span></h4>';
//        return $this->_HTML_inSpan( $output, $this->_css_class_subtitle ) . $this->_HTML_newline( );
        return $output;
    }

    function _HTML_listItemBlurb( $blurb ) {
        if (!trim( $blurb )) return false;
        return $this->_HTML_in_P( AMP_trimText( $blurb, $this->_max_blurb_length ) , array('class' => $this->_css_class_text ) ); 
    }
}
?>
