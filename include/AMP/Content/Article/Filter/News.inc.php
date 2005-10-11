<?php

class ContentFilter_News {
    var $_allowed_classes = array( AMP_CONTENT_CLASS_NEWS );

    function execute( &$source ) {
        $source->addCriteriaClass( $this->_allowed_classes );
        $source->readData( );
    }
}

?>
