<?php

class ContentFilter_Fp {
    var $criteria;
   
    function assign( ) {
        require_once( 'AMP/Content/Article.inc.php');
        $crit_builder = new Article( AMP_Registry::getDbcon( ) );
        $this->criteria = $crit_builder->makeCriteriaFrontpage( 1 );
    }
    function execute( &$source ) {
        $this->assign( );
        $source->addCriteria( $this->criteria );
        #$source->addCriteriaFp(    );
        $source->readData( );
    }
}

?>
