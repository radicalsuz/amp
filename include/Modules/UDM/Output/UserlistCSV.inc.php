<?php

/*****
 *
 * AMP UserDataModule CSV Export
 *
 * Creates an CSV Export File based on the contents of
 * an UDM object.
 *
 *****/


require_once( 'AMP/UserData/Plugin.inc.php' );
class UserDataPlugin_UserlistCSV_Output extends UserDataPlugin {

    // A little bit of friendly information about the plugin.
    var $short_name  = 'ExportFile';
    var $long_name   = 'Export List in CSV Format';
    var $description = 'Use this to set options for the CSV export plugin';
    //List Vars
    //create default options array
    var $options=array(
        'allow_edit'=>TRUE,
        'display_fields'=>"*",
        'default_qty'=>50,
        'sort_by'=>"Last_Name, First_Name",
        'usertable'=>"userdata",
        'show_headers'=>TRUE,
        'allow_lookups'=>TRUE
    );

    function UserDataPlugin_UserlistCSV_Output ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );

    }

    function execute ( $options = null ) {

        if (isset($options)) $this->_shallow_replace('options', $options);
        return udm_output_userlist_csv( $this->udm, $this->options );

    }

    function _register_options_dynamic() {

		global $MM_email_from; // - returns current user variable
        $options['return_location']="modinput4_data.php?modin=>".$udm->instance;
        $options['page_name']=$_SERVER['PHP_SELF'];
        $options['Lookups']['publish']=array("LookupSet"=>array("0"=>"draft" , "1"=>"live"), 'LookupName'=>'status');
		
        //check display fields for admin/enabled
        #$options=$udm->_check_fields($options);
        $options['filename']=list_setFileName($this->udm, $this->options);
        

        if ($options['allow_include_modins']&&is_array($_REQUEST['UDM_include_modins'])) {
            $options['include_modin']=join(",", $_REQUEST['UDM_include_modins']);
        }
        if ($options['allow_include_modins']&&($_REQUEST['UDM_include_all_modins']==true)) {
            $options['include_modin']="*";
        }


        if ($options['allow_include_modins']&&isset($options['include_modin'])) {
            $options['Lookups']['modin']['LookupName']="Source";
            $options['Lookups']['modin']['LookupTable']="userdata_fields";
            $options['Lookups']['modin']['LookupField']="name";
        }

        if ($options['allow_lookups']) {
            $options=list_setupLookups($this->udm, $options);
        }

        if($this->options['display_fields']=="*") {$options['display_fields']=list_translateAllFieldsForSql($this->udm, $options);}
        
        $this->_shallow_replace('options', $options);
        
     }
}




function udm_output_userlist_csv($udm, $options=null) {
    global $ID, $MM_email_from; // - returns current user variable

	
	$udm->set_sql['from']=$options['usertable'];
	$udm->set_sql['where']="(modin=".$udm->instance;
	if ($options['allow_include_modins']&&isset($options['include_modin'])) {
		$udm->set_sql['where'].= ($options['include_modin']=="*")?" OR modin !=".$udm->instance:" OR modin in(".$options['include_modin'].") ";
	} 
	$udm->set_sql['where'].=")";
	$udm->set_sql['select']=$options['display_fields'].$options['hidden_fields'];
	$udm->set_sql['orderby']=$options['sort_by'];


	if ($udm->getSet($options)) {
		$output=list_export($udm, $options, $_REQUEST['id']);
		#$output.=list_output_dynamic($udm, $options);
		#$output.="</form>";
	} else {
		$output="This Module is currently empty";
	}
	if ($udm->authorized) {
		return $output;
	} else {
		return 'You do not have permission to view this list';
	}
}


	function list_translateallFieldsForSql(&$udm, &$options){
		if ( isset( $udm->_module_def[ 'field_order' ] ) ) {
    
		    $fieldOrder = split( ',', $udm->_module_def[ 'field_order']  );
			if ($options['include_id_column']&&$udm->admin) { $fieldOrder[]='id';}
			if (isset($options['include_modin_column'])) { $fieldOrder[]='modin';}
			foreach ( $fieldOrder as $field ) {
				$field = trim( $field );
				if (strlen($field)>0&&$udm->fields[$field]['type']!='static'&&$udm->fields[$field]['type']!='header'){
					if ($options['allow_lookups']&&isset($options['Lookups'][$field])) {
							if($this_lookup=list_makeLookupsforSql($udm, $options, $field)) {
								$output.=$this_lookup;
							}
					} else {	
						if ($udm->admin) {
							$output.= $field." AS ".$udm->dbcon->qstr(list_translateFields($field, $udm, $options)).", "; 
						} else {
							if ($udm->fields[$field]['public']) {
								$output.= $field." AS ".$udm->dbcon->qstr(list_translateFields($field, $udm, 	$options)).", "; 
							}
						}
					}
				}
				$finishedElements[ $field ] = 1;
			}
		}
    
		foreach ( $udm->fields as $field => $field_def ) {
    
			// Skip fields that have already been added.
			if ( isset( $finishedElements[ $field ] ) ) continue;
			$output.=$field." AS ".$udm->dbcon->qstr(str_replace('"', '_', list_translateFields($field, $udm, $options))).", ";
    
		}
		if (strlen($output)>5) { $output=substr($output, 0, strlen($output)-2);}
		return $output;
	}
		
	
function list_makeLookupsforSQL(&$udm, $options, $field) {
	if (isset($options['Lookups'][$field]['LookupSet'])) {
		#print $field."<BR>";
		$lookup=$options['Lookups'][$field]['LookupSet'];
		ksort($lookup);
		end($lookup);
		for ($n=1; $n<key($lookup); $n++) {
			if (isset($lookup[$n])) {
				$lookup_str.= $udm->dbcon->qstr($lookup[$n]).", ";
			} else {
				$lookup_str.="' ', ";
			}
		}
		if (strlen($lookup_str)>3) { $lookup_str=substr($lookup_str, 0, strlen($lookup_str)-2);}
		$lookup_str="ELT(".$field.", ".$lookup_str.") AS ".$options['Lookups'][$field]['LookupName'].", ";
	} else {
		return FALSE;
	}
	return $lookup_str;
}


	// converts system fieldnames to UDM-assigned fieldnames
/*
	function list_translateFields($fieldname, &$udm, $options) {
		$returnField=strip_tags($udm->fields[$fieldname]['label']);
		if ($options['allow_lookups']) {
			if (isset($options['Lookups'][$fieldname]['LookupName'])){
				$returnField=$options['Lookups'][$fieldname]['LookupName'];
			}
		}

		if ($returnField==NULL) {$returnField=$fieldname;}
		return $returnField;
	}
*/

if (!function_exists('list_setupLookups')) {
	//retrieves Lookup values from database tables and stores them in the options array
	function list_setupLookups(&$udm, $options) {
		if (is_array($options['Lookups'])) {
			foreach($options['Lookups'] as $key=>$this_lookup) {
				if (isset($this_lookup['LookupTable'])) {
					$options['Lookups'][$key]['LookupSet']=$udm->dbcon->GetAssoc( "Select id, ".$this_lookup['LookupField']." FROM ".$this_lookup['LookupTable']);
				}
			}
		}
		return $options;
	}
}


if (!function_exists('list_translateFields')){

	// converts system fieldnames to UDM-assigned fieldnames

	function list_translateFields($fieldname, &$udm, $options) {
		$returnField=strip_tags($udm->fields[$fieldname]['label']);
		if ($options['allow_lookups']) {
			if (isset($options['Lookups'][$fieldname]['LookupName'])){
				$returnField=$options['Lookups'][$fieldname]['LookupName'];
			}
		}

		if ($returnField==NULL) {$returnField=$fieldname;}
		return $returnField;
	}
}

function list_setFileName(&$udm, $options){
	$file = ereg_replace ("'", "" ,$udm->name);
	$file = ereg_replace (",", "" ,$file);
	$file = ereg_replace (" ", "_" ,$file);
	return $file.'.csv';
	
}





//export a set of ids
function list_export(&$udm, $options, $ids) {
	global $base_path;
	#require_once('CSV/CSV.php');
	require_once('adodb/toexport2.inc.php');
	if (is_array($ids)){
		$udm->set_sql['where'].=" and id IN(".join(",", $ids).") ";
	}
	if($rs=$udm->returnRS()) {
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=".$options['filename']);
		$output= rs2csv($rs);
	} 
	return $output;
}


?>
