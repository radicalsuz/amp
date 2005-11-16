<?php


$mod_name='udm';
require_once( 'AMP/System/Base.php');
require_once( "AMP/Form/Deprecated.inc.php");
#require("Connections/freedomrising.php");
#require_once("Connections/sysmenu.class.php");
require_once ( 'AMP/System/IntroText.inc.php' );
require_once ( 'AMP/System/Tool.inc.php' );
require_once ( 'AMP/System/UserData.php' );
require_once ( 'AMP/System/Page/Display.inc.php');


#$buildform = new BuildForm;
$form = &_buildNewFormForm( );
$name = $form->getItemName( );
$sub = $form->submitted( );

if ( 'cancel' == $sub ){
    ampredirect( "modinput4_list.php");
}
if ( 'save'==$sub && $form->validate( ) ) {
    $form_values = $form->getValues( );

## insert UDM
	$UDM = &new AMPSystem_UserData ( $dbcon );
	$udm_data= array('name' =>$name);
	$UDM->setData($udm_data);
	$UDM->save();

##make new module
	
	$file = "modinput4_data.php?modin=" . $UDM->id;

	$module = &new AMPSystem_Tool ( $dbcon );
	$module_data= array('name' =>$name,'userdatamod'=>'1','userdatamodid'=>$UDM->id,'file'=>$file,'perid'=>AMP_PERMISSION_FORM_DATA_EDIT,'publish'=>'1','module_type'=>'1');
	$module->setData($module_data);
	$module->save();

# insert header text
	$hname = "$name Input";
	$linkpage = "modinput4.php?modin=".$UDM->id;
	
	$header = &new AMPSystem_IntroText ( $dbcon );
	$header_data= array('title' =>$form_values['htitle'],'test'=>$form_values['harticle'],'name'=>$hname,'modid'=>$module->id, 'searchtype'=>$linkpage);
	$header->setData($header_data);
	$header->save();
	
##insert header response page
	$rname = "$name Thank You";
	$response = &new AMPSystem_IntroText ( $dbcon );
	$response_data = array('title' =>$form_values['rtitle'],'test'=>$form_values['rarticle'],'name'=>$rname,'modid'=>$module->id);
	$response->setData($response_data);
	$response->save();

#update UDM
	$udm_data_update = array('modidinput'=>$header->id, 'modidresponse'=>$response->id );
	$UDM->mergeData($udm_data_update);
	$UDM->save();
	
	ampredirect("modinput4_edit.php?modin=".$UDM->id);
}


//build form

function &_buildNewFormForm ( ){
    require_once( 'AMP/System/Form.inc.php');
    $form = &new AMPSystem_Form( 'newUDM') ;
    $fields = array( 
        'name'      =>  array( 'label'  =>  'Form Name',            'type'  =>  'text',     'required'  => true),
        'it_header' =>  array( 'label'  =>  'Input Page',           'type'  =>  'header'),
        'htitle'    =>  array( 'label'  =>  'Input Page Title',     'type'  =>  'text'),
        'harticle'  =>  array( 'label'  =>  'Intro Page Text',      'type'  =>  'textarea', 'size'  => '10:60'),
        'rp_header' =>  array( 'label'  =>  'Response Page',        'type'  =>  'header'),
        'rtitle'    =>  array( 'label'  =>  'Response Page Title',  'type'  =>  'text'),
        'rarticle'  =>  array( 'label'  =>  'Response Page Text',   'type'  =>  'textarea', 'size'  => '10:60')
        );
    $form->addFields( $fields );
    $form->enforceRequiredFields( );
    $form->removeSubmit( 'copy');
    $form->removeSubmit( 'delete');
    $form->Build( );
    return $form;
}
/*
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
*/

include ("header.php");
echo AMPSystem_Page_Display::pagetitle( 'New Form', 'Add');
echo $form->output();
include ("footer.php");


?>
