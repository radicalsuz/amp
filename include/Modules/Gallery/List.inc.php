<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/Gallery/Set.inc.php');

class Gallery_List extends AMPSystem_List {
    var $name = 'Photo Galleries';
    var $col_headers = array( 'Gallery' => 'galleryname', 'ID'=>'id' );
    var $extra_columns = array( 'Add to Content System'=> 'module_contentadd.php?gallery=' );
	var $editlink = 'gallery_type.php';    

    function Gallery_List ( &$dbcon ){
        $source = &new GallerySet( $dbcon );
        $this->init( $source );
    }
}
?>
