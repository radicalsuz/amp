<?php


###########################################
###Functions to insert, update and delte items from teh database
###########################################

// *** Update Record: construct a sql update statement and execute it
function update_record($MM_editTable,$MM_recordId,$MM_fieldsStr,$MM_columnsStr,$MM_editRedirectUrl=null,$MM_editColumn ="id"){
	global $dbcon;
	set_time_limit(0); 
	
	$MM_fields = Explode("|", $MM_fieldsStr);
	$MM_columns = Explode("|", $MM_columnsStr);

	// set the form values
	for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
		$MM_fields[$i+1] = $GLOBALS[$MM_fields[$i]];
	}

	// create the sql update statement
	$MM_editQuery = "update " . $MM_editTable . " set ";
	for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
	
		$formVal = $MM_fields[$i+1];
		$MM_typesArray = Explode(",", $MM_columns[$i+1]);
		$delim =    ($MM_typesArray[0] != "none") ? $MM_typesArray[0] : "";
		$altVal =   ($MM_typesArray[1] != "none") ? $MM_typesArray[1] : "";
		$emptyVal = ($MM_typesArray[2] != "none") ? $MM_typesArray[2] : "";
	
		if ($formVal == "" || !isset($formVal)) {
			$formVal = $emptyVal;
		} else {
	
			if ($altVal != "") {
				$formVal = $altVal;
			} elseif ($delim == "'") { 
				//deal with magic qoutes
	   			if (!MAGIC_QUOTES_ACTIVE) {
					$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				} else {
					$formVal = "'" .$formVal . "'";
				}
			} else {
				//done with magic quotes 
				$formVal = $delim . $formVal . $delim;
			}
	
		}
	
		if ($i != 0) {
			$MM_editQuery = $MM_editQuery . ", " . $MM_columns[$i] . " = " . $formVal;
		} else {
			$MM_editQuery = $MM_editQuery . $MM_columns[$i] . " = " . $formVal;
		}
	}
	
	$MM_editQuery = $MM_editQuery . " where " . $MM_editColumn . " = " . $MM_recordId;
	
	$queryrs = $dbcon->Execute($MM_editQuery) or DIE($dbcon->ErrorMsg());
	
	if ($MM_editRedirectUrl) {
			header ("Location: $MM_editRedirectUrl");
	}		 
	return $MM_recordId;
}

// *** Delete Record: construct a sql delete statement and execute it
function delete_record($MM_editTable,$MM_recordId,$MM_editRedirectUrl=null,$MM_editColumn="id") {
	global $dbcon;
	
	$MM_editQuery = "delete from " . $MM_editTable . " where " . $MM_editColumn . " = " . $MM_recordId;

	$queryrs = $dbcon->Execute($MM_editQuery) or DIE($dbcon->ErrorMsg());
	if ($MM_editRedirectUrl) {
		header ("Location: $MM_editRedirectUrl");
	}		 
	
}
  
// *** Insert Record: construct a sql insert statement and execute it
function insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr, $MM_editRedirectUrl=NULL) {
	global $dbcon;
	
	$MM_fields = Explode("|", $MM_fieldsStr);
	$MM_columns = Explode("|", $MM_columnsStr);
    
// set the form values
	for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
		$MM_fields[$i+1] = $GLOBALS[$MM_fields[$i]];
		//echo $MM_fields[$i+1];
	}

	// create the sql insert statement
	$MM_tableValues = "";
	$MM_dbValues = "";
	
	for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
	
		$formVal = $MM_fields[$i+1];
		$MM_typesArray = explode(",", $MM_columns[$i+1]);
	
		$delim = $MM_typesArray[0];
		if ($delim=="none") $delim="";
	
		$altVal = $MM_typesArray[1];
		if ($altVal=="none") $altVal="";
	
		$emptyVal = $MM_typesArray[2];
		if($emptyVal=="none") $emptyVal="";
	
		if ($formVal == "" || !isset($formVal)) {
			$formVal = $emptyVal;
		} else {
	
			if ($altVal != "") {
	
				$formVal = $altVal;
	
			} elseif ($delim == "'") { 
	
		       		//deal with magic qoutes
	 			if (!MAGIC_QUOTES_ACTIVE) {
					$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				} else {
					$formVal = "'" .$formVal . "'";
				}
				//done with magic quotes
			} else {
	
			        $formVal = $delim . $formVal . $delim;
			}
		}
	
		if ($i == 0) {
			$MM_tableValues = $MM_tableValues . $MM_columns[$i];
			$MM_dbValues = $MM_dbValues . $formVal;
		} else {
			$MM_tableValues = $MM_tableValues . "," . $MM_columns[$i];
			$MM_dbValues = $MM_dbValues . "," . $formVal;
		}
	}
	
	$MM_editQuery = "insert into " . $MM_editTable . " (" . $MM_tableValues . ") values (" . $MM_dbValues . ")";
	$db = $dbcon->Execute($MM_editQuery) or DIE("insert".$MM_editQuery.$dbcon->ErrorMsg());
	if ($MM_editRedirectUrl) {
		ampredirect($MM_editRedirectUrl);		
	}
	$lastid = $dbcon->Insert_Id();
	return $lastid;
}

function databaseactions() {
	global $_POST, $MM_editTable, $MM_fieldsStr, $MM_columnsStr, $MM_editRedirectUrl,$MM_editColumn,$MM_recordId;
	if ($_POST[MM_insert]) 	{
		$id =insert_record($MM_editTable, $MM_fieldsStr, $MM_columnsStr, $MM_editRedirectUrl); 
	}
	if (($_POST[MM_update]) & ($_POST[MM_recordId])) {
		$id =update_record($MM_editTable,$MM_recordId,$MM_fieldsStr,$MM_columnsStr,$MM_editRedirectUrl);
	}
	if (($_POST[MM_delete]) & ($_POST[MM_recordId])) {
		delete_record($MM_editTable,$MM_recordId,$MM_editRedirectUrl);	
	}
	return $id;
}

?>
