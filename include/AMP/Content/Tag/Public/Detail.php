<?php

require_once( 'AMP/Display/Detail.php');

class AMP_Content_Tag_Public_Detail extends AMP_Display_Detail {

    function AMP_Content_Tag_Public_Detail( $source ) {
        $this->__construct( $source );
    }

    function renderItem( $source ) {
        $name = $source->getName( );
        $image = $source->getImageRef( );
        $blurb = $source->getBlurb( );

        $output = '';
        if ( $image ) {
            $output .= $this->_renderImage( $image );
        }
        if ( $name ) {
            $output .= $this->_renderer->inSpan( $name, array( 'class' => $this->_css_class_title ))
                        . $this->_renderer->newline( );
        }
        if ( $blurb ) {
            $output .= $this->_renderer->in_P( $blurb, array( 'class' => $this->_css_class_blurb ));
        }
        $output .= $this->_renderer->newline( );

        return $output;

    }

    function _renderImage( $image ) {
       $image_url = AMP_Url_AddVars( 
                        AMP_CONTENT_URL_IMAGE, 
                            array(  'filename=' . $image->getName( ), 
                                    'class=' . AMP_IMAGE_CLASS_THUMB, 
                                    'height=30', 'action=resize' )
                                    );

       return $this->_renderer->image( $image_url, $this->_image_attr );
    }

    function _renderFooter( ) {
        $criteria = array( 'tag' => $this->_source->id );

        require_once( 'AMP/Content/Tag/Item/Public/List.php');
        $empty_value = false;
        $tagged_items_list = & new AMP_Content_Tag_Item_Public_List( $empty_value, $criteria );
        return $tagged_items_list->execute( );
    }
}

?>
