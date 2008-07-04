<?php
 

$intro_id = 63;
$modid = 46;
include("AMP/BaseDB.php");
require_once("Modules/Quiz/Quiz.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  


//$modid = 46;
//$mod_id = 69;
$quiz_id = isset( $_REQUEST['quiz_id'] ) && $_REQUEST['quiz_id'] ? intval(  $_REQUEST['quiz_id']) : false;
$question_id = isset( $_REQUEST['question_id'] ) && $_REQUEST['question_id'] ? intval(  $_REQUEST['question_id']) : false;
$answer_id = isset( $_REQUEST['answer_id'] ) && $_REQUEST['answer_id'] ? intval(  $_REQUEST['answer_id']) : false;

$Q = new Quiz($dbcon, $quiz_id );

$Q->answer = $answer_id;
$Q->id = $question_id;
$Q->set_section();




if ( $answer_id && $question_id ) {
	echo $Q->show_answer();
	//echo $Q->show_next();

} else {
	echo $Q->show_question();
}


require_once("AMP/BaseFooter.php");


?>
