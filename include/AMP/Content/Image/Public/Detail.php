<?php
require_once( 'AMP/Display/Detail.php');

class AMP_Content_Image_Public_Detail extends AMP_Display_Detail {
    function AMP_Content_Image_Public_Detail( $source ) {
        $this->__construct( $source );
    }

    function _init_attributes( ) {

        $css = 
<<<STYLESHEET
img.thumb {
    border:0;
    width: %spx;
    max-height: 300px;
}
STYLESHEET;
        $css = sprintf( $css, AMP_IMAGE_WIDTH_THUMB );
        $header = &AMP_get_header( );
        $header->addStylesheetDynamic( $css, strtolower( get_class( $this )));
    }

    function render_thumb( $source = null, $attr_set=array( )) {
        if ( !isset( $source )) $source = $this->_source;
        if( !isset( $attr_set['class']) ) $attr_set['class'] = 'thumb';
        return $this->_renderer->image( $this->url_for( AMP_IMAGE_CLASS_THUMB ), $attr_set );
    }

    function url_for( $url_type ='' ) {
        $name = $this->_source->getName( );
        $url_type .= $url_type ? '/' : '';
        return( AMP_IMAGE_PATH . $url_type . $name );
    }
}
?>
