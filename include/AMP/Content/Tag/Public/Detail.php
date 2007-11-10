<?php

require_once( 'AMP/Display/Detail.php');

class AMP_Content_Tag_Public_Detail extends AMP_Display_Detail {

    var $display_content = true;
    var $_css_class_container_item = 'tag_public_detail';

    function AMP_Content_Tag_Public_Detail( $source ) {
        $this->__construct( $source );
    }

    function renderItem( $source ) {
        return    $this->render_image( $source )
                . $this->render_title( $source )
                . $this->render_blurb( $source )
                . $this->_renderer->newline( );
    }

    function render_title( $source ) {
        if ( !( $name = $source->getName( ))) return false;
        return $this->_renderer->div( $name, array( 'class' => $this->_css_class_title ));

    }

    function render_blurb( $source ) {
        if ( !( $blurb = $source->getBlurb( ))) return false;
        return $this->_renderer->div( $blurb, array( 'class' => $this->_css_class_blurb ));
    }

    function render_image( $source ) {
        if ( !( $image = $source->getImageRef( ))) return false;
        $image_url = AMP_Url_AddVars( 
                        AMP_CONTENT_URL_IMAGE, 
                            array(  'filename=' . $image->getName( ), 
                                    'class=' . AMP_IMAGE_CLASS_THUMB, 
                                    'height=30', 'action=resize' )
                                    );

       return $this->_renderer->image( $image_url, $this->_image_attr );
    }

    function _renderFooter( ) {
        if ( $this->display_content ) {
            return $this->_render_all_items_list( );
        }
    }

    function _render_all_items_list( ) {
        $criteria = array( 'tag' => $this->_source->id );

        require_once( 'AMP/Content/Tag/Item/Public/List.php');
        $empty_value = false;
        $tagged_items_list = & new AMP_Content_Tag_Item_Public_List( $empty_value, $criteria );
        return $tagged_items_list->execute( );

    }

}

?>
