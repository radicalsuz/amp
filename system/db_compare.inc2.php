<?php

class dbDefinition {
	var $tableSet;
	var $fieldSet;
	var $fieldSet_extended;
	var $dbOutput;
	var $dbname;
	var $dbCompareInfo;
	var $tables_missing;
	var $tables_found;
	var $tables_common;
	var $fields_missing;
	var $fields_found;
	var $fields_common;
	var $fields_newtype;
	var $recordcount;

	function dbDefinition(){
	}

	function dbLoadDef($dbcon, $makename) {
		$this->fieldSet=array();
		$this->tableSet = $dbcon->MetaTables('TABLES');
		foreach ($this->tableSet as $table) {
			$this->fieldSet[$table] = $dbcon->MetaColumns( $table, FALSE );
			foreach ($this->fieldSet[$table] as $fieldobj) {
				$this->fieldSet_extended[$table][$fieldobj->name]=$fieldobj->type.$fieldobj->max_length;
			}
	#		$temp_recordset=$dbcon->Execute("SELECT * from $table");
	#		$this->recordcount[$table]=$temp_recordset->RecordCount();
		}
		$this->dbname = $makename;
		return $this;
	}

	function dbtable_Structure($currentTable, $dbdef) {
		//display Database ->Table->All Fields & Characteristics
		#$fieldSet=$dbcon->Metacolumns($currentTable, FALSE);
		$fieldSet=$dbdef->fieldSet[$currentTable];
		$this->dboutput['TablesNotFound'][$dbdef->dbname] .= "Non-template table found: <B>".$currentTable."</b> in database ".$dbdef->dbname." has <B>".count($fieldSet)."</B> fields &nbsp;&nbsp;&nbsp;No matching table in template<BR>\r\n";
		$this->dboutput['TablesNotFound'][$dbdef->dbname] .="<table><tr><Td colspan=2>Fields from $currentTable</td></tr><TR><TD>FIELDNAME</TD><TD>TYPE</TD></TR>";
		foreach ($fieldSet as $fieldobj){
			$this->dboutput['TablesNotFound'][$dbdef->dbname] .="<tr><td>".$fieldobj->name."</td><td>".$fieldobj->type."</td></tr>";
		}
		$this->dboutput['TablesNotFound'][$dbdef->dbname] .="</table><BR>\r\n";
	}

	function dbFindField ($currentTable, $fieldobj, $dbdef) {
		//Checks for a field in the master template and returns messages if field does not match master
		$fieldfound=FALSE;
		foreach ($this->fieldSet[$currentTable] as $master_fields){ 
			if ($master_fields->name==$fieldobj->name){  
			$fieldfound=TRUE ;
				if (($master_fields->type.$master_fields->max_length) != ($fieldobj->type.$fieldobj->max_length)) {
					$this->dboutput[$currentTable][$dbdef->dbname] .= "Field Type mismatch: <B>".$fieldobj->name."</B> in table <B>".$currentTable."</b> in database ".$this->dbname." is of type <B>".$master_fields->type."(".$master_fields->max_length.")</B><BR>\r\n";
				}
			}
		}
		if (!$fieldfound) {
			$this->dboutput[$currentTable][$dbdef->dbname] .= "Non-template field found: <B>".$fieldobj->name."</B> in table <B>".$currentTable."</b> in database ".$dbdef->dbname." is of type <B>".$fieldobj->type."</B>&nbsp;&nbsp;&nbsp;No matching field in template<BR>\r\n";
		}
#		return $fieldfound;
	}


	function dbCompareTable ($currentTable, $dbdef) {
	
		#$fieldSet=$dbcon->Metacolumns($currentTable, FALSE);
		$fieldSet=$dbdef->fieldSet[$currentTable];
		$masterSet = $this->fieldSet[$currentTable];
		//begin field loop
		foreach ($fieldSet as $fieldobj){
		//Look for field in master and check data type
			$this->dbFindField($currentTable, $fieldobj, $dbdef);
		} //end of field loop
		//check for fields present in master but not in local
		foreach($masterSet as $master_fields) {
			$fieldfound=FALSE;
			foreach ($fieldSet as $fieldobj) {
				if ($fieldobj->name==$master_fields->name) {$fieldfound=TRUE;}
			}
			if (!$fieldfound){
				$this->dboutput[$currentTable][$dbdef->dbname] .= "Template field missing: No copy of <B>".$master_fields->name."</b>, type <B>".$master_fields->type."</b>, in table $currentTable, exists in database ".$dbdef->dbname."<BR>\r\n";
			}
		}
#		return $output;

	}

	function dbCheckTable ($currentTable, $dbdef) {
		$tablefound=FALSE;
		//begin table loop
		foreach($this->tableSet as $tablename){
			if ($tablename==$currentTable) {$tablefound=TRUE;}
		}
		if ($tablefound) {
			$this->dbCompareTable($currentTable, $dbdef);
		} else {
			$this->dbtable_Structure($currentTable, $dbdef);	
		}
	}
	
	function dbCheckTables($dbdef) {
		//Check for tables present in called set and not in secondary set
		$this->tables_missing[$dbdef->dbname] =array_diff($this->tableSet, $dbdef->tableSet);
		$this->tables_found[$dbdef->dbname]=array_diff($dbdef->tableSet, $this->tableSet);
		
		$this->tables_common[$dbdef->dbname] = array_intersect($this->tableSet, $dbdef->tableSet);
		
		foreach ($this->tables_common[$dbdef->dbname] as $currentTable) {
			$master_fieldnameSet = array_keys($this->fieldSet_extended[$currentTable]);
			$client_fieldnameSet = array_keys($dbdef->fieldSet_extended[$currentTable]);
			$this->fields_missing[$dbdef->dbname][$currentTable]=array_diff($master_fieldnameSet, $client_fieldnameSet);
			
		
			$this->fields_found[$dbdef->dbname][$currentTable]=array_diff($client_fieldnameSet, $master_fieldnameSet);
			$this->fields_common[$dbdef->dbname][$currentTable]=array_intersect($client_fieldnameSet, $master_fieldnameSet);
			$temp_fields_newtype = array_diff_assoc($dbdef->fieldSet_extended[$currentTable], $this->fieldSet_extended[$currentTable]);
			$debug_flag=FALSE;
			
			foreach (array_keys($temp_fields_newtype) as $temp_field) {
				if (in_array($temp_field, $this->fields_common[$dbdef->dbname][$currentTable])){
					$this->fields_newtype[$dbdef->dbname][$currentTable][$temp_field]=$temp_fields_newtype[$temp_field];
				}
			}
		}

	}
	


}

?>
