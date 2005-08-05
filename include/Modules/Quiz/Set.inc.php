<?php

require_once ('AMP/System/Data/Set.inc.php' );

class QuizSet extends AMPSystem_Data_Set {

	var $datatable = "quiz";

	function QuizSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
