<?php
$modid = "";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "template";
$listtitle ="HTML Templates";
$listsql ="select id, name   from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Template'=>'name','ID'=>'id'
					);
$filename="template.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
    $MM_fieldsStr =
"header2c|value|rnav1|value|rnav2|value|rnav3|value|rnav4|value|rnav5|value|rnav6|value|rnav7|value|rnav8|value|rnav9|value|rnav10|value|rnav11|value|rnav12|value|rnav13|value|lnav1|value|lnav2|value|lnav3|value|lnav4|value|lnav5|value|lnav6|value|lnav7|value|lnav8|value|lnav9|value|lnav10|value|lnav11|value|lnav12|value|lnav13|value|css|value|fp|value|imgpath|value|name|value";
    $MM_columnsStr = "header2|',none,''|rnav1|',none,''|rnav2|',none,''|rnav3|',none,''|rnav4|',none,''|rnav5|',none,''|rnav6|',none,''|rnav7|',none,''|rnav8|',none,''|rnav9|',none,''|rnav10|',none,''|rnav11|',none,''|rnav12|',none,''|rnav13|',none,''|lnav1|',none,''|lnav2|',none,''|lnav3|',none,''|lnav4|',none,''|lnav5|',none,''|lnav6|',none,''|lnav7|',none,''|lnav8|',none,''|lnav9|',none,''|lnav10|',none,''|lnav11|',none,''|lnav12|',none,''|lnav13|',none,''|css|',none,''|fp|',none,''|imgpath|',none,''|name|',none,''";
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
$html .= $buildform->add_header('Edit '.$listtitle, 'banner');
$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
$html .= addfield('name','Name','text',$R->Fields("name"));
$html .= $buildform->add_header('HTML Template');
$html .= $buildform->add_colspan('Add [-body-] [-left nav-] [-right nav-] to the template where you want content to appear');
$html .= addfield('header2c','HTML Template','textarea',$R->Fields("header2"),'',65,40);
$html .= $buildform->add_header('Left Side Navigation');
$html .= addfield('lnav3','left nav #1<br><br>start heading row','textarea',$R->Fields("lnav3"),'',65,6);
$html .= addfield('lnav4','left nav #2<BR><BR>end heading row','textarea',$R->Fields("lnav4"),'',65,6);
$html .= addfield('lnav7','left nav #3<BR><BR>start content table row<br>repeats','textarea',$R->Fields("lnav7"),'',65,6);
$html .= addfield('lnav8','left nav #4<BR><BR>end content table row<br>repeats','textarea',$R->Fields("lnav8"),'',65,6);
$html .= addfield('lnav9','left nav #5<br>spacer','textarea',$R->Fields("lnav9"),'',65,6);
$html .= $buildform->add_header('Right Side Navigation');
$html .= addfield('rnav3','right nav #1<br><br>start heading row','textarea',$R->Fields("rnav3"),'',65,6);
$html .= addfield('rnav4','right nav #2<BR><BR>end heading row','textarea',$R->Fields("rnav4"),'',65,6);
$html .= addfield('rnav7','right nav #3<BR><BR>start content table row<br>repeats','textarea',$R->Fields("rnav7"),'',65,6);
$html .= addfield('rnav8','right nav #4<BR><BR>end content table row<br>repeats','textarea',$R->Fields("rnav8"),'',65,6);
$html .= addfield('rnav9','right nav #5<br>spacer','textarea',$R->Fields("rnav9"),'',65,6);
$html .= $buildform->add_header('Template Paths');
$html .= addfield('css','CSS File','text',$R->Fields("css"),'custom/styles.css');
$html .= addfield('imgpath','Image Path','text',$R->Fields("imgpath"),'img/');

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
