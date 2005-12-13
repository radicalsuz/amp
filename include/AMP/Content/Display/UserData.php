<?php

require_once( 'AMP/Content/Display/HTML.inc.php');
require_once( 'AMP/UserData/Input.inc.php');

class AMPDisplay_UserData extends AMPDisplay_HTML {

    var $_udm;
    var $_dbcon;
    var $_introtext;

    function AMPDisplay_UserData( &$udm ){
        $this->init( $udm );
    }

    function init( &$udm ){
        $this->setUDM( $udm );
        $this->_dbcon = &$udm->dbcon;
    }

    function setUDM( &$udm ){
        $this->_udm = &$udm;
    }

    function execute( ){
        return $this->showIntroText( ) . $this->_udm->output( );
    }

    function &getIntroText() {
        require_once( 'AMP/System/IntroText.inc.php');
        if ( !$intro_id = $this->_udm->getIntrotextId() ) return false;
        $this->_introtext = &new AMPSystem_Introtext( $this->_dbcon, $intro_id );
        return $this->_introtext;

    }

    function showIntroText() {
        if ( $this->_udm->admin ) return false;
        if ( !$introtext = &$this->getIntroText( )) return false;
        $display = $introtext->getDisplay( );
        return $display->execute( );
    }
}

?>
