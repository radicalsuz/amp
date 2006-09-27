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
    var $_actions_cacheable = array( );
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
        $this->__construct( ) ;
    }

    function __construct( ){
        $this->init( ) ;
    }

    function init( ){
        $this->_init_request( );
        $this->_init_display( );
    }

    function set_model( &$model, $auto_init=true ){
        $this->_model = &$model;
        if ( $auto_init ) $this->_init_model( );
    }

    function &get_model( ){
        if( !isset( $this->_model )) return false;
        return $this->_model;
    }

    function get_model_id( ){
        if( !isset( $this->_model_id )) return false;
        return $this->_model_id;
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
        $this->_display = call_user_func_array( array( $this->_display_class, 'instance'), $this );

        $flash = &AMP_System_Flash::instance( );
        $this->_display->add( $flash, 'flash' );
    }

    function &get_display( $key = null ){
        if ( !isset( $key )) return $this->_display;
        return $this->_display->retrieve( $key );
    }

    function _init_request( ) {
        $url_vars = AMP_URL_Read( );
        $this->_request_vars = $_POST;
        if ( $url_vars ) {
            $this->_request_vars = array_merge( $_POST, $url_vars );
        }

        //pull useful info from request values
        if ( isset( $this->_request_vars['action'] ) && $this->_request_vars['action']){
            $this->request ( $this->_request_vars['action'] );
        }
    }

    function execute( $output = true ){
        $data = $this->get_actions( );
        foreach ( $this->get_actions( ) as $action ){
            //try running the action cached
            if( $this->_commit_cached( $action )) {
                continue;
            }

            //run the action for real
            if( $this->_results[$action] = $this->commit( $this->_model, $action, $this->get_arguments( $action ) )) {
                $this->_action_committed = $action;
                continue;
            }
            $this->_commit_default( );
        }
        if ( !$output ) return;

        if ( defined( 'AMP_CONTENT_PAGE_REDIRECT' )) return;

        $output = $this->_display->execute( );
        AMP_cache_close( );

        return $output;
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

    function _commit_fail( ){
        return $this->_commit_default( );
    }

    function _commit_default( ){
        $this->clear_actions( );
        return $this->execute( false );
    }

    function clear_actions( ){
        unset( $this->_action_committed );
        unset( $this->_action_requested );
    }

    function request( $action ){
        if ( $this->allow( $action )){
            $this->_action_requested = $action;
        } else {
            $this->error( sprintf( AMP_TEXT_ERROR_ACTION_NOT_ALLOWED, $action ));
        }
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

    function notify( $action, $passthru_values = null ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action, $passthru_values );
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

    function message( $message, $key = null, $edit_url = false ) {
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( $message, $key, $edit_url ) ;
    }

    function error( $error_item, $key = null ){
        $error_set = ( is_array( $error_item )) ? $error_item : array(  $error_item );
        $flash = &AMP_System_Flash::instance( );
        foreach( $error_set as $error_message ){
            $flash->add_error( $error_message, $key ) ;
        }
    }

    function set_banner( $action = null, $heading ) {
        $text = ucfirst( isset( $action ) ? $action :  join( "", $this->get_actions( )));

        $plural_headings = array( AMP_TEXT_LIST, AMP_TEXT_SEARCH, AMP_TEXT_VIEW );
        if ( array_search( $text , $plural_headings ) !== FALSE ) $heading = AMP_pluralize( $heading );
        $this->add_component_header( $text, ucwords( $heading ), 'banner', AMP_CONTENT_DISPLAY_KEY_INTRO );

        $header_text = $text . ' ' . $heading;
        $header = &AMP_getHeader( );
        $header->setPageAction( $header_text );
    }

    function add_component_header( $action_text, $heading, $css_class = 'system_heading', $display_key = null ){

        $header_text = $action_text . ' ' . $heading;
        $renderer = &new AMPDisplay_HTML( );

        $buffer = &new AMP_Content_Buffer( );
        $buffer->add( $renderer->inDiv( $header_text, array( 'class' => $css_class )));
        $this->_display->add( $buffer , $display_key );
    }

    function redirect( $url ){
        ampredirect( $url );
    }

    function _commit_cached( $action ){
        if ( array_search( $action, $this->_actions_cacheable ) === FALSE ) return false;

        $cache_key = sprintf( AMP_CACHE_TOKEN_ACTION_OUTPUT, $action ). $_SERVER['REQUEST_URI'];
        $user_id = defined( 'AMP_SYSTEM_USER_ID' ) ? AMP_SYSTEM_USER_ID : null;
        $cached_output = AMP_cache_get( $cache_key, $user_id );
        if ( !$cached_output ) return false;

        require_once( 'AMP/Content/Buffer.php' );
        $buffer = & new AMP_Content_Buffer;
        $buffer->add( $cached_output );

        $this->_display->add( $buffer, $action );

        $this->_results[$action] = true;
        $this->_action_committed = $action;
        return true;

    }

    function _request_cache( $display_key, $action ) {
        if ( array_search( $action, $this->_actions_cacheable ) === FALSE ) return false;

        $cache = &AMP_get_cache( );
        if ( !$cache ) return false;
        $cache_key = sprintf( AMP_CACHE_TOKEN_ACTION_OUTPUT, $action ). $_SERVER['REQUEST_URI'];

        if ( defined( 'AMP_SYSTEM_USER_ID')) {
            $cache_key = $cache->identify( $cache_key, AMP_SYSTEM_USER_ID );
        }
        
        $this->_display->cache( $display_key, $cache_key );
        return true;
    }

    function _unique_action_key( ) {
        return sha1( get_class( $this->_model ) . $this->_model->id . $this->get_action( ));
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
        $this->_action_default = $this->_map->getDefaultDisplay( );

        //set methods based on map values

        if ( $form  = &$this->_map->getComponent( 'form' )){
            $this->_init_form_request ( $form ) ;
            $this->_form = &$form;
        }
        if ( $model = &$this->_map->getComponent( 'source')) $this->_init_model( $model) ;

        if ( !$this->allow( $this->get_action( ))) $this->clear_actions( );
        $this->set_banner( $this->get_action( ));
        if ( method_exists( $this->_display, 'add_nav')){
            $this->_display->add_nav( $this->_map->getNavName( ));
        }
    }

    function _init_form( $read_request = true ){
        
        // init_running is a flag to keep an infinite loop from forming;
        // in case an observer requests the form
        static $init_running = false;
        if ( $this->_form->isBuilt || $init_running ) return false;

        $init_running = true;
        $this->notify( 'initForm' );
        $init_running = false;

        $this->_form->Build( );
        
    }

    function _init_form_request( &$form ){
        $request_id = $form->getIdValue( );
        if ( is_array( $request_id )) return false;

        $action = $form->submitted( );

        if ( !$request_id ) {
            $form->initNoId( );
        } else {
            $this->_model_id = $request_id;
        }

        if ( $request_id && !$action && !isset( $this->_request_vars['action'] )) $action  = 'edit';
        
        if ( $action ) $this->request( $action );

    }

    function &get_form( ){
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );
        return $this->_form;
    }

    function get_form_data( $copy_mode = false ) {
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        $copy_values = $this->_form->getValues( );
        if ( $copy_mode ) {
            unset( $copy_values[ $this->_model->id_field ] );
        }
        return $copy_values;
    }

    function set_banner( $action = null  ){
        $text = ucfirst( isset( $action ) ? $action :  join( "", $this->get_actions( )));
        if ( $text == 'Cancel' ) $text = AMP_TEXT_LIST;

        $heading = $this->_map->getHeading( );
        return parent::set_banner( $text, $heading );
    }

    function allow( $action ){
        if ( isset( $this->_map )) return $this->_map->isAllowed( $action );
        if ( !isset( $this->_model )) return true;
        if ( !isset( $this->_model->protected_actions[$action] ) ) return true;
        return AMP_Authorized( $this->_model->protected_actions[$action]);
    }

    function display_default( ){
        $display = &$this->_map->get_action_display( $this->get_action( )); 
        $this->set_banner( $this->_action_default );
        if ( $display ) $this->_display->add( $display , 'default');
    }

    function commit_list( ) {
        //$this->display_default( );
        $display = &$this->_map->getComponent( 'list' );
        $display->setController( $this );

        $this->set_banner( 'list' );
        $this->notify( 'initList' );

        $this->_display->add( $display, 'default' );
        return true;
    }

}

class AMP_System_Component_Controller_Input extends AMP_System_Component_Controller_Map {
    var $_action_default = 'add';
    var $_form;

    function AMP_System_Component_Controller_Input( ){
        $this->init( );
    }

    function display_default() {
        // if no list exists, return to the blank input form
        if ( !( $display = &$this->_map->getComponent( 'list' ))) {
           $display = &$this->_map->getComponent( 'form' );
           $this->_form = &$display;
           $this->_init_form( false );
           $this->set_banner( 'add');

        } else {
            $display->setController( $this );
            $this->set_banner( 'list');
            $this->notify( 'initList' );
            #if ( $search = $this->_map->getComponent( 'search' )) $this->_init_search( $search, $display );
        }

        //add the list / blank form to the display manager
        $this->_display->add( $display, 'default' );
        return true;

    }

    function commit_new( ){
        return $this->commit_add( );
    }

    function commit_add( ){
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        $this->_form->applyDefaults( );
        $this->_display->add( $this->_form, 'form' );
        //$this->_request_cache( 'form', 'add' );

        return true;
    }


    function commit_cancel( ){
        $this->display_default( );
        return true;
    }

    function commit_save( ){
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
        }

        $this->notify( 'beforeUpdate' );
        if ( !isset( $this->_model->id )) $this->_model->setDefaults( );
        $this->_model->mergeData( $this->get_form_data( ));

        $this->notify( 'beforeSave' );
        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
            return false;
        }

        $this->_model_id = $this->_model->id;
        $this->notify( 'save' );

        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $this->_model->getName( )), 
                        $this->_unique_action_key( ), 
                        $this->_model->get_url_edit( ) );

        $this->_form->postSave( $this->_model->getData() );
        $this->display_default( );
        return true;
    }

}

class AMP_System_Component_Controller_Standard extends AMP_System_Component_Controller_Input {

    function AMP_System_Component_Controller_Standard( ){
        $this->init( );
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

    function commit_edit( ) {
        if ( !$this->_model->readData( $this->_model_id )) return $this->_commit_fail( );

        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        $this->_form->setValues( $this->_model->getData( ));
		$this->_display->add( $this->_form, 'form' ); 
		return true;
    }

    function commit_delete( ){
        if ( !$this->_model_id ) return $this->_commit_fail( );
        $name = $this->_form->getItemName( );
        $this->notify( 'beforeDelete' );
        if ( !$name ) $name = AMP_TEXT_ITEM_NAME;
        if ( !$this->_model->deleteData( $this->_model_id )){
            if ( method_exists( $this->_model, 'getErrors')) $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
            return false;
        }
        $this->notify( 'delete' );
        $this->message( sprintf( AMP_TEXT_DATA_DELETE_SUCCESS, $name ));
        $this->display_default( ) ;
        return true;
    }

    function commit_copy( ){
        if ( is_array( $this->_model->id_field )) {
            $this->error( AMP_TEXT_ERROR_DATA_COPY_FAILURE_MULTIPLE_IDS);
            return false;
        }

        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
        }

        $form_data = $this->get_form_data( );
        unset( $form_data[$this->_model->id_field]);
        $this->_model->setDefaults( );
        $this->_model->mergeData( $form_data );

        $this->notify( 'beforeCopy' );
        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
            return false;
        }

        $this->_model_id = $this->_model->id;
        $this->notify( 'copy' );

        $this->message( sprintf( AMP_TEXT_COPY_SUCCESS, $this->_model->getName( )), 
                        $this->_unique_action_key( ),
                        $this->_model->get_url_edit( ));

        $this->_form->postSave( $this->_model->getData() );
        $this->display_default( );
        return true;

    }

}


?>
