<?php


class AMP_Display_Detail {

    var $_renderer;
    var $_source;

    var $_image_attr = array( 'border' => 0, 'align' => AMP_IMAGE_DEFAULT_ALIGNMENT );

    var $_css_class_title = 'title';
    var $_css_class_image = 'image';
    var $_css_class_blurb = 'text';
    var $_css_class_container_item = 'item_detail';

    var $_item_display_method = 'renderItem';

    var $_suppress_header;
    var $_suppress_footer;

    function AMP_Display_Detail( &$source ) {
        $this->__construct( $source );
    }

    function __construct( $source ) {
        $this->_source = &$source;
        $this->_renderer = &AMP_get_renderer( );
        $this->_init_attributes( );
    }

    function _init_attributes( ) {
        $this->_image_attr['class'] = $this->_css_class_image;
    }

    function execute( ) {

        //verify display method
        $local_method = ( $this->_item_display_method == 'renderItem' ) ;
        if ( !$local_method ){
            if ( !function_exists( $this->_item_display_method )) {
                trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), $this->_item_display_method ));
                return $this->_output_empty( );
            }
        }
        $display_method = $this->_item_display_method;

        if ( $local_method ) {
            $item_output = $this->renderItem( $this->_source );
        } else {
            $item_output = $display_method( $this->_source, $this );
        }

        return $this->_renderBlock( $item_output ) ;

    }

    function _renderBlock( $html ) {
        $list_block = $this->_renderer->inDiv( 
                            $html,
                            array( 'class' => $this->_css_class_container_item )
                        );

        $output = '';
        if ( !$this->_suppress_header ) {
            $output .= $this->_renderHeader( );
        }

        $output .= $list_block;

        if ( !$this->_suppress_footer ) {
            $output .= $this->_renderFooter( );
        }
        return $output;

    }

    function renderItem( $source ) {
        $name = $source->getName( );
        $image = $source->getImageRef( );
        $blurb = $source->getBlurb( );

        $output = '';
        if ( $name ) {
            $output .= $this->_renderer->inSpan( $name, array( 'class' => $this->_css_class_title ))
                        . $this->_renderer->newline( );
        }
        if ( $image ) {
            $output .= $this->_renderImage( $image );
        }
        if ( $blurb ) {
            $output .= $this->_renderer->in_P( $blurb, array( 'class' => $this->_css_class_blurb ));
        }
        return $output;

    }

    function _renderImage( $image ) {
        $image_url = $image->getURL( );
        return $this->_renderer->image( $image_url, $this->_image_attr )
                    . $this->_renderer->newline( );

    }

    function _renderHeader( ) {
        return false;
    }

    function _renderFooter( ) {
        return false;
    }

    function _init_display_methods( ) {
        $display_id = strtoupper( get_class( $this ));

        if ( $display_id == 'AMP_DISPLAY_DETAIL' ) {
            if ( isset( $this->_source_object )) {
                $display_id .= '_' . $this->_source_object ;
            } elseif ( isset( $this->name )) {
                $display_id .= '_' . str_replace( ' ', '_' , $this->name );
            } 
        }
        $display_id =  strtoupper( $display_id );
        if ( defined( 'AMP_RENDER_' . $display_id )) {
            $this->_item_display_method = constant( 'AMP_RENDER_' .$display_id );
        }
    }

    function set_source ( &$source ) {
        $this->_source = $source;
    }

}


?>
