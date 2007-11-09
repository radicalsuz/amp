<?php

require_once( 'AMP/Content/Article/Public/List.php');
require_once( 'AMP/Content/Article/Public/Detail/Frontpage.php');

class Article_Public_List_Frontpage extends Article_Public_List {

    var $_css_class_container_list = "article_public_list home list_block";
    var $_source_criteria = array( 'displayable_frontpage' => 1 );
    var $_sort_sql_default = 'ordered';

    function Article_Public_List_Frontpage( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function _renderItem( $source ) {
        $display = $source->getDisplay( );
        return $display->execute( );
    }

    function render_intro( ) {
        return false;
    }
}

?>
