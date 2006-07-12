<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Gallery/Image.inc.php');

class GalleryImageSet extends AMPSystem_Data_Set {
    var $datatable = 'gallery';
    var $sort = array( 'img' );

    function GalleryImageSet( &$dbcon, $id = null ){
        $this->sort =
                array( "if(isnull(listorder) or listorder='', Concat( ". AMP_CONTENT_LISTORDER_MAX .", img), listorder) ASC");
        $this->init( $dbcon );
        if ( isset( $id )) $this->addCriteriaGallery( $id );
    }

    function addCriteriaGallery( $gallery_id ){
        return $this->addCriteria( 'galleryid='.$gallery_id);
    }
    function addCriteriaLive( ){
        return $this->addCriteria( 'publish=1');
    }
    function addCriteriaSection( $section_id ){
        return $this->addCriteria( 'section='.$section_id );
    }
    function addCriteriaStatus($value ){
        return $this->addCriteria( 'publish='.$value);
    }

    function getCriteriaImage( $img_filename ){
        return 'img=' . $this->dbcon->qstr( $img_filename );
    }
}
?>
