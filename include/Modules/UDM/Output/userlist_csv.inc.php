<?php

function udm_output_userlist_csv($udm, $options=null) {
		global $ID, $MM_email_from; // - returns current user variable
		//List Vars
		//create default options array
		$default_options['allow_edit']=TRUE;
		$default_options['display_fields']="*";
		$default_options['default_qty']=50;
		$default_options['sort_by']="Last_Name, First_Name";
		$default_options['page_name']=$_SERVER['PHP_SELF'];
		$default_options['usertable']="userdata";
		#$default_options['Lookups'][]=array("fieldname"=>"modin", "LookupTable"=>"userdata_fields", "LookupField"=>"name");
		$default_options['Lookups']['publish']=array("LookupSet"=>array("0"=>"draft" , "1"=>"live"), 'LookupName'=>'status');
		
		$default_options['show_headers']=TRUE;
		$default_options['allow_lookups']=TRUE;

		$default_options['return_location']="modinput4_data.php?modin=".$udm->instance;

		//pass default options into standard options array when no value exists
		foreach ($default_options as $key=>$this_option) {
			if ($key=='Lookups') {
				foreach ($default_options['Lookups'] as $look_key=>$lookup) {
					if (!isset($options['Lookups'][$look_key])) { $options['Lookups'][$look_key]=$lookup;}
				}
			} else {
				if (!isset($options[$key])) { $options[$key]=$this_option;}
			}
		}

	//check display fields for admin/enabled
	#$options=$udm->_check_fields($options);
	$options['filename']=list_setFileName($udm, $options);
	

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
		$options=list_setupLookups($udm, $options);
	}

	if($options['display_fields']=="*") {$options['display_fields']=list_translateAllFieldsForSql($udm, $options);}
	
	
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