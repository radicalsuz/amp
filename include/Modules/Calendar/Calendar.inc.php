<?php
/***************************
 *  class Calendar
 *  base Class for all Calendar output
 *  written by Austin Putman
 *  with deep indebtedness to Userdata by Blaine Cook
 *  
 *  03-21-2005
 **********************/

require_once('Modules/Calendar/Lookups.inc.php');
 
class Calendar {

/* $events variable contains an array of calendar items */
var $events;

//Instance determines which calendar is to be searched
var $instance;

//Admin sets access level
var $admin;
var $dbcon;

// fields and plugins arrays allow extensions like the UDM module
var $fields;
var $plugins;
var $error;

var $url_criteria;
var $sql_criteria;

var $allow_recurring_events = false;
var $allow_registration = false;
var $registration_form_modin;

var $id;
    var $_debug_cache_constant = 'AMP_DISPLAYMODE_DEBUG_CACHE';

	function Calendar(&$dbcon, $eventset=null, $admin=false, $instance=1) {
		$this->instance=$instance;
		$this->dbcon=$dbcon;
        $this->admin=$admin;
		if (isset($eventset)) $this->setEvents($eventset);

        // Register methods for data access / manipulation.
        $this->_register_plugins();


	}

    function output ( $format = 'html', $options = null ) {
	
				return $this->doPlugin( 'Output', $format, $options );

    }


    function setEvents(&$eventset) {
        $this->events=$eventset;
    }
	

	function addEvent ($event) {
		//adds an event to the Calendar object
        $this->events[]=$event;
	}

	function removeEvent ($eventid) {
		//removes an event from the Calendar object
		unset ($this->events[$eventid]);
	}


	//return the results of the current list as an array or recordset
	function results() {
		return $this->events;
	}
    
    function getFields() {


        $fields = array();
        $fields['publish'] = array('type'=>'checkbox', 'label'=>'<font color="#CC0000" size="3">PUBLISH</font>', 'required'=>false, 'public'=>false,'enabled'=>true);
		$fields['event'] = array('type'=>'text', 'label'=>'Event Name', 'required'=>true, 'public'=>true,  'values'=>null, 'size'=>40, 'enabled'=>true);
		$fields['typeid']=array('type'=>'select', 'label'=>'Event Type', 'required'=>false, 'public'=>true, 'values'=>'Lookup(eventtype, name, id)', 'enabled'=>true);
		$fields['student']=array('type'=>'checkbox', 'label'=>'Student Event',  'required'=>false, 'public'=>true, 'values'=>null, 'enabled'=>true);
		$fields['fpevent']=array('type'=>'checkbox', 'label'=>'Front Page Event',  'required'=>false, 'public'=>false, 'values'=>null, 'enabled'=>true);
        $fields = array_merge( $fields, $this->addRegistrationFields());
		$fields['date'] = array('type'=>'date', 'label'=>'Event Date', 'required'=>true, 'public'=>true,  'default'=>'today', 'size'=>null, 'enabled'=>true);
		$fields['time'] = array('type'=>'text', 'label'=>'Event Start Time', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>10, 'enabled'=>true);
		$fields['endtime'] = array('type'=>'text', 'label'=>'Event End Time', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>10, 'enabled'=>true);
		$fields['cost']=array('type'=>'text', 'label'=>'Event Cost', 'required'=>false, 'public'=>true, 'size'=>'10', 'enabled'=>true);
		$fields['url']=array('type'=>'text', 'label'=>'Website', 'required'=>false, 'public'=>true, 'enabled'=>true, 'default' => 'http://');
		$fields['shortdesc'] = array('type'=>'textarea', 'label'=>'Brief description of the Event', 'required'=>true, 'public'=>true,  'values'=>null, 'size'=>"5:40", 'enabled'=>true);
		$fields['org']= array('type'=>'textarea', 'label'=>'Endorsing Organizations (if any)', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>"4:40", 'enabled'=>true);

        $fields = array_merge( $fields, $this->addRecurringFields());

		$fields['header1']= array('type'=>'header', 'label'=>'Public Contact Information', 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
		$fields['contact1']=array('type'=>'text', 'label'=>'Contact Name', 'required'=>true, 'public'=>true, 'enabled'=>true);
		$fields['email1']=array('type'=>'text', 'label'=>'Contact Email', 'required'=>true, 'public'=>true, 'enabled'=>true);
		$fields['phone1']=array('type'=>'text', 'label'=>'Contact Phone', 'required'=>false, 'public'=>true, 'enabled'=>true);
		$fields['header2']= array('type'=>'header', 'label'=>'Event Location<BR><span class=photocaption>use Location box for directions<BR>Street Address MUST be a standard mailing address</span>', 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
		$fields['location'] = array('type'=>'textarea', 'label'=>'Event Location', 'required'=>true, 'public'=>true,  'values'=>null, 'size'=>"3:40", 'enabled'=>true);
		$fields['laddress'] = 
			array('type'=>'text', 
						'label'=>'Event Street Address', 
						'required'=>false, 
						'public'=>true,  
						'values'=>null, 
						'size'=>40, 'enabled'=>true);
		$fields['lcity'] = array('type'=>'text', 'label'=>'Event City', 'required'=>true, 'public'=>true,  'values'=>null, 'size'=>30, 'enabled'=>true);
		$fields['lstate'] = 
					array('type'=>'select', 
								'label'=>'Event State', 
								'required'=>true, 
								'public'=>true,  
								'region'=>'US AND CANADA', 
								'values'=>null, 
								'size'=>null, 'enabled'=>true);
		$fields['lzip'] = array('type'=>'text', 'label'=>'Event Zip', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>10, 'enabled'=>true);
		$fields['lcountry'] = 
					array('type'=>'select', 
								'label'=>'Event Country', 
								'required'=>true, 
								'public'=>true,  
								'region'=>'WORLD', 
								'values'=>null, 
								'size'=>null, 'enabled'=>true);
		
		$fields['uid']=array('type'=>'hidden', 'label'=>'', 'required'=>false, 'public'=>true, 'values'=>null, 'size'=>null, 'enabled'=>true);
		$fields['id']=array('type'=>'hidden', 'label'=>'', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>null, 'enabled'=>true);

		if( !defined('AMP_CALENDAR_USE_FULLDESC') || (AMP_CALENDAR_USE_FULLDESC != false)) {
			$fields['fulldesc'] = array('type'=>'textarea', 'label'=>'Full Description of the Event (optional)', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>"10:40", 'enabled'=>true);
		} else {
			$fields['fulldesc'] = array('type'=>'textarea', 'label'=>'Full Description of the Event (optional)', 'required'=>false, 'public'=>false,  'values'=>null, 'size'=>"10:40", 'enabled'=>true);
		}

		$fields['header3']=array('type'=>'header', 'label'=>'The following information is for the staff at '. AMP_SITE_NAME .' and will not be listed on the website, unless it is the same as the information above.', 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true) ;
		$fields['lat']=array('type'=>'hidden', 'required'=>false, 'public'=>true, 'values'=>null, 'enabled'=>true);
		$fields['lon']=array('type'=>'hidden', 'required'=>false, 'public'=>true, 'values'=>null, 'enabled'=>true);
		$fields['dia_key']=array('type'=>'hidden', 'required'=>false, 'public'=>false, 'values'=>null, 'enabled'=>true);

        return $fields;

    }

    function addRecurringFields( ) {
        $fields = array();
        if (!$this->allowRecurringEvents()) return null;
        $fields['header_recur']= array('type'=>'header', 'label'=>'Repeating Events<BR><span class=photocaption>The next three items apply to Repeating events only:</span>', 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
        $fields['recurring_options']= array('type'=>'select', 'label'=>'Event Frequency', 'required'=>false, 'public'=>true, 'values'=>'Lookup(calendar_recur, name, id)', 'default'=>0, 'enabled'=>true);
        $fields['enddate']=array('type'=>'date', 'label'=>'Choose a date for the event to stop appearing on the calendar:', 'required'=>false, 'public'=>true,  'default'=>'today', 'enabled'=>true);
        $fields['recurring_description']=array('type'=>'textarea', 'label'=>'Describe the schedule for a repeating event <BR>(e.g <i>Every 2nd Tuesday of the Month</i>)', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>'3:40', 'enabled'=>true);
        return $fields;
    }

    function addRegistrationFields ( ) {
        if (! $this->allowRegistration() ) return null;
        if ($this->admin) {
            $fields['registration_modin'] = array (
                'label' => 'Registration Form',
                'public' =>false,
                'type' => 'select',
                'required' => false,
                //'values'=>'Lookup(userdata_fields, name, id)',
                'default'=> $this->registrationForm(),
                'enabled' => true,
                'size' => null,
                );
        } else {
            $fields['rsvp']=array(
                'type'=>'checkbox', 
                'label'=>'Please setup registration/RSVPs for this event', 
                'required'=>false, 
                'public'=>true, 
                'enabled'=>true);
        }
        return $fields;
    }

    function allowRegistration( $value = null ) {
        if (!isset($value)) return $this->allow_registration;
        $this->allow_registration = $value;
        if (is_numeric($value)) $this->setRegistrationForm( $value );
    }

    function setRegistrationForm( $modin ) {
        $this->registration_form_modin = $modin;
    }

    function registrationForm() {
        return $this->registration_form_modin;
    }

    function allowRecurringEvents( $value = null ) {
        if (!isset($value)) return $this->allow_recurring_events;
        $this->allow_recurring_events = $value;
    }

    function readData ($cal_id = null) {
        if (!isset($cal_id)) return false;
        //Read Calendar Record
        $sql  = "SELECT * FROM calendar WHERE "; 
        $sql .= "id='" . $cal_id . "'";      
    
        if ($event = $this->dbcon->CacheExecute( $sql )){
            $data = $event->FetchRow();
            $this->addEvent( $data );
            return $data;
        }

        return false;
    }

    function saveEvent( $save_data ) {
        if ( $this->allowRegistration() && isset( $save_data['rsvp']) && $save_data['rsvp'] ) {
            $save_data['registration_modin'] = $this->registrationForm() ;
        }
        unset( $save_data['rsvp'] );

        $rs = $this->dbcon->Replace("calendar", $save_data, "id", $quote = true );

        if ($rs == ADODB_REPLACE_INSERTED ) {
            $this->id = $this->dbcon->Insert_ID();
        } elseif ( $rs ){
            $this->id= $save_data['id'];
        } 
        if ( $rs && isset( $save_data['lzip']) && substr( $save_data['lzip'], 0, 1 ) == '0') {
            $this->dbcon->Execute( 'update calendar set lzip = '.$this->dbcon->qstr( $save_data['lzip']) . 'where id = '. $this->id );
        }

        $this->clearEventCache( $this->id );

        if ($rs) return true;

        return false;
    }

    function clearEventCache( $cal_id ){
        $sql  = "SELECT * FROM calendar WHERE "; 
        $sql .= "id='" . $cal_id . "'";      

        $this->dbcon->CacheFlush( );
        if (defined( $this->_debug_cache_constant ) && constant( $this->_debug_cache_constant )) AMP_DebugSQL( $sql, get_class($this)." cleared cache"); 
    }

	function updateEvent( $save_data ) {
        trigger_error( 'updating dia key: ' . $save_data['dia_key'] . 'for event id:' . $save_data['id']);
		if (!isset($save_data['id'])) return false;	
		$new_data = array_merge($this->readData($save_data['id']), $save_data);
        
		return $this->saveEvent($new_data);
	}
	

    function parse_URL_criteria () {
        parse_str($_SERVER['QUERY_STRING'], $parsed_criteria);
        foreach ($parsed_criteria as $pkey=>$pvalue) {

            if (isset($pvalue)&&($pvalue||$pvalue==='0')) {

                if ($pkey!='offset'&&$pkey!='qty') {
                    $this->url_criteria[]=$pkey.'='.$pvalue;
                }
            }
        }
        return $this->url_criteria;
    }


    #############################
    ### Public Plugin Methods ###
    #############################
    function &getPlugin( $namespace, $action ) {
        $empty_value = false;

        $plugins =& $this->plugins;

        if (!isset($plugins[$action])) return $empty_value;
        if (!isset($plugins[$action][$namespace])) return $empty_value;

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
        $plugin = $this->registerPlugin( $namespace, $action );

        if ($plugin) {
            return $plugin->execute( $options );
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
        
        if ($plugin = $this->getPlugin( $namespace, $action )) return $plugin->execute( $options );
        else return false;

    }

    /*****
     *
     * getPlugins( string action )
     *
     * Returns an array of the available plugins for a given method $action
     *
     *****/

    function &getPlugins ( $action = null ) { 
        
        $empty_value = false;
        if (!isset($this->plugins)) return $empty_value;

        if ($action) {
            if ( isset($this->plugins[$action]) ) {
                return $this->plugins[$action];
            } else {
                return $empty_value;
            }
        } else {
            return $this->plugins;
        }

        return $empty_value;
    }

    /*****
     *
     * registerPlugin ( string action, string namespace [, array options ] )
     *
     * Register a plugin for use within the Calendar module.
     * Checks for existence of module file first.
     *
     *****/

    function &registerPlugin ( $namespace, $action, $plugin_instance=null ) {
        $empty_value = false;

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
        if ($plugin = $this->getPlugin( $namespace, $action )) {
            return $plugin;
        }

        $incl = join( DIRECTORY_SEPARATOR, array( 'Modules', 'Calendar', $namespace, $action . '.inc.php' ) );

        // Do not pass GO if the plugin doesn't actually exist.
        if ( !file_exists_incpath( $incl ) ) return $empty_value;

        require_once( $incl );

        if ( !isset($this->plugins[$action]) ) {
            $actions = array();
            $this->plugins[ $action ] =& $actions;
        } else {
            $actions =& $this->plugins[ $action ];
        }
        
        $plugin_class = "CalendarPlugin_" . $action . "_" . $namespace;

        // If the class doesn't exist (but we have a file for it), trigger an
        // error, and failt.
        if (!class_exists( $plugin_class )) {
            trigger_error( "Unable to instantiate data class $action in $namespace." );
            return $empty_value;
        }

        // Add the plugin to our repertoire.
        $plugin =& new $plugin_class( $this );
        $actions[$namespace] =& $plugin;

        // Add the fields from the plugin. Prefix with the plugin name.
        $plugin->_field_prefix="plugin_$namespace"; 
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

        if ( isset($this->plugins[$action][$namespace]) ) {

            unset( $this->plugins[$action][$namespace] );

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

        $plugins =  $this->getPlugins( $action );

        if (!isset( $plugins )|| !is_array($plugins) ) return;
        
        $result = false;
        foreach ( array_keys($plugins) as $plugin_name ) {

            $plugin =& $plugins[$plugin_name];

            $plugin->setOptions( $options );
            $result = $plugin->execute() or $result;

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

/*
        $sql  = "SELECT id, namespace, action FROM calendar_plugins WHERE instance_id=";
        $sql .= $dbcon->qstr( $this->instance ) . " AND active='1' ORDER BY priority";
        $rs = $dbcon->CacheExecute( $sql ) or
            die( "Couldn't register module data: " . $dbcon->ErrorMsg() );

        $r = false;

        if ( $rs->RecordCount() != 0 ) {

            while ( $plugin = $rs->FetchRow() ) {

                $namespace = $plugin[ 'namespace' ];
                $action    = $plugin[ 'action'    ];
                $plugin_instance = $plugin[ 'id' ];

                $r = $this->registerPlugin( $namespace, $action, $plugin_instance ) or $r;
            }

        } else {
*/
            if ( method_exists( $this, '_register_default_plugins' ) ) {
                return $this->_register_default_plugins();
            }

        #}

        return false;
    }

    /****
        _register_default_plugins();

        Standard Calendar Actions
    */

    function _register_default_plugins() {
        $r = $this->registerPlugin( 'Output',   'SearchForm') and $r;
        $r = $this->registerPlugin( 'Output',   'Pager'     ) and $r;
        $r = $this->registerPlugin( 'AMP',      'Search'    ) and $r;
        $r = $this->registerPlugin( 'Output',   'TableHTML' ) and $r;
        $r = $this->registerPlugin( 'Output',   'DisplayHTML') and $r;
        $r = $this->registerPlugin( 'AMP',      'Sort'      ) and $r;
        $r = $this->registerPlugin( 'Output',   'Actions'   ) and $r;

        return $r;
        
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


    function __sleep( ){
        $response = get_object_vars( $this );
        unset( $response['dbcon']);
        return array_keys( $response );
    }

    function __wakeup( ){
        $this->dbcon = AMP_Registry::getDbcon( );
    }

}

class CalendarInput  extends Calendar {

	function saveEvent() {

	}

	function getEvent() {
	
	}

}


?>
