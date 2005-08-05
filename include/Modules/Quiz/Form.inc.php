<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('AMP/Content/Map/Select.inc.php');
require_once ('Modules/Quiz/Quiz.php' );
require_once ('Modules/Quiz/ComponentMap.inc.php' );


class Quiz_Form extends AMPSystem_Form_XML {

	var $inital_form_links = array();
	var $answers = array( '1'=>'Answer 1','2'=>'Answer 2','3'=>'Answer 3','4'=>'Answer 4','5'=>'Answer 5','6'=>'Answer 6','7'=>'Answer 7', );

	function Quiz_Form() {
		$name = "Quiz";

		$this->init( $name );
		$this->addTranslation( 'date', '_makeDbDateTime', 'get' );
		$this->addTranslation( 'date_start', '_makeDbDateTime', 'get' );
		$this->addTranslation( 'date_end', '_makeDbDateTime', 'get' );

	}
 
   function setDynamicValues() {
		
        $map = ContentMap_Select::getIndentedValues();
        $this->setFieldValueSet( 'section' , $map );
		$this->setFieldValueSet( 'correct_answer' , $this->answers );
		
    }

	function _makeDbDateTime( $date_array ) {
		if ($date_array ['a'] == 'pm') $date_array['h']+=12;

		$stamp = mktime(0, 0, 0, $date_array['M'], $date_array['d'], $date_array['Y']);
		return date('YmdHis', $stamp);
	}

}

?> 
