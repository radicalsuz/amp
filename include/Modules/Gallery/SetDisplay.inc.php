<?php

require_once( 'AMP/Content/Display/List.inc.php');
require_once( 'Modules/Gallery/Set.inc.php');
require_once( 'Modules/Gallery/Gallery.php');

class GallerySet_Display extends AMPContent_DisplayList_HTML {

#    var $_css_class_container_listentry = "gallerylist";
#    var $_css_class_title    = "listtitle";
    var $_css_class_title    = "gallerytitle";
    var $_sourceItem_class = "Gallery";

    function GallerySet_Display( &$source ){
        $this->init( $source );
        $this->_layout_table_attr['class'] = 'gallerylist';
    }

    function _HTML_title( $title, $link ){
        if ( !$title ) return false;
        return $this->_HTML_link(  
                    $link, 
                    $title ,
                    array( "class"=>$this->_css_class_title ) ); 
    }

    function _HTML_blurb( $blurb ) {
        if (!$blurb) return false;
        return $this->_HTML_in_P( converttext($blurb), array('class'=>$this->_css_class_text));
    }

    function _HTML_listItemDescription( &$gallery ) {
        return  $this->_HTML_title( $gallery->getName( ), $gallery->getURL()) 
                . $this->_HTML_newline( )
                . $this->_HTML_blurb( $gallery->getBlurb( ))
                . $this->_HTML_newline( );
    }

    function _HTML_thumbnail( &$gallery ){
        $this->_thumb_attr['class'] = 'imgpad';
        if ( !$result = parent::_HTML_thumbnail( $gallery->getImageRef( true ))) return false;
        
        return $this->_HTML_link( $gallery->getURL( ), $result, array( 'target' => '_blank') );
    }
   /* 
    function _HTML_listItemLayout( $text, $image ) {
       
        return  "<table" . $this->_HTML_makeAttributes( $this->_layout_table_attr ) . "><tr>" . 
                $this->_HTML_inTD( $text , array( 'class' => $this->_css_class_container_listentry ) ) . 
                $this->_HTML_inTD( $image, array( 'class' => $this->_css_class_container_listimage ) ) .
                $this->_HTML_endTable() . 
                $this->_HTML_newline();
    }
    */

    function _HTML_listItem( &$contentItem ) {
       
        $thumb = false;
        if (method_exists( $contentItem, 'getImageRef' )) {
            $thumb  = $this->_HTML_thumbnail( $contentItem );
        }

        $text_description   = $this->_HTML_listItemDescription( $contentItem );

        return $this->_HTML_listItemLayout( $text_description, $thumb );

    }

}
?>
