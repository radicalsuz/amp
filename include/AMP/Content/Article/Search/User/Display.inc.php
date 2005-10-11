<?php
require_once( 'AMP/Content/Article/Search/User/Form.inc.php');
require_once( 'AMP/Content/Article/SetDisplay.inc.php');
require_once( 'AMP/Content/Display/Criteria.inc.php');

class ContentSearch_Display_User extends ArticleSet_Display {

    var $_searchForm;

    function ContentSearch_Display_User ( ) {
        $this->init( );

    }

    function init( ) {
        $this->_searchForm = &new ContentSearch_Form_User( );
        $this->_searchForm->Build( true );
        if ( !$action = $this->_searchForm->submitted( )) return $this->_searchForm->applyDefaults( );
        return $this->initResults();
    }

    function execute() {
        return $this->_searchForm->output( ) . $this->displayResults( );
    }

    function displayResults( ) {
        if ( !$this->_searchForm->submitted( )) return false;
        if ( !$this->_source->makeReady( )) return false;
        return PARENT::execute( );
    }

    function initResults() {
        $source_set = &new ArticleSet( AMP_Registry::getDbcon( ));
        PARENT::init( $source_set , false);
        $display_criteria = &new AMPContent_DisplayCriteria( );
        $display_criteria->clean( $this->_source );
        $this->_source->applySearch( $this->_searchForm->getSearchValues() );
    }
}
?>
