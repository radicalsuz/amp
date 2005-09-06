<?php

require_once( 'AMP/Content/Article/SetDisplay.inc.php' );

class SectionContentDisplay_ArticlesActionItems extends ArticleSet_Display {

    function SectionContentDisplay_ArticlesActionItems( &$source ) {
        $this->init( $source );
    }

    function _HTML_listingFormat( $html ) {
        return "<fieldset class=\"fieldset\"><legend>Take Action</legend>". $html . "</fieldset>";
    }
}
?>
