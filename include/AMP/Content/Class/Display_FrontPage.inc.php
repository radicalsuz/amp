<?php

require_once( 'AMP/Content/Class/Display.inc.php' );
if ( !defined( 'AMP_CONTENT_CLASS_FRONTPAGE_DISPLAY_PAGER')) 
        define( 'AMP_CONTENT_CLASS_FRONTPAGE_DISPLAY_PAGER', false);

class ContentClass_Display_FrontPage extends ContentClass_Display {

//    var $_pager_limit  = AMP_CONTENT_CLASS_FRONTPAGE_DISPLAY_PAGER;
//    var $_pager_active = AMP_CONTENT_CLASS_FRONTPAGE_DISPLAY_PAGER;
    var $_css_class_container_list = "home";
    var $_css_class_container_listentry = 'list_entry' ;

    function ContentClass_Display_FrontPage( &$classRef, $read_data = true ) {
        $this->_class  = & $classRef;
        $fp_articles =  &$classRef->getContents() ;

        $this->init( $fp_articles, $read_data );
    }

    function _HTML_listing( &$sourceItems ) {
        $output = "";
        foreach ($sourceItems as $sourceKey => $contentItem ) {
            $output .= $this->_HTML_listItem( $sourceItems[ $sourceKey ] );
        }
        return $this->_HTML_inDiv( $this->_HTML_listTable($output), array('class' => $this->_css_class_container_list ) );
    }

    function _HTML_listTable( $content ) {
        if (!$content ) return false;
        if ( AMP_CONTENT_LAYOUT_CSS ) return $content;
        return  '<table width="100%" class="'.$this->_css_class_text.'" cellpadding="0" cellspacing="0">' . $content . '</table>';
    }


    function _HTML_listItem( &$contentItem ) {
       
        if (!($display = &$contentItem->getDisplay())) return false;
        if ( AMP_CONTENT_LAYOUT_CSS ) return $this->_HTML_inDiv( $display->execute(), $this->_css_class_container_listentry );
        return  "<tr>".
                $this->_HTML_inTD( $display->execute() ). 
                "</tr>";
    }


    function _HTML_listIntro( &$intro ) {
        return false;
    }

    function &getIntroDisplay() {
        $empty_value = false;
        return $empty_value;
    }

}

?>
