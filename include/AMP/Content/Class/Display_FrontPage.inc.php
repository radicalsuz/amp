<?php

require_once( 'AMP/Content/Class/Display.inc.php' );

class ContentClass_Display_FrontPage extends ContentClass_Display {

    var $_pager_active = false;

    function ContentClass_Display_FrontPage( &$dbcon ) {
        $this->_class = &new ContentClass ( $dbcon, AMP_CONTENT_CLASS_FRONTPAGE );

        $fp_articles =  &$this->_class->getContents() ;
        $fp_articles->addSort( 'pageorder' );

        $this->init( $fp_articles );
    }

    function _HTML_listing( &$sourceItems ) {
        $output = "";
        foreach ($sourceItems as $sourceKey => $contentItem ) {
            $output .= $this->_HTML_listItem( $sourceItems[ $sourceKey ] );
        }
        return $this->_HTML_inDiv( $this->_HTML_listTable($output), array('class' => 'home' ) );
    }

    function _HTML_listTable( $content ) {
        if (!$content ) return false;
        return  '<table width="100%" class="text">' . $content . '</table>';
    }


    function _HTML_listItem( &$contentItem ) {
       
        if (!($display = &$contentItem->getDisplay())) return false;
        return  "<tr>".
                $this->_HTML_inTD( $display->execute() ). 
                "</tr>";
    }


    function _HTML_listIntro( &$intro ) {
        return false;
    }

    function &getIntroDisplay() {
        return false;
    }

}

?>
