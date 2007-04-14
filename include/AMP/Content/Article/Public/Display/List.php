<?php

require_once( 'AMP/Display/List.inc.php');

class AMP_Content_Article_Public_Display_List extends AMP_Display_List {

    var $_css_class_container_list = 'main_content';

    var $_css_class_title    = "listtitle";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_morelink = "go";
    var $_css_class_text     = "text";
    var $_css_class_date     = "bodygreystrong";

    function AMP_Content_Article_Public_Display_List( $source = false, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function addFilter( $filter_name, $filter_var = null  ) {
        trigger_error( AMP_TEXT_ERROR_NOT_DEFINED , get_class( $this), 'addFilter' );
        return;
    }

    function setPageLimit( $limit ) {
        $this->_pager_limit = $limit;
    }
}

?>
