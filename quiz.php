<?php
include("AMP/BaseDB.php"); 
require_once("Modules/Quiz/Quiz.php");

$Q = new Quiz($dbcon,$_GET['quiz']);

$Q->answer = $_REQUEST['answer'];
$Q->id = $_REQUEST['quiz'];
$Q->set_section();
//$_GET['type'] = $Q->section;
$MM_type= $Q->section;


include("AMP/BaseTemplate.php"); 


if ($_REQUEST['answer'] && $_REQUEST['quiz']) {
	echo $Q->show_answer();
}

else if ($_REQUEST['quiz']){
	echo $Q->show_question();
}

else {
	// echo $Q->show_question_random();
}


require_once("AMP/BaseFooter.php");


?>