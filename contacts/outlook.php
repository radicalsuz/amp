<?php

/**
 *
 * outlook.php 
 * import and export to a outlook address book
 *
 **/

#todo: query to fill input_choice


require_once("Connections/freedomrising.php");   # for $dbcon
require_once("fieldmaps.php");   # for outlook_to_mysql and mysql_to_outlook
 

### things to configure ###################

$download = true;  # for testing you can turn off download to view the output.
$header = "headersec.php";

$contacts_table = "contacts2";
$user_field = 'enteredby'; 
$user_table = 'users';

### event handler #########################

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
if ($action == "export")
	do_export();
elseif ($action == "import")
	do_import();
else
	do_form();
	
return;

### functions #############################

function do_form()
{
	global $header, $input_choice, $user_field;
	include($header);

	$users = get_users();
	
	?><h2>Outlook Contacts Import/Export</h2>
	<table>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
	<tr><td>
		<b>Export</b><br>
        <a href="<?=$_SERVER['PHP_SELF']?>?action=export"?>Click here to download 
        a CSV file</a>. This file can be imported into Outlook. <br>
		<?php if (strstr($_SERVER["HTTP_USER_AGENT"], "Mozilla/5.0")) { ?>
			Mozilla Users: You will have to rename the downloaded file
			from "contacts.csv.php" to "contacts.csv".
		<?php } ?>
		<p>
	</td></tr>
	<tr><td>
		<b>Import</b><br>
        You can import a CSV file created by Outlook.<br>
		Who:&nbsp;<select name="<?=$user_field?>">
			<?php foreach($users as $id => $name) {
				echo "<option value=\"$id\">$name</option>";
			} ?>
		</select>
		Select a CSV file:&nbsp;<input type="file" name="csvfile" />
		<input type="hidden" name="action" value="import" />
		<input type="submit" value="Upload" />
		<br>
		Any records in your outlook address book which are new will be
		added to the online database. However, changes made in your outlook
		address book will be ignored if there is already a record with the
		same name in the online	database.
	</td></tr>
	</form>
	</table><?php
}

function do_export()
{
	global $download;
	global $dbcon, $mysql_fields, $outlook_fields;
	global $contacts_table;
	
	$record_sep = "\r\n"; # 
	$field_sep = ",";     # hardcoded for outlook 
	$field_esc = '"';     #
	
	if ($download) {
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=contacts.csv");
	}
	else {
		header("Content-type: text/plain");	
	}
	
	$select_fields = join(", ", $mysql_fields);
	$record_set = $dbcon->Execute("select $select_fields from $contacts_table");

	if ($record_set == null)
		die($dbcon->ErrorMsg());
	
	print join(",",$outlook_fields);
	print $record_sep;

	$count = count($outlook_fields);
	while (!$record_set->EOF)
	{
		for($i=0; $i<$count; $i++)
		{
			if (isset($record_set->fields[$i]) && $record_set->fields[$i] != "")
			{
				print $field_esc;
				# escape the escape char (ie joe "the bomb" smith => joe ""the bomb"" smith)
				print str_replace($field_esc, $field_esc.$field_esc, $record_set->fields[$i]);
				print $field_esc;
			}
			if ($i<$count-1)
				print $field_sep;
		}
		$record_set->MoveNext();
		if (!$record_set->EOF)
			print $record_sep;
	}	
	$record_set->Close();
	$dbcon->Close();
}

function do_import()
{
	global $dbcon, $mysql_fields;
	global $outlook_fields, $outlook_to_mysql;
	global $contacts_table, $header;
	global $user_field;
	
	$enteredby = isset($_REQUEST[$user_field]) ? $_REQUEST[$user_field] : '';
	
	#header("Content-type: text/plain");	
	include($header);
	
	############################
	# GET THE UPLOADED FILE
	
	if (!is_uploaded_file($_FILES['csvfile']['tmp_name']))
	{
		report_error("Error uploading file");
		do_form();
		return;
	}
	$fd = fopen ($_FILES['csvfile']['tmp_name'], "r");
		
	############################
	# PARSE THE UPLOADED FILE
	
	# for each row in the cvs file uploaded
	# put the data in a row of $sql_data
	# mapping the outlook headers in the cvs file to
	# sql headers in the $sql_data array.

	$firstline = trim(fgets($fd, 4096));	
	$headers = split(",",$firstline);
	
	# this array holds the names of all fields which are skipped
	$fields_skipped = array();
	
	$got_data = false;
	$row = 0;
	$column_count = count($headers);
	$sql_data = array();
	while (!feof ($fd)) {
		$data = fgetcsv($fd, 4096);
		// map the outlook columns to mysql columns	
		$sql_data[$row] = array();
		for($column=0; $column<$column_count; $column++) {
			if (!isset($outlook_to_mysql[$headers[$column]])) {
				$fields_skipped[$headers[$column]] = true;
				continue; # skip reverse mapping which are not defined
			}
			$mysql_column = $outlook_to_mysql[$headers[$column]];
			$sql_data[$row][$mysql_column] = addslashes($data[$column]);
			$got_data = true;
		}
		$row++;
	}
	fclose ($fd);
	
	if (!$got_data)	{
		report_error("Error: no data found in the uploaded file!");
		do_form();
		return;
	}
	
	if (!empty($fields_skipped)) {
		report_success("Note: these fields are being skipped, because there is no defined mapping for them:<br>"
		. join(", ", array_keys($fields_skipped)) . ".") . "<br>";
	}
	

	####################################
	# COMMIT THE DATA TO THE DATABASE
	
	# this routine is very simple and incredibly inefficient
	# which is ok as long as the database does not grow to thousands.
	# algorithm:
	# - load every name from the database into memory
	# - eliminate all rows in $sql_data where the first name and
	#   last name are already in the database.
	# - commit the remaining rows of $sql_data
	
	# get the current names from the database
	# we trim away whitespace and cat the two names together
		
	$names = array();
	$query = "SELECT CONCAT(TRIM(FirstName),TRIM(LastName)) FROM `$contacts_table` WHERE 1;";
	$record_set = $dbcon->Execute($query);
	if ($record_set == null)
		die($dbcon->ErrorMsg());
	while (!$record_set->EOF)
	{
		if (isset($record_set->fields[0]))
			$names[$record_set->fields[0]] = true;
		$record_set->MoveNext();
	}	
	$record_set->Close();
	
	# loop over each row in the uploaded data
	# insert into database if the names are different
	# (the data in $sql_data has already had addslashes called on it)
	
	$newdata = false;
	foreach($sql_data as $row)
	{
		$name = trim($row['FirstName']).trim($row['LastName']);
		if ( $name=="" || isset($names[$name]) )
			continue; # skip over names which already exist
		
		foreach($row as $column => $value) {
			if ($value=='')
				unset($row[$column]);
			else
				$row[$column] = "'$value'";
		}		
		if ($enteredby!='')
			$row[$user_field] = $enteredby;
		$columns = join(",", array_keys($row));
		$values  = join(",", array_values($row));
		$query = "INSERT INTO $contacts_table ($columns) VALUES($values);";
		#echo "$query\n";
		
		$ok = $dbcon->Execute($query);
		if (!$ok)
			die($dbcon->ErrorMsg());
		else
			$newdata = true;
			
		report_success("added new record for $row[FirstName] $row[LastName].");
	}	
	if (!$newdata)
		report_success("No action performed: the uploaded file contained no new records.");
		
	$dbcon->Close();
}

# returns array mapping user id => name
function get_users()
{
	global $dbcon;
	global $user_table;
		
	$sql = "SELECT id, name FROM $user_table;";
	$record_set = $dbcon->Execute($sql);
	if ($record_set == null)
		die($dbcon->ErrorMsg());
	$users = array();
	while (!$record_set->EOF)
	{
		$users[$record_set->fields[0]] = $record_set->fields[1];
		$record_set->MoveNext();
	}	
	$record_set->Close();
	return $users;
}

function report_error($msg)
{
	print "<font color=red>$msg</font><br>";
}

function report_success($msg)
{
	print "<font color=green>$msg</font><br>";
}

?>
