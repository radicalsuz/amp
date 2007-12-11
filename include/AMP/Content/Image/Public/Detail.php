<?php
require_once( 'AMP/Display/Detail.php');

class AMP_Content_Image_Public_Detail extends AMP_Display_Detail {
    var $_css_class_photocaption = AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE_CAPTION;
    var $_css_class_container_item = 'item_container image_container';

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
        $this->use_class( AMP_IMAGE_CLASS_THUMB );
        return $this->_renderer->image( $this->url_for( AMP_IMAGE_CLASS_THUMB ), $attr_set );
    }

    function url_for( $url_type ='' ) {
        $name = $this->_source->getName( );
        $url_type .= $url_type ? '/' : '';
        return( AMP_IMAGE_PATH . $url_type . $name );
    }

    function render_url_for( $source ) {
        return str_replace( AMP_LOCAL_PATH, '', $source->getPath( ));
    }


    function render_url_for_scaled( $source, $width_limit ) {
        if( $source->width <= $width_limit ) return $this->render_url_for( $source );
        $img_class = end( split( DIRECTORY_SEPARATOR,  dirname( $source->getPath( ))));
        return AMP_url_update( AMP_CONTENT_URL_IMAGE, array( 'filename' => $source->getName( ), 'class' => $img_class, 'action' => 'resize', 'width' => $width_limit )) ;

    }

    function container_attributes( $source ) {
        $attributes = $source->getData( );
        $float_type = isset( $attributes['align']) ? $attributes['align'] : AMP_IMAGE_DEFAULT_ALIGNMENT;
        $container_css = $float_type ? AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE . ' ' . AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE . '-' . $float_type : AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE;
        $container_attr = array( 'class' => $container_css );
        if ( $width = $source->width) $container_attr['style'] = "width: {$width}px";
        if ( isset( $attributes['width'])) $container_attr['style'] = "width: {$attributes['width']}px";
        return $container_attr;
    }

    function image_attributes( $source ) {
        $attributes = $source->getData( );
        if ( $width = $source->width) $container_attr['style'] = "width: {$width}px";
        $image_attr = array( 'class' => 'img_main');
        if ( isset( $attributes['alt']) && $attributes['alt']) {
            $image_attr['alt'] = $attributes['alt'];
        }
        return $image_attr;

    }
    
    function source_class( $source = null ) {
        if( !$source ) $source = $this->_source;
        $path = $source->getPath( );
        $classes = AMP_lookup( 'image_classes');
        foreach( $classes as $class => $key ) {
            if( strpos( $path, AMP_IMAGE_PATH . $class )) return $class;
        }
        return '';
    }

    function use_class( $class ) {
        if( !( $current = $this->source_class( ))) return false;
        $this->_source->setFile( AMP_image_path( $this->_source->getName( ), $class ));
    }

    function get_source_version( $source, $class ) {
        if ( $this->source_class( $source_class ) == $class ) return $source;
        return new AMP_System_File_Image( AMP_image_path( $source->getName( ), $class ));
    }

    function renderItem( $source ) {
        $attributes = $source->getData( );
        if ( !isset( $attributes['image_size'])) {
            $attributes['image_size'] = $this->source_class( $source );
        }
        $image_attr = $this->image_attributes( $source );
        if ( !( $link_url = $source->getData( 'link_url') )){
            $link_url = $this->url_for( AMP_IMAGE_CLASS_ORIGINAL );
        }
        if ( !( $link_target = $source->getData( 'link_target') )){
            $link_target = '_blank';
        }

        return $this->render_image_format_main( 
            $this->_renderer->link( 
                $link_url,
                $this->_renderer->image( $this->url_for( $attributes['image_size']), array( 'class' => 'img_main')),
                array( 'target' => $link_target, 'class' => 'image-link'))
            . $this->render_credit( $source )
            . $this->render_caption( $source ),
            $source
        );
        
    }

    function render_image_format_main( $image_html, $source = null ) {
        if( !isset( $source )) $source = $this->_source;
        $container_attr = $this->container_attributes( $source );
        return $this->_renderer->div( 
            $image_html
            ,
            $container_attr
        );

    }

    function render_credit( &$source ) {
        if ( !AMP_RENDER_ARTICLE_PHOTOCREDIT ) return false;
        #$credit = $source->getData( 'author');
        $credit = $this->render_author_link( $source );
        $license = $source->getData( 'license');
        if ( !$credit ) return false;
        return $this->_renderer->div( 
            $this->_renderer->content_license_thin( $license )
            . $credit,
            array( 'class' => 'photo-credit-article')
            ) ;
    }

    function render_author_link( $source ) {
        $credit = $source->getData( 'author');
        $url = $source->getData( 'author_url');
        if( !( $url && AMP_validate_url( $url ))) return $credit;
        return $this->_renderer->link( $url, $credit, array( 'target' => 'blank' ));

    }

    function render_caption( $source ) {
        if( !( $caption = $source->getData('caption' ))) return false;
        return $this->_renderer->div( 
            $caption,
            array( 'class' => $this->_css_class_photocaption, 
                    'width' => $source->width )
        );

    }

    function render_proofsheet( $source ) {
        $classes = AMP_lookup( 'image_classes');
        $items = array( );

        foreach( $classes as $class => $class_name ) {
            if ( $class == AMP_IMAGE_CLASS_CROP ) continue;
            $version = $this->get_source_version( $source, $class );
            $items[ $version->width ] = ucwords( strtolower( $class_name )) . ': '
                        . $this->_renderer->newline( )
                        . $this->render_scaled_as_link( $version, 600 );
                        #. $this->_renderer->image( $this->url_for( $class) );
        }
        ksort( $items );
        return $this->_renderer->span( AMP_TEXT_ALL_IMAGE_SIZES . ': ' . $source->getName( ), array( 'class' => 'page_result'))
                . $this->_renderer->UL( $items );

    }

    function render_scaled_as_link( $source, $width ) {
        if( $source->width <= $width ) return $this->_renderer->image( $this->render_url_for( $source ));
        $height = ( $source->height / $source->width ) * $width;
        $background_image = $this->render_url_for_scaled( $source, $width );
        $inline_style = "display:block;text-align:center;padding-top:".ceil($height/3)."px;color:#333333;font-weight:bold;text-decoration:none;font-size:18px;height:$height;width:$width;background: url( $background_image ) no-repeat top left;";
        return $this->_renderer->link( $this->render_url_for( $source ), AMP_TEXT_CONTENT_SCALED_FOR_EASY_VIEWING, array( 'style' => $inline_style, 'target' => 'blank' ));
    }
}
?>
