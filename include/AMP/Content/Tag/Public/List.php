<?php
require_once( 'AMP/Content/Tag/Tag.php');
require_once( 'AMP/Display/List.php');

class AMP_Content_Tag_Public_List extends AMP_Display_List {
    var $name = 'Tags';

    var $_source_object = 'AMP_Content_Tag';
    var $_pager_active = false;
    var $_display_columns = 3;

    var $_css_class_container_list = 'list_tags';

    function AMP_Content_Tag_Public_List( $source = false, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }
}


?>
