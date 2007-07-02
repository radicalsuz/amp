<?php

require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/Content/Template/Archive/Archive.php');

class AMP_Content_Template_Archive_List extends AMP_Display_System_List {

    var $_source_object = 'AMP_Content_Template_Archive';
    var $columns = array( 'controls', 'name', 'archived_at', 'archive_id', 'template' ) ;
    var $column_headers = array( );
    var $_suppress_create= true;
    var $_suppress_toolbar = true;

    function AMP_Content_Template_Archive_List( &$source, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

}
?>
