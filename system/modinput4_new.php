<?php
# UDM Wizard
#set differnt list 
#defulat file

$mod_name='udm';
require("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
include("FCKeditor/fckeditor.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

if ($_POST['MM_insert']) {

	$MM_insert = 1;

	## insert UDM
    $MM_editTable  = "userdata_fields";
    $MM_fieldsStr = "name|value";
    $MM_columnsStr = "name|',none,''";
 	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	## get UDM id
	$getmodid=$dbcon->Execute( "SELECT id FROM userdata_fields ORDER BY id DESC LIMIT 1") or die($dbcon->ErrorMsg());
	$modid = $getmodid->Fields("id");

	## insert new permission
	$pname="$name Module";
	$publish  =1;
    $MM_editTable  = "per_description ";
    $MM_fieldsStr = "pname|value|description|value|publish|value";
    $MM_columnsStr = "name|',none,''|description|',none,''|publish|,none,''";
		 	require ("../Connections/insetstuff.php");
	  		require ("../Connections/dataactions.php");
##get per id
	$getperid=$dbcon->Execute( "select id from  per_description   order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$udmper = $getperid->Fields("id");

##make new module
	$addmodule=$dbcon->Execute( "insert into modules (name) values ('$name')") or DIE($dbcon->ErrorMsg());
## get module id
	$getmoduleid=$dbcon->Execute( "select id from modules  order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$udmmodid = $getmoduleid->Fields("id");




	## insert header page
	$hname = "$name Input";
    $MM_editTable  = "moduletext";
    $MM_fieldsStr = "htitle|value|harticle|value|hname|value|udmmodid|value";
    $MM_columnsStr = "title|',none,''|test|',none,''|name|',none,''|modid|',none,''";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	##get heder id
	$getheaderid=$dbcon->Execute( "select id from moduletext  order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$modidinput  = $getheaderid->Fields("id");

	##insert header response page
	$rname = "$name Thank You";
    $MM_editTable  = "moduletext";
    $MM_fieldsStr = "rtitle|value|rarticle|value|rname|value|udmmodid|value";
    $MM_columnsStr = "title|',none,''|test|',none,''|name|',none,''|modid|',none,''";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	# get reposne id
	$getheaderid=$dbcon->Execute( "select id from moduletext  order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$modidresponse = $getheaderid->Fields("id");

	#add source
	$source= "Web $name";
	$MM_editTable  = "source";
	$MM_fieldsStr = "source|value";
	$MM_columnsStr = "title|',none,''";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	#get source id
	$getsourceid=$dbcon->Execute( "select id from source order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$sourceid = $getsourceid->Fields("id");

	#update udm
	unset($MM_insert);

	$MM_update=1;
    $MM_editTable  = "userdata_fields";
    $MM_editColumn = "id";
    $MM_recordId =$modid;
	$MM_fieldsStr ="modidinput|value|modidresponse|value|sourceid|value|enteredbyid|value|useemail|value|mailto|value|subject|value|redirect|value|list1|value|list2|value|list3|value|uselists|value";
	$MM_columnsStr = "modidinput|none,none,NULL|modidresponse|none,none,NULL|sourceid|none,none,NULL|enteredby|none,none,NULL|useemail|none,none,NULL|mailto|',none,''|subject|',none,''|redirect|',none,''|list1|',none,''|list2|',none,''|list3|',none,''|uselists|none,1,0";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	$file = "modinput4_data.php?modin=$modid";
	$userdatamod =1 ;
	$navhtml= "<A class=side href=\"modinput4_data.php?modin=$modid\">View/Edit $name</A><br>
<A class=side href=\"modinput4_view.php?modin=$modid\">Add $name</A><br>
<A class=side href=\"modinput4_edit.php?modin=$modid\">Data Module Settings</A><br>
<A class=side href=\"module_control_list.php?modid=$udmmodid\">Settings</A>";
    $MM_editColumn = "id";
    $MM_recordId =$udmmodid;
    $MM_editTable  = "modules";
    $MM_fieldsStr ="name|value|userdatamod|value|modid|value|file|value|udmper|value|navhtml|value|publish|value|module_type|value";
    $MM_columnsStr = "name|',none,''|userdatamod|',none,''|userdatamodid|',none,''|file|',none,''|perid|',none,''|navhtml|',none,''|publish|',none,''|module_type|',none,''";
 	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");
  
	while (list($k, $v) = each($pergroup)) { 
		$perupdate=$dbcon->Execute("INSERT INTO permission  VALUES ( '',$v,$udmper)") or DIE($dbcon->ErrorMsg());
	} 
	header("Location: modinput4_edit.php?modin=$modid");
}



$Recordset1__MMColParam = 9999999999999;
$R = $dbcon->Execute("SELECT * FROM userdata_fields WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
$U = $dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$L = $dbcon->Execute("SELECT id, name from lists ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$P=$dbcon->Execute("select * from per_group ") or die($dbcon->ErrorMsg());
$M=$dbcon->Execute("select * from module_type ") or die($dbcon->ErrorMsg());

//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add New Form', 'banner');
$html .= addfield('name','Form Name','text');
$m_options = makelistarray($M,'id','name','Select Module Type');
$Mo= & new Select('module_type',$m_options);
$html .=  $buildform->add_row('Module Type', $Mo);

$html .= $buildform->add_header('Intro Text');

$html .= addfield('htitle','Intro Text Title','textarea');
$html .= addfield('harticle','Intro Text','text');
$html .= addfield('rtitle','Response Page Title','text');
$html .= addfield('rarticle','Response Page Text','textarea');

$html .= $buildform->add_header('Email Lists');
$html .= addfield('uselists','use lists','checkbox');

$list_options = makelistarray($L,'id','name','Select List');
$List1 = & new Select('list1',$list_options);
$List2 = & new Select('list2',$list_options);
$List3 = & new Select('list3',$list_options);
$html .=  $buildform->add_row('List #1', $List1);
$html .=  $buildform->add_row('List #2', $List2);
$html .=  $buildform->add_row('List #3', $List3);

$html .= $buildform->add_header('Email Form Contnets');
$html .= addfield('useemail','Use E-mail','checkbox');
$html .= addfield('mailto','Mail to','text');
$html .= addfield('subject','Subject','text');
$html .= $buildform->add_header('System Permissions');

$html .= addfield('','Permission Groups','text');
$per_options = makelistarray($P,'id','name');
$Per= & new Select('pergroup[]',$per_options,'','true',5);
$html .=  $buildform->add_row('Permission Groups', $Per);


$html .= $buildform->add_header('Data Source');
$usr_options = makelistarray($U,'id','name','Select Source');
$Us= & new Select('enteredbyid',$usr_options);
$html .=  $buildform->add_row('Entered By', $Us);

$html .= $buildform->add_content($buildform->add_btn() );
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
if ($_GET['action'] == "list") {
	listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);
}
else {
	echo $form->fetch();
}	
include ("footer.php");

?>