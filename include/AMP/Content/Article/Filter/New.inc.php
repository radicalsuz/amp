<?php

class ContentFilter_New {
    var $criteria;
   
    function assign( ) {
        require_once( 'AMP/Content/Article.inc.php');
        $crit_builder = new Article( AMP_Registry::getDbcon( ) );
        $this->criteria = $crit_builder->makeCriteriaNew( true );
    }
        
    function execute( &$source ) {
        $this->assign( );
        $source->addCriteria( $this->criteria );
        $source->readData( );
    }
}

?>
