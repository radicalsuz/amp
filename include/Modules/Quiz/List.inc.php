<?php

require_once ('Modules/Quiz/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class Quiz_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "Name" => "name" );
	var $editlink = "quiz.php";
	var $name = "Quiz";

	function Quiz_List ( &$dbcon ) {
		$source = & new QuizSet( $dbcon );
		$this->init ($source );
	}

}
?>
