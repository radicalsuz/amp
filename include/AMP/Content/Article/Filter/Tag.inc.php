<?php

class ContentFilter_Tag {
    var $_tag_value;

    function ContentFilter_Tag( $tag_value ) {
        $this->__construct( $tag_value );
    }

    function __construct( $tag_value ) {
        $this->_tag_value = $tag_value;
    }

    function execute( &$source ) {
        $source->addCriteria( 
            $source->makeCriteriaTag( $this->_tag_value )
        );
    }

}

?>
