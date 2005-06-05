<?php
$modid = "payments";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "payment";
$listtitle ="Payments";
$listsql ="select id, Name_On_Card, Amount  from $table  ";
$orderby =" order by  id desc  ";
$fieldsarray=array( 'Name On Card'=>'Name_On_Card','Amount'=>'Amount','ID'=>'id'
					);
$filename="payments.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "user_ID|value|payment_item_ID|value|payment_merchant_ID|value|Name_On_Card|value|Credit_Card_Type|value|Credit_Card_Number|value|Credit_Card_Secrity_Code|value|Credit_Card_Expiration|value|Amount|value|Status|value| Billing_Street|value|Billing_Street2|value|Billing_City|value|Billing_State|value|Billing_Zip|value|Billing_Email|value";
    $MM_columnsStr = "user_ID|',none,''|payment_item_ID|',none,''|payment_merchant_ID|',none,''|Name_On_Card|',none,''|Credit_Card_Type|',none,''|Credit_Card_Number|',none,''|Credit_Card_Secrity_Code|',none,''|Credit_Card_Expiration|',none,''|Amount|',none,''|Status|',none,''| Billing_Street|',none,''|Billing_Street2|',none,''|Billing_City|',none,''|Billing_State|',none,''|Billing_Zip|',none,''|Billing_Email|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|

	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

//$R=$dbcon->Execute("SELECT p.*, t.name FROM payment p, payment_type t WHERE  t.id = p.payment_type_ID and p.id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$R=$dbcon->Execute("SELECT * FROM payment  WHERE   id = $R__MMColParam") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= add_view_row('user_ID');
//add_view_row('Payment Type',$R->Fields("name"),$R);
$html .=add_view_row('Name_On_Card');
$html .=add_view_row('Credit_Card_Type');
$html .=add_view_row('Credit_Card_Number');
$html .=add_view_row('Credit_Card_Secrity_Code');
$html .=add_view_row('Credit_Card_Expiration');
$html .=add_view_row('Amount');
$html .=add_view_row('Status');
$html .=add_view_row('Billing_Street');
$html .=add_view_row('Billing_Street2');
$html .=add_view_row('Billing_City');
$html .=add_view_row('Billing_State');
$html .=add_view_row('Billing_Zip');
$html .=add_view_row('Billing_Email');
 

$html .= $rec_id->fetch();
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
