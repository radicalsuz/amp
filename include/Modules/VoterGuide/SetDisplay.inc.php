<?php

require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'Modules/VoterGuide/Set.inc.php' );
require_once( 'AMP/Content/Display/List.inc.php' );

if (!defined( 'AMP_TEXT_VOTERGUIDE_SEARCH_EMPTY')) 
    define('AMP_TEXT_VOTERGUIDE_SEARCH_EMPTY',
        '<BR><BR>No voter guides found for this area. Why don\'t you <a href="newvoterguide">post a new one</a>'
    );

class VoterGuideSet_Display extends AMPContent_DisplayList_HTML {

    var $_sourceItem_class = "VoterGuide";
    var $_blurb_size_limit = 9000;
    var $_no_results_message_html = AMP_TEXT_VOTERGUIDE_SEARCH_EMPTY;
    var $_css_class_search_empty = 'text';

    function VoterGuideSet_Display( &$dbcon ) {
        $source = &new VoterGuideSet( $dbcon );
        $this->init( $source );
    }

    function _HTML_listItemDescription( &$guide ) {
        $guide_display = &$guide->getDisplay( );
        return 
            $this->_HTML_newline().
            $this->_HTML_listItemTitle( $guide ).
            $guide_display->_HTML_affiliations( $guide->getAffiliation( )).
            $guide_display->_HTML_location( $guide->getLocation( ) ).
            $guide_display->_HTML_date( $guide->getItemDate( ) ).
            $guide_display->_HTML_blurb( AMP_trimText( $guide->getBlurb( ), $this->_blurb_size_limit));
    }

    function noResultsDisplay(){
        return $this->_HTML_inDiv( $this->_no_results_message_html , array( 'class' => $this->_css_class_search_empty ));
    }

    function _HTML_listingFormat( $html ) {
        return $this->_pager->_HTML_topNotice( 'Voter Guides' ) . $html;
    }
}

?>
