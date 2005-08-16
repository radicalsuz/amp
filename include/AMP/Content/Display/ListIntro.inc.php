<?php

require_once ('AMP/Content/Display/HTML.inc.php' );
require_once ('AMP/Content/Article/Display.inc.php' );

class ListIntro_Display extends AMPDisplay_HTML {

    var $_source;

    function ListIntro_Display( &$source ) {
        $this->_source = &$source;
    }
    
    function execute() {
        if ($article = &$this->_source->getHeaderRef() ) {
            $display = &new Article_Display( $article );
            $output = $display->execute();
        } else {
            $output =
                $this->_HTML_title( $this->_source->getName() ).
                $this->_HTML_blurb( $this->_source->getBlurb() ).
                $this->_HTML_date( $this->_source->getItemDate() );
        } 

        return $this->_HTML_inDiv( $output, array( 'id'=>'content_header') );
                    
    }

    function _HTML_title( $title ) {
        if (!$title) return false;
        return $this->_HTML_in_P( $title, array('class'=>'title'));
    }

    function _HTML_blurb( $blurb ) {
        if (!$blurb) return false;
        return $this->_HTML_in_P( converttext($blurb), array('class'=>'text'));
    }

    function _HTML_date( $date_value ) {
        if (!$date_value ) return false;
        return $this->_HTML_in_P( DoDate( $date_value, 'F j, Y').$this->_HTML_newline(), array('class'=>'text'));
    }
}
?>
