<?php

# system email plugin
#this will display the details of emails sent to a person 

#$sql ="select b.created, b.subject, b.from_email from blast_system_users u, blast b where u.blast_ID = b.blast_ID where u.Email = '".$email."'" ;

#
# build form
# set hidden value as email array
# 
ob_start();
$mod_name="email";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
require_once("WYSIWYG/editor.php");

$obj = new SysMenu; 
$buildform = new BuildForm;
require("AMP/Blast/EmailBlast.php");

ob_start();
if (isset($_REQUEST['sendformat'] )) {
	$bc = new EmailBlast($dbcon);	
	$sql = "Select distinct Email ".stripslashes($_POST['passedsql']);
	$E=$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());
	while (!$E->EOF) {
		$emails[]=$E->Fields("Email");	
		$E->MoveNext();
	}
	
	$message = array('subject'=>$_POST['subject'],
					'messagetext'=>$_POST['message_email_text'],
					'messagehtml'=>$_POST['article'],
					'from_email'=>$_POST['from_email'],
					'from_name'=>$_POST['from_name'],
					'replyto_email'=>$_POST['reply_to_address']
					);
	$response = $bc->new_system_blast($emails,$message);
	$location= "modinput4_data.php?modin=".$_POST['modin']."&response=".$response;
	#ampredirect($location);
	ob_end_flush();
}



if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM blast WHERE blast_ID = $R__MMColParam") or DIE($dbcon->ErrorMsg());
#$T=$dbcon->Execute("SELECT id, name FROM blast_templates") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
$blast_type = new Input('hidden', 'blast_type', 'System-Email' );
$sendformat = new Input('hidden', 'sendformat', 'HTML and Text' );
$passedsql = new Input('hidden', 'passedsql', stripslashes($_POST['sqlp'] ));
$modin = new Input('hidden', 'modin', $_POST['modin'] );


//build form
$html  = $buildform->start_table('name');




$html .= $buildform->add_header('Send System Email', 'banner');

if (!$R->Fields("status")) {$stat = "New";}
else {$stat = $R->Fields("status"); }
$blast_status = & new Input('hidden', 'status', $stat );


$html .= addfield('subject','Subject','text',$R->Fields("subject"));
$html .= addfield('from_email','From Email','text',$R->Fields("from_email"));
$html .= addfield('from_name','From Name','text',$R->Fields("from_name"));
$html .= addfield('reply_to_address','Reply To Address','text',$R->Fields("reply_to_address"));

$Text = WYSIWYG('',$R->Fields("message_email_html"),1);
$html .=  $buildform->add_row('HTML Email Message', $Text);

$html .= addfield('message_email_text','Text Email Message','textarea',$R->Fields("message_email_text"));

#$t_options = makelistarray($T,'id','name','Select Template');
#$temp = & new Select('message_template_ID',$t_options,$R->Fields("message_template_ID"));
#$html .=  $buildform->add_row('HTML Email Template', $temp);

$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch().$sendformat->fetch().$blast_type->fetch().$passedsql->fetch().$modin->fetch());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);


include ("header.php");
if ($response) {
	echo "<h3>$response</h3>";
} else {
	echo $form->fetch();
}
include ("footer.php");

?>
