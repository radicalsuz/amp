<?php

class WebAction extends AMPSystem_Data_Item {

    var $datatable = 'webactions';
    var $name_field = 'name';
    var $_field_status = 'status';

    function WebAction( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }
    /*
    function _afterSave( $data ){
        #$this->_saveIntroTexts( );
        #$this->_saveFormPlugin( );
    }
    */

    function &getMessageForm( ){
        require_once( 'Modules/WebAction/Message/Form.inc.php');
        $form = &new WebActionMessage_Form( );
        $form->setTargets(  $this->getTargets( ),  $this->getTargetMethod( ));
        return $form;
    }

    function getFormId( ){
        return $this->getData( 'modin' );
    }

    function &getDisplay( ){
        require_once( 'AMP/UserData/Controller.php' );
        require_once( 'AMP/Content/Display/HTML.inc.php' );

        $udm = &new UserDataInput( $this->dbcon, $this->getFormId( ));
        $action_plugin = &$udm->registerPlugin( 'AMPAction', 'Start' );
        $action_plugin->setOptions( array( 'action_id' => $this->id ));

        $controller = &new UserData_Controller( $udm );
        $controller->execute( );
        return $controller->getDisplay( );
    }

    function &getListDisplay() {
        require_once( 'Modules/WebAction/Set.inc.php');
        require_once( 'Modules/WebAction/SetDisplay.inc.php');
        $list = &new WebActionSet( $this->dbcon );
        $list->addCriteriaLive( );
        $list_display = &new WebActionSet_Display( $list );
        return $list_display;
    }

    function getBlurb( ) {
        return $this->getData( 'blurb ');
    }

    function getURL( )   {
        if ( !$this->id ) return AMP_CONTENT_URL_ACTION;
        return AMP_url_add_vars( AMP_CONTENT_URL_ACTION, 'action='.$this->id );
    }

    function getTitle( ) {
        return $this->getName( );
    }

    function getTargets( ) {
        $result = $this->getData( 'target_id') ;
        if ( !$result ) return false;
        return split( ',', $result );
    }

    function getTargetMethod( ) {
        return $this->getData( 'target_method' );
    }

    function getExpirationDate( ) {
        $result = $this->getData( 'enddate');
        if ( $result == AMP_NULL_DATETIME_VALUE ) return false;
        return $result;
    }

    function makeCriteriaLive( ) {
        return ( 'status=1 and ( isnull( enddate ) or enddate = "0000-00-00" or enddate >= CURRENT_DATE )') ;
    }

    function isLive( ){
        if ( !( $result = $this->getData( 'status'))) return false;
        return !$this->isExpired( );
    }

    function isExpired( ){
        if ( !( $expire_date = $this->getExpirationDate( ))) return false;
        return ( time( ) >= strtotime( $expire_date )); 
    }

    function getStatus( ){
        if ( $this->isExpired( )) return AMP_TEXT_CONTENT_STATUS_EXPIRED;
        return $this->isLive( ) ? AMP_TEXT_CONTENT_STATUS_LIVE : AMP_TEXT_CONTENT_STATUS_DRAFT;
    }

}
?>
