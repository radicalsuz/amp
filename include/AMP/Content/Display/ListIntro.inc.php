<?php

require_once ('AMP/Content/Display/HTML.inc.php' );
require_once ('AMP/Content/Article/Display.inc.php' );

class ListIntro_Display extends AMPDisplay_HTML {

    var $_source;
    var $_css_id_container = 'content_header';
    var $_css_class_title = "title";
    var $_css_class_text = "text";
    var $_css_class_date = "text";

    function ListIntro_Display( &$source ) {
        $this->_source = &$source;
    }
    
    function execute() {
        if ($article = &$this->_source->getHeaderRef() ) {
            $display = &$article->getDisplay();
            $output = $display->execute();
        } else {
            $output =
                $this->_HTML_title( $this->_source->getName() ).
                $this->_HTML_blurb( $this->_source->getBlurb() ).
                $this->_HTML_date( $this->_source->getItemDate() );
        } 

        return $this->_HTML_inDiv( $output, array( 'id'=> $this->_css_id_container ) );
                    
    }

    function _HTML_title( $title ) {
        if (!$title) return false;
        return $this->_HTML_in_P( $title, array('class'=>$this->_css_class_title));
    }

    function _HTML_blurb( $blurb ) {
        if (!$blurb) return false;
        return $this->_HTML_in_P( converttext($blurb), array('class'=>$this->_css_class_text));
    }

    function _HTML_date( $date_value ) {
        if (!$date_value ) return false;
        return $this->_HTML_in_P( DoDate( $date_value, 'F j, Y').$this->_HTML_newline(), array('class'=>$this->_css_class_date));
    }
}
?>
