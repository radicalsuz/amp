<?php

class ContentFilter_Native {
    var $criteria;

    function assign( ) {
        $currentPage = &AMPContent_Page::instance( );
        require_once( 'AMP/Content/Article.inc.php');
        $crit_builder = new Article( AMP_Registry::getDbcon( ) );
        $this->criteria = $crit_builder->makeCriteriaPrimarySection( $currentPage->getSectionId( ));
    }

    function execute( &$source ) {
        $currentPage = &AMPContent_Page::instance( );

        switch( true ) {
            case $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION  )  :
                $this->assign( );
                $source->addCriteria(  $this->criteria );
                break;
            default;
        }
    }
}

?>
