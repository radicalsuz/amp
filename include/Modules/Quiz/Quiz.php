<?php

require_once ( 'AMP/System/Data/Item.inc.php' );


 class AMPSystem_Quiz extends AMPSystem_Data_Item {


    var $datatable = "quiz";
    var $name_field = 'name';

    function AMPSystem_Quiz ( &$dbcon, $id=null ) {
        $this->init( $dbcon, $id );
    }
    


 }



class Quiz {

	var $dbcon;
	var $id;
	var $answer;
	var $q;
	var $section;
	
	
	function Quiz($dbcon) {
		$this->dbcon = $dbcon;
	}
	
	function get_quiz() {
		if ($this->id) {
			$sql = 'select * from quiz where id = '.$this->id;
		} else {
			$sql = 'select * from quiz where id =1';
			
		}
		$this->$q = $this->dbcon->CacheExecute($sql) or DIE($this->dbcon->ErrorMsg());
		$this->id = $this->$q->Fields("id");
	}
	
	function show_question() {
		$this->get_quiz();
		$o .= '<p>'.$this->$q->Fields("question").'</p>';
		$o .= '<form action="quiz.php" method="post">';
		$o .= '<input type="hidden" name="quiz" value="'.$this->id.'">';
		$o .= '<table>';
		$o .= $this->build_questions(); 
		$o .= '</table>';
		$o .= '<input type="Submit" value="Submit">';
		$o .= '</form>';
		return $o;
	}
	
	function show_question_random() {
		$sql = 'select id from quiz';
		$C = $this->dbcon->CacheExecute($sql) or DIE($this->dbcon->ErrorMsg());
		$C->RecordCount();

	}
	
	function show_answer() {
		$this->get_quiz();
		if ($this->answer == $this->$q->Fields("correct_answer")) {
			$o = '<p><font color="red">Your answer is correct!</font></p>';
		} else {
			$o = '<p><font color="red">Sorry, your answer is wrong.</font></p>';
		}
		$o .= '<p><b>Question:  </b>'.$this->$q->Fields("question").'<br>';	
		$field = 'answer_'.$this->$q->Fields("correct_answer");  
		$o .= '<b>Answer:  </b>'.$this->$q->Fields($field).'<br><br>';	
		$o .= $this->$q->Fields("explanation").'<br><br>';	
		if ($this->$q->Fields("link")) {
			$o .= '<b>Learn more here:</b><br>  ';
			$o .= '<a href="'.$this->$q->Fields("link").'" target="_blank">'.$this->$q->Fields("link").'</a><br>';
			if ($this->$q->Fields("link_2")) {
				$o .= '<a href="'.$this->$q->Fields("link_2").'" target="_blank">'.$this->$q->Fields("link_2").'</a><br>';
			}
			if ($this->$q->Fields("link_3")) {
				$o .= '<a href="'.$this->$q->Fields("link_3").'" target="_blank">'.$this->$q->Fields("link_3").'</a><br>';
			}
		}

			$o .= $this->show_next_question();
		return $o;
	}
	
	function show_next_question() {
		$ct = $this->dbcon->CacheExecute("select count(id) from quiz where publish =1 ") or DIE($this->dbcon->ErrorMsg());
		$count =  $ct->Fields("count(id)");
		if ($this->id <= ($count-1)) {
			$o = '<p><a href="quiz.php?quiz='.($this->id + 1).'">Next Question</a></p>';
		} else {
			$o = '<br><br><h3><b>Thank you for taking our quiz!<br><br>Learn more by taking a look around our website</h3>';
		}
		
		return $o;
	}
	function build_questions(){
		$x =1 ;
		while ($x<8) {
			$field = 'answer_'.$x; 
			if ($this->$q->Fields($field)) {
				$o.= '<tr><td><input type ="radio" name="answer" value="'.$x.'"></td><td>'.$this->$q->Fields($field).'</td><tr>';
			}
			$x++;
		}
		return $o;
	}

	function set_section(){
		$this->get_quiz();
		$this->section = $this->$q->Fields("section");
	
	}

}
?>
