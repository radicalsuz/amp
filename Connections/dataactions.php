<?php // *** Update Record: construct a sql update statement and execute it
  set_time_limit(0); 
//check action globals
if (!isset($MM_update)&&isset($_POST['MM_update'])&&$_POST['MM_update']){
$MM_update=$_POST['MM_update'];
} else {
#print 'mm_update set or post not set';

}

 if (!isset($MM_insert)&&isset($_POST['MM_insert'])&&$_POST['MM_insert']) $MM_insert=$_POST['MM_insert'];
if (!isset($MM_delete)&&isset($_POST['MM_delete'])&&$_POST['MM_delete']) $MM_delete=$_POST['MM_delete'];
if (!isset($MM_recordId)&&isset($_POST['MM_recordId'])&&$_POST['MM_recordId']) $MM_recordId=$_POST['MM_recordId'];


 if (isset($MM_update) && (isset($MM_recordId))) {
	// create the sql update statement
	$MM_editQuery = "update " . $MM_editTable . " set ";
	for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) 
	{
		$formVal = $MM_fields[$i+1];
		$MM_typesArray = Explode(",", $MM_columns[$i+1]);
		$delim =    ($MM_typesArray[0] != "none") ? $MM_typesArray[0] : "";
		$altVal =   ($MM_typesArray[1] != "none") ? $MM_typesArray[1] : "";
		$emptyVal = ($MM_typesArray[2] != "none") ? $MM_typesArray[2] : "";
		if ($formVal == "" || !isset($formVal)) 
		{
			$formVal = $emptyVal;
		} 
		else 
		{
			if ($altVal != "") 
			{
				$formVal = $altVal;
			} 
			//deal with magic qoutes
			else if ($delim == "'") 
			{ 
         	if (!MAGIC_QUOTES_ACTIVE)
				{
				$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				}
				else
				{
				$formVal = "'" .$formVal . "'";
				}
				} 
			//done with magic quotes 
			else 
			{
				$formVal = $delim . $formVal . $delim;
			}
		}
		if ($i != 0)
			{
				$MM_editQuery = $MM_editQuery . ", " . $MM_columns[$i] . " = " . $formVal;
			}
			else
			{
				$MM_editQuery = $MM_editQuery . $MM_columns[$i] . " = " . $formVal;
			}
		}

	$MM_editQuery = $MM_editQuery . " where " . $MM_editColumn . " = " . $MM_recordId;

	if ($MM_abortEdit != 1)
	{
		// execute the insert
		$queryrs = $dbcon->Execute($MM_editQuery) or DIE( "Couldn't execute $MM_editQuery: " . $dbcon->ErrorMsg());
		if ($MM_editRedirectUrl) 
		{
			header ("Location: $MM_editRedirectUrl");
		}		 
	}
}

  // *** Delete Record: construct a sql delete statement and execute it
  if (isset($MM_delete) && (isset($MM_recordId))) {
    $MM_editQuery = "delete from " . $MM_editTable . " where " . $MM_editColumn . " = " . $MM_recordId;
    if ($MM_abortEdit!=1) {
      $queryrs = $dbcon->Execute($MM_editQuery) or DIE( "Couldn't execute $MM_editQuery: " . $dbcon->ErrorMsg());
      if ($MM_editRedirectUrl) {
        header ("Location: $MM_editRedirectUrl");
      }		 
    }
  }
  
// *** Insert Record: construct a sql insert statement and execute it
if (isset($MM_insert) && $MM_insert) {
   // create the sql insert statement
  $MM_tableValues = "";
  $MM_dbValues = "";
  for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $formVal = $MM_fields[$i+1];
    $MM_typesArray = explode(",", $MM_columns[$i+1]);
    $delim = $MM_typesArray[0];
    if($delim=="none") $delim="";
    $altVal = $MM_typesArray[1];
    if($altVal=="none") $altVal="";
    $emptyVal = $MM_typesArray[2];
    if($emptyVal=="none") $emptyVal="";
    if ($formVal == "" || !isset($formVal)) {
      $formVal = $emptyVal;
    }
    else {
      if ($altVal != "") {
        $formVal = $altVal;
      }			//deal with magic qoutes
			else if ($delim == "'") 
			{ 
         	if (!MAGIC_QUOTES_ACTIVE)
				{
				$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				}
				else
				{
				$formVal = "'" .$formVal . "'";
				}
				} 
			//done with magic quotes

      else {
        $formVal = $delim . $formVal . $delim;
      }
    }
    if ($i == 0) {
      $MM_tableValues = $MM_tableValues . $MM_columns[$i];
      $MM_dbValues = $MM_dbValues . $formVal;
    }
    else {
      $MM_tableValues = $MM_tableValues . "," . $MM_columns[$i];
      $MM_dbValues = $MM_dbValues . "," . $formVal;
    }
  }
  $MM_editQuery = "insert into " . $MM_editTable . " (" . $MM_tableValues . ") values (" . $MM_dbValues . ")";
  if ($MM_abortEdit!=1) {
    // execute the insert
    $queryrs = $dbcon->Execute($MM_editQuery) or DIE( "Couldn't execute $MM_editQuery: " . $dbcon->ErrorMsg());
    if ($MM_editRedirectUrl) {
    	  header ("Location: $MM_editRedirectUrl");		
    }
  }
}

// work flow update

if (isset($WF_update) && (isset($MM_recordId))) {
  
	// create the sql update statement
	$MM_editQuery = "update " . $MM_editTable . " set ";
	for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) 
	{
		$formVal = $MM_fields[$i+1];
		$MM_typesArray = Explode(",", $MM_columns[$i+1]);
		$delim =    ($MM_typesArray[0] != "none") ? $MM_typesArray[0] : "";
		$altVal =   ($MM_typesArray[1] != "none") ? $MM_typesArray[1] : "";
		$emptyVal = ($MM_typesArray[2] != "none") ? $MM_typesArray[2] : "";
		if ($formVal == "" || !isset($formVal)) 
		{
			$formVal = $emptyVal;
		} 
		else 
		{
			if ($altVal != "") 
			{
				$formVal = $altVal;
			} 
			//deal with magic qoutes
			else if ($delim == "'") 
			{ 
         	if (!MAGIC_QUOTES_ACTIVE)
				{
				$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				}
				else
				{
				$formVal = "'" .$formVal . "'";
				}
				} 
			//done with magic quotes 
			else 
			{
				$formVal = $delim . $formVal . $delim;
			}
		}
		if ($i != 0)
			{
				$MM_editQuery = $MM_editQuery . ", " . $MM_columns[$i] . " = " . $formVal;
			}
			else
			{
				$MM_editQuery = $MM_editQuery . $MM_columns[$i] . " = " . $formVal;
			}
		}

	$MM_editQuery = $MM_editQuery . " where " . $MM_editColumn . " = " . $MM_recordId;

	if ($MM_abortEdit != 1)
	{
		// execute the insert
		$queryrs = $dbcon->Execute($MM_editQuery) or DIE( "Couldn't execute $MM_editQuery: " . $dbcon->ErrorMsg());
		if ($MM_editRedirectUrl) 
		{
			header ("Location: $MM_editRedirectUrl");
		}		 
	}
}
// *** WORK FLOW Insert Record: construct a sql insert statement and execute it
if (isset($WF_insert)) {
   // create the sql insert statement
  $MM_tableValues = "";
  $MM_dbValues = "";
  for ( $i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $formVal = $MM_fields[$i+1];
    $MM_typesArray = explode(",", $MM_columns[$i+1]);
    $delim = $MM_typesArray[0];
    if($delim=="none") $delim="";
    $altVal = $MM_typesArray[1];
    if($altVal=="none") $altVal="";
    $emptyVal = $MM_typesArray[2];
    if($emptyVal=="none") $emptyVal="";
    if ($formVal == "" || !isset($formVal)) {
      $formVal = $emptyVal;
    }
    else {
      if ($altVal != "") {
        $formVal = $altVal;
      }
			//deal with magic qoutes
			else if ($delim == "'") 
			{ 
         	if (!MAGIC_QUOTES_ACTIVE)
				{
				$formVal = "'" . str_replace("'","\'",$formVal) . "'";
				}
				else
				{
				$formVal = "'" .$formVal . "'";
				}
				} 
			//done with magic quotes

      else {
        $formVal = $delim . $formVal . $delim;
      }
    }
    if ($i == 0) {
      $MM_tableValues = $MM_tableValues . $MM_columns[$i];
      $MM_dbValues = $MM_dbValues . $formVal;
    }
    else {
      $MM_tableValues = $MM_tableValues . "," . $MM_columns[$i];
      $MM_dbValues = $MM_dbValues . "," . $formVal;
    }
  }
  $MM_editQuery = "insert into " . $MM_editTable . " (" . $MM_tableValues . ") values (" . $MM_dbValues . ")";
  if ($MM_abortEdit!=1) {
    // execute the insert
    $queryrs = $dbcon->Execute($MM_editQuery) or DIE( "Couldn't execute $$MM_editQuery: " . $dbcon->ErrorMsg());
  
  }
}


?>
