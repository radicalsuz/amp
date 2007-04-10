<?php

class ContentFilter_General {
    var $_allowed_classes = array( AMP_CONTENT_CLASS_DEFAULT );

    function execute( &$source ) {
        $source->addCriteriaClass( $this->_allowed_classes );
        $source->readData( );
    }
}

?>
