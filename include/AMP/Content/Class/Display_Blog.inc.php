<?php

require_once( 'AMP/Content/Class/Display.inc.php' );

class ContentClass_Display_Blog extends ContentClass_Display {


    function ContentClass_Display_Blog( &$dbcon, $read_data = true ) {
        $this->_class = &new ContentClass ( $dbcon, AMP_CONTENT_CLASS_BLOG );

        $blog_articles =  &$this->_class->getContents() ;
        $blog_articles->addSort( 'pageorder' );

        $this->init( $blog_articles, $read_data );
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
        return  '<table width="100%" class="'.$this->_css_class_text.'">' . $content . '</table>';
    }


    function _HTML_listItem( &$contentItem ) {
       
        if (!($display = &$contentItem->getDisplay())) return false;

        if ( AMP_CONTENT_LAYOUT_CSS ) return $this->_HTML_inDiv( $display->execute(), $this->_css_class_container_listentry );
        return  "<tr>".
                $this->_HTML_inTD( $display->execute() ). 
                "</tr>";
    }


 function _HTML_commentLink( &$contentItem) {
    
    $counts = AMPContent_Lookup::instance("CommentsByArticle");
    $mycount = isset($counts[$contentItem->id])?  $counts[$contentItem->id]:'0';
    $text= $mycount.' Comments';
    return $this->_HTML_link(AMP_Url_AddVars($contentItem->getURL(),'#comment'),$text);
 
 }
}

?>
