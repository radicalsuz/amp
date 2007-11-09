<?php

require_once( 'AMP/System/List/Request.inc.php');

class AMP_Content_Image_List_Request extends AMP_System_List_Request {

    function AMP_Content_Image_List_Request( &$source ){
        $this->init( $source );
    }

    function commitActionLocal( &$target_set, $action, $args = null ){
        if ( $action != 'recalculate' ) return false;
        $image_finder = new AMP_System_File_Image( );
        $targets = $image_finder->find( array( 'path' => AMP_LOCAL_PATH . '/img/original/'));
        $this->recalculate( $targets, $args );
        return true;
    }

    function recalculate( &$target_set, $args = null ){
        $new_sizes  = array( );
        if ( isset( $args[ 'image_width_thumb' ] ) && is_numeric( $args['image_width_thumb']) && $args['image_width_thumb'] && $args[ 'image_width_thumb' ] != AMP_IMAGE_WIDTH_THUMB ){
            $new_sizes['thumb'] = $args['image_width_thumb'];
            $reg = &AMP_Registry::instance( );
            $reg_value = array( 'width' => $new_sizes['thumb']);
            $reg->setEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES, $reg_value ) ;
        }
        if ( isset( $args[ 'image_width_tall' ] ) && is_numeric( $args['image_width_tall']) && $args['image_width_tall'] && $args[ 'image_width_tall' ] != AMP_IMAGE_WIDTH_TALL ){
            $new_sizes['tall'] = $args[ 'image_width_tall' ];
        }
        if ( isset( $args[ 'image_width_wide' ] ) && is_numeric( $args['image_width_wide']) && $args['image_width_wide'] && $args[ 'image_width_wide' ] != AMP_IMAGE_WIDTH_WIDE ){
            $new_sizes['wide'] = $args[ 'image_width_wide' ];
        }
        if ( empty( $new_sizes )) return false;

        $this->_saveNewWidthSettings( $new_sizes );
        require_once( 'AMP/Content/Image.inc.php');

        foreach( $target_set as $target ){
            $this->resize( $target, $new_sizes );
        }
        $this->_clear_cache( $target );
        
        
    }

    function _clear_cache( &$target ){
        $cache_key = $target->getCacheKeySearch( );
        if ( !( $cache = &AMP_get_cache( ))) return false;
        $cache->delete( $cache_key );

    }

    function resize( &$target, $widths ){
        set_time_limit( 30 );
        trigger_error( sprintf( AMP_TEXT_ACTION_NOTICE, AMP_TEXT_RECALCULATE, $target->getName( ) ));
        $content_image = &new Content_Image( $target->getName() );
        $action_flag = false;
        if ( isset( $widths['thumb'])) {
            if ( file_exists( $content_image->getPath( AMP_IMAGE_CLASS_CROP ))) {
                $crop_target = &new AMP_System_File_Image( $content_image->getPath( AMP_IMAGE_CLASS_CROP ));
            } else {
                $crop_target = &$target;
            }
            if ( $this->_rewriteVersion( $crop_target, $widths['thumb'], $content_image->getPath( AMP_IMAGE_CLASS_THUMB ))) {
                $action_flag = true;
            }
        }
        if ( isset( $widths['tall']) && ( $target->height >= $target->width )) {
            if ( $this->_rewriteVersion( $target, $widths['tall'], $content_image->getPath( AMP_IMAGE_CLASS_OPTIMIZED ))) {
                $action_flag = true;
            }
        }
        if ( isset( $widths['wide']) && ( $target->width > $target->height )) {
            if ( $this->_rewriteVersion( $target, $widths['wide'], $content_image->getPath( AMP_IMAGE_CLASS_OPTIMIZED ))) {
                $action_flag = true;
            }
        }
        if ( $action_flag ) ++$this->_committed_qty;
    }

    function _rewriteVersion( &$target, $new_width, $file_path ){
        $new_height = intval( $target->height *( $new_width / $target->width ));
        return $target->write_image_resource( 
            $target->resize( $new_width, $new_height ),
            $file_path
        );

    }

    function _saveNewWidthSettings( $sizes ){
        require_once( 'AMP/System/Setup/Setup.php');
        $setup = &new AMP_System_Setup( AMP_Registry::getDbcon( ));
        $setup->setImageWidths( $sizes );
        $result = $setup->save( );
        $setup->dbcon->CacheFlush( );
    }

}

?>
