<?php
$mod_name="email";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
include("FCKeditor/fckeditor.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

if ($_GET['action'] == "list" && (!$_GET['status'])) { $show_sub = "all";}
if ($_GET['status'] == "pending") { $show_sub = "pending";}
//if (!$_GET) {$show_sub = "new"; }

//if ($_GET['status'] =='pending') { 
//	$wherestatus = " and c.status != 'Complete' ";

	$extra = array(
					#'Load Blast'=>'blast_control.php?type=Email&load=',				
					'Blast Report'=>'blast_report.php?blast_ID='
					);

//}
//else { 	
	//$extra = array('Blast Report'=>'blast_report.php?blast_ID='); 
//}

$table = "blast";
$listtitle ="Email Blast";
$listsql ="select distinct c.blast_ID as id, c.status, c.subject, l.name from $table c left join blast_lists l on c.list_ID = l.id  where c.blast_type='Email' $wherestatus ";
$orderby =" order by id desc  ";
$fieldsarray=array('Subject'=>'subject','List'=>'name','Status'=>'status');
$filename="blast_email.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {
    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "blast_ID";
	$MM_fieldsStr = "blast_type|value|from_email|value|from_name|value|reply_to_address|value|subject|value|article|value|message_email_text|value|embargo|value|sendformat|value|message_template_ID|value|status|value|list_ID|value|publish|value";
    $MM_columnsStr =     $MM_columnsStr = "blast_type|',none,''|from_email|',none,''|from_name|',none,''|reply_to_address|',none,''|subject|',none,''|message_email_html|',none,''|message_email_text|',none,''|embargo|',none,''|sendformat|',none,''|message_template_ID|',none,''|status|',none,''|list_ID|',none,''|publish|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE blast_ID = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$List=$dbcon->Execute("SELECT id, name FROM blast_lists") or DIE($dbcon->ErrorMsg());
$T=$dbcon->Execute("SELECT id, name FROM blast_templates") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
$blast_type = new Input('hidden', 'blast_type', 'Email' );
$sendformat = new Input('hidden', 'sendformat', 'HTML and Text' );


//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');

if (!$R->Fields("status")) {$stat = "New";}
else {$stat = $R->Fields("status"); }
$blast_status = & new Input('hidden', 'status', $stat );
$html .=  $buildform->add_row('status', $stat.$blast_status->fetch());

$html .= addfield('subject','Subject','text',$R->Fields("subject"));
$html .= addfield('from_email','From Email','text',$R->Fields("from_email"));
$html .= addfield('from_name','From Name','text',$R->Fields("from_name"));
$html .= addfield('reply_to_address','Reply To Address','text',$R->Fields("reply_to_address"));

$Text = WYSIWYG('article',$R->Fields("message_email_html"),1);
$html .=  $buildform->add_row('HTML Email Message', $Text);

$html .= addfield('message_email_text','Text Email Message','textarea',$R->Fields("message_email_text"));


#$html .= addfield('embargo','Embargoed Till','text',$R->Fields("embargo"));
$list_options = makelistarray($List,'id','name','Select List');
$lists = & new Select('list_ID',$list_options,$R->Fields("list_ID"));
$html .=  $buildform->add_row('Send to List', $lists);

$t_options = makelistarray($T,'id','name','Select Template');
$temp = & new Select('message_template_ID',$t_options,$R->Fields("message_template_ID"));
$html .=  $buildform->add_row('HTML Email Template', $temp);

$html .= addfield('publish','Show Email on Website','checkbox',$R->Fields("publish"));


$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch().$sendformat->fetch().$blast_type->fetch());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
if ($_GET['action'] == "list") {
	if ($_REQUEST['response']) {
		echo '<p><b>'.$_REQUEST['response'].'</b></p>';
	}
	listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);
	echo '<p><a href="#" onClick="newwindow=window.open(\'blast_control.php?type=Email&process=1\',\'name\',\'height=20,width=150\'); alert (\'You may close the pop up window and your message will send without interpution\')" >START SENDING NEW EMAIL BLASTS</a></p>';

}
else {
	echo $form->fetch();
}	
include ("footer.php");

?>
