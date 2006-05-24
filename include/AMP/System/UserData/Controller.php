<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_UserData_Controller extends AMP_System_Component_Controller_Standard {

    var $_saved_tool_id;
    var $_saved_response_page_id;
    var $_saved_input_page_id;

    function AMP_System_UserData_Controller( ){
        $this->init( );
    }

    function commit_edit( ){
        return false;
    }

    function commit_save( ){
        $result = PARENT::commit_save( );
        if ( !$result ) return false;
        $this->_saveModuleData( );
        $this->_saveInputPageData( );
        $this->_saveResponsePageData( );
        $this->_model->mergeData( array( 
            'modidinput' => $this->_saved_input_page_id,
            'modidresponse' => $this->_saved_response_page_id )
        );
        return $this->_model->save( );

    }

    function _saveInputPageData( ){
        require_once( 'AMP/System/IntroText.inc.php');
        $intro = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ) );
        $linkpage = AMP_URL_AddVars( AMP_CONTENT_URL_FORM, array( 'modin='.$this->_model_id ));
        $intro_data = array( 
            'title' => $this->_request_vars['input_page_title'],
            'body'  => $this->_request_vars['input_page_text'],
            'name'  => $this->_model->getName( ) . ' Input',
            'modid' => $this->_saved_tool_id,
            'searchtype' => $linkpage 
        );
        $intro->setData( $intro_data );
        $result = $intro->save();
        $this->_saved_input_page_id = $intro->id;
        return $result;

    }

    function _saveResponsePageData( ){
        require_once( 'AMP/System/IntroText.inc.php');
        $response = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ) );
        $response_data = array( 
            'title' => $this->_request_vars['response_page_title'],
            'body'  => $this->_request_vars['response_page_text'],
            'name'  => $this->_model->getName( ) . ' Thank You',
            'modid' => $this->_saved_tool_id
        );
        $response->setData( $response_data );

        $result = $response->save();
        $this->_saved_response_page_id = $response->id;
        return $result;
    }

    function _saveModuleData( ){
        $system_data_url = AMP_URL_AddVars( AMP_SYSTEM_URL_FORM_DATA, array( 'modin' => $this->_model_id ));
        require_once( 'AMP/System/Tool.inc.php');
        $module = &new AMPSystem_Tool( AMP_Registry::getDbcon( ) );
        $module_data = array( 
            'name' => $this->_model->getName( ),
            'userdatamod' => 1,
            'userdatamodid' => $this->_model_id,
            'file' => $system_data_url,
            'perid' => AMP_PERMISSION_FORM_DATA_EDIT,
            'publish' => 1,
            'module_type' => 1
            );
        $module->setData( $module_data );
        $result = $module->save( );
        $this->_saved_tool_id = $module->id;
        return $result;

    }

}

?>
