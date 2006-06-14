<?php

require_once( 'AMP/Content/Article/Comment/List.inc.php');

class AMP_Content_Article_Comment_List_Basic extends ArticleComment_List {

    var $suppress = array( 'messages' => true, 'toolbar' => true, 'header' => true);
    var $_pager_active = false;
    var $name_field = 'name';

    function AMP_Content_Article_Comment_List_Basic( &$dbcon, $criteria = null ){
        $this->init( $this->_init_source( $dbcon, $criteria ));
    }

    function _noRecordsOutput( ){

        return  AMP_TEXT_SEARCH_NO_MATCHES
                . $this->newline( 2 ) . $this->_HTML_addLink();
    }

}

?>
