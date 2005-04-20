<?php
require_once ('AMP/BaseDB.php');

if($_REQUEST['MM_haveMaster']&&$_REQUEST['master_dbname']){
	require_once('db_compare.inc2.php');

	$master_template=&new dbDefinition;
    $master_dbname=$_REQUEST['master_dbname'];
	#set_time_limit(0);

	##### LOAD MASTER TEMPLATE ####
    $dbcon2 = & ADONewConnection( AMP_DB_TYPE );
    $ms_user = (isset($_REQUEST['uname'])&&$_REQUEST['uname'])?$_REQUEST['uname']:AMP_DB_USER;
    
    $ms_pass = (isset($_REQUEST['upass'])&&$_REQUEST['upass'])?$_REQUEST['upass']:AMP_DB_PASS;
    $dbcon2->NConnect(AMP_DB_HOST, AMP_DB_USER, AMP_DB_PASS, $master_dbname);
	$master_template->dbLoadDef($dbcon2, $master_dbname);
	
	##### GRAB CURRENT DB DEF ###
		$currentDB = &new dbDefinition;
		$currentDB->dbLoadDef($dbcon, $MM_DATABASE);
		$master_template->dbCheckTables($currentDB);
	
	#####OUTPUT REPORT######

    $thisDB=$MM_DATABASE;
		$db_divided_output=array();
		$db_menu ="";
		//MOVE ERROR RECORD FROM master_template OBJECT into HTML array for OUTPUT
		

		foreach ($master_template->tables_missing[$thisDB] as $output_set){
			$db_divided_output["Tables_Missing"].= $thisDB.": missing table: ".$output_set."<BR>\r\n";
		}
		foreach ($master_template->tables_found[$thisDB] as $output_set){
			$db_divided_output["Tables_Found"].= $thisDB.": found table: ".$output_set."<BR>\r\n";
		}
		foreach($master_template->tables_common[$thisDB] as $currentTable) {
			foreach ($master_template->fields_missing[$thisDB][$currentTable] as $output_set){
					$db_divided_output["Fields_Missing"].= $thisDB.": in table: ".$currentTable." missing field: ".$output_set."<BR>\r\n";
			}
			foreach ($master_template->fields_found[$thisDB][$currentTable] as $output_set){
					$db_divided_output["Fields_Found"].= $thisDB.": in table: ".$currentTable." found field: ".$output_set."<BR>\r\n";
			}
			if (count($master_template->fields_newtype[$thisDB][$currentTable])) {
				foreach (array_keys($master_template->fields_newtype[$thisDB][$currentTable]) as $output_set) {
					$db_divided_output["Field_Type_Changes"].= $master_template->dbname.": in table: ".$currentTable." field type non-match: ".$output_set." is of type :".$master_template->fieldSet_extended[$currentTable][$output_set]."<BR>\r\n";
				}
			}	
		}		
		foreach ($currentDB->tableSet as $currentTable) {
			$db_divided_output["DB_Structure"].=$currentTable." has <A href=\"#\" onclick=\"change_any('field_list_".$currentTable."_".$thisDB."', '');\"> ".count($currentDB->fieldSet[$currentTable])." fields</a><BR>";
			$db_divided_output["DB_Structure"].="<ul id=\"field_list_".$currentTable."_".$thisDB."\" class=\"field_list\" style=\"display:none;\">";
			foreach (array_keys($currentDB->fieldSet_extended[$currentTable]) as $currentField) {
				$db_divided_output["DB_Structure"].="<LI>$currentField is of type ".$currentDB->fieldSet_extended[$currentTable][$currentField]."</LI>";
			}
			$db_divided_output["DB_Structure"].="</ul>";
		}

		//CREATE FINAL OUTPUT DATA
		$content_html_header = "<div class=db_container id=\"database_".$MM_DATABASE."\"><H1>".$MM_DATABASE."</H1>";

		$content_html_body="";
		$db_menu_set=array_keys($db_divided_output);
		foreach($db_menu_set as $problemtype) {
			if ($problemtype>""){
				$content_html_header.="<a href=\"#\" title=\"show $problemtype\" onclick=\"change_any('database_".$thisDB."_$problemtype', 'list_entry');\" class=content_nav>".str_replace("_", " ", $problemtype)."</a>&nbsp;";
				$content_html_body.="<div class=\"list_entry\" id=\"database_".$thisDB."_$problemtype\">";
				$content_html_body.="<H2>".str_replace("_", " ",$problemtype).":</h2><BR>".$db_divided_output[$problemtype];
				$content_html_body.="</div>";
			}
		}
		$content_html.= $content_html_header.$content_html_body."</div>";
	$footer_html="</div></body></html>";
	$output = $content_html.$debug_html.$footer_html;
 #####FORM FOR TEMPLATE DB####   
} else {
$output = 
'<FORM name = "compare" action="db_compare.php" method="POST">
Enter database to use as a template for comparison:<BR>
<INPUT type="text" name="master_dbname" size="20" value="victoria"><BR>
Username & Password: (if required)<BR>
<INPUT type="text" name="uname" size="12" value="">
<INPUT type="text" name="upass" size="20" value=""><BR>
<INPUT type="hidden" name="MM_haveMaster" value="1">
<INPUT type="submit" value="Compare Databases">
</FORM>';
}
$mod_name="system";
include ('header.php');

print '<H2>DB Compare Tool</H2>';
print $output;

include ('footer.php');
 ?>
