<?php

if($MM_haveMaster){
	require_once("../adodb/adodb.inc.php");
	require_once( 'db_compare.inc2.php');

	$master_template=new dbDefinition;
	#set_time_limit(0);

	##GET LIST OF DATABASES IN SYSTEM#
	ADOLoadCode("mysql");
	#$ampdbcon=&ADONewConnection("mysql");
	$dbcon1=&ADONewConnection("mysql");
	$dbcon2=&ADONewConnection("mysql");
		
   $dbcon2->Connect("localhost","david","havlan","amp");
#	$dbcon2->Connect("localhost:3306","root","","amp_system");
 
	$currentsql = "SELECT DISTINCT `database` FROM system where !IsNull(`database`)";
	$AMPsql=$dbcon2->Execute($currentsql);
	$dbcon2->Close;


	##### LOAD MASTER TEMPLATE ####
	$dbcon1->NConnect("localhost","david","havlan", $master_dbname);
	$master_template->dbLoadDef($dbcon1, $master_dbname);
	/*
	echo "Master: ".$master_dbname."<BR>";
	echo "Master: ".$master_template->dbname."<BR>";
	foreach ($master_template->tableSet as $table){
		echo "Master table: ".$table."<BR>";
		foreach ($master_template->fieldSet[$table] as $fieldobj){
			echo "field in master: ".$fieldobj->name;
		}
	}
	*/
	##### BEGIN COMPARISON LOOP ###
	while (!$AMPsql->EOF){
	 	$MM_DATABASE=$AMPsql->Fields("database");
		#connect to Database
		$dbcon=&ADONewConnection("mysql");
		$dbcon->NConnect("localhost","david","havlan", $MM_DATABASE);
		$currentDB[$MM_DATABASE] = new dbDefinition;
		$currentDB[$MM_DATABASE]->dbLoadDef($dbcon, $MM_DATABASE);
		$master_template->dbCheckTables($currentDB[$MM_DATABASE]);
		$AMPsql->MoveNext();
		$dbcon->Close();
	
	}
	#####OUTPUT REPORT######
	//HEADERDATA and JAVASCRIPT HIDE FUNCTIONS
	$head_html= "<HTML><HEAD><TITLE=\"DB Compare Tool\"></HEAD><LINK rel=\"stylesheet\" href=\"db_compare.css\" type=\"text/css\"><script type=\"text/javascript\">\r\n 
	
	function hideClass(theclass, objtype) {
	if (objtype=='') {objtype='div';}
	for (i=0;i<document.getElementsByTagName(objtype).length; i++) {
		if (document.getElementsByTagName(objtype).item(i).className == theclass){
			document.getElementsByTagName(objtype).item(i).style.display = 'none';
		}
	}
	}

	function showClass(theclass, objtype) {
	if (objtype=='') {objtype='div';}
	for (i=0;i<document.getElementsByTagName(objtype).length; i++) {
		if (document.getElementsByTagName(objtype).item(i).className == theclass){
			document.getElementsByTagName(objtype).item(i).style.display = 'block';
		}
	}
	}

	function change(which, whatkind) {
	if (whatkind!='') {hideClass(whatkind, '');}
		if(document.getElementById(which).style.display == 'block' ) {
			document.getElementById(which).style.display = 'none';
		} else {
		document.getElementById(which).style.display = 'block';
		//alert(which+'/'+whatkind);
		}
	}
	</script>";
	$head_html.="<BODY BGCOLOR=\"#FFFFFF\"><div id=\"container\">";
	 /*$AMPsql->MoveFirst();
	 while (!$AMPsql->EOF) {
		$head_html .= "document.getElementById('database_".$AMPsql->fields("database")."').style.display = 'none';\r\n";
		$AMPsql->MoveNext();
	 }*/	
	$control_html="<div id=\"left_nav\"><form name=\"show_db\">";
	$AMPsql->MoveFirst();
	$control_html.=$AMPsql->GetMenu("db_control", "", FALSE, 0, 10, "onchange=\"change(('database_'+document.forms['show_db'].elements['db_control'].value), 'db_container'); change(('db_control_'+document.forms['show_db'].elements['db_control'].value), 'db_control_nav');\"");
	$AMPsql->MoveFirst();

	while(!$AMPsql->EOF){
		$thisDB=$AMPsql->Fields("database");
		//Parse table errors into categories
		/*
		foreach($master_template->tableSet as $currentTable) {
			if ($master_template->dboutput[$currentTable][$AMPsql->Fields("database")]>"") {
				$myOutput=explode("<BR>",  $master_template->dboutput[$currentTable][$AMPsql->Fields("database")]);
				foreach ($myOutput as $thisLine) {
					$problemtype=substr($thisLine, 0, strpos($thisLine, ":"));
					if ($problemtype!=""){
						#$debug_html.=$AMPsql->Fields("database").": ".$problemtype."<BR>";
						$problemtype=str_replace(" ", "_", trim($problemtype));
						$db_divided_output[trim($problemtype)].=$thisLine;
						if (strpos($db_menu, trim($problemtype))===FALSE) {
							$db_menu.=trim($problemtype).":";
						}
					}
				}
			}
		}
		if ($master_template->dboutput['TablesNotFound'][$AMPsql->Fields("database")]>"") {
			$db_divided_output['New_Tables_Found'].= "<HR><H2>New Table(s) Found</H2><BR>".$master_template->dboutput['TablesNotFound'][$AMPsql->Fields("database")];
			$db_menu.='New_Tables_Found:';
		}
		*/
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
					$db_divided_output["Field_Type_Changes"].= $thisDB.": in table: ".$currentTable." field type non-match: ".$output_set." is of type :".$master_template->fields_newtype[$thisDB][$currentTable][$output_set]."<BR>\r\n";
				}
			}	
		}		
		foreach ($currentDB[$thisDB]->tableSet as $currentTable) {
			$db_divided_output["DB_Structure"].=$currentTable." has <A href=\"#\" onclick=\"change('field_list_".$currentTable."_".$thisDB."', '');\"> ".count($currentDB[$thisDB]->fieldSet[$currentTable])." fields</a><BR>";
			#$db_divided_output["DB_Structure"].="<div id=\"field_list_".$currentTable."_".$thisDB."\" class=\"field_list\">";
			#foreach (array_keys($currentDB[$thisDB]->fieldSet_extended[$currentTable]) as $currentField) {
			#	$db_divided_output["DB_Structure"].="$currentField is of type ".$currentDB[$thisDB]->fieldSet_extended[$currentTable][$currentField]."<BR>";
			#}
			#$db_divided_output["DB_Structure"].="</div>";
			$db_divided_output["DB_Structure"].="<ul id=\"field_list_".$currentTable."_".$thisDB."\" class=\"field_list\">";
			foreach (array_keys($currentDB[$thisDB]->fieldSet_extended[$currentTable]) as $currentField) {
				$db_divided_output["DB_Structure"].="<LI>$currentField is of type ".$currentDB[$thisDB]->fieldSet_extended[$currentTable][$currentField]."</LI>";
			}
			$db_divided_output["DB_Structure"].="</ul>";
		}

		//CREATE FINAL OUTPUT DATA
		$content_html_header = "<div class=db_container id=\"database_".$AMPsql->fields("database")."\"><H1>".$AMPsql->fields("database")."</H1>";

		$content_html_body="";
		//echo errors out to display form by category
		#$debug_html.=$AMPsql->Fields("database").": ".$db_menu."<BR>";
		#echo $thisDB.": Menu :".$db_menu;
		#$db_menu_set=explode(":",$db_menu);
		$db_menu_set=array_keys($db_divided_output);
		$control_html .= "<div class=db_control_nav id=\"db_control_$thisDB\"><select name =\"".$thisDB."_action\" size=\"6\" onchange=\"change(('database_".$thisDB."_'+document.forms['show_db'].elements['".$thisDB."_action'].value), 'list_entry');\">";
		foreach($db_menu_set as $problemtype) {
			if ($problemtype>""){
				$control_html.="<option value=\"$problemtype\">".str_replace("_", " ", $problemtype)."</option>\r\n";
				$content_html_header.="<a href=\"#\" title=\"show $problemtype\" onclick=\"change('database_".$thisDB."_$problemtype', 'list_entry');\" class=content_nav>".str_replace("_", " ", $problemtype)."</a>";
				$content_html_body.="<div class=\"list_entry\" id=\"database_".$thisDB."_$problemtype\">";
				$content_html_body.="<H2>".str_replace("_", " ",$problemtype).":</h2><BR>".$db_divided_output[$problemtype];
				$content_html_body.="</div>";
			}
		}
		$control_html.="</select></div>";
		$content_html.= $content_html_header.$content_html_body."</div>";
		$AMPsql->MoveNext();
	}
	$control_html .= "</form></div>";
	$footer_html="</div></body></html>";
	echo $head_html.$control_html.$content_html.$debug_html.$footer_html;
 #####FORM FOR TEMPLATE DB####   
} else {?>
<HTML>
<HEAD><TITLE="DB Compare Tool"></HEAD>
<BODY BGCOLOR=#FFFFFF>
<FORM name = "compare" action="db_compare.php" method="GET">
Enter master database template for comparison:<BR>
<INPUT type="text" name="master_dbname" size="20" value="victoria">
<INPUT type="hidden" name="MM_haveMaster" value="1">
</FORM>
</BODY></HTML>
<?php } ?>