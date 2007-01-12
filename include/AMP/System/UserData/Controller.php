<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_UserData_Controller extends AMP_System_Component_Controller_Standard {

    var $_saved_tool_id;
    var $_saved_response_page_id;
    var $_saved_input_page_id;

    var $_form_import;

    function AMP_System_UserData_Controller( ){
        $this->init( );
    }

    function commit_edit( ){
        return false;
    }

    function commit_save( ){
        $result = parent::commit_save( );
        if ( !$result ) return false;
        $this->_saveModuleData( );
        $this->_saveInputPageData( );
        $this->_saveResponsePageData( );
        $this->_model->mergeData( array( 
            'modidinput' => $this->_saved_input_page_id,
            'modidresponse' => $this->_saved_response_page_id )
        );
        AMP_cacheFlush( );
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

    function commit_upload( ) {
        if ( !isset( $this->_form_import )) {
            $this->_form_import = &$this->_map->getComponent( 'import');

        }
        if ( isset( $_REQUEST['modin']) && $_REQUEST['modin']) {
            $this->_form_import->setValues( array( 'modin' => $_REQUEST['modin']));
        }
        $this->_form_import->initNoId( );
        $this->_form_import->Build( );

        $this->_display->add( $this->_form_import );
        return true;
    }

    function commit_import( ){
        if ( !isset( $this->_form_import )) {
            $this->_form_import = &$this->_map->getComponent( 'import');
        }

        if ( !$this->_form_import->submitted( ) || !$this->_form_import->validate( )) {
            $this->_form_import->initNoId( );
            $this->_form_import->applyDefaults( );
            $this->_form_import->Build( );
            $this->_display->add( $this->_form_import );
            return true;
        }

        $this->_form_import->Build( );
        $target_form_id = $this->_form_import->getFormId( );
        $import_map = $this->_form_import->getMap( );

        $import_file = $this->_form_import->getSource( );
        $import_fields = $this->_form_import->getSourceFields( );
        if ( !$import_file )

        $count = 0;
        // doing this with the UserData structure would be more correct
        // but is too slow to be useful at this time
        //require_once( 'AMP/UserData/Input.inc.php');
        //$user = &new UserDataInput( AMP_Registry::getDbcon( ), $target_form_id, $admin = true );
        //$user->doPlugin( 'QuickForm', 'Build');
        require_once( 'AMP/System/User/Profile/Profile.php');
        $user = &new AMP_System_User_Profile( AMP_Registry::getDbcon( ));

        foreach( $import_file as $row_id => $row_data ) {
            $saveable_data = array( 'modin' => $target_form_id );
            set_time_limit( 10 );
            foreach( $row_data as $key => $value ) {
                $source_key = $import_fields[ $key ];
                if ( !isset( $import_map[ $source_key ])) continue;
                $saveable_data[ $import_map[ $source_key ]] = $value;

            }
            /*
            if ( isset( $saveable_data['id'])) {
                $user->uid = $saveable_data['id'];
            }
            */
            $user->setData( $saveable_data );
            $count += $user->save( );
            //$count += $user->doAction( 'Save' );
            //$user->clearData( );
            trigger_error( 'imported ' . $count );
        }

        $import_action_text = AMP_past_participle( AMP_TEXT_IMPORT );
        if ( $count ){
            $this->message( sprintf( AMP_TEXT_LIST_ACTION_SUCCESS, $import_action_text, $count )) ;
        } else {
            $this->message( sprintf( AMP_TEXT_LIST_ACTION_FAIL, $import_action_text ));
        }
    }

    function _init_form_request( &$form ) {
        $import_form = &$this->_map->getComponent( 'import');
        if ( $import_form ) {
            $action = $import_form->submitted( );
            $this->_form_import = &$import_form;
        }
        if ( !$import_form || !$action ) {
            return parent::_init_form_request( $form );
        }
        $this->request( $action );

    }

}

?>
