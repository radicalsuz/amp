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

	// $javascript contains an array of javascript tag(s) needed by this plugin
	var $javascript = array();

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
        $this->_register_options_global( );

        $dbcon   = &$this->dbcon;
        $options = &$this->options;

        $osql = "SELECT * FROM userdata_plugins_options " .
                "WHERE plugin_id = " . $dbcon->qstr( $this->plugin_instance );

        $rs = $this->dbcon->CacheExecute( $osql )
                or die( "Error retrieving plugin options from database: " . $dbcon->ErrorMsg() );

        while ( $option = $rs->FetchRow() ) {
            if (isset( $options[ $option['name'] ] )) {
                #$options[ $option['name'] ] = $option['value'];
                $options[ $option['name'] ]['value'] = $option['value'];
            }
        }

        // Push the database-defined options back.
        $this->options = $options;

        // If the necessary method is set, fetch dynamic options.
        if (method_exists( $this, '_register_options_dynamic' )) {
            $this->_register_options_dynamic();
        }
    }

    function _register_options_global( ){
        $plugin_key_parts = split( '_', strtoupper( get_class( $this )));
        $plugin_subclass = array_shift( $plugin_key_parts );
        $plugin_key = join ( '_', array_reverse( $plugin_key_parts ));

		foreach( array_keys($this->options) as $option_name) {
			$option_const = 'AMP_PLUGIN_OPTION_'. $plugin_key .'_'. strtoupper($option_name);
			if(!isset($this->options[$option_name]['value']) && defined($option_const)) {
				$this->options[$option_name]['value'] = constant($option_const);
			}
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

	function _register_javascript ( $javascript, $name = NULL ) {
		if($name) {
			$this->javascript[$name] .= $javascript;
		} else {
			$this->javascript[] .= $javascript;
		}
	}

	function get_javascript() {
		if(!empty($this->javascript)) {
			$javascript = '';
			foreach ($this->javascript as $script) {
				$javascript .= $script;
			}
			return $javascript;
		}
		return false;
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
                if ( !is_array( $this_merge_value[$key] ) ){
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
    function getData ($request_fields = null){

        $udm_request_fields = null;
        if (isset($request_fields)) {
            $udm_request_fields = $this->convertFieldNamestoUDM( $request_fields );
        }

        // get the data 
        $udmdata = $this->udm->getData($udm_request_fields);
        
        $udmdata = array_merge( $udmdata, $this->reinstateBlankCheckBoxValues( $udmdata ) );


        //retain only local-prefixed data
        foreach($udmdata as $keyname => $value) {
            if ( !($localkey = $this->checkPrefix($keyname)) ) continue;
            if ( !isset( $this->udm->fields[ $keyname ] )) continue;

            $data[$localkey] = $this->checkData( $this->udm->fields[$keyname], $value, $keyname );
        }

        if (!empty ($request_fields)) $data = array_combine_key($request_fields, $data);

        return $data;

    }


    function getOptions( $options=null ) {
        if (!isset($options)) $options = array_keys($this->options);
        
        if (!is_array($options)) return false;

        foreach ( $options as $option_name ) {
            $option_def=$this->options[$option_name];

            if (isset($option_def['value'])) {
                $return_options[$option_name]=$option_def['value'];
                continue;
            }

            if (isset($option_def['default'])) $return_options[$option_name]=$option_def['default'];
        }
        if (!isset ($return_options)) return false;

        return $return_options;
    }

    function setOptions ( $options ) {
        if (!is_array($options)) return false;

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


    function checkData( $fDef, $value, $keyname=null ) {

        $translation_method = $fDef['type']."FieldtoText";
        if (method_exists( $this, $translation_method )) {
            return $this->$translation_method( $value, $keyname );
        }
        
        /*
        if ( $fDef[ 'type' ] == 'file' ) {
            return $this->manageUpload( $fDef, $value, $keyname );
        }
        */ 

        return $value;
    }

    function fileFieldtoText( $value, $keyname ) {
        $value_key = $keyname . '_value';
        if (!( isset( $_FILES[ $keyname ][ 'tmp_name' ] ) && $_FILES[ $keyname ]['tmp_name'])) {
            if ( $filevalue = current( $this->udm->getData( $value_key )) ) return $filevalue;
        }
        
        $builder = &$this->udm->getPlugin( 'QuickForm', 'Build');
        $engine = &$builder->getFormEngine( );
        if ( !( $text = $engine->getValues(  array( $keyname )))) return false;
        return current( $text ); 
    }


    function dateFieldtoText( $value, $keyname ) {
        if (!is_array($value)) return $value;

        $month  = isset($value['M'])? $value['M']:(isset($value['m'])?$value['m']:0);
        $day    = isset($value['D'])? $value['D']:(isset($value['d'])?$value['d']:false);
        $year   = isset($value['Y'])? $value['Y']:(isset($value['y'])?$value['y']:0);
        $hour   = isset($value['H'])? $value['H']:0;
        $minute = isset($value['i'])? $value['i']:0;
        $second = isset($value['s'])? $value['s']:0;

        $time_stamped = mktime($hour,$minute,$second,$month,$day,$year);
        if (!$time_stamped) return false;

        if ($day) return   date("Y-m-d",$time_stamped);
        $day=1;
        $time_stamped = mktime($hour,$minute,$second,$month,$day,$year);
        if ($year && $month) return date("m/Y", $time_stamped);

        return $time_stamped;
    }


    function checkgroupFieldtoText ($value, $keyname ) {
        if (!is_array($value)) return $value;
        return join (", ",array_keys($value));
    }

    function multiselectFieldtoText ( $value , $keyname ) {
        if (!is_array($value)) return $value;
        return join (", ", $value);
    }

    //fix checkbox problem - blank checkboxes don't save values
    function reinstateBlankCheckBoxValues( $data, $local = true ) {
        $returnSet = array();
        if (!isset($this->udm)) return false;

        foreach ($this->udm->fields as $fname =>$fDef) {
            if (isset($data[$fname])) continue;
            if ($fDef['type']!='checkbox') continue;
            if ($fDef['public']==false && $this->udm->admin==false) continue;
            $returnSet[$fname]='0';
        }

        return $returnSet;
    }
    
    function uncheckData( $keyname, $value ) {

        if ( !isset( $this->udm->fields[ $keyname ])) return $value;
        $fDef = $this->udm->fields[ $keyname ];
        $types_to_modify = array( "checkgroup" );
        if (array_search($fDef['type'], $types_to_modify)===FALSE) return $value;

        switch ($fDef['type']) { 
            case "checkgroup":
                return $this->expandCheckGroup($value);
                break;
            default:
                return $value;
        }
    }

    function expandCheckGroup ( $value ) {
        $returnSet = array();
        $dataset=split('[ ]?,[ ]?', $value);
        if (!is_array($dataset)) return false;

        foreach ($dataset as $item) {
            $returnSet[$item]=1;
        }
        return $returnSet;
    }
    

    function setData ( $data ) {
        foreach ($data as $key=>$value) {
            $udmkey = $this->addPrefix($key);
            $plugin_data[$udmkey]=$this->uncheckData( $udmkey, $value );
        }

        return $this->udm->setData( $plugin_data );
    }

    function addPrefix( $fieldname ) {
        if (empty($this->_field_prefix)) return $fieldname;
        if (substr($fieldname, 0, strlen($this->_field_prefix)) == $this->_field_prefix ) return $fieldname;
        return $this->_field_prefix .'_'. $fieldname;
    }
    function dropPrefix( $fieldname ) {
        if (empty($this->_field_prefix)) return $fieldname;
        if (substr($fieldname, 0, strlen($this->_field_prefix)) != $this->_field_prefix ) return $fieldname;
        return substr($fieldname, strlen($this->_field_prefix) + 1);
    }

    //check to see if the field is local, if so return the local fieldname
    function checkPrefix ( $fieldname ) {
        $dropped = $this->dropPrefix( $fieldname );
        if ($dropped == $fieldname && (!empty($this->_field_prefix))) return false;
        return $dropped;
    }

    function getPrefix( ){
        return $this->_field_prefix;
    }

    function convertFieldNamestoUDM( $fields=null, $use_keys=FALSE ) {
        $udmFields = array();
        if (!isset($fields)) {
            $fields = array_keys($this->fields);
        }

        if ($use_keys) $fields = array_keys($fields);
        if (empty($this->_field_prefix)) return $fields;


        foreach ($fields as $fieldname) {
            $udmFields[] = $this->addPrefix($fieldname);
        }
        return $udmFields;
    }

    function convertFieldNamesfromUDM ( $udmfields=null ) {
        $localFields = array();

        if (!isset($udmfields)) {
            $udmfields = array_keys($this->udm->fields);
        }

        if (!is_numeric( key($udmfields) )) $fields = array_keys($udmfields);


        foreach ($udmfields as $fieldname) {
            if (!$this->checkPrefix($fieldname)) continue;
            $localFields[] = $this->dropPrefix( $fieldname );
        }
        return $localFields;
    }

    function convertFieldDefstoDOM ( $fields=null ) {
        $domFields = array();
        
        foreach ($fields as $fieldname => $fDef) {
            $element = $this->returnDOMElement( $fieldname, $fDef );                
            
            if (!is_array($element)) {
                $domFields[] = $this->addPrefix($element);
                continue;
            }

            foreach ($element as $DOMelement) {
                $domFields[] = $this->addPrefix($DOMelement);
            }
        }
        return $domFields;
    }

    function getDateFormat($fDef) {
        $default_format =  array('d','M','Y');
        if (!is_array($fDef['values'])) return $default_format;
        if (!isset($fDef['values']['format'])) return $default_format;

        $format = array();
        for ($n=0;$n<strlen($fDef['values']['format']); $n++) {
            $format[] = $fDef['values']['format'][$n];
        }
        return $format;
    }

    function returnDOMElement( $fieldname, $fDef ) {
        switch ($fDef['type']) {
            case 'date':
                $date_format = $this->getDateFormat( $fDef );
                $fieldname_template = $fieldname."[%s]";
                foreach($date_format as $date_component) {
                    $date_dom[] = sprintf($fieldname_template, $date_component);
                }
                return $date_dom;
                break;
            case 'radiogroup':
            case 'checkgroup':
                $option_set = $this->getValueSet( $fDef );
                if (is_array($option_set)) {
                    return $this->convertFieldNamestoUDM( $option_set );
                }
                break;
            default:
                return $fieldname;
        }

        return $fieldname;
    }

    function insertBeforeFieldOrder( $fields = null, $beforeField = 0 ) {
        $udmFields = $this->convertFieldNamestoUDM($fields);

        if ((!is_numeric($beforeField)) && (isset($this->fields[$beforeField]))) {
            $beforeField = $this->addPrefix($beforeField);
        }

        $this->udm->insertBeforeFieldOrder ( $udmFields, $beforeField );
    }

    function insertAfterFieldOrder( $fields = null ) {
        $udmFields = $this->convertFieldNamestoUDM($fields);
        $this->udm->insertAfterFieldOrder( $udmFields );
    }

    function returnLookup ( $tablename, $displayfield, $valuefield, $restrictions=null) {
        $lookup_sql="Select $valuefield, $displayfield from $tablename";
        if (isset($restrictions)&&$restrictions) {
            $lookup_sql.=" WHERE $restrictions";
        }
        $lookup_sql.=" ORDER BY $displayfield";
        return $this->dbcon->GetAssoc($lookup_sql);
    }

    function getValueSet ( &$field_def ) {
        $defaults = (isset($field_def['values'])) ? $field_def[ 'values' ] : null;
        if (is_array($defaults)) return $defaults;

        $fieldtypes_possessing_valuesets = array( 'select', 'multiselect', 'radiogroup', 'checkgroup' );

        if (array_search( $field_def['type'], $fieldtypes_possessing_valuesets ) === FALSE ) return $defaults;

        // Return region information
        if ( isset( $field_def[ 'region' ] )
                && strlen( $field_def[ 'region' ] ) > 1 ) {

            return $GLOBALS['regionObj']->getSubRegions( $field_def[ 'region' ] );
        }

        // Return a defined index from the DB
        if (is_string( $defaults ) && ( substr($defaults,0,7) == "Lookup(" ) ) {

            $just_values = str_replace(")", "", substr($defaults, 7));
            $valueset = preg_split("/\s?,\s?/", $just_values );
            if (isset($valueset[4])) $field_def['default'] = $valueset[4];
            return $this->returnLookup($valueset[0], $valueset[1], $valueset[2], $valueset[3]);
        }

        // Split string with commas into an array
        // Check to see if we have an array of values.
        $defArray = split( "[ ]?,[ ]?", $defaults );
        if (count( $defArray ) > 1) {
            $defaults = array();
            foreach ( $defArray as $option ) {
                $defaults[ $option ] = $option;
            }
        }

        return $defaults;
    }

    function inForm( $raw_html ) {
        return "<tr><td colspan=2 class = \"form_span_col\">". $raw_html ."</td></tr>\n";
    }

    function saveRegistration( $namespace, $action) {
        $reg_data = array(
            "id" => $this->plugin_instance,
            "instance_id" => $this->udm->instance,
            "namespace" => $namespace, 
            "action" => $action,
            "priority" => 10,
            "active" => 1 );

        $result = $this->dbcon->Replace( 'userdata_plugins', $reg_data, 'id', $quote=true);
        if ($result == ADODB_REPLACE_INSERTED ) $this->plugin_instance = $this->dbcon->Insert_ID();
        if ($result) return true;

        return false;

    }

    function saveOption( $name, $value ) {
		if (! $this->plugin_instance ) die( "you suck" );

        $option_data = array(
            "plugin_id" => $this->plugin_instance,
            "name" => $name,
            "value" => $value );
        $primary = array( 'plugin_id', 'name' );
        $result = $this->dbcon->Replace( 'userdata_plugins_options', $option_data, $primary, $quote=true);
        if ($result == ADODB_REPLACE_INSERTED ) $this->plugin_instance = $this->dbcon->Insert_ID();
        if ($result) return true;

        return false;
    }

	function deleteRegistration( $namespace, $action ) {
		if (!$this->plugin_instance) return false;
		$this->deleteRegisteredOptions();
		$sql = "DELETE FROM userdata_plugins where id = " . $this->plugin_instance;
		$result = $this->dbcon->Execute( $sql );
	}
		

	function deleteRegisteredOptions() {
		if (!$this->plugin_instance) return false;
		$sql = "DELETE FROM userdata_plugins_options where plugin_id = " . $this->plugin_instance;
		$result = $this->dbcon->Execute( $sql );
	}

	function error($message, $level=E_USER_WARNING) {
		trigger_error($message);
	}
}

?>
