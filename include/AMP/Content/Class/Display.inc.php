<?php

if (!defined( 'AMP_CONTENT_LIST_INTRO_DISPLAY' )) define ('AMP_CONTENT_LIST_INTRO_DISPLAY', 'ListIntro_Display' );

require_once ('AMP/Content/Article/SetDisplay.inc.php' );
require_once ('AMP/Content/Class.inc.php' );

class ContentClass_Display extends ArticleSet_Display {

    var $_class;
    var $_showListIntro = true;

    function ContentClass_Display( &$classRef, $read_data = true ) {
        $this->_class = &$classRef;
        $this->init( $classRef->getContents(), $read_data );
    }

    function execute() {
        $intro = &$this->getIntroDisplay();
        return  $this->_HTML_listIntro( $intro ) . 
                parent::execute();
    }

    function &getIntroDisplay() {

        $empty_value = false;
        if (! $this->_showListIntro) return $empty_value; 

        $introClass = AMP_CONTENT_LIST_INTRO_DISPLAY;
        $path = split( '_', $introClass );
        include_once( 'AMP/Content/'.join( '/', array_reverse( $path ) ) .'.inc.php' );

        if( class_exists( $introClass ) ) {
            $result = &new $introClass( $this->_class );
            return $result;
        }
        trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP',$introClass ));
        return $empty_value;

    }

    function setListIntro( $show_intro = true ) {
        $this->_showListIntro = $show_intro;
    }

    function _HTML_listIntro( &$intro ) {
        if ( !$intro ) return false;
        if ( isset ($this->_pager) && !( $this->isFirstPage() && $intro )) return $this->_pager->_HTML_topNotice( $this->_class->getName() );
        return $intro->execute(); # . $this->_HTML_newline();
    }

    function _activatePager() {
        if ( $class_limit = $this->_class->getListItemLimit( )) {
            $this->_pager_limit = $class_limit;
        }
        return parent::_activatePager( );
    }

}
?>
