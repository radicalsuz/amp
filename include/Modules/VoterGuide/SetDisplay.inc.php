<?php

require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'Modules/VoterGuide/Set.inc.php' );
require_once( 'AMP/Content/Display/List.inc.php' );

class VoterGuideSet_Display extends AMPContent_DisplayList_HTML {

    var $_sourceItem_class = "VoterGuide";
    var $_blurb_size_limit = 9000;

    function VoterGuideSet_Display( &$dbcon ) {
        $source = &new VoterGuideSet( $dbcon );
        $this->init( $source );
    }

    function _HTML_listItemDescription( &$guide ) {
        $guide_display = &$guide->getDisplay( );
        return 
            $this->_HTML_listItemTitle( $guide ).
            $guide_display->_HTML_location( $guide->getLocation( ) ).
            $guide_display->_HTML_affiliations( $guide->getAffiliation( )).
            $guide_display->_HTML_date( $guide->getItemDate( ) ).
            $guide_display->_HTML_blurb( AMP_trimText( $guide->getBlurb( ), $this->_blurb_size_limit));
    }
}
?>
