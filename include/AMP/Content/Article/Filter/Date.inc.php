<?php

class ContentFilter_Date {
    function ContentFilter_Date( $date_value ) {
        $this->__construct( $date_value );
    }

    function __construct( $date_value ){
        $this->_date_value = $date_value ;
    }

    function execute( &$source ) {
        $source->addCriteria( 
            $source->makeCriteriaDate( $this->_date_value )
        );
    }
}
