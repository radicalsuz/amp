<?php
$modid = "payments";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "payment_merchant";
$listtitle ="Merchant";
$listsql ="select id, Merchant   from $table  ";
$orderby =" order by  Merchant asc  ";
$fieldsarray=array( 'Merchant'=>'Merchant','ID'=>'id'
					);
$filename="payment_merchant.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "Merchant|value|Acount_Type|value|Account_Username|value|Account _Password|value|Server|value|Notes|value|Payment_Method|value|Payment_Transaction|value|trans_key|value";
    $MM_columnsStr = "Merchant|',none,''|Acount_Type|',none,''|Account_Username|',none,''|Account_Password|',none,''|Server|',none,''|Notes|',none,''|Payment_Method|',none,''|Payment_Transaction|',none,''|trans_key|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|	
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
$html .= addfield('field','Line Description','text',$R->Fields(""));
$html .= addfield('Merchant','Merchant','text',$R->Fields("Merchant"));
$html .= addfield('Acount_Type','Acount_Type','text',$R->Fields("Acount_Type"));
$html .= addfield('Account_Username','Account_Username','text',$R->Fields("Account_Username"));
$html .= addfield('Account_Password','Account_Password','text',$R->Fields("Account_Password"));
$html .= addfield('Server','Server','text',$R->Fields("Server"));
$html .= addfield('Notes','Notes','text',$R->Fields("Notes"));
$html .= addfield('Payment_Method','Payment_Method','text',$R->Fields("Payment_Method"),'CC');
$html .= addfield('Payment_Transaction','Payment_Transaction','text',$R->Fields("Payment_Transaction"),'AUTH_CAPTURE');
$html .= addfield('trans_key','trans_key','text',$R->Fields("trans_key"));
 
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