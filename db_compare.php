<?php

require_once("adodb/adodb.inc.php");

$ampasp = 1;

if($MM_haveMaster){
	echo "<HTML><BODY BGCOLOR=\"#FFFFFF\">";
	if ($ampasp == 1) {
		 ADOLoadCode("mysql");
		$dbcon=&ADONewConnection("mysql");
		 
	    $dbcon->Connect("localhost","david","havlan","amp");
#		$dbcon->Connect("localhost:3306","root","","amp_system");
 
		$currentsql = "SELECT DISTINCT `database`, amppath FROM system where !IsNull(`database`)";
		$AMPsql=$dbcon->Execute($currentsql);

	}
	##### LOAD MASTER TEMPLATE ####
	$dbcon->Close;
#	$dbcon->Connect("localhost:3306","root","",$master_template);
	$dbcon->Connect("localhost","david","havlan",$master_template);

	$dbDefinition = array();
	$master_tables = $dbcon->MetaTables('TABLES');
	foreach ( $master_tables as $table ) {
		$dbDefinition[ $table ] = $dbcon->MetaColumns( $table, FALSE );
	}
	
	#$master_template_sql="SHOW tables from ".$master_template;
	#	$master_tables = $dbcon->Execute($master_template_sql);
	#while ($master_tables->EOF) {
		#$current_Table=$master_tables->Fields('Tables_in_'.$master_template);
		#echo $current_Table."<BR>";
		#$master_template_sql="SHOW fields from ".$master_template.".".$current_Table;
		#$master_fields_temp=$dbcon->Execute($master_template_sql);
	#	$master_fields['$currenttable']=MetaColumns($master_template);
	#	$master_tables->MoveNext();
	#}
	
	####START COMPARISONS######

	$output="";
	while (!$AMPsql->EOF){
	$MM_DATABASE=$AMPsql->Fields("database");
	#connect to Database
		
		$dbcon->Connect("localhost","david","havlan",$MM_DATABASE);
		$table_Set= $dbcon->MetaTables('TABLES');
		//Create a loop to compare the listing of tables to the Master Listing
		foreach ($table_Set as $current_Table) {
		//If a table is matched in the Master Listing, compare the field names, lengths, types
			$tablefound[$current_Table]=FALSE;
			//begin table loop
			foreach($master_tables as $table){
				$master_tablename=$table;
				if ($master_tablename==$current_Table) {$tablefound[$current_Table]=TRUE;}
			}
			if ($tablefound[$current_Table]) {
#				$current_sql="SHOW fields from ".$MM_DATABASE.".".$current_Table;
				$field_Set=$dbcon->Metacolumns($current_Table, FALSE);
				//Zero out the field finder
#				$fieldfound = array_fill(0,50, FALSE); 

				//begin field loop
				foreach ($field_Set as $fieldobj){
#					reset($master_fields['$current_Table']);

					//Look for field in master and check data type
					#while ((!$master_fields['$current_Table']->EOF)&&(!$fieldfound[$field_Set->Fields("Field")])) {
					foreach ($dbDefinition[$current_Table] as $master_fields){ 
						if ($master_fields->name==$fieldobj->name){  $fieldfound[$master_fields->name]=TRUE ;
							if (($master_fields->type.$master_fields->max_length) != ($fieldobj->type.$fieldobj->max_length)) {
								$output .= "Field Type mismatch: <B>".$fieldobj->name."</B> in table <B>".$current_Table."</b> in database ".$MM_DATABASE." is of type <B>".$fieldobj->type."(".$fieldobj->max_length.")</B>&nbsp;&nbsp;&nbsp;Template field type: ".$master_fields->type."(".$master_fields->max_length.")<BR>\r\n";
							}
						}
					}
					if (!$fieldfound[$fieldobj->name]) {
						$output .= "Non-template field found: <B>".$fieldobj->name."</B> in table <B>".$current_Table."</b> in database ".$MM_DATABASE." is of type <B>".$fieldobj->type."</B>&nbsp;&nbsp;&nbsp;No matching field in template<BR>\r\n";
					}
				} //end of field loop
				//check for fields present in master but not in local
				foreach($dbDefinition[$current_Table] as $master_fields) {
					if (!$fieldfound[$master_fields->name]){
						$output .= "Template field missing: No copy of <B>".$master_fields->name."</b>, type <B>".$master_fields->type."</b>, in table $current_Table, exists in database ".$MM_DATABASE."<BR>\r\n";
					}
				}
			}//end table_found code
			else { //table is not found
					//If a table is not matched, display Database ->Table->All Fields & Characteristics
					$field_Set=$dbcon->Metacolumns($current_Table, FALSE);
					$output .= "Non-template table found: <B>".$current_Table."</b> in database ".$MM_DATABASE." has <B>".count($field_Set)."</B> fields &nbsp;&nbsp;&nbsp;No matching table in template<BR>\r\n";
					$output.="<table><tr><Td colspan=2>Fields from $current_Table</td></tr><TR><TD>FIELDNAME</TD><TD>TYPE</TD></TR>";
					foreach ($field_Set as $fieldobj){
						$output.="<tr><td>".$fieldobj->name."</td><td>".$fieldobj->type."</td></tr>";
					}
					$output.="</table><BR>\r\n";
			}
		}//end table_Set loop
		//Check to see if any tables fromt the Master are missing in the Current
		foreach ($master_tables as $current_Table) { //check to ensure all tables from template included
			if (!$tablefound[$current_Table]) {
				$output .= "Template table missing: No copy of <B>$current_Table</b>, exists in database ".$MM_DATABASE."<BR>\r\n";
			}
		}	
		$AMPsql->Movenext();
	}//end of databases loop
	echo $output;
} else {?>
<HTML>
<HEAD><TITLE="DB Compare Tool"></HEAD>
<BODY BGCOLOR=#FFFFFF>
<FORM name = "compare" action="db_compare.php" method="GET">
Enter master database template for comparison:<BR>
<INPUT type="text" name="master_template" size="20" value="victoria">
<INPUT type="hidden" name="MM_haveMaster" value="1">
</FORM>
</BODY></HTML>
<?php } 

#SET DATABASE CACHING
#$dbcon->cacheSecs = $cacheSecs;
	
#INCLUDE FUNCTIONS
#require ($base_path."Connections/functions.php");

?>