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
    var $submitted;

    // Templating information for AMP
    var $modTemplateID;

    // Computed Fields ( including plugin fields )
    var $name;
    var $fields;

    // User ID
    var $uid;
    var $pass;

    var $authorized;
    var $authenticated;

    var $values;
    var $useDefaults;

    // Placeholder for HTML_QuickForm. Not required for display-only.
    var $form;

    // Configuration settings, legacy only.
    var $redirect;
    var $mailto;
    var $subject;
    var $uselists;

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

    function UserData ( $dbcon, $instance, $admin = false ) {

        // Setup database connection. Required.
        if (!isset($dbcon)) return false;
        $this->dbcon =& $dbcon;
        $dbcon->SetFetchMode( ADODB_FETCH_ASSOC );

        $this->instance( $instance );
        $this->submitted = true;
        $this->useDefaults = true;

        $this->admin = $admin;

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

        // Register the base module definition
        $this->_register_base();

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
     * getStoredValues ( [ array fields ] )
     *
     * returns a fieldname => value array of the values in the udm instance.
     *
     *****/

    function getStoredValues ( $fields = array() ) {

        $retarray = array();
        foreach ( $this->fields as $name => $fDef ) {

            if ( count( $fields ) > 0 ) {
                if ( !array_search( $name, $fields ) ) continue;
            }

            if ( $this->useDefaults ) {
                $valueKey = 'values';
            } else {
                $valueKey = 'value';
            }

            if ( isset( $fDef[ $valueKey ] ) )
                $retarray[ $name ] = $fDef[ $valueKey ];

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
     * instance()
     *
     * Accessor. Available for overriding to allow multiple instances.
     *
     *****/

    function instance( $instance ) {

        // Get module instance. Required.
        $this->instance = preg_replace( "/(\d+)/", "\$1", $instance );
        if ( $this->instance == '' ) trigger_error( "No module specified!" );

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
     * _register_base ()
     *
     * Registers core module information.
     *
     *****/

    function _register_base () {

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

            if ( !$this->admin ) {
                if ( !isset( $md[ 'enabled_' . $fname ] )) continue;
                if ( !$md[ 'enabled_' . $fname ] ) continue;
            }

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

            if ( method_exists( $this, '_register_default_plugins' ) ) {
                $this->_register_default_plugins();
            }

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
        $this->uselists = $md[ 'uselists' ];

        $lists = array_filter( array_keys( $md ), array( &$this, "_register_lists_filter" ) );

        foreach ( $lists as $list ) {
	    if ( $md[ $list ] ) {
	        $list_id[] = $md[ $list ];
	    }
        }

        if (!isset( $list_id)) return false;

        $table = $GLOBALS['MM_listtable'];
        if ( !isset( $table ) ) $table = 'lists';

        $sql = 'SELECT name, id FROM ' . $table . ' WHERE id IN ( ';
        $sql .= join( ", ", $list_id ) . ' )';

        $rs = $this->dbcon->CacheExecute( $sql )
            or die( "Error fetching list info from database: " . $this->dbcon->ErrorMsg() );

        if ( $this->uselists && (count( $lists ) > 0)) {
            $listField = array( 'label' => 'Subscribe to the following lists:',
                                'public' => true,
                                'type' => 'header' );
            $this->fields[ 'list_header' ] = $listField;
        }

        while ( $list = $rs->FetchRow() ) {

            $this->lists[ $list['id'] ] = $list[ 'name' ];

            // Add lists to fields. This is a *temporary* change, and should be
            // removed, along with all changes in SVN r121
            if ( $this->uselists ) {
                $listField = array( 'label'    => $list[ 'name' ],
                                    'public'   => true,
                                    'type'     => 'checkbox',
                                    'required' => false,
                                    'values'   => 1 );

                $this->fields[ 'list_' . $list['id'] ] = $listField;
            }

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
        return ( substr( $var, 0, 4 ) == "list" );
    }

}

?>
