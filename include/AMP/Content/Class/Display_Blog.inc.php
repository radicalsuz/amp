<?php

require_once( 'AMP/Content/Class/Display.inc.php' );

class ContentClass_Display_Blog extends ContentClass_Display {

    var $_css_id_container_list = "main_content";

    function ContentClass_Display_Blog( &$classRef, $read_data = true ) {
        $this->_class = &$classRef;

        $blog_articles =  &$this->_class->getContents() ;

        $this->init( $blog_articles, $read_data );
    }

    function _HTML_listing( &$sourceItems ) {
        $output = "";
        foreach ($sourceItems as $sourceKey => $contentItem ) {
            $output .= $this->_HTML_listItem( $sourceItems[ $sourceKey ] );
        }
        return $this->_HTML_inDiv( $this->_HTML_listTable($output), array('class' => $this->_css_class_container_content ) );
    }

    function _HTML_listTable( $content ) {
        if (!$content ) return false;
        if ( AMP_CONTENT_LAYOUT_CSS ) return $content;
        return  '<table width="100%" class="'.$this->_css_class_text.'">' . $content . '</table>';
    }


    function _HTML_listItem( &$contentItem ) {
    
        if (!($display = &$contentItem->getDisplay())) return false;
        $display->useShortComments();
        if ( AMP_CONTENT_LAYOUT_CSS ) return $this->_HTML_inDiv( $display->execute(), $this->_css_class_container_listentry );
        return  "<tr>".
                $this->_HTML_inTD( $display->execute() ). 
                "</tr>";
    }


}

?>
