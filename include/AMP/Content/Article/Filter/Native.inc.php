<?php

class ContentFilter_Native {

    function execute( &$source ) {
        $currentPage = &AMPContent_Page::instance( );

        switch( true ) {
            case $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION  )  :
                $source->addCriteria( 
                        $source->_makeCriteriaSectionBase( $currentPage->getSectionId( )));
                break;
            default;
        }
    }
}

?>
