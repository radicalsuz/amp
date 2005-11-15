<?php

require_once( 'AMP/Content/Article/DocumentLink.inc.php');
require_once( 'AMP/Content/Image.inc.php');


class ImageLink extends DocumentLink {

    var $_default_display = 'ImageLink_Display';
    var $_image;
    var $_image_id;

    function ImageLink( $filename = null ) {
        if ( isset( $filename )) $this->init( $filename );
    }
    function setFile( $filename, $filetype = AMP_CONTENT_DOCUMENT_TYPE_DEFAULT ) {
        PARENT::setFile( $filename, $filetype );
        if ( $this->verifyFileType( )){
            if ( 'img' != $this->getFileType( )) return false;
        }
        $this->_image = &new Content_Image( $filename );

    }

    function display( $display_type = null ) {
        if ( $this->getFileName() && !isset( $this->_image) ) $this->_default_display = 'ArticleDocumentLink_Display';
        return PARENT::display( $display_type );
    }
    function getURL( $image_class = AMP_IMAGE_CLASS_ORIGINAL ) {
        if ( !isset( $this->_image)) return false;
        return $this->_image->getURL( $image_class );
    }
    function setImageId( $id ){
        $this->_image_id = $id;
    }
    function getImageId( ){
        if ( !isset( $this->_image_id)) return false;
        return $this->_image_id;
    }

}

class ImageLink_Display extends ArticleDocumentLink_Display {

    function ImageLink_Display( $document_link ) {
        $this->setDocument( $document_link );
    }

    function _HTML_docLink() {
        $attr = array( 'border' => 0 );
        $link_attr = array( 'target' => '_blank' );

        if ( $id = $this->document_link->getImageId( )) $attr['id'] = $id;
        $linkhtml = $this->_HTML_image( $this->document_link->getURL( AMP_IMAGE_CLASS_OPTIMIZED ), $attr );
        if ( $id ) $link_attr['id'] = $id . '_link';

        return $this->_HTML_link( $this->document_link->getURL(), $linkhtml, $link_attr );
    }
}

class ImageLink_DisplayDiv extends ImageLink_Display {
    function ImageLink_Display( $document_link ){
        $this->setDocument( $document_link );
    }
    function _HTML_start( ) {
        $attr = array( 'style' => 
                        'margin-left:150px;' . ( $this->document_link->getURL( ) ? '' : 'display:none;')
                        );
        if ( $id = $this->document_link->getImageId( )) $attr['id'] = $id . '_container';
        return '<div '. $this->_HTML_makeAttributes( $attr ).'>';
    }

    function _HTML_end( ) {
        return '</div>';
    }

}
?>
