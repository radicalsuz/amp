<?php

class ContentFilter_General {
    var $_allowed_classes = array( AMP_CONTENT_CLASS_DEFAULT );
    var $criteria;

    function assign( ) {
        require_once( 'AMP/Content/Article.inc.php');
        $crit_builder = new Article( AMP_Registry::getDbcon( ) );
        $this->criteria = $crit_builder->makeCriteriaClass( $this->_allowed_classes );
    }

    function execute( &$source ) {
        $this->assign( );
        $source->addCriteria( $this->criteria );
        #$source->addCriteriaClass( $this->_allowed_classes );
        $source->readData( );
    }
}

?>
