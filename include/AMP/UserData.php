<?php

/*****
 *
 * AMP/UserData.php
 *
 * AMP User Data Module base class
 *
 * Copyright (c) 2004 Radical Designs
 * Author: Blaine Cook
 * Based on code from modinput v2, written by David Taylor
 *
 * This code licensed under the AGPL.
 *
 * Steal it if you'd be apt to steal stuff 'coz yer poor or
 * just don't like the whole capitalism thing. But send us
 * a copy of any code-graffiti (or fine art or poetry or whatever), ok?
 *
 * Get more info and updated versions at:
 *   http://www.radicaldesigns.org/
 *
 *****/

class UserData {

    #######################
    ### Class Variables ###
    #######################

    var $dbcon;

    // Store raw (database-backed) module definition.
    var $_module_def;
    var $instance;

    // Templating information for AMP
    var $modTemplateID;

    // Computed Fields ( including plugin fields )
    var $name;
    var $fields;

    // User ID
    var $uid;
    var $pass;
    var $authorized;
    var $values;

    // Placeholder for HTML_QuickForm. Not required for display-only.
    var $form;

    // Configuration settings, legacy only.
    var $redirect;
    var $mailto;
    var $subject;

    // flag to enable / disable display of form to user.
    var $showForm;

    // Flag to indicate administrator access.
    var $admin;

    // Arrays for holding result information.
    var $results;
    var $errors;

    ##################################
    ### Core Constructor Functions ###
    ##################################

    /*****
     *
     * dataModule constructor
     *
     * $datamod = new dataModule( ADODB $dbcon, int instance )
     *
     * Creates and initializes a new user data module.
     *
     * UDMs are defined in the userdata_fields table, and plugins are loaded from
     * userdata_plugins.
     *
     *****/

    function UserData ( $dbcon, $instance ) {

        // Setup database connection. Required.
        if (!isset($dbcon)) return false;
        $this->dbcon =& $dbcon;
        $dbcon->SetFetchMode( ADODB_FETCH_ASSOC );

        // Get module instance. Required.
        $this->instance = preg_replace( "/(\d+)/", "\$1", $instance );
        if ( $this->instance == '' ) trigger_error( "No module specified!" );

        // Initialise against database
        $this->init();

    }

    /*****
     *
     * dataModule Initialiser
     *
     * Pulls in all relevant information about module, includes
     * calls to appropriate plugins, etc.
     *
     *****/

    function init () {

        // Fetch database definition of module.
        $sql = "SELECT * FROM userdata_fields WHERE id='" . $this->instance . "'";

        $rs = $this->dbcon->CacheExecute( $sql )
            or trigger_error( "Error retreiving module information from database: " . $dbcon->ErrorMsg() );

        $md = $this->_module_def = $rs->FetchRow();

        // Define module class
        $this->class = ( isset($md[ 'class' ]) ) ? $md['class'] : 1;
        $this->modTemplateID = $md['modidinput'] or 10;

        // Define module variables.
        $this->name = $md[ 'name' ];

        // Define redirect config. Legacy, will be replaced by redirect plugin.
        $this->redirect = $md[ 'redirect' ];

        // Define email config. Legacy, will be replaced by email plugins.
        if ( $md[ 'useemail' ] == 1 ) {
            $this->mailto  = $md[ 'mailto' ];
            $this->subject = $md[ 'subject' ];
        }

        // Register the fields from the database.
        $this->_register_fields();

        // Register the mailing lists.
        $this->_register_lists();

        // Register methods for data access / manipulation.
        $this->_register_plugins();

    }

    ###############################
    ### Public Output Functions ###
    ###############################

    /*****
     *
     * string output ( [ string format [, array options ] ] )
     *
     * Return a textual representation of the user data module,
     * in the format specified by $format.
     *
     * If $format is not provided, the output defaults to an
     * XHTML form.
     *
     * Output is plugin-based; see the plugin documentation for
     * more information.
     *
     *****/

    function output ( $format = 'html', $options = null ) {

            return $this->doPlugin( 'Output', $format, $options );

    }

    ##################################
    ### Public Data Access Methods ###
    ##################################

    /*****
     *
     * getUser ( [ int userid ] )
     *
     * fetches user data for a given userid. If userid is not present,
     * the object should be populated with sufficient data to allow
     * plugins to perform a Query-By-Example.
     *
     * See specific plugin documentation for more information.
     *
     *****/

    function getUser ( $userid = null ) {

        return $this->doAction( 'read', array( '_userid' => $userid, 'admin' => $this->admin ) );

    }

    /*****
     *
     * saveUser ()
     *
     * saves the data stored in the object. Requires HTML_QuickForm object
     * to validate submitted values before saving. This object is created
     * if not already present.
     *
     * Plugins should use the $form->process() function to call internal
     * methods, saving user-submitted data only once it has been laundered
     * with the HTML_QuickForm object.
     *
     * See specific plugin documentation for more information.
     *
     *****/

    function saveUser () {

        $options = array( 'admin' => $this->admin );

        if (!isset( $this->form )) {

            $this->doPlugin( 'QuickForm', 'build', $options );

        }

        $this->modTemplateID = $this->_module_def['modidresponse'];

        return $this->doAction( 'save', $options );

    }

    /*****
     *
     * addResult()
     *
     *****/

    function addResult ( $type, $result ) {

        if ( !isset( $this->results ) ) {
            $this->results = array();
        }

        if ( !isset( $this->results[ $type ] ) ) {
            $this->results[ $type ] = array();
        }

        $this->results[ $type ][] = $result;

    }

	function getResults ( $type = null ) {

        $retarray = array();

        if ( isset( $type ) ) {
            $udmResults[ $type ] = $this->results[ $type ];
        } else {
            $udmResults = $this->results;
        }

        if ( is_array( $udmResults ) ) {
            foreach ( $udmResults as $type => $results ) {
                foreach ( $udmResults[ $type ] as $result ) {
                    $retarray[] = array( 'type' => $type,
                                         'result' => $result );
                }
            }
        }

        return $retarray;

    }

    /*****
     *
     * addError()
     *
     *****/

    function addError ( $type, $result ) {

        if ( !isset( $this->errors ) ) {
            $this->errors = array();
        }

        $this->errors[ $type ] = $result;

    }

    ################################
    ### Public Utility Functions ###
    ################################

    /*****
     *
     * authenticate ( mixed uid [, mixed passphrase ] )
     *
     * Authenticates a user; returns true if authentication
     * succeeds, false otherwise.
     *
     *****/

    function authenticate ( $uid, $pass = null ) {

        $options = array( 'uid'  => $uid,
                          'pass' => $pass );

        return $this->doAction( 'authenticate', $options );

    }

    /*****
     *
     * findDuplicates ()
     *
     * Find Duplicate records in the UserData database.
     *
     *****/

    function findDuplicates () {

        return $this->doAction( 'duplicate_check' );

    }

    /*****
     *
     * getStoredValues ( [ array fields ] )
     *
     * returns a fieldname => value array of the values in the udm instance.
     *
     *****/

    function getStoredValues ( $fields = array() ) {

        $retarray = array();
        foreach ( $this->fields as $name => $array ) {

            if ( isset( $fields ) ) {
                if ( array_search( $name, $fields ) ) continue;
            }

            if ( isset( $array[ 'value' ] ) ) {
                $retarray[ $name ] = $array['value'];
            }

        }

        return $retarray;
    }

    /*****
     *
     * getModuleDefaultValues ( [ array fields ] )
     *
     * returns a fieldname => value array of the values in the udm instance.
     *
     *****/

    function getModuleDefaultValues ( $fields = array() ) {

        $retarray = array();
        foreach ( $this->fields as $name => $array ) {

            if ( isset( $fields ) ) {
                if ( array_search( $name, $fields ) ) continue;
            }

            if ( isset( $array[ 'values' ] ) ) {
                $retarray[ $name ] = $array['values'];
            }

        }

        return $retarray;
    }


    /*****
     *
     * errorMessage( string message )
     *
     * Adds an error message to the stack for output.
     *
     *****/

    function errorMessage( $message ) {

        $this->errors[] = $message;

        return true;

    }

    #############################
    ### Public Plugin Methods ###
    #############################

    /*****
     *
     * doPlugin( str namespace, str action, array options )
     *
     * Executes a plugin, registering it if necessary.
     *
     *****/

    function doPlugin( $namespace, $action, $options = null ) {

        $func_a = array( 'udm', $namespace, $action );
        $func = join( '_', $func_a );

        if ( !function_exists( $func ) ) {
            $this->registerPlugin( $namespace, $action, $options );
        }

        if ( function_exists( $func ) ) {
            return $func( &$this, $options );
        } else {
            return false;
        }

    }

    /*****
     *
     * tryPlugin( str namespace, str action, array options )
     *
     * Executes a plugin if it is already registered.
     *
     *****/

    function tryPlugin( $namespace, $action, $options = null ) {

        $func_a = array( 'udm', $namespace, $action );
        $func = join( '_', $func_a );

        if ( function_exists( $func ) ) {
            return $func( &$this, $options );
        } else {
            return false;
        }

    }

    /*****
     *
     * getPlugins( string action )
     *
     * Returns an array of the available plugins for a given method $action
     *
     *****/

    function getPlugins ( $action ) { 

        if ( isset( $this->plugins ) ) {
            if ( isset( $this->plugins[ $action ] ) ) {
                return $this->plugins[ $action ];
            }
        }

        return null;

    }

    /*****
     *
     * registerPlugin ( string action, string namespace [, array options ] )
     *
     * Register a plugin for use within the userData module.
     * Checks for existence of module file first.
     *
     *****/

    function registerPlugin ( $namespace, $action, $options = null ) {

        $incl = join( DIRECTORY_SEPARATOR, array( 'Modules', 'UDM', $namespace, $action . '.inc.php' ) );

        if ( file_exists_incpath( $incl ) ) {

            if ( !isset( $this->plugins[ $action ] ) ) $this->plugins[ $action ] = array();

            $this->plugins[ $action ][ $namespace ] = $options;
            require_once( $incl );

        } else {

            return false;

        }

    }

    /*****
     *
     * unregisterPlugin ( string action, string namespace )
     *
     * Remove a plugin from use.
     *
     *****/

    function unregisterPlugin ( $action, $namespace ) {

        if ( isset( $this->plugins[ $action ][ $namespace ] ) ) {

            unset( $this->plugins[ $action ][ $namespace ] );

        }

        // We always return true, since in any event the plugin
        // is no longer registered.
        return true;

    }

    #################################
    ### Private Utility Functions ###
    #################################

    /*****
     *
     * doAction( str action, array options )
     *
     * All internal options should be prefixed with an underscore, as they may be
     * overwritten otherwise.
     *
     *****/

    function doAction ( $action, $options = array() ) {

        $plugins = $this->getPlugins( $action );

        if (!isset( $plugins )) return;

        $result = false;
        foreach ( $plugins as $namespace => $plug_options ) {

            if ( is_array( $plug_options ) ) {
                $options = $plug_options + $options;
            }

            $result = $this->doPlugin( $namespace, $action, $options ) or $result;

        }

        return $result;

    }

    /*****
     *
     * _make_mail_header ()
     *
     * Creates a header appropriate to UserDataModule, common
     * to all UDM mailout functions.
     *
     *****/

    function _make_mail_header () {

        $header  = "From: " . $GLOBALS['MM_email_from'];
        $header .= "\nX-Mailer: AMP/UserDataModule\n";

        return $header;

    }

    /*****
     *
     * _register_fields ()
     *
     * Gathers information about module fields.
     *
     *****/

    function _register_fields () {

        $md = $this->_module_def;

        $fields = array_map( array( &$this, "_register_fields_filter" ), array_keys( $md ) );

        $keys = array( 'label', 'public', 'type', 'required', 'values', 'region', 'size' );

        foreach ( $fields as $fname ) {

            if ( !isset( $md[ 'enabled_' . $fname ] )) continue;
            if ( !$md[ 'enabled_' . $fname ] ) continue;

            $field = array();

            foreach ( $keys as $key ) {
                $field[ $key ] = $md[ $key . "_" . $fname ];
            }

            $this->fields[ $fname ] = $field;
        }

        return true;

    }

    /***
     *
     * _register_fields_filter
     *
     * helper function for _register_fields. Finds names of all defined fields.
     *
     ***/

    function _register_fields_filter ( $var ) {
        if ( substr( $var, 0, 8 ) == "enabled_" ) {
            return substr( $var, 8 );
        }
    }

    /*****
     *
     * _register_plugins ()
     *
     * Obtains stored plugin information and registered eligible plugins.
     *
     * Returns true if any plugins have been registered, false if none are registered.
     *
     *****/

    function _register_plugins () {

        $this->plugins = array();

        $dbcon = $this->dbcon;

        $sql  = "SELECT namespace, action, options FROM userdata_plugins WHERE instance_id='";
        $sql .= $this->instance . "' AND active='1' ORDER BY priority";
        $rs = $this->dbcon->CacheExecute( $sql ) or
            die( "Couldn't register module data: " . $dbcon->ErrorMsg() );

        $r = false;

        if ( $rs->RecordCount() != 0 ) {

            while ( $plugin = $rs->FetchRow() ) {

                $namespace = $plugin[ 'namespace' ];
                $action    = $plugin[ 'action'    ];
                $optionStr = $plugin[ 'options'   ];

                $optArray = split( '::', $optionStr );

                foreach ( $optArray as $option ) {

                    $thisOptions = split( '=', $option );
                    $options[ $thisOptions['0'] ] = $thisOptions['1'];

                }

                $r = $this->registerPlugin( $namespace, $action, $options ) or $r;
            }

        } else {

            // No plugins were attached to this module, but we can't very well
            // get along without data access functions. Register the default
            // AMP plugins.

            $r = $this->registerPlugin( 'AMP', 'read' ) or $r;
            $r = $this->registerPlugin( 'AMP', 'save' ) or $r;
            $r = $this->registerPlugin( 'AMP', 'duplicate_check' ) or $r;
            $r = $this->registerPlugin( 'AMP', 'authenticate' ) or $r;

        }

        return $r;

    }

    /*****
     *
     * _register_lists ()
     *
     * Obtains information about mailing lists attached to userData module.
     *
     *****/

    function _register_lists () {

        /* This is currently hard-coded, but should be replaced by
           objects at some point in the (near) future */

        $md = $this->_module_def;

        $lists = array_filter( array_keys( $md ), array( &$this, "_register_lists_filter" ) );

        foreach ( $lists as $list ) {
            $list_id[] = $md[ $list ];
        }

        if (!isset( $list_id)) return false;

        $table = $GLOBALS['MM_listtable'];
        $sql = 'SELECT name, id FROM ' . $table . 'WHERE id IN ( ';
        $sql .= join( ", ", $list_id ) . ' )';

        $rs = $this->dbcon->CacheExecute( $sql );

        foreach ( $rs->FetchRow() as $list ) {

            $this->lists[ $list['id'] ] = $list[ 'name' ];

        }

    }

    /***
     *
     * _register_lists_filter
     *
     * helper function for _register_lists()
     *
     ***/

    function _register_lists_filter ( $var ) {
        return ( substr( $var, 0, 3 ) == "list" );
    }

}

?>
