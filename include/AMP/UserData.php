<?php

require_once( 'AMP/Region.inc.php' );
require_once( 'AMP/System/UserData.php');

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

$GLOBALS['regionObj'] = new Region();

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
    var $plugins;
    var $fieldOrder;

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
    var $_lists;

    // flag to enable / disable display of form to user.
    var $showForm;

	// form callbacks for form post processing
	var $_form_callbacks;

    // Flag to indicate administrator access.
    var $admin;

    // Arrays for holding result information.
    var $results;
    var $errors;

	// Flag for suppressing javascript output (for emails, for example)
	var $no_javascript = false;

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

        if ( !isset( $this->instance)) return false;

        // Register the base module definition
        $this->_register_base();

        // Register the fields from the database.
        $this->_register_fields();

        // Register the mailing lists.
        #$this->_register_lists();

        // Register methods for data access / manipulation.
        $this->_register_plugins();


		// Allow for overriding, but don't set (yet)
		if(!defined('AMP_UDM_FORM_INVALID_ERROR')) {
			define('AMP_UDM_FORM_INVALID_ERROR', '');
		}
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

	function get_plugin_javascript() {
		if ($this->no_javascript) {
			return false;
		}

		$javascript = '';
		foreach ($this->getPlugins() as $action) {
			foreach ($action as $component) {
				if($plugin_javascript = $component->get_javascript()) {
					$javascript .= $plugin_javascript;
				}
			}
		}
		return $javascript;
	}

	function disable_javascript() {
		$this->no_javascript = true;
	}

	function enable_javascript() {
		$this->no_javascript = false;
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

//apps can register error handlers and pass the error message as a parameter array
    function outputErrors () {
        if (!isset($this->errors)) return false;
        $output = "";
        foreach ($this->errors as $type => $message) {
			$handler =& $this->getErrorHandler($type);
			if($handler) {
				if(!is_array($message)) {
					$message = array($message);
				}
				$message = call_user_func_array($handler, $message);
			} 
			if($message && is_string($message)) {
				$output .= $this->formatError($message)."<BR>";
			}
        }
        return $output;
    }

	function formatError($error) {
		if(function_exists('udm_format_error')) {
			return udm_format_error($error);
		}
		return $error;
	}

	function &getErrorHandler($type) {
		$callback =& $this->_error_handlers[$type];
		if(isset($callback)) {
			return $callback;
		}
		return false;
	}

	function setErrorHandler($type, $callback) {
		$this->_error_handlers[$type] = $callback;
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

        return $this->doAction( 'Authenticate', $options );

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

            if ( !empty( $fields ) ) {
                if ( !array_search( $name, $fields ) ) continue;
            }

            if ( ! ( $valueKey = $this->getValueKey( $fDef ) ) ) continue;

            if ( isset( $fDef[ $valueKey ] ) )
                $retarray[ $name ] = $fDef[ $valueKey ];

        }

        return $retarray;
    }

    function getValueKey( $fDef ) {
        if (!$this->useDefaults) return 'value';

        if (isset($fDef['default'])) return 'default';
        if (isset($fDef['region']) && $fDef['region']) return 'values';

        $no_default_types = array( "select", "multiselect","checkgroup","radiogroup" );
        if (array_search( $fDef['type'], $no_default_types ) !== FALSE) return FALSE;

        return 'values';
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
     * setData( array $data )
     *
     * Sets the values of form fields to the values in the associative array
     * $data. Keys are field names, values are field values.
     *
     *****/

    function setData( $data ) {

        if (!is_array($data)) return false;

        foreach ($data as $fname=>$fvalue) {

            if (!isset($this->fields[$fname])) continue;

            $this->fields[$fname]['value']=$fvalue;
        }
    }

    /*****
     *
     * getData( array $fields )
     *
     * Gets the values of submitted form fields, either from the form or from
     * the default / constant values from the fields definition if
     * $this->form['field'] isn't set.
     *
     *****/

    function getData( $fields=null ) {

        if (!$this->form)  {
            $data=array();
            foreach ($this->fields as $fname=>$fDef) {
                if (isset($fields) && (array_search($fname, $fields)===FALSE)) continue;
                if (isset($fDef['value']))
                    $data[$fname]=$fDef['value'];

            }
            return $data;
        }
        
        if ( isset( $fields ) ) {
            foreach ($fields as $key=>$fname ) {
                if (!$this->form->elementExists( $fname ) ) unset ($fields[$key] );
            }
        }
		$data = $this->form->exportValues($fields);
		
        if (PEAR::isError($data)) {
			return false;
		} else {
			return $data;
		}
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

    #########################################
    ### Public Field Management Functions ###
    #########################################


    /****
     *
     * setFieldOrder ()
     *
     * allows plugin to manipulate form field order
     * 
     * var $fields: array of fieldnames to insert
     * var $beforeField: numeric index or existing fieldname
     *
     *********/

     function insertBeforeFieldOrder ( $fields, $beforeField = 0 ) {
        $fieldOrderSet = $this->getFieldOrder();
        $startinsert = 0;

        if (is_numeric($beforeField) && ($beforeField!=0)) $startinsert = $beforeField;
        elseif ($key = array_search($beforeField, $fieldOrderSet)) $startinsert = $key;

        if ($startinsert) {
            $newfieldOrder = array_slice($fieldOrderSet, 0, $startinsert);
            $fieldOrderSet = array_slice($fieldOrderSet, $startinsert);
        }

        foreach ( $fields as $fieldname ) {
            $newfieldOrder[] = $fieldname;
        }

        foreach ($fieldOrderSet as $fieldname) {
            $newfieldOrder[] = $fieldname;
        }

        $this->setFieldOrder( $newfieldOrder );
     }

     function insertAfterFieldOrder( $fields ) {
        $fieldOrderSet = $this->getFieldOrder();

        foreach ( $fields as $fieldname ) {
            $fieldOrderSet[] = $fieldname;
        }
        $this->setFieldOrder( $fieldOrderSet );
     }

     function getFieldOrder() {
        if (isset($this->fieldOrder)) return $this->fieldOrder;
        if ($this->_module_def['field_order']) return $this->setFieldOrder(preg_split("/\s?,\s?/", $this->_module_def['field_order']));
        return array_keys($this->fields);
     }

     function setFieldOrder($fieldOrderSet) {
        $this->fieldOrder = $fieldOrderSet;
        return $fieldOrderSet;
     }

     function addFields( $field_definitions ) {
        foreach ($field_definitions as $fname => $field_def) {
            $this->fields[$fname] = $field_def;
        }
     }

     function formNotBlank() {
        $types_to_avoid = array("header", "hidden", "html", "static");
        $fields_to_avoid = array("otp", "modin", "uid", "btnUdmSubmit");
        foreach ($this->fields as $fname => $field_def) {
            if (array_search($fname, $fields_to_avoid)!==FALSE) continue;
            if (array_search($field_def['type'], $types_to_avoid)!==FALSE) continue;
            if (!(isset($_REQUEST[$fname]) && $_REQUEST[$fname])) continue;
            return true;
        }
        return false;
     }

	function formInvalidCallback() {
		if(defined('AMP_UDM_FORM_INVALID_ERROR')) {
			$this->addError('AMP_UDM_FORM_INVALID', AMP_UDM_FORM_INVALID_ERROR);
		}
		foreach($this->getFormCallbacks('AMP_UDM_FORM_INVALID') as $callback) {
			call_user_func_array($callback['callback'], $this);
		}
	}

	function getFormCallbacks($type) {
		return $this->_form_callbacks[$type];
	}

	function addFormCallback($type, $callback) {
		$this->_form_callbacks[$type][] = $callback;
	}

    #############################
    ### Public Plugin Methods ###
    #############################

    function &getPlugin( $namespace, $action ) {

        $plugins =& $this->plugins;

        if (!isset($plugins[$action])) return false;
        if (!isset($plugins[$action][$namespace])) return false;

        $actions =& $plugins[$action];
        $plugin  =& $actions[$namespace];

        return $plugin;
    }

    /*****
     *
     * doPlugin( str namespace, str action, array options )
     *
     * Executes a plugin, registering it if necessary.
     *
     *****/

    function doPlugin( $namespace, $action, $options = null ) {
        
        $plugin =& $this->registerPlugin( $namespace, $action );

        if ($plugin) {
            $retval = $plugin->execute( $options );
            return $retval;
        }

        return false;
    }

    /*****
     *
     * tryPlugin( str namespace, str action, array options )
     *
     * Executes a plugin if it is already registered.
     *
     *****/

    function tryPlugin( $namespace, $action, $options = array() ) {

        if ($plugin =& $this->getPlugin( $namespace, $action )) return $plugin->execute( $options );
        else return false;

    }

    /*****
     *
     * getPlugins( string action )
     *
     * Returns an array of the available plugins for a given method $action
     *
     *****/

    function getPlugins ( $action = null ) { 

        if (!isset($this->plugins)) return false;

        if ($action) {
            if ( isset($this->plugins[$action]) ) {
                return $this->plugins[$action];
            } else {
                return false;
            }
        } else {
            return $this->plugins;
        }

        return false;
    }

    /*****
     *
     * registerPlugin ( string action, string namespace [, array options ] )
     *
     * Register a plugin for use within the userData module.
     * Checks for existence of module file first.
     *
     *****/

    function &registerPlugin ( $namespace, $action, $plugin_instance=null ) {

        // temporary fixup. 
        if (strpos($action, "_") !== false) {
            $action_parts = explode( "_", $action );
            $action="";
            foreach ($action_parts as $action_part) {
                $action .= ucfirst($action_part);
            }
        } else {
            $action = ucfirst($action);
        }

        // just return the plugin if it already exists.
        if ($plugin =& $this->getPlugin( $namespace, $action )) {
            return $plugin;
        }

        $incl = join( DIRECTORY_SEPARATOR, array( 'Modules', 'UDM', $namespace, $action . '.inc.php' ) );

        // Do not pass GO if the plugin doesn't actually exist.
        if ( !file_exists_incpath( $incl ) ) return false;

        require_once( $incl );

        if ( !isset($this->plugins[$action]) ) {
            $actions = array();
            $this->plugins[ $action ] =& $actions;
        } else {
            $actions =& $this->plugins[ $action ];
        }
        
        $plugin_class = "UserDataPlugin_" . $action . "_" . $namespace;

        // If the class doesn't exist (but we have a file for it), trigger an
        // error, and failt.
        if (!class_exists( $plugin_class )) {
            trigger_error( "Unable to instantiate data class $action in $namespace." );
            return false;
        }

        // Add the plugin to our repertoire.
        $plugin =& new $plugin_class( $this , $plugin_instance );
        $actions[$namespace] =& $plugin;

        // Add the fields from the plugin. Prefix with the plugin value..
        $this->registerFields( $plugin->fields, $plugin->_field_prefix );

        return $plugin;

    }

    /*****
     *
     * unregisterPlugin ( string action, string namespace )
     *
     * Remove a plugin from use.
     *
     *****/

    function unregisterPlugin ( $action, $namespace ) {

        if ($plugin = &$this->getPlugin( $namespace, $action ) ) {

            unset( $this->plugins[$action][$namespace] );
            $this->unregisterFields( $plugin->fields, $plugin->_field_prefix );

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

        $plugins =& $this->getPlugins( $action );

        if (!isset( $plugins )|| !is_array($plugins) ) return;
        
        $result = false;
        foreach ( array_keys($plugins) as $plugin_name ) {
            $plugin =& $plugins[$plugin_name];

            $plugin->setOptions( $options );
            $result = $plugin->execute() or $result;
            if ($result === false) return false;

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
     * _register_base ()
     *
     * Registers core module information.
     *
     *****/

    function _register_base () {

        /*
        $dbcon = &$this->dbcon;

        // Fetch database definition of module.
        $sql = "SELECT * FROM userdata_fields WHERE id=" . $dbcon->qstr( $this->instance );

        $rs = $dbcon->CacheExecute( $sql )
            or trigger_error( "Error retreiving module information from database: " . $dbcon->ErrorMsg() );

        $md = $this->_module_def = $rs->FetchRow();
        */
        $moduleSource = &new AMPSystem_UserData( $this->dbcon, $this->instance );
        if ( !$moduleSource->hasData( )) return false;

        $md = $this->_module_def = $moduleSource->getData();

        // Define module class
        $this->class = ( isset($md[ 'class' ]) ) ? $md['class'] : 1;
        $this->modTemplateID = $md['modidinput'] or 10;

        // Define module variables.
        $this->name = $md[ 'name' ];

        // Define redirect config. Legacy, will be replaced by redirect plugin.
        $this->redirect = $md[ 'redirect' ];

        // Define email config. fixme, will be replaced by email plugins.
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

        $keys = array( 'label', 'public', 'type', 'required', 'values', 'region', 'size', 'enabled' );

        foreach ( $fields as $fname ) {

            if (!$fname) continue;

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
        //Publish Field Hack
        if ($this->admin && $md['publish']) {
            $publish_field = array('type'=>'checkbox', 'label'=>'<font color="#CC0000" size="3">PUBLISH</font>', 'required'=>false, 'public'=>false,  'values'=>0, 'size'=>null, 'enabled'=>true);
            $this->fields['publish']=$publish_field;
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

    /****
     *
     * registerFields
     *
     * registers fields from an array definition.
     *
     ****/

    function registerFields( $fields_def, $prefix = '' ) {

        if ( $prefix ) $prefix .= "_";

        foreach ( $fields_def as $field_name => $field ) {
            if (!$field['enabled']) continue;
            $this->fields[ $prefix . $field_name ] = $field;
        }
    }

    /****
     *
     * unregisterFields
     *
     * unregisters fields from an array definition.
     *
     ****/

    function unregisterFields( $fields_def, $prefix = '' ) {

        if ( $prefix ) $prefix .= "_";
        $fieldOrder = $this->getFieldOrder();

        foreach ( $fields_def as $field_name => $field ) {
            $prefixedname  = $prefix.$field_name;
            if (!isset($this->fields[ $prefixedname ])) continue;
            unset ($this->fields[ $prefixedname  ]);

            $fieldOrderKey = array_search( $prefixedname, $fieldOrder );
            if ($fieldOrderKey === FALSE ) continue;
            unset ($this->fieldOrder[ $fieldOrderKey ] );

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

        $plugins = array();
        $this->plugins =& $plugins;

        $dbcon = $this->dbcon;

        $sql  = "SELECT id, namespace, action FROM userdata_plugins WHERE instance_id=";
        $sql .= $dbcon->qstr( $this->instance ) . " AND active='1' ORDER BY priority";

        if (AMP_DISPLAYMODE_DEBUG_PLUGINS) AMP_DebugSQL( $sql, get_class($this));

        $rs = $dbcon->CacheExecute( $sql ) or
            die( "Couldn't register module data: " . $dbcon->ErrorMsg() );

        $r = false;

        if ( $rs->RecordCount() != 0 ) {

            while ( $plugin = $rs->FetchRow() ) {

                $namespace = $plugin[ 'namespace' ];
                $action    = $plugin[ 'action'    ];
                $plugin_instance = $plugin[ 'id' ];

                $r = $this->registerPlugin( $namespace, $action, $plugin_instance ) and $r;
            }

        } else {

            if ( method_exists( $this, '_register_default_plugins' ) ) {
                $this->_register_default_plugins();
            }

        }
        $this->_register_lists( );

        return $r;

    }

    /*****
     *
     * _register_lists ()
     *
     * Obtains information about mailing lists attached to userData module.
     *
     * fixme, replace with lists plugins.
     *
     *****/

    function _register_lists () {

        if ( $this->uselists = $this->_module_def[ 'uselists' ] ) {
            $list = &$this->registerPlugin( 'AMPlist', 'Start');
            $options = $list->getOptions( );

            if( !isset( $options['lists'])) return;
            $this->lists = explode( ",", $options['lists']);
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


    function &saveRegisteredPlugin ( $namespace, $action ) {
        if (! ($plugin = &$this->registerPlugin( $namespace, $action ))) return false;
        $plugin->saveRegistration( $namespace, $action );
        return $plugin;
    }

    function getRegisteredLists( ){
        if( !$this->uselists ) return false;
        if( isset( $this->lists )) return $this->lists;
        $md = $this->_module_def;
        $lists = array_filter( array_keys( $md ), array( &$this, "_register_lists_filter" ) );

        $list_id = array();
        foreach ( $lists as $list ) {
            if (! $md[ $list ] ) continue; 
            $list_id[] = $md[ $list ];
            
        }

        if (count($list_id) == 0) return false;
        $this->lists = array_combine_key( $list_id, AMPSystem_Lookup::instance( 'lists' ));
        return $this->lists;

    }

}

?>
