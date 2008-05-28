<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('Modules/Quiz/Quiz.php' );
require_once ('Modules/Quiz/ComponentMap.inc.php' );
require_once ( 'AMP/System/UserData.php');
require_once ( 'AMP/UserData/Input.inc.php');
require_once ( 'AMP/System/XMLEngine.inc.php');


class Quiz_Form extends AMPSystem_Form_XML {

	var $answers = array( '1'=>'Answer 1','2'=>'Answer 2','3'=>'Answer 3','4'=>'Answer 4','5'=>'Answer 5','6'=>'Answer 6','7'=>'Answer 7', );
    var $_question_fields = array( 'question', 'correct_answer', 'explanation', 'answer_1', 'answer_2', 'answer_3', 'answer_4', 'answer_5', 'answer_6', 'answer_7');
    var $_udm;

	function Quiz_Form() {
		$name = "Quiz";

		$this->init( $name );

	}

    function _after_init( ) {
		#$this->addTranslation( 'date', '_makeDbDateTime', 'get' );
		#$this->addTranslation( 'date_start', '_makeDbDateTime', 'get' );
		#$this->addTranslation( 'date_end', '_makeDbDateTime', 'get' );
        $header = &AMP_get_header( );
        $header->addJavascript( 'scripts/related.js', 'related_form');
        $header->addJavascriptOnload( 'RelatedQuestions = RelatedForm.create( document.forms["Quiz"], Array( "question", "explanation", "correct_answer", "answer_1", "answer_2", "answer_3", "answer_4", "answer_5", "answer_6", "answer_7"), "quiz", 31 );', 'load_related_qs');

        $this->addTranslation( 'modin', '_make_new_quiz_form', 'get');
        $this->addTranslation( 'modin', '_set_quiz_form', 'set');
        $this->addTranslation( 'questions', '_save_questions', 'get');
        #$header->addJavascriptDynamic(  'Event.observe( window, "load", function( ) {Event.observe( document.forms["Quiz"] , "submit", function(e ) { RelatedQuestions.prepare_submit( ); });});', 'related_form_submit');
    }

    function setDynamicValues( ) {
		$this->setFieldValueSet( 'correct_answer' , $this->answers );
    }

    function _set_quiz_form( $data, $fieldname ) {
        if ( !( isset( $data[$fieldname]) && $data[$fieldname] )) return false;
        $this->_udm = &new UserDataInput( AMP_Registry::getDbcon( ), $data[$fieldname]);
    }

    function _make_new_quiz_form( $data, $fieldname ) {
        if ( !( isset( $data[$fieldname ]) && $data[$fieldname ])) {
            $new_form = new AMPSystem_UserData( AMP_Registry::getDbcon( ));
            $new_form->setDefaults( );
            $new_form->mergeData( array( 'name' => $data['name'], 'publish_form' => $data['publish'] ));
            $new_form->save( );
            $udm = &new UserDataInput( AMP_Registry::getDbcon( ), $new_form->id, true );
            $read_plugin = &$udm->registerPlugin( "AMP", 'Save');
            $read_plugin->saveRegistration( 'AMP', 'Save');
            $save_plugin = &$udm->registerPlugin( 'AMP', 'Read');
            $save_plugin->saveRegistration( 'AMP', 'Read');
        } else {
            $udm = &new UserDataInput( AMP_Registry::getDbcon( ), $data[$fieldname], true );
        }
        $this->_udm = &$udm;
        $override_plugin = &$udm->registerPlugin( "QuickForm", 'Override');
        if( !$override_plugin->plugin_instance ) {
            $override_plugin->saveRegistration( 'QuickForm', 'Override');
            $override_plugin->saveOption( 'override_file', "form.{$udm->instance}.quiz.xml");
        }
        return $udm->instance;
    }

    function _save_questions( $data, $fieldname ) {
        trigger_error( "running save questions");
        $questions = array( );
        foreach( $this->_question_fields as $field ) {
            if( isset( $data['quiz'][$field]) and is_array( $data['quiz'][$field])) {
                foreach( $data['quiz'][$field] as $index => $value ) {
                    if( !isset( $questions[$index])) $questions[ $index ] = array( );
                    $questions[$index][$field] = $value;
                }
            }
        }
        $udm_fields = array( );
        foreach( $questions as $index => $question ) {
            $current_field = array( );
            $current_field['label'] = $question['question'];
            $current_field['correct_answer'] = $question['correct_answer'];
            $current_field['explanation'] = $question['explanation'];
            $current_field['type'] = 'radiogroup';
            for( $answer_index=1; $answer_index<8; $answer_index++) {
                if ( !( isset( $question["answer_$answer_index"]) && $question["answer_$answer_index"])) continue;
                $current_field[] = array( 'key' => $answer_index, 'value' => $question[ "answer_$answer_index"]);
            }
            $udm_fields["custom$index"] = $current_field;
        }
        $xml_writer = new AMPSystem_XMLEngine( "form.{$this->_udm->instance}.quiz.xml");
        $xml_writer->save( $udm_fields);

    }


 
    function _selectAddNull( $value_set, $name ) {
        if ( $name == 'modin' ){
            return array( '' => 'Create new quiz') + $value_set;
        }
        return parent::_selectAddNull( $value_set, $name );

    }

    function _blankValueSet( $value_set, $name ){
        if ( $name == 'modin') {
            return array( '' => 'Create new quiz');
        }
        return parent::_blankValueSet( $value_set, $name );
    }

	function _makeDbDateTime( $date_array ) {
        if( !$date_array ) return;
		if ($date_array ['a'] == 'pm') $date_array['h']+=12;

		$stamp = mktime(0, 0, 0, $date_array['M'], $date_array['d'], $date_array['Y']);
		return date('YmdHis', $stamp);
	}

}

?> 
