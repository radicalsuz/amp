<?php

class ContentFilter_Related {

    function execute( &$source ) {
        $currentPage = &AMPContent_Page::instance( );

        switch( true ) {
            case $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION  )  :
                $source->addCriteria( 
                        $source->_makeCriteriaSectionRelated( $currentPage->getSectionId( )));
                break;
            default;
        }
    }
}

?>
