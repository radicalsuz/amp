<?php
class UserDataPlugin {

    // A little bit of friendly information about the plugin.
    var $short_name  = 'Undefined';
    var $long_name   = 'Undefined as yet';
    var $description = 'This plugin is undefined. This may or may not be a problem.';

    // The UDM is passed to plugins to enable the runtime modificaion of
    // behaviour of the data modules. However, to whatever extent possible, the
    // UDM object should be accessed for reads only as much as possible.
    var $udm;
    var $dbcon;

    // $options contains information about available settings for the module.
    // $fields contains information about available user-fields for the module.
    var $options = array();
    var $fields  = array();

    // $available, bool, denotes whether or not this module is available for
    // use by system admins via the plugin menu. True for available, false for
    // not.
    var $available;

    // Plugins can be called multiple times within a single form instance.
    // (e.g., for sending emails to multiple people, or other myriad hacks)
    var $plugin_instance;

    // The field prefix is the text used by the UserData object
    // to distinguish data from separate plugins
    var $_field_prefix;
    
    // The executed flag should be set upon sucessful execution of the plugin.
    // This is to prevent multiple runs of the plugin.
    var $executed;

    // Stub constructor. Doesn't do anything in particular.
    function UserDataPlugin ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    /*****
     *
     * init ( &$udm )
     *
     * This method should be called by all plugins to ensure that any available
     * optons and fields are registered properly. Two optional callback
     * methods can be passed. These methods must be defined in the plugin
     * class.
     *
     *****/

    function init ( &$udm, $plugin_instance=null ) {

        $this->udm = &$udm;
        $this->dbcon = &$udm->dbcon;
        $this->plugin_instance=$plugin_instance;

        $this->_register_options();
        $this->_register_fields();

    }

    /*****
     *
     * Execute; this method performs the plugin action.
     *
     * This function should always be overfidden in plugins to enable actions
     * to be performed.
     *
     *****/

    function execute ( $options = null ) {

        trigger_error( 'Plugin ' . $this->short_name . ' failed to override execute function.', E_USER_WARNING );
        return false;

    }

    function _register_options () {

        // Make sure we fetch options set across our inherited classes.
        $this->_register_common('options');

        $dbcon   = &$this->dbcon;
        $options = &$this->options;

        $osql = "SELECT * FROM userdata_plugins_options " .
                "WHERE plugin_id = " . $dbcon->qstr( $this->plugin_instance );

        $rs = $this->dbcon->CacheExecute( $osql )
                or die( "Error retrieving plugin options from database: " . $dbcon->ErrorMsg() );

        while ( $option = $rs->FetchRow() ) {
            if (isset( $options[ $option['name'] ] )) {
                $options[ $option['name'] ] = $option['value'];
                #$options[ $option['name'] ]['value'] = $option['value'];
            }
        }

        // Push the database-defined options back.
        $this->options = $options;

        // If the necessary method is set, fetch dynamic options.
        if (method_exists( $this, '_register_options_dynamic' )) {
            $this->_register_options_dynamic();
        }
    }

    function _register_fields () {

        $this->_register_common('fields');

        $dbcon  = &$this->dbcon;
        $fields = &$this->fields;

        // We only need to worry about the fields attached to this instance,
        // since fields are attached directly to specific instances of plugins.
        $fsql = "SELECT * FROM userdata_plugins_fields " .
                "WHERE plugin_id = " . $dbcon->qstr( $this->plugin_instance );

        $rs = $this->dbcon->CacheExecute( $fsql )
                or die( "Error retrieving plugin data from database: " . $dbcon->ErrorMsg() );

        while ( $field = $rs->FetchRow() ) {

            if (isset( $fields[ $field['name'] ] )) {
                $fields[ $field['name'] ] = $field;
            }
        }

        if ( method_exists( $this, '_register_fields_dynamic' ) ) {
            $this->_register_fields_dynamic();
        }
    }

    function _register_common ( $type ) {

        // Fetch all the options. Note that we don't descend more than the
        // parent class, due to some inherent limitations in php. If you're
        // inheriting from a class that's two levels removed from
        // UserDataPlugin, the options won't get pulled in.
        $parent_class      = get_parent_class($this);
        $parent_class_vars = get_class_vars($parent_class);
        if ($parent_class != 'UserDataPlugin') {
            $this->_shallow_replace($type, $parent_class_vars[$type]);
        }

        $this_class      = get_class($this);
        $this_class_vars = get_class_vars($this_class);
        $this->_shallow_replace($type, $this_class_vars[$type]);

    }

    function _shallow_replace ( $merge_to, $merge_from ) {

        $this_merge_to = &$this->$merge_to;
        
        if (!isset($merge_from) || !is_array($merge_from)) return false;

        foreach ( $merge_from as $merge_key => $merge_value ) {

            // If the key doesn't already exist, set it and move on.
            if (!isset($this_merge_to[$merge_key])) {
                $this_merge_to[$merge_key] = $merge_value;
                continue;
            }

            $this_merge_value = &$this_merge_to[$merge_key];

            if (!is_array($this_merge_value) || !is_array($merge_value)) {
                $this_merge_value = $merge_value;
                continue;
            }

            // The array is already set, so now we need to override any
            // key => value pairs. We only do this one level deep; more
            // complicated substitutions can be done by manually modifying the 
            // array in question. Try _register_options_dynamic or
            // _register_fields_dynamic as appropriate.
            foreach ( $merge_value as $key => $value ) {
		if (!is_array($this_merge_value[$key])){
	   	   $this_merge_value[$key] = $value; 
		} else {
                   $this_merge_value[$key]['value'] = $value;
		}
            }
        }
    }

    /*****
     *
     * Plugin Field Definition Methods
     *
     *****/

    function getFields ( $fields = null ) {

        if (isset($fields)) {

            if (!is_array( $fields )) $fields = array( $fields );

            return array_intersect_key( $this->fields, $fields );

        }

        return $this->fields;

    }

    function addFields ( $fields ) {

        $this->fields = array_merge( $this->fields, $fields );

        return $this->fields;

    }

    function addField ( $field ) {

        $this->fields[ $field['id'] ] = $field;

        return $this->fields[ $field['id'] ];

    }

    function removeField ( $field ) {
        $oldval = $this->fields[ $field ];
        unset( $this->fields[ $field ] );
        return $oldval;
    }

    /*****
     *
     * Plugin Data Methods
     *
     * The data (at runtime) is *not* stored within the plugin, but rather in the UDM
     * object's data store (in memory).
     *
     * This is done so that handling form submission is simplified. On plugin
     * execute, the plugin has access to all the form data in any event.
     *
     *****/

    function getData ( $fields = null ) {
        
        //Filter returned data for items with the appropriate plugin field
        //prefix
        if ($this->_field_prefix) {

            $data=$this->udm->getData();
            $prefix=$this->_field_prefix.'_';

            if (isset($fields)) {

                foreach ($fields as $key=>$value) {
                    $returnData[$value]=
                        (isset($data[$prefix.$value])?
                            $data[$prefix.$value]
                            :NULL);
                }

            } else {

                foreach ($data as $key=>$value) {

                    if (substr($key, $prefix)===0) {
                        $returnData[substr($key, strlen($prefix))]=$value;
                    }
                }
            }
            $data=$returnData;

        } else {

            $data=$this->udm->getData( $fields );

        }


        
        return $data; 
    }
    

    function setData ( $data ) {
        if ($this->_field_prefix) {

            foreach ($data as $key=>$value) {
                $plugin_data[$this->_field_prefix.'_'.$key]=$value;
            }

            $data=$plugin_data;
        }

        return $this->udm->setData( $data );
    }

    function getOptions( $options=null ) {
        if (!isset($options)) $options = array_keys($this->options);
        
        if (is_array($options)) {

            foreach ( $options as $option_name ) {
                $option_def=$this->options[$option_name];

                if (isset($option_def['value'])) {
                    $return_options[$option_name]=$option_def['value'];
                } else {

                    if (isset($option_def['default'])) {
                        $return_options[$option_name]=$option_def['default'];
                    }
                }
            }
        }
        if (!isset ($return_options)) return false;

        return $return_options;
    }
        
    function setOptions ( $options ) {

        foreach ( $options as $option_name => $option_value ) {

            if (isset($this->options[$option_name])) {
                if ( is_array( $option_value ) ) {
                    foreach ( $option_value as $key => $value ) {
                        $this->options[$option_name][$key] = $value;
                    }
                } else {
                    $this->options[$option_name]['value'] = $option_value;
                }
            } else {
                continue;
            }
        }

        return true;
    }

}

?>
