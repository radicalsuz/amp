<?php
require_once( 'AMP/Display/Detail.php');

class AMP_Content_Image_Public_Detail extends AMP_Display_Detail {
    var $_css_class_photocaption = AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE_CAPTION;

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

    function renderItem( $source ) {
        $attributes = $source->getData( );
        $float_type = isset( $attributes['align']) ? $attributes['align'] : AMP_IMAGE_DEFAULT_ALIGNMENT;
        unset( $attributes['align']);
        $container_css = $float_type ? AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE . ' ' . AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE . '-' . $float_type : AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE;
        $container_attr = array( 'class' => $container_css );
        if ( $width = $source->width) $container_attr['style'] = "width: {$width}px";
        $image_attr = array( 'class' => 'img_main');
        if ( isset( $attributes['alt']) && $attributes['alt']) {
            $image_attr['alt'] = $attributes['alt'];
        }

        return $this->_renderer->div( 
            $this->_renderer->link( 
                $this->url_for( AMP_IMAGE_CLASS_ORIGINAL ),
                $this->_renderer->image( $this->url_for( $attributes['image_size']), array( 'class' => 'img_main')),
                array( 'target' => '_blank', 'class' => 'image-link'))
            . $this->render_credit( $source )
            . $this->render_caption( $source ),
            $container_attr
        );
        
    }

    function render_credit( &$source ) {
        if ( !AMP_RENDER_ARTICLE_PHOTOCREDIT ) return false;
        $credit = $source->getData( 'author');
        $license = $source->getData( 'license');
        if ( !$credit ) return false;
        return $this->_renderer->div( 
            $this->_renderer->content_license_thin( $license )
            . $credit,
            array( 'class' => 'photo-credit-article')
            ) ;
    }

    function render_caption( $source ) {
        if( !( $caption = $source->getData('caption' ))) return false;
        return $this->_renderer->div( 
            $caption,
            array( 'class' => $this->_css_class_photocaption, 
                    'width' => $source->width )
        );

    }
}
?>
