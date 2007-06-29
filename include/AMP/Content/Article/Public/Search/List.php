<?php

require_once( 'AMP/Content/Article/Public/List/Legacy.php');

class Article_Public_Search_List extends Article_Public_List_Legacy {
    var $_css_class_container_list = 'list_block content_search';
    var $_sort_sql_default;

    function Article_Public_Search_List ( $source = false, $criteria = array( )) {
        $source=false;
        $this->__construct( $source, $criteria );
    }

    function _init_criteria( ) {
		if (!isset($this->_source_criteria['fulltext'])) {
			$this->_sort_sql_default = 'default';
		}
	}

    function _output_empty( ) {
        $this->message( AMP_TEXT_SEARCH_NO_MATCHES );
        return $this->render_search_form( ) . AMP_TEXT_SEARCH_NO_MATCHES;
    }
    

}


?>
