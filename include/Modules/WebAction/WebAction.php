<?php

class WebAction extends AMPSystem_Data_Item {

    var $datatable = 'webactions';
    var $name_field = 'name';

    function WebAction( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function _afterSave( $data ){
        #$this->_saveIntroTexts( );
        #$this->_saveFormPlugin( );
    }

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

    function getBlurb( ){
        return $this->getData( 'blurb ');
    }

    function getURL( ){
        if ( !$this->id ) return AMP_CONTENT_URL_ACTION;
        return AMP_Url_AddVars( AMP_CONTENT_URL_ACTION, 'action='.$this->id );
    }

    function getTitle( ){
        return $this->getName( );
    }

    function getTargets( ){
        return $this->getData( 'target_id');
    }

    function getTargetMethod( ){
        return $this->getData( 'target_method' );
    }

}
?>
