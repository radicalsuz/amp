<?php
  require("Connections/freedomrising.php");
  include ("header.php") ;
  ?>
  <h2>User Data Module Import</h2>
  <?php
  $download = true;  # for testing you can turn off download to view the output.
$header = "headersec.php";
$outlook_to_mysql = array(
	"$_POST[Organization]" => "Organization",
	"$_POST[FirstName]" => "FirstName",
	"$_POST[LastName]" => "LastName",
	"$_POST[EmailAddress]" => "EmailAddress",
	"$_POST[Phone]" => "Phone",
	"$_POST[Fax]" => "Fax",
	"$_POST[Address]" => "Address",
	"$_POST[Address2]" => "Address2",
	"$_POST[City]" => "City",
	"$_POST[State]" => "State",
	"$_POST[PostalCode]" => "PostalCode",
	"$_POST[Country]" => "Country",
	"$_POST[WebPage]" => "WebPage",
	"$_POST[notes]" => "notes",
	"$_POST[field1text]"=>"field1",
	"$_POST[field2text]"=>"field2",
	"$_POST[field3text]"=>"field3",
	"$_POST[field4text]"=>"field4",
	"$_POST[field5text]"=>"field5",
	"$_POST[field6text]"=>"field6",
	"$_POST[field7text]"=>"field7",
	"$_POST[field8text]"=>"field8",
	"$_POST[field9text]"=>"field9",
	"$_POST[field10text]"=>"field10",
	"$_POST[field11text]"=>"field11",
	"$_POST[field12text]"=>"field12",
	"$_POST[field13text]"=>"field13",
	"$_POST[field14text]"=>"field14",
	"$_POST[field15text]"=>"field15",
	"$_POST[field16text]"=>"field16",
	"$_POST[field17text]"=>"field17",
	"$_POST[field18text]"=>"field18",
	"$_POST[field19text]"=>"field19",
	"$_POST[field20text]"=>"field20",
	"$_POST[publish]"=>"publish",
	"$_POST[region]"=>"region"
	
);

$daction = $_POST[daction];
$mysql_to_outlook = array_flip($outlook_to_mysql);
$mysql_fields     = array_values($outlook_to_mysql);
$outlook_fields   = array_keys($outlook_to_mysql);
$outlook_to_id    = array_flip($outlook_fields);

$contacts_table = "moduserdata";
$user_field = 'enteredby'; 
$user_table = 'users';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
if ($action == "export")
	do_export();
elseif ($action == "import")
	do_import();
elseif ($action == "form")
do_form();
else
	do_setform();
	
return;


function do_import()
{
	global $dbcon, $mysql_fields;
	global $outlook_fields, $outlook_to_mysql;
	global $contacts_table, $header;
	global $user_field, $_POST, $daction;
	
	$enteredby = isset($_REQUEST[$user_field]) ? $_REQUEST[$user_field] : '';
	
	#header("Content-type: text/plain");	
	#include($header);
	
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
	//echo $firstline."<br>";	
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
	$query = "SELECT TRIM(Organization), id FROM `$contacts_table` WHERE modinid = $_POST[modinid] ;";
	$record_set = $dbcon->Execute($query);
	if ($record_set == null)
		die($dbcon->ErrorMsg());
	while (!$record_set->EOF)
	{
		if (isset($record_set->fields[0]))
			$names[$record_set->fields[0]] = $record_set->fields[1];
		$record_set->MoveNext();
	}	
	$record_set->Close(); 
	
	
	# loop over each row in the uploaded data
	# insert into database if the names are different
	# (the data in $sql_data has already had addslashes called on it)
		$newdata = false;
			if ($daction=='delete') {
				$queryd= "DELETE from  $contacts_table  where modinid= $_POST[modinid];";
		$ok = $dbcon->Execute($queryd);
		if (!$ok)
			die($dbcon->ErrorMsg());
		}
		
	foreach($sql_data as $row)
	{
	$query2 ="SELECT id FROM `states` WHERE `state` = '$row[State]'";
	$staten = $dbcon->Execute($query2) or 	die($dbcon->ErrorMsg());
	$query2 ="SELECT id FROM `region` WHERE `title` = '$row[region]'";
	$region = $dbcon->Execute($query2) or 	die($dbcon->ErrorMsg());
	if ($region->Fields("id")){
	$row[region] =  $region->Fields("id");}
	else {$row[region] =  $staten->Fields("id");}
	
	$row[State] =  $staten->Fields("id");
		if($row[WebPage]){
			$row[WebPage] = eregi_replace( "http://", "", $row[WebPage] ); 
			$row[WebPage] = "http://" . $row[WebPage]; 
		}
	
if ($_POST[publishx]){$row[publish] = $_POST[publishx]; }
if ($_POST[regionx]){$row[region] = $_POST[regionx]; }
if ($_POST[field1]){$row[field1] = $_POST[field1]; }
if ($_POST[field2]){$row[field2] = $_POST[field2]; }
if ($_POST[field3]){$row[field3] = $_POST[field3]; }
if ($_POST[field4]){$row[field4] = $_POST[field4]; }
if ($_POST[field5]){$row[field5] = $_POST[field5]; }
if ($_POST[field6]){$row[field6] = $_POST[field6]; }
if ($_POST[field7]){$row[field7] = $_POST[field7]; }
if ($_POST[field8]){$row[field8] = $_POST[field8]; }
if ($_POST[field9]){$row[field9] = $_POST[field9]; }
if ($_POST[field10]){$row[field10] = $_POST[field10]; }
if ($_POST[field11]){$row[field11] = $_POST[field11]; }
if ($_POST[field12]){$row[field12] = $_POST[field12]; }
if ($_POST[field13]){$row[field13] = $_POST[field13]; }
if ($_POST[field14]){$row[field14] = $_POST[field14]; }
if ($_POST[field15]){$row[field15] = $_POST[field15]; }
if ($_POST[field16]){$row[field16] = $_POST[field16]; }
if ($_POST[field17]){$row[field17] = $_POST[field17]; }
if ($_POST[field18]){$row[field18] = $_POST[field18]; }
if ($_POST[field19]){$row[field19] = $_POST[field19]; }
if ($_POST[field20]){$row[field20] = $_POST[field20]; }


	






				$name = trim(stripslashes($row['Organization']));
		
		if ($daction=='skip') {
		if ( $name=="" || isset($names[$name]) ) 
			continue; # skip over names which already exist
		}
	if ($daction=='update') {
	if ( $name=="" || isset($names[$name]) ) 
	{					foreach($row as $column => $value) {
			if ($value=='')
				unset($row[$column]);
			else
				$row[$column] = "'$value'";
		}		
	
			$row[modinid] = $_POST[modinid];
						
		$columns =  array_keys($row);
		$values=array_values($row);
		
		$one=NULL;
		$one = array();
		$sizeof = count($columns);
		foreach($columns as $key => $value) {
		$one[$key]= $columns[$key]." = ".$values[$key];
		}
		
		$uvalues  = join(",", array_values($one));
		$query = "UPDATE $contacts_table  set  $uvalues  where id = $names[$name];";
	echo "<br><br>$query\n";
		
		$ok = $dbcon->Execute($query);
		if (!$ok)
			die($dbcon->ErrorMsg());
		else
			$newdata = true;
			
		report_success("updated record for $row[Organization].");
continue; }}
		
				foreach($row as $column => $value) {
			if ($value=='')
				unset($row[$column]);
			else
				$row[$column] = "'$value'";
		}		
	
			$row[modinid] = $_POST[modinid];
		$columns = join(",", array_keys($row));
		$values  = join(",", array_values($row));
		$query = "INSERT INTO $contacts_table ($columns) VALUES($values);";
		//echo "<br><br>$query\n";
		
		$ok = $dbcon->Execute($query);
		if (!$ok)
			die($dbcon->ErrorMsg());
		else
			$newdata = true;
			
		report_success("added new record for $row[Organization].");
	
		
		
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

function do_form()
{
	global $header, $input_choice, $_POST,  $dbcon, $user_field;
	//include($header);

	//$users = get_users();
	
	?>

	<table>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
	<tr> 
      <td>

		<?php $query = "SELECT * from modfields where id = $_POST[modinid] ;";
	$modf= $dbcon->Execute($query);
	if ($modf == null)
		die($dbcon->ErrorMsg());?>
	</td></tr>
	<tr>
      <td> <p><b>Import</b><br>
          You can import a CSV file created by Outlook.<br>
          Select a CSV file:&nbsp; 
          <input type="file" name="csvfile" />
          <input type="hidden" name="action" value="import" />
          <input type="hidden" name="modinid" value="<?php echo $_POST[modinid]; ?>" />
          <input type="submit" value="Upload" />
          <br>
          Duplication Action. 
          <select  name="daction">
            <option value="add">add with duplicates</option>
            <option value="skip">add skipping duplicates</option>
            <option value="update">add updating duplicates</option>
            <option value="delete">delete all and add new from upload</option>
          </select>
        </p>
        <p>Duplication Field: 
          <input name="dupfield" type="text" id="dupfield" value="Organization">
        </p>
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td >LastName</td>
            <td colspan="2" ><input type="text" name="LastName" value="LastName"></td>
          </tr>
          <tr> 
            <td>FirstName </td>
            <td colspan="2"><input type="text" name="FirstName" value="FirstName"></td>
          </tr>
          <tr> 
            <td>Organization </td>
            <td colspan="2"><input type="text" name="Organization" value="Organization"></td>
          </tr>
          <tr> 
            <td>EmailAddress </td>
            <td colspan="2"><input type="text" name="EmailAddress" value="EmailAddress"></td>
          </tr>
          <tr> 
            <td>Phone </td>
            <td colspan="2"><input type="text" name="Phone" value="Phone"></td>
          </tr>
          <tr> 
            <td>WebPage </td>
            <td colspan="2"><input type="text" name="WebPage" value="WebPage"></td>
          </tr>
          <tr> 
            <td>Address </td>
            <td colspan="2"><input type="text" name="Address" value="Address"></td>
          </tr>
          <tr> 
            <td>Address2 </td>
            <td colspan="2"><input type="text" name="Address2" value="Address2"></td>
          </tr>
          <tr> 
            <td>City </td>
            <td colspan="2"><input type="text" name="City" value="City"></td>
          </tr>
          <tr> 
            <td>State </td>
            <td colspan="2"><input type="text" name="State" value="State"></td>
          </tr>
          <tr> 
            <td>PostalCode </td>
            <td colspan="2"><input type="text" name="PostalCode" value="PostalCode"></td>
          </tr>
          <tr> 
            <td>Country </td>
            <td colspan="2"><input type="text" name="Country" value="Country"></td>
          </tr>
          <tr> 
            <td>Fax</td>
            <td colspan="2"><input type="text" name="Fax" value="Fax"></td>
          </tr>
          <tr> 
            <td>notes </td>
            <td colspan="2"><input type="text" name="notes" value="notes"></td>
          </tr>
		     <tr> 
            <td>publish </td>
            <td ><input name="publish" type="text" id="publish" value="publish"></td>
			 <td><input type="text" name="publishx" value="1"></td>
          </tr>		     <tr> 
            <td>region </td>
            <td ><input name="region" type="text" id="region" value="region"></td>
			 <td><input type="text" name="regionx"></td>
          </tr>
		  
          <tr> 
            <td><?php echo $modf->Fields("field1text"); ?></td>
            <td><input type="text" name="field1text" value="<?php echo $modf->Fields("field1text"); ?>"></td>
            <td><input type="text" name="field1" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field2text"); ?></td>
            <td ><input type="text" name="field2text" value="<?php echo $modf->Fields("field2text"); ?>"></td>
			  <td><input type="text" name="field2" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field3text"); ?></td>
            <td ><input type="text" name="field3text" value="<?php echo $modf->Fields("field3text"); ?>"></td>
			  <td><input type="text" name="field3" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field4text"); ?></td>
            <td ><input type="text" name="field4text" value="<?php echo $modf->Fields("field4text"); ?>"></td>
			  <td><input type="text" name="field4" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field5text"); ?></td>
            <td ><input type="text" name="field5text" value="<?php echo $modf->Fields("field5text"); ?>"></td>
			  <td><input type="text" name="field5" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field6text"); ?></td>
            <td ><input type="text" name="field6text" value="<?php echo $modf->Fields("field6text"); ?>"></td>
			  <td><input type="text" name="field6" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field7text"); ?></td>
            <td ><input type="text" name="field7text" value="<?php echo $modf->Fields("field7text"); ?>"></td>
			  <td><input type="text" name="field7" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field8text"); ?></td>
            <td ><input type="text" name="field8text" value="<?php echo $modf->Fields("field8text"); ?>"></td>
			  <td><input type="text" name="field8" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field9text"); ?></td>
            <td ><input type="text" name="field9text" value="<?php echo $modf->Fields("field9text"); ?>"></td>
			  <td><input type="text" name="field9" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field10text"); ?></td>
            <td ><input type="text" name="field10text" value="<?php echo $modf->Fields("field10text"); ?>"></td>
			  <td><input type="text" name="field10" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field11text"); ?></td>
            <td ><input type="text" name="field11text" value="<?php echo $modf->Fields("field11text"); ?>"></td>
			  <td><input type="text" name="field11" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field12text"); ?></td>
            <td ><input type="text" name="field12text" value="<?php echo $modf->Fields("field12text"); ?>"></td>
			  <td><input type="text" name="field12" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field13text"); ?></td>
            <td ><input type="text" name="field13text" value="<?php echo $modf->Fields("field13text"); ?>"></td>
			<td><input type="text" name="field13" value=""></td>
			
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field14text"); ?></td>
            <td ><input type="text" name="field14text" value="<?php echo $modf->Fields("field14text"); ?>"></td>
			<td><input type="text" name="field14" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field15text"); ?></td>
            <td ><input type="text" name="field15text" value="<?php echo $modf->Fields("field15text"); ?>"></td>
			<td><input type="text" name="field15" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field16text"); ?></td>
            <td ><input type="text" name="field16text" value="<?php echo $modf->Fields("field16text"); ?>"></td>
			<td><input type="text" name="field16" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field17text"); ?></td>
            <td ><input type="text" name="field17text" value="<?php echo $modf->Fields("field17text"); ?>"></td>
			<td><input type="text" name="field17" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field18text"); ?></td>
            <td ><input type="text" name="field18text" value="<?php echo $modf->Fields("field18text"); ?>"></td>
			<td><input type="text" name="field18" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field19text"); ?></td>
            <td ><input type="text" name="field19text" value="<?php echo $modf->Fields("field19text"); ?>"></td>
			<td><input type="text" name="field19" value=""></td>
          </tr>
          <tr> 
            <td><?php echo $modf->Fields("field20text"); ?></td>
            <td ><input type="text" name="field20text" value="<?php echo $modf->Fields("field20text"); ?>"></td>
			<td><input type="text" name="field20" value=""></td>
          </tr>
        </table>
        <p>&nbsp; </p>

        <p>&nbsp; </p></td></tr>
</form>
	</table>
		
	<?php
}
function do_setform() {

global $dbcon;
$query = "SELECT id, name from modfields ";
	$record_set= $dbcon->Execute($query);
	if ($record_set== null)
		die($dbcon->ErrorMsg());
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" >

          <select  name="modinid">
		  <?php 	while (!$record_set->EOF)
	{
		
?>
            <option value="<?php echo $record_set->Fields("id"); ?>"><?php echo $record_set->Fields("name"); ?></option>
			  <?php  $record_set->MoveNext();
	}	?>
          </select>
<input type="hidden" name="action" value="form" />
<input type="submit" value="Next" />
</form>
<?php
}
include ("footer.php") 
?>