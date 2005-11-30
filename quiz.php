<?php
 

$intro_id = 63;
$modid = 46;
include("AMP/BaseDB.php");
require_once("Modules/Quiz/Quiz.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  


//$modid = 46;
//$mod_id = 69;

$Q = new Quiz($dbcon,$_GET['quiz']);

$Q->answer = $_REQUEST['answer'];
$Q->id = $_REQUEST['quiz'];
$Q->set_section();
//$_GET['type'] = $Q->section;
//$MM_type= $Q->section;




if ($_REQUEST['answer'] && $_REQUEST['quiz']) {
	echo $Q->show_answer();
	//echo $Q->show_next();

}

else if ($_REQUEST['quiz']){
	echo $Q->show_question();
}

else {
	echo $Q->show_question();
}


require_once("AMP/BaseFooter.php");


?>
