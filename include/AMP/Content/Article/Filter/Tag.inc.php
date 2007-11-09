<?php

class ContentFilter_Tag {
    var $_tag_value;
    var $criteria;

    function ContentFilter_Tag( $tag_value ) {
        $this->__construct( $tag_value );
    }

    function __construct( $tag_value ) {
        require_once( 'AMP/Content/Article.inc.php');
        $this->_tag_value = $tag_value;
    }

    function assign( ) {
        $crit_builder = new Article( AMP_Registry::getDbcon( ) );
        $this->criteria = $crit_builder->makeCriteriaTag( $this->_tag_value );
    }

    function execute( &$source ) {
        $this->assign( );
        $source->addCriteria( 
            $this->criteria
        );
    }

}

?>
