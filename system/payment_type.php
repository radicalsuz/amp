<?php
#payments
$modid = "payments";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "payment_type";
$listtitle ="Payment Type";
$listsql ="select id, name   from $table  ";
$orderby =" order by  name asc  ";
$fieldsarray=array( 'Name'=>'name','ID'=>'id'
					);
$filename="payment_type.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr =  "merchant_ID|value|name|value|description|value|Amount|value|Amount_Array|value|Amount_Other|value|Tax_Status|value|Donation_Limit|value|Thank_You_Email|value|Email_Alert|value|Alert_Customer|value|Alert_Merchant|value";
    $MM_columnsStr = "merchant_ID|',none,''|name|',none,''|description|',none,''|Amount|',none,''|Amount_Array|',none,''|Amount_Other|',none,''|Tax_Status|',none,''|Donation_Limit|',none,''|Thank_You_Email|',none,''|Email_Alert|',none,''|Alert_Customer|',none,''|Alert_Merchant|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('merchant_ID','Merchant_ID','text',$R->Fields("merchant_ID"));
$html .= addfield('name','Name','text',$R->Fields("name"));
$html .= addfield('description','Description','text',$R->Fields("description"));
$html .= addfield('Amount','Amount','text',$R->Fields("Amount"));
$html .= addfield('Amount_Array','Amount Array','text',$R->Fields("Amount_Array"));
$html .= addfield('Amount_Other','Amount_Other','text',$R->Fields("Amount_Other"));
$html .= addfield('Tax_Status','Tax_Status','text',$R->Fields("Tax_Status"));
$html .= addfield('Donation_Limit','Donation_Limit','text',$R->Fields("Donation_Limit"));
$html .= addfield('Thank_You_Email','Thank_You_Email','text',$R->Fields("Thank_You_Email"));
$html .= addfield('Email_Alert','Email_Alert','text',$R->Fields("Email_Alert"));
$html .= addfield('Alert_Customer','Alert_Customer','text',$R->Fields("Alert_Customer"));
$html .= addfield('Alert_Merchant','Alert_Merchant','text',$R->Fields("Alert_Merchant"));

$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
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
