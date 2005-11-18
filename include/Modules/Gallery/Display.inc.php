<?php
require_once( 'AMP/Content/Display/List.inc.php');
require_once( 'Modules/Gallery/Image/Set.inc.php');

class Gallery_Display extends AMPContent_DisplayList_HTML {
    var $_css_class_title    = "gallerytitle";
    var $_css_class_photocaption = "gallerycap";
    var $_css_class_container_listentry = "gallerycon";
    var $_css_id_container_content = "gallery";

    var $_gallery;
    var $_sourceItem_class = "GalleryImage";
    var $_pager_limit = 6;

    function Gallery_Display( &$gallery, $read_data=true ){
        $this->_gallery = &$gallery;
        $source = &new GalleryImageSet( $gallery->dbcon, $gallery->id );
        $this->init( $source, $read_data );
    }

    function _HTML_listingFormat( $html ) {
        $intro =  $this->_HTML_title( $this->_gallery->getName( ))
                . $this->_HTML_blurb( $this->_gallery->getBlurb( ));
        return $intro . $this->_HTML_inDiv( $html, array( 'id' => $this->_css_id_container_content ) );
    }

    function _HTML_title( $name ){
        if ( !$name ) return false;
        return $this->_HTML_in_P( $name, array( "class"=>$this->_css_class_title ) ); 
    }

    function _HTML_blurb( $blurb ) {
        if (!$blurb) return false;
        return $this->_HTML_in_P( converttext($blurb), array('class'=>$this->_css_class_text));
    }

    function _HTML_listItemLayout ( $text, $image ) {
        return  $this->_HTML_inDiv( $image . $text, array( 'class' => $this->_css_class_container_listentry ) );
    }

    function _HTML_listItemDescription( &$image ) {
        $caption = $image->getCaption( ); 
        $imagedate = $this->_HTML_listItemDate( $image->getItemDate( ));
        $source = $this->_HTML_listItemSource( $image->getSource( ) );
        if ( $complete_caption = $caption ) {
            if ( $imagedate ) $complete_caption .= "&nbsp;" . $imagedate;
        } else {
            $complete_caption = $imagedate ? $imagedate : false;
        }
        return $this->_HTML_inDiv( $complete_caption . $source, array( 'class' => $this->_css_class_photocaption ));
        

    }

    function _HTML_listItemSource( $source ){
        if ( !$source ) return false;
        return    $this->_HTML_newline( )
                . $this->_HTML_italic( $source )
                . $this->_HTML_newline( );
    }

    function _HTML_thumbnail( &$image ){
        if ( !$result = PARENT::_HTML_thumbnail( $image )) return false;
        return $this->_HTML_link( $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $result, array( 'target' => '_blank') );
    }


}
?>
