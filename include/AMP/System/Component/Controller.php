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

require_once( 'AMP/System/Base.php');
require_once( 'AMP/System/Flash.php');

class AMP_System_Component_Controller {

    var $_page;

    var $_display;
    var $_display_class = 'AMP_System_Page_Display';
    var $_display_custom = array( );

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
        require_once( 'AMP/System/Page/Display.php');
        if ( isset( $this->_display_custom[ $this->_action_requested ] )){
            $this->_display_class = $this->_display_custom[ $this->_action_requested ];
        }
        $this->_display = &call_user_func_array( array( $this->_display_class, 'instance'), $this );

        $flash = &AMP_System_Flash::instance( );
        $this->_display->add( $flash, 'flash' );
    }

    function _init_request( ) {
        $this->_request_vars = array_merge( $_POST, AMP_URL_Read( ));
        //pull useful info from request values
        if ( isset( $this->_request_vars['action'] ) && $this->_request_vars['action']){
            if ( $this->allow( $this->_request_vars['action'])){
                $this->_action_requested = $this->_request_vars['action'];
            }
        }
    }

    function execute( $output = true ){
        foreach ( $this->get_actions( ) as $action ){
            if( $this->_results[$action] = $this->commit( $this->_model, $action, $this->get_arguments( $action ) )) {
                $this->_action_committed = $action;
                continue;
            }
            $this->commit_default( );
        }
        if ( !$output ) return;
        return $this->_display->execute( );
    }

    function commit( &$target, $action, $args = null ){
        $local_method = 'commit_' . $action;
        if ( method_exists( $this, $local_method )) return $this->$local_method( $args );
        if ( !method_exists( $target, $action )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED, get_class( $target ), $action , get_class( $this )));
            return false;
        }
        return call_user_func_array( array( $target, $action ), $args ) ;
    }

    function commit_default( ){
        $this->clear_actions( );
        return $this->execute( false );
    }

    function clear_actions( ){
        unset( $this->_action_committed );
        unset( $this->_action_requested );
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
        
    function get_arguments( $action = null ) {
        if ( !isset( $action )) $action = $this->get_action( );
        if ( !isset( $this->_action_args[$action] )) return null;
        return array_combine_key( $this->_action_args[ $action ], $this->_request_vars );
    }

    function allow( $action ){
        if ( !isset( $this->_model->protected_actions[$action] ) ) return true;
        return AMP_Authorized( $this->_model->protected_actions[$action]);
    }

    function add_action( $action_name, $action_args = null ){
        $this->_actions[] = $action_name;
        if ( isset( $action_args )) $this->_action_args[$action] = $action_args;
    }

    function notify( $action ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action );
        }
    }

    function add_observer( &$observer, $observer_key = null ){
        if ( isset( $observer_key )){
            $this->_observers[$observer_key] = &$observer;
            return;
        }
        $this->_observers[] = &$observer;
    }

    function assert_action( $action ){
        return $this->assert_var( 'action', $action );
    }

    function assert_var( $varname, $value = null ) {
        if ( !isset( $this->_request_vars[ $varname ])) return false;
        if ( !isset( $value )) return $this->_request_vars[$varname ];
        return ( $this->_request_vars[ $varname ] == $value );
    }

    function message( $message ) {
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( $message ) ;
        $this->_display->add( $flash, 'flash' );
    }

    function error( $error_item ){
        $error_set = ( is_array( $error_item )) ? $error_item : array(  $error_item );
        $flash = &AMP_System_Flash::instance( );
        foreach( $error_set as $error_message ){
            $flash->add_error( $error_message ) ;
        }
        $this->_display->add( $flash, 'flash' );
    }

}

class AMP_System_Component_Controller_Map extends AMP_System_Component_Controller {
    var $_map;
    var $_action_default = 'list';

    function AMP_System_Component_Controller_Map ( ){
        $this->init( );
    }

    function set_map( &$map ){
        $this->_map = &$map;
        $this->_init_map( );
    }

    function _init_map( ) {
        $this->add_observer( $this->_map );

        //set methods based on map values
        if ( $form  = &$this->_map->getComponent( 'form'))   $this->_init_form ( $form ) ;
        if ( $model = &$this->_map->getComponent( 'source')) $this->_init_model( $model) ;

        if ( !$this->allow( $this->get_action( ))) $this->clear_actions( );
        $this->set_banner( $this->get_action( ));
        $this->_display->add_nav( $this->_map->getNavName( ));
        
    }

    function set_banner( $action = null) {
        $text = ucfirst( isset( $action ) ? $action :  join( "", $this->get_actions( )));
        if ( $text == 'Cancel' ) $text = 'List';

        $heading = $this->_map->getHeading( );
        if ( $text == 'List' ) $heading = AMP_Pluralize( $heading );
        $renderer = &new AMPDisplay_HTML( );

        $buffer = &new AMP_Content_Buffer( );
        $buffer->add( $renderer->inDiv( $text." ".$heading, array( 'class' => 'banner')));
        $this->_display->add( $buffer , AMP_CONTENT_DISPLAY_KEY_INTRO );
    }

    function allow( $action ){
        if ( isset( $this->_map )) return $this->_map->isAllowed( $action );
        if ( !isset( $this->_model->protected_actions[$action] ) ) return true;
        return AMP_Authorized( $this->_model->protected_actions[$action]);
    }

    function display_default( ){
        $display = &$this->_map->get_action_display( $this->get_action( )); 
        if ( $display ) $this->_display->add( $display );
    }

    function commit_list( ){
        $this->display_default( );
        return true;
    }

}

class AMP_System_Component_Controller_Standard extends AMP_System_Component_Controller_Map {

    var $_action_default = 'add';
    var $_form;

    function AMP_System_Component_Controller_Standard( ){
        $this->init( );
    }

    function _init_form( &$form, $read_request = true ){
        $this->_form = &$form;
        $this->notify( 'initForm' );

        $this->_form->Build( );
        if ( $read_request ) $this->_init_form_request( );
        
    }

    function &get_form( ){
        return $this->_form;
    }

    function _init_search( &$search, &$display ){
        $this->_search = &$search;
        $this->_display->add( $search );
        $this->notify( 'initSearch' );

        $search->Build( true );

        if ( !$search->submitted( ) ) return $search->applyDefaults( );

        $display->applySearch( $search->getSearchValues( )) ;
        $this->set_banner( 'search');
    }

    function _init_form_request( ){
        $request_id = $this->_form->getIdValue( );
        if ( is_array( $request_id )) return false;

        $action = $this->_form->submitted( );

        if ( !$request_id ) {
            $this->_form->initNoId( );
        } else {
            $this->_model_id = $request_id;
        }

        if ( $request_id && !$action ) $action  = 'edit';
        
        if ( $action ) $this->_action_requested = $action;

    }

    function commit_add( ){
        $this->_form->applyDefaults( );
        $this->_display->add( $this->_form, 'form' );
        return true;
    }

    function commit_cancel( ){
        $this->display_default( );
        return true;
    }

    function commit_edit( ) {
        if ( !$this->_model->readData( $this->_model_id )) return $this->commit_default( );
        $this->_form->setValues( $this->_model->getData( ));
        $this->_display->add( $this->_form, 'form' );
        return true;
    }

    function commit_save( $copy_mode = false ){
        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
        }
        $this->_model->setData( $this->get_form_data( $copy_mode ));

        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
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
           $this->set_banner( 'add');
        } else {
            $display->setController( $this );
            $this->set_banner( 'list');
            $this->notify( 'initList' );
            if ( $search = $this->_map->getComponent( 'search' )) $this->_init_search( $search, $display );
        }

        //add the list / blank form to the display manager
        $this->_display->add( $display );
        return true;

    }

    function commit_delete( ){
        if ( !$this->_model_id ) return $this->commit_default( );
        $name = $this->_form->getItemName( );
        if ( !$name ) $name = 'Item';
        if ( !$this->_model->deleteData( $this->_model_id )){
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
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

    function get_form_data( $copy_mode = false ) {
        $copy_values = $this->_form->getValues( );
        if ( $copy_mode ) {
            unset( $copy_values[ $this->_model->id_field ]);
        }
        return $copy_values;
    }

}

?>
