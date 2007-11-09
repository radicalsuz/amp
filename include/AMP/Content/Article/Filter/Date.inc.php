<?php

class ContentFilter_Date {
    var $criteria;
    function ContentFilter_Date( $date_value ) {
        $this->__construct( $date_value );
    }

    function __construct( $date_value ){
        require_once( 'AMP/Content/Article.inc.php');
        $this->_date_value = $date_value ;
    }
    
    function assign( ) {
        $crit_builder = new Article( AMP_Registry::getDbcon( ) );
        $this->criteria = $crit_builder->makeCriteriaDate( $this->_date_value );

    }

    function execute( &$source ) {
        $this->assign( );

        $source->addCriteria( 
            $this->criteria
        );
    }
}
