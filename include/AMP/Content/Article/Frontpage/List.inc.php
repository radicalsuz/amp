<?php

require_once( 'AMP/Content/Article/ListForm.inc.php' );

class Article_Frontpage_List extends Article_ListForm {

    var $_source_criteria;

    function Article_Frontpage_List( &$dbcon, $criteria = array( )) {
        $criteria = array_merge( array( 'class' => AMP_CONTENT_CLASS_FRONTPAGE ), $criteria );
        $this->init( $this->_init_source( $dbcon, $criteria ));
    }

    function _after_init_search( $criteria = null ) {
        $this->_url_add = AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'action=add', 'class=' . AMP_CONTENT_CLASS_FRONTPAGE ));
    }

}

?>
