<?php
$modid = "30";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

if (isset($_GET['nons'])){
	$where_con = "  and nosql=1 "; 
}
$table = "navtbl";
$listtitle ="Navigation Files";
$listsql ="SELECT m.name as modname, n.name, n.id FROM navtbl n left join  modules m on  n.modid = m.id  $where_con ";
$orderby =" order by  m.name, n.name asc";
$fieldsarray=array( 'Module'=>'modname','Navigation File'=>'name');
$filename="nav.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
    if ($_POST['sql'] != NULL) {
		$nosql = 0;
	} else {
		$nosql = 1;
	}
	$MM_fieldsStr =
	
"name|value|sql|value|titleimg|value|titletext|value|titleti|value|linkfile|value|mfile|value|mcall1|value|mvar2|value|mcall2|value|repeat|value|linkextra|value|mvar1|value|linkfield|value|mvar1val|value|nosqlcode|value|nosql|value|templateid|value|modid2|value|rss|value";
    $MM_columnsStr = "name|',none,''|sql|',none,''|titleimg|',none,''|titletext|',none,''|titleti|none,1,0|linkfile|',none,''|mfile|',none,''|mcall1|',none,''|mvar2|',none,''|mcall2|',none,''|repeat|',none,''|linkextra|',none,''|mvar1|',none,''|linkfield|',none,''|mvar1val|',none,''|nosqlcode|',none,''|nosql|',none,''|templateid|',none,''|modid|',none,''|rss|',none,''";

	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$M = $dbcon->Execute("SELECT id, name FROM modules ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$T = $dbcon->Execute("SELECT name, id FROM template ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$C = $dbcon->Execute("SELECT class, id FROM class ORDER BY class ASC") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html = '<h2>Add/Edit '.$listtitle. '</h2>';
$html .= '

<script type="text/javascript">
function change(which) {
    document.getElementById(\'main\').style.display = \'none\';
	document.getElementById(\'advanced\').style.display = \'none\'; 
    document.getElementById(which).style.display = \'block\';
    }
</script>
<ul id="topnav">
	<li class="tab1"><a href="#" id="a0" onclick="change(\'main\');" >Basic</a></li>
	<li class="tab2"><a href="#" id="a1" onclick="change(\'advanced\');" >Advanced</a></li>
</ul>';

$html .= '<div id="main" style="display:block; clear: both;">';

$html  .= $buildform->start_table('name');

$html .= $buildform->add_header('Navigation Info', 'intitle');
$mod_options = makelistarray($M,'id','name','Select Module');
$Mod = & new Select('modid2',$mod_options,$R->Fields("modid"));
$html .=  $buildform->add_row('Module', $Mod);
$html .= addfield('name','Navigation Name','text',$R->Fields("name"));

$html .= $buildform->add_header('Navigation Content', 'intitle');
$html .= addfield('titletext','Navigation Title','text',$R->Fields("titletext"));
$html .= addfield('titleimg','Title Image','text',$R->Fields("titleimg"));
$html .= addfield('nosqlcode','Navigation HTML','textarea',$R->Fields("nosqlcode"));

$html .= $buildform->add_header('Navigation Template', 'intitle');
$template_options = makelistarray($T,'id','name','Select Template');
$Tempalte = & new Select('tempateid',$template_options,$R->Fields("tempateid"));
$html .=  $buildform->add_row('Template Override', $Tempalte);
$html .= addfield('linkextra','Link CSS Override','text',$R->Fields("linkextra"));

$html .= $buildform->end_table();
$html .= '</div><div id="advanced" style="display:none; clear: both;">';
$html  .= $buildform->start_table('advanced');

$html .= $buildform->add_header('RSS Based Navigation', 'intitle');
$html .= addfield('rss','RSS Feed URL','text',$R->Fields("rss"));

$html .= $buildform->add_header('Dynamic Navigation Settings', 'intitle');
$Type = & new Select('section', $obj->select_type_tree2(0));
$html .=  $buildform->add_row('Pull Content From Section', $Type);
$class_options = makelistarray($C,'id','class','Select Class');
$Class = & new Select('class',$class_options);
$html .=  $buildform->add_row('Pull Content From Class', $Class);
$html .= addfield('sql','SQL','textarea',$R->Fields("sql"));

$html .= $buildform->add_header('Link for Dynamic Content', 'intitle');
$html .= $buildform->add_row('','<div align="center">&lt;a href=1?2(or id)=3(or 
              $id)&gt; 4 (or $linktext) &lt;a&gt;<br>
              (where 3and 4 are field values from above sql)</div>');
$html .= addfield('linkfile','Link File (1)','text',$R->Fields("linkfile"));
$html .= addfield('mvar1','Other File Var (2)','text',$R->Fields("mvar1"));
$html .= addfield('mvar1val','Other File Var Value (3)','text',$R->Fields("mvar1val"));
$html .= addfield('linkfield','Other Link Field (4)','text',$R->Fields("linkfield"));

$html .= $buildform->add_header('Dynamic More Link', 'intitle');
$html .= addfield('repeat','Content Repeats before more link','text',$R->Fields("repeat"));
$html .= $buildform->add_row('','<div align=center>&lt;A HREF=1?list=2&amp;3=4&gt;more&lt;/a&gt;<br> (where 4 is the db field from above sql)</div>');
$html .= addfield('mfile','More link file (1)','text',$R->Fields("mfile"));
$html .= addfield('mcall1','More list name (2)','text',$R->Fields("mcall1"));
$html .= addfield('mvar2','More Var #2 (3)','text',$R->Fields("mvar2"));
$html .= addfield('mcall2','More Field #2 (4)','text',$R->Fields("mcall2"));
$html .= $buildform->end_table();
$html .= '</div>';
$html .= $buildform->start_table( 'buttons' );
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
