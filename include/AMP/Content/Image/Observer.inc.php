<?php

require_once( 'AMP/System/Observer.php');
require_once( 'AMP/Content/Image.inc.php');

class AMP_Content_Image_Observer extends AMP_System_Observer {
    var $_list;

    function AMP_Content_Image_Observer( &$list ){
        //interface
        $this->_list = $list;
    }

    function onDelete( &$source ){
        //delete all versions
        foreach( AMP_lookup('image_classes' ) as $current_class ){
            $fullpath = AMP_image_path( $source->getName( ), $current_class );
            if ( !file_exists( $fullpath )) continue;
            unlink( $fullpath );
        }
        
        //remove gallery records
        require_once( 'Modules/Gallery/Image/Set.inc.php');
        $gallerySet = &new GalleryImageSet( AMP_Registry::getDbcon( ));
        $gallerySet->deleteData( $gallerySet->getCriteriaImage( $source->getName( )));

        $this->_list->removeSourceItemId( $source->getName( ));

        //clear image folder cache
        $this->_update_image_cache_delete( $source->getName( ));
        /*
        $cache = &AMP_get_cache( );
        if ( $cache ) {
            trigger_error( 'attempting cache delete with key ' . $source->getCacheKeySearch( ));
            $cache->delete( $source->getCacheKeySearch( ) );
        }
        */

    }

    function _update_image_cache_delete( $image_name ){
        $imageRef = &new AMP_System_File_Image( AMP_image_path( $image_name, AMP_IMAGE_CLASS_ORIGINAL ));
        $image_cache_key = $imageRef->getCacheKeySearch( );
        $image_cache = &AMP_cache_get( $image_cache_key );

        if ( !$image_cache ) return;

        unset( $image_cache[ $image_name ] );
        AMP_cache_set( $image_cache_key, $image_cache );
    }

}

?>
