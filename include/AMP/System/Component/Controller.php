<?php

/**
 * AMP_System_Component_Controller 
 * 
 * @package AMP_System
 * @version 3.5.8
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_System_Component_Controller {

    var $_page;
    var $_display;

    var $_actions_available = array();
    var $_action_args = array( );

    var $_model;
    var $_model_id;

    var $_request_vars;
    
    var $_action_committed;
    var $_action_requested;
    var $_action_default;

    var $_results;

    var $_observers = array( );

    function AMP_System_Component_Controller( ){
        //
    }

    function init( ){
        $this->_init_request( );
        $this->_init_display( );
    }

    function set_model( &$model, $auto_init=true ){
        $this->_model = &$model;
        if ( $auto_init ) $this->_init_model( );
    }

    function set_page( &$page ){
        $this->_page = &$page;
    }

    function _init_model( &$model ) {
        //create a set of available methods based on methods of the model object
        $this->_model = &$model;
        $methods_available = get_class_methods( $this->_model );
        foreach( $this->_actions_available as $method_name ){
            if ( substr( $methods_available, 0, 1) == '_') continue;
            $this->_actions_available[] = $method_name;
        }
    }

    function _init_display( ){
        require_once( 'AMP/Content/Manager.inc.php');
        $this->_display = AMPContent_Manager::instance( );
    }

    function _init_request( ) {
        $this->_request_vars = array_merge( $_POST, AMP_URL_Read( ));
        //pull useful info from request values
    }

    function execute( $output = true ){
        foreach ( $this->get_actions( ) as $action ){
            $this->_results[$action] = $this->commit( $this->_model, $action, $this->get_arguments( $action ) );
            $this->_action_committed = $action;
        }
        if ( !$output ) return;
        $template = &new AMPSystemPage_Display( );
    }

    function commit( &$target, $action, $args = null ){
        $local_method = 'commit_' . $action;
        if ( method_exists( $local_method )) return $this->$local_method( $args );
        if ( !method_exists( $target, $action )) {
            trigger_error( sprintf( AMP_ERROR_METHOD_NOT_SUPPORTED, get_class( $target ), $action , get_class( $this )));
            return false;
        }
        return call_user_func_array( array( $target, $action ), $args ) ;
    }

    function commit_default( ){
        unset( $this->_action_committed );
        unset( $this->_action_requested );
        return $this->execute( );
    }


    function get_actions( ){
        $action = $this->get_action( ) ;
        if ( is_array( $action ) ) return $action;
        return array( $action );
    }

    function get_action( ){
        if ( isset( $this->_action_committed)) return $this->_action_committed;
        if ( isset( $this->_action_requested)) return $this->_action_requested;
        return $this->_action_default;
    }
        
    function allow( $action ){
        if ( !isset( $this->_model->protected_actions[$action] ) ) return true;
        return AMP_Authorized( $this->_model->protected_actions[$action]);
    };

    function add_action( $action_name, $action_args = null ){
        $this->_actions[] = $action_name;
        if ( isset( $action_args )) $this->_action_args[$action] = $action_args;
    }

    function notify( $action ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action );
        }
    }

    function addObserver( &$observer, $observer_key = null ){
        if ( isset( $observer_key )){
            $this->_observers[$observer_key] = &$observer;
            return;
        }
        $this->_observers[] = &$observer;
    }
}

class AMP_System_Component_Controller_Crud extends AMP_System_Component_Controller {

    var $_map;
    var $_action_default = 'add';

    function AMP_System_Component_Controller_Crud( ){
        $this->init( );
    }

    function set_map( &$map ){
        $this->_map = &$map;
        $this->_init_map( );
    }

    function _init_map( ) {
        $this->_display->addDisplay( $this->make_banner( ), 'banner');
        //set methods based on map values
        if ( $form =  &$map->getComponent( 'form'))   $this->_init_form ( $form ) ;
        if ( $model = &$map->getComponent( 'source')) $this->_init_model( $model) ;
        
    }

    function _init_form( &$form, $read_request = true ){
        $this->_form = &$form;
        $this->notify( 'initForm' );

        $this->_form->Build( );
        if ( $read_request ) $this->_init_form_request( );
        
    }

    function _init_search( &$search, &$display ){
        $this->_search = &$search;
        $this->_display->addDisplay( $search );
        $search->Build( true );

        if ( !$search->submitted( ) ) return $search->applyDefaults( );
        $display->applySearch( $search->getSearchValues( )) ;
        $this->_display->make_banner( 'search');
    }

    function _init_form_request( ){
        $request_id = $this->_form->getIdValue( );
        $action = $this->_form->submitted( );

        if ( !$request_id ) {
            $this->_form->initNoId( );
        } else {
            $this->_model_id = $request_id;
        }

        if ( $request_id && !$action ) $action  = 'edit';
        if ( $action ) $this->_action_requested['form'] = $action;

    }

    function commit_add( ){
        $this->_form->applyDefaults( );
        $this->_display->addDisplay( $this->_form, 'form' );
    }

    function commit_cancel( ){
        $this->display_default( );
    }

    function commit_edit( ) {
        if ( !$this->_model->readData( $this->_model_id )) return $this->commit_default( );
        $this->_form->setValues( $this->_model->getData( ));
        $this->_display->addDisplay( $this->_form, 'form' );
    }

    function commit_save( $copy_mode = false ){
        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->addDisplay( $this->_form, 'form' );
            return false;
        }
        $this->_model->setData( $this->get_form_data( $copy_mode ));

        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->addDisplay( $this->_form );
            return false;
        }

        $this->_model_id = $this->_model->id;

        $success_message = $copy_mode ? AMP_TEXT_DATA_COPY_SUCCESS : AMP_TEXT_DATA_SAVE_SUCCESS;
        $this->message( sprintf( $success_message, $this->_model->getName( )));

        $this->_form->postSave( $this->_model->getData() );
        $this->display_default( );
        return true;
    }
        
    function display_default() {
        // if no list exists, return to the blank input form
        if ( !( $display = &$this->_map->getComponent( 'list' ))) {
           $display = &$this->_map->getComponent( 'form' );
           $this->_init_form( $display, false );
        } else {
            if ( $search = $this->_map->getComponent( 'search' )) $this->_init_search( $search, $display );
            
        }

        //add the list / blank form to the display manager
        $this->_display->addDisplay( $display );
        return true;

    }

    function commit_delete( ){
        if ( !$this->_model_id ) return false;
        $name = $this->_form->getItemName( );
        if ( !$name ) $name = 'Item';
        if ( !$this->_model->deleteData( $this->_model_id )){
            $this->error( $this->_model->getErrors( ));
            $this->_display->addDisplay( $this->_form );
            return false;
        }
        $this->message( sprintf( AMP_TEXT_DATA_DELETE_SUCCESS, $name ));
        $this->display_default( ) ;
        return true;
    }

    function commit_copy( ){
        if ( is_array( $this->_model->id_field )) {
            $this->error( AMP_TEXT_ERROR_DATA_COPY_FAILURE_MULTIPLE_IDS);
            return false;
        }
        return $this->commit_save( true );

    }

    function message( $message ){
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( $error_set ) ;
        $this->_display->addDisplay( $flash, 'flash' );

    }
    function error( $error_item ){
        $error_set = ( is_array( $error_item )) ? $error_item : array(  $error_item );
        $flash = &AMP_System_Flash::instance( );
        $flash->add_error( $error_set ) ;
        $this->_display->addDisplay( $flash, 'flash' );
    }

    function get_form_data( $copy_mode = false ) {
        $copy_values = $this->_form->getValues( );
        if ( $copy_mode ) {
            unset( $copy_values[ $this->_model->id_field ]);
        }
        return $copy_values;
    }

    function make_banner( $action = null) {
        $text = ucfirst( isset( $action ) ? $action :  join( "", $this->get_actions( )));
        $heading = $this->_map->getHeading( );
        if ( $text == 'List' ) $heading = AMP_Pluralize( $heading );
        $renderer =&new AMPDisplay_HTML( );
        $buffer->add( $renderer->inDiv( $text." ".$heading, array( 'class' => 'banner')));
        return $buffer;
    }

}

?>
