<?php

require_once( 'AMP/System/Data/Set.inc.php');

class GalleryImageSet extends AMPSystem_Data_Set {
    var $datatable = 'gallery';
    var $sort = array( 'img');

    function GalleryImageSet( &$dbcon, $id = null ){
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
}
?>
