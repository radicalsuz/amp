<?php


$mod_name='udm';
require("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
require_once ( 'AMP/System/IntroText.inc.php' );
require_once ( 'AMP/System/Tool.inc.php' );
require_once ( 'AMP/System/UserData.php' );


$obj = new SysMenu; 
$buildform = new BuildForm;

$name = $_REQUEST['name'];

if ($_POST['MM_insert']) {

## insert UDM
	$uid = lowerlimitInsertID('userdata_fields', 50);
	$UDM = new AMPSystem_UserData ( $dbcon );
	$udm_data= array('id'=>$uid, 'name' =>$name);
	$UDM->setData($udm_data);
	$UDM->save();

##make new module
	
	$mid = lowerlimitInsertID('modules', 100);
	$file = "modinput4_data.php?modin=".$UDM->id;

	$module = new AMPSystem_Tool ( $dbcon );
	$module_data= array('id'=>$mid,'name' =>$name,'userdatamod'=>'1','userdatamodid'=>$UDM->id,'file'=>$file,'perid'=>AMP_PERMISSION_FORM_DATA_EDIT,'publish'=>'1','module_type'=>'1');
	$module->setData($module_data);
	$module->save();

# insert header text
	$hid = lowerlimitInsertID('moduletext',100);
	$hname = "$name Input";
	$linkpage = "modinput4.php?modin=".$modid;
	
	$header = new AMPSystem_IntroText ( $dbcon );
	$header_data= array('id'=> $hid,'title' =>$_REQUEST['htitle'],'test'=>$_REQUEST['harticle'],'name'=>$hname,'modid'=>$UDM->id, 'searchtype'=>$linkpage);
	$header->setData($header_data);
	$header->save();
	
##insert header response page
	$rname = "$name Thank You";
	$response = new AMPSystem_IntroText ( $dbcon );
	$response_data = array('title' =>$_REQUEST['rtitle'],'test'=>$_REQUEST['rarticle'],'name'=>$rname,'modid'=>$UDM->id);
	$response->setData($response_data);
	$response->save();

#update UDM
	$udm_data_update = array('modidinput'=>$header->id, 'modidresponse'=>$response->id );
	$UDM->mergeData($udm_data_update);
	$UDM->save();
	
	#ampredirect("modinput4_edit.php?modin=".$UDM->id);
}


//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add New Form', 'banner');
$html .= addfield('name','Form Name','text');

$html .= $buildform->add_header('Intro Text');

$html .= addfield('htitle','Intro Text Title','text');
$html .= addfield('harticle','Intro Text','textarea');
$html .= addfield('rtitle','Response Page Title','text');
$html .= addfield('rarticle','Response Page Text','textarea');

$html .= $buildform->add_content($buildform->add_btn() );
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
echo $form->fetch();
include ("footer.php");


?>
