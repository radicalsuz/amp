<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Gallery/Set.inc.php');
require_once( 'Modules/Gallery/Gallery.php');

class Gallery_List extends AMPSystem_List {
    var $name = 'Photo Galleries';
    var $col_headers = array( 
        'Gallery' => 'name', 
        'ID'=>'id', 
        'Status' => 'publish',
        'Publish' => 'publishButton'
        );
	var $editlink = 'gallery_type.php';    
    var $_source_object = 'Gallery';

    function Gallery_List ( &$dbcon ){
        //$source = &new GallerySet( $dbcon );
        //$this->init( $source );
        $this->init( $this->_init_source( $dbcon ));
    }

    function publishButton( &$source, $fieldname ){
        return AMP_publicPagePublishButton( $source->id, 'gallery_id'); 
    }

}
?>
