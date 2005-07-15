<?php
$modid = "11";
$mod_name = 'links';

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "links";
$listtitle ="Links";
$listsql ="select id, url, linkname, publish from $table  ";
$orderby =" order by linkname asc  ";
$fieldsarray=array( 'Link Name'=>'linkname',
					'URL'=>'url',
					'Status'=>'publish');
$filename="links.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
	$MM_editColumn = "id";
    $MM_fieldsStr = "linkname|value|description|value|linktype|value|url|value|type|value|publish|value|image|value";
    $MM_columnsStr = "linkname|',none,''|description|',none,''|linktype|',none,''|url|',none,''|type|',none,''|publish|none,1,0|image|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	
	if ($_POST['MM_insert']) {
		$MM_recordId =$dbcon->Insert_ID();
	} 
	$reldelete=$dbcon->Execute("Delete FROM linksreltype WHERE linkid =$MM_recordId") or DIE($dbcon->ErrorMsg());
	if ((!$_POST['MM_delete']) && ($_POST['reltype'])) {
		while (list($k, $v) = each($reltype)) { 
			if ($v) {
				$dbcon->Execute("INSERT INTO linksreltype VALUES ( $MM_recordId,$v)") or DIE($dbcon->ErrorMsg());
			}
		}
	}
	redirect($filename."?action=list");

    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$L=$dbcon->Execute("SELECT * FROM linktype") or DIE($dbcon->ErrorMsg());
$related=$dbcon->Execute("SELECT typeid FROM linksreltype where  linkid = " . ($R__MMColParam) . "") or DIE("40".$dbcon->ErrorMsg());

while (!$related->EOF) {
	$rel[$related->Fields("typeid")] = $related->Fields("typeid");
	$related->MoveNext();
}


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"));
$html .= addfield('url','Link URL','text',$R->Fields("url"));
$html .= addfield('linkname','Name','text',$R->Fields("linkname"));
$html .= addfield('description','Description','textarea',$R->Fields("description"));

$link_options = makelistarray($L,'id','name','Select Link Type');
$Link = & new Select('linktype',$link_options,$R->Fields("linktype"));
$html .=  $buildform->add_row('Link Type', $Link);


$html .= $buildform->add_header('Image');
$html .= addfield('image','Thumbnail','text',$R->Fields("image"));
$html .=  $buildform->add_row('', '<a href="imgdir.php" target="_blank">view images</a>&nbsp;|&nbsp;<a href="imgup.php" target="_blank">upload image</a>');

$html .= $buildform->add_header('Section');
$Type = & new Select('type', $obj->select_type_tree2(0),$R->Fields("type"));
$html .=  $buildform->add_row('Section', $Type);

$Type = & new Select('reltype[]', $obj->select_type_tree2(0),$rel,'true',10);
$html .=  $buildform->add_row('Related Sections', $Type);




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
