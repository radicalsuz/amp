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
        $imageRef = &new Content_Image( $source->getName( ));
        foreach( $imageRef->getImageClasses( ) as $current_class ){
            $fullpath = $imageRef->getPath( $current_class );
            if ( !file_exists( $fullpath )) continue;
            unlink( $fullpath );
        }
        
        //remove gallery records
        require_once( 'Modules/Gallery/Image/Set.inc.php');
        $gallerySet = &new GalleryImageSet( AMP_Registry::getDbcon( ));
        $gallerySet->deleteData( $gallerySet->getCriteriaImage( $source->getName( )));

        $this->_list->removeSourceItemId( $source->getName( ));

    }

}

?>
