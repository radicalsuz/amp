<?php

#require_once('AMP/PostSet.php');

class HTML_List {

var $options;
var $dataset;
var $list_sql;
var $dbcon;
var $post_session;
var $name;
var $recordcount;
var $jumpto_set;
var $persistent_values;

	//Init function

	function HTML_List($options=null) {
		//Assign a name
		$this->name=(isset($options['name']))?$options['name']:'List';

		//define the defaults
		$this->define_list_options($options);
		
		
		$this->readFormOptions();
	}

	
	//default list options
	
	function define_list_options($options=null) {
		$this->options['current_offset']=0;
		$this->options['qty_displayed']=25;
		$this->options['list_form']=str_replace(" ", "_", $this->name."_form");
		$this->options['count_field']='id';
		$this->options['no_matches_message']="&nbsp;<P>No items are available at this time";
		$this->options['header_display_function']='display_header';
		$this->options['item_display_function']='display_item';
		$this->options['footer_display_function']='display_footer';
		$this->options['is_dynamic']=true;
		if (is_array($options)) { $this->options=array_merge($this->options, $options); }


		//setup persistent values for the list
		$this->persistent_values=array('sort_by', 'current_offset', 'qty_displayed', 'list_criteria');
		
		if (isset($options['persistent'])&&$options['persistent']) {
			$per_set=split(",", $options['persistent']);
			foreach ($per_set as $per_value) {
				$this->persistent_values[]=$per_value;
			}
		}

		

	}

	function output_options() {
		foreach ($this->options as $key=>$value) {
			$output.= $key.": ".strip_tags($value)."<BR>";
		}
		return $output;
	}



	//create SQL statement from current list options
	function _render_sql($save_it=true) {
		$options=&$this->options;
		/*
		if(!isset($parts['query'])) {
			$parts['select']=$this->options['display_fields'];
			$parts['where']=$this->options['list_criteria'];
			$parts['from']=$this->options['datatable'];
			$parts['orderby']=$this->options['sort_by'];
			
		}
		*/
		$query="SELECT ".$options['display_fields'].$options['hidden_fields'];
		if (isset($options['sort_by'])) $query.=$this->setupSort();
		$query.=" FROM ".$options['datatable'];

		if (isset($options['list_criteria'])) {			
			#print '<B>criteria set in renderer</b>';
			$query.=" WHERE ".$options['list_criteria'];
		}
/*		if (isset($options['group_by'])) {
			$query .= " GROUP BY " . $options['group_by'];
		} */
		if (isset($options['sort_by'])) {
			$query.=" ORDER BY ".$options['sort_by'];
		}
if ( $_REQUEST['debug'] ) print $query;		

		if ($save_it) $this->list_sql=$query;
		
		return $query;
	}


	//return the results of the current list as an array or recordset
	//take no other action
	function returndataset(&$dbcon, $options=null, $format="array") {
		$this->dbcon=&$dbcon;
		
		//Overwrite existing options with passed options
		if (isset($options)) { $this->options=array_merge($this->options, $options);}
		
		//Set SQL values 

		if (!isset($this->list_sql)) {
			if (isset($this->options['sql'])) {
				$this->list_sql=$this->options['sql'];
			} else {
				$this->_render_sql();
			}
		}
		if ($format=="array") {
			return $dbcon->getArray($this->list_sql);
		} else {
			return $dbcon->Execute($this->list_sql);
		}
	}


	//master output function

	function output (&$dbcon, $options=null) {
			$this->dbcon=&$dbcon;
			//Overwrite existing options with passed options
		if (isset($options)) { $this->options=array_merge($this->options, $options);}
		#print '<BR>recieved_crit:'.$this->options['list_criteria'];
		
		//Set SQL values 
		if (!isset($this->list_sql)) {
			if (isset($this->options['sql'])) {
				$this->list_sql=$this->options['sql'];
			} else {
				$this->_render_sql();
			}
		}
		//Get the recordcount
		$count_sql="SELECT count(".$this->options['count_field'].") as qty".substr($this->list_sql, strpos($this->list_sql, " FROM"));
		#print "COUNT: ".$count_sql."<BR>";
		$datacount_set=$dbcon->getArray($count_sql);
		$this->recordcount=$datacount_set[0]['qty'];
		if ($this->recordcount==0) {
			$this->error=$this->make_header();
			$this->error.=$this->options['no_matches_message'];
			
			return false;
		}
	
		//set offset to 0 if display qty exceeds results;
		if ($this->options['qty_displayed']>$this->recordcount) $this->options['current_offset']=0;

		//Get the current page
		$page_sql=$this->list_sql;
		if ($this->options['qty_displayed']!="*") {
			$page_sql.='  limit '.strval($this->options['current_offset']). ", ".strval($this->options['qty_displayed']);
		}

		#print "PAGE: ".$page_sql."<BR>";
		$this->dataset=$dbcon->getArray($page_sql);
		#if (!is_array($this->dataset)) return "&nbsp;<P>No items are available at this time";
		##print $this->list_sql['query']; 

		//Make Header
		$output.=$this->make_header();
	

		//determine display format
		if (($inclass=method_exists($this, $this->options["item_display_function"]))||function_exists($this->options["item_display_function"])) {
			$display_function=$this->options["item_display_function"];
		} else {
			$output.="The display function: ".$this->options["item_display_function"]." was not found.  Using default<BR>.";
			$display_function="display_item";
			$inclass=true;
		}

		//output display format
		foreach($this->dataset as $dataitem) {
			if($inclass) $output.=$this->$display_function($dataitem, $this->options);
			else $output.=$display_function($dataitem, $this->options);
		}

		//determine footer format
		if (method_exists($this, $this->options["footer_display_function"])) {
			$func=$this->options["footer_display_function"];
			$output.=$this->$func();
		} elseif (function_exists($this->options["footer_display_function"]) ) {
			$output.=$this->options["footer_display_function"]($this->dataset, $this->options);
		} else {
			$output.="The display function: ".$this->options["footer_display_function"]." was not found.  Using default<BR>.".$this->display_footer();
		}
		//close the form if the whole list is one form
		if ($this->options['allow_select']) { $output.="</form>"; }

		return $output;
}



/// Header for List Pages
/// CREATES the Prev/Next/Current Location/Qty/Go Control Set for the Results list
	function make_header () {
		$options=&$this->options;
		$dataset=&$this->dataset;

		//include persistent list variables in a hidden form or object
		if ($this->recordcount>0&&$options['is_dynamic']) {
			$output.=$this->writeFormOptions();
		}
		//determine the header format
		if (method_exists($this, $this->options["header_display_function"])) {
			$func=$this->options["header_display_function"];
			$output.=$this->$func();
		} elseif (function_exists($this->options["header_display_function"]) ) {
			$output.=$this->options["header_display_function"]($this->dataset, $this->options);
		} else {
			$output.="The display function: ".$this->options["header_display_function"]." was not found.  Using default<BR>.".$this->display_header();
		}

		return $output;
	}


	function display_header() {
		return $this->page_controls();
	}

	function display_footer() {
		return $this->page_controls();
	}

	function display_item($data_item, $options) {
		foreach ($data_item as $key=>$data_part) {
			$output.=$key.": ".$data_part.'<BR>';
		}
		return $output;
	}

	//Create the Page Controls for the list
	
	function page_controls() {
		$options=&$this->options;
		$dataset=&$this->dataset;
		
		//Setup variable names in case of session
		if (isset($this->post_session)) { 
			$options['post_key_form']=$this->post_session->form_name;
			$options['post_key']=$this->post_session->getKey($this->name);
			$offsetname=$post_key.'current_offset';
		} else {
			$options['post_key_form']=$options['list_form'];
			$options['post_key']='';
			$offsetname='current_offset';
		}



		if (!isset($options['control_class'])) $options['control_class']="sidelist";
		$output .="<div class=".$options['control_class']." style=\"width:100%;text-align:center;padding-bottom:5px;padding-top:2px;background-color:#E5E5E5;\">";
		
		if ($options['qty_displayed']=="*") {$options['qty_displayed']=$this->recordcount;}
		if ($this->recordcount>$options['qty_displayed']) {
		

			//PREV button
			if ($options['current_offset']>0) {
				$output .= "&nbsp;<a href=\"javascript: document.forms['".$options['post_key_form']."'].elements['".$offsetname."'].value='";
				if ($options['current_offset']>$options['qty_displayed']) {
					$output.= $options['current_offset']-$options['qty_displayed']; 
				} else {
					$output.="0";
				}
				$output.="'; document.forms['".$options['list_form']."'].submit();\"><< Prev </a>";	
			}
			//NEXT button
			if ($this->recordcount > ($options['current_offset']+$options['qty_displayed'])){
				$output .= "&nbsp;&nbsp;<a href=\"javascript: document.forms['".$options['post_key_form']."'].elements['".$offsetname."'].value=".($options['current_offset']+$options['qty_displayed'])."; document.forms['".$options['post_key_form']."'].submit();\">Next >></a>";
			}
		}
		
		//Current Location

		$output.="&nbsp;Showing ".$options['current_offset']."-";
		if ($this->recordcount < ($options['current_offset']+$options['qty_displayed'])) {
			$output .= $this->recordcount;
		} else {
			$output .= ($options['current_offset']+$options['qty_displayed']);
		}
		$output.=" of ".$this->recordcount;

		//Display Qty choice - convert the qty back to a * for listbox
		if ($options['qty_displayed']==$this->recordcount) {$options['qty_displayed']="*";}
		$output.="&nbsp;&nbsp;".$this->qty_choice();
		if ($options['qty_displayed']=="*") {$options['qty_displayed']=$this->recordcount;}

		if ($this->recordcount>$options['qty_displayed']) {
			//Go: Jumpto box
			$output.="&nbsp;".$this->jumpto_box();
		}

		$output.="</div>";
	
		return $output;
	}

////Creates the HTML for the Go Box on Paginated Lists
	function jumpto_box () {
		$options=&$this->options;
		#print "<BR>sort: ".$this->options['sort_by'];
		$output=" Go:&nbsp;<SELECT name=\"List_offset\" class=\"".$options['control_class']."\" onchange=\"document.forms['".$options['post_key_form']."'].elements['".$options['post_key']."current_offset'].value=this.value; document.forms['".$options['post_key_form']."'].submit();\">";
		$sortfields = explode(",", $options['sort_by']);
		$sortfields[0]=str_replace(" DESC", " ", $sortfields[0]);
		$sortfields[0]=str_replace(" ASC", " ", $sortfields[0]);
		$mysort=trim($sortfields[0]);
		$mysort_alias=(isset($options['Lookups'][$mysort]['LookupName']))?$options['Lookups'][$mysort]['LookupName']:$mysort;
		if (!isset($this->jumpto_set)) {
			$jumpto_sql="SELECT ".$options["sort_by"].substr($this->list_sql, strpos($this->list_sql, " FROM"));
			#print "sql:".$jumpto_sql."<BR>";
			#print "mysort:".$mysort;
			$this->jumpto_set=$this->dbcon->getArray($jumpto_sql);
		}
		for ($n=0;$n<$this->recordcount; $n=$n+$options['qty_displayed']) {
			$output.="<option value=\"$n\"";
			if ($n==$options['current_offset']) {
				$output.=" selected";
			}
			$output.=">".$mysort_alias.": ";
			if (isset($options['Lookups'][$mysort]['LookupSet'])) {
				$output.=$options['Lookups'][$mysort]['LookupSet'][$this->jumpto_set[$n][$mysort]];
			} else {
				$output.=$this->jumpto_set[$n][$mysort];
			}
			$output.="</option>";
		}
		$output.="</select>";
		return $output;
	}

	///// CREATES the Display Quantity Select Box
	function qty_choice() {
		$options=&$this->options;
		$output="Qty:&nbsp;<SELECT name=\"qty_selector[]\" class=".$options['control_class']."  onchange=\"document.forms['".$options['post_key_form']."'].elements['".$options['post_key']."qty_displayed'].value=this.value;document.forms['".$options['post_key_form']."'].submit();\" class=\"name\">";
		if ($options['qty_displayed']<50&&is_numeric($options['qty_displayed'])) {
			$display_options[$options['qty_displayed']]=$options['qty_displayed'];
		}
		$display_options['50']='50';
		$display_options['100']='100';
		$display_options['*']='All';
		foreach ($display_options as $dvalue => $dtext) {
			$output .= "<option value=\"".$dvalue."\"";
			if ($dvalue==$options['qty_displayed']) {
				$output .= " selected";
			}
			$output .=">".$dtext."</option>";
		}
		
		$output.="</select>";
		return $output;
	}







	
	/**************************
	  * Variable Management
	  * Passing Options from one page to the next
	  * The routines here are default
	  * as part of a larger application a PostSet class should be used
	  *
	  *
	  */

	//Master Read Function
	//runs on List Init

	function readFormOptions() {
		global $MM_sysvar_mq;
		
		
		//check to see if the options are being passed from a postset object
		if (!isset($this->post_session)) {
			//if not use standard reads
			#print "checking persistent vals<BR>";
			foreach ($this->persistent_values as $pkey) {
				if (isset($_REQUEST[$pkey])&&$_REQUEST[$pkey]) {
					$this->options[$pkey]=$_REQUEST[$pkey];
					if ($MM_sysvar_mq) { $this->options[$pkey]=stripslashes($_REQUEST[$pkey]); }
				}
				#print "$pkey :".$this->options[$pkey]."<BR>";
			}
		} else {
			//get vars from postset
			$postvars=$this->post_session->getItem($this->name);
			$this->options=array_merge($this->options, $postvars);
		}
		
		
		//reset current offset if necessary
		if ($this->options['qty_displayed']=="*"){
			$this->options['current_offset']=0;
		}
		#print "<BR>OFFSET: ".$this->options['current_offset'];
		
		//re-compute the Sort if multiple items are in sort string
		if (!strpos($this->options['sort_by'], ",")===FALSE) {
			$this->computeSort($this->options['sort_by']);
		}

		/*
		//render the SQL query with passed criteria values
		$this->list_sql['where']=$this->options['list_criteria'];
		$this->_render_sql();
		*/
	}

	//hold a value during pagination calls
	//call this for any values you want to keep
	//from page to page
	function hold($key, $value) {
		$this->persistent_values[]=$key;
		$this->options[$key]=$value;
	}

	//Output function
	//runs on output

	function writeFormOptions() {
		$options=&$this->options;
		$form.="<form name='".$options['list_form']."' action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method=\"POST\">";

		if (!isset($this->post_session)) {
			foreach ($this->persistent_values as $pkey) {
				$form.="<input name=\"".$pkey."\" value=\"".$options[$pkey]."\" type=\"hidden\">";
			}
			#<input name=\"offset\" value=\"".$options['current_offset']."\" type=\"hidden\"><input name=\"list_page_qty\" value=\"".$options['qty_displayed']."\" type=\"hidden\"><input name=\"list_criteria_sql\" value=\"".$options['list_criteria']."\" type=\"hidden\">
			
		} else {
			foreach ($this->persistent_values as $item) {
				$per_set[$item]=$options[$item];
			}
			$this->post_session->newItem($this->name, $per_set);

		}
	
	
		if (!$this->options['allow_select']) {
			$form.="</form>";
		}

		if (isset($options['message'])) {$form=$options['message']."<BR>".$form;}

		return $form;
	}

	//returns Sort columns for inclusion in a SQL query
	function setupSort() {
		$options=&$this->options;
		if($sort_set = explode(',', $options['sort_by'])) {
			$sort_set[0]=str_replace(" DESC", " " , $sort_set[0]);
			$sort_set[0]=str_replace(" ASC", " ", $sort_set[0]);
			$primary_sort=trim($sort_set[0]);
			$output = ", ".$primary_sort." as hide_sort_".$primary_sort;
		}
		return $output;
	}

//sets the sort to Descending when a sort item appears twice in sort list

	function computeSort($sortvalue) {
		$sort_set=explode(",", $sortvalue);
		$final_sort='';
		foreach ($sort_set as $this_sort) {
			$this_sort=str_replace(" DESC", " " , $this_sort);
			$this_sort=str_replace(" ASC", " ", $this_sort);
			$this_sort=trim($this_sort);
			if (strpos($final_sort, $this_sort)===FALSE) {
				//sort descending when sortfield appears in form data twice
				//but not if the sortfield is already DESC
				if (substr_count($sortvalue, $this_sort)>1&&strpos($sortvalue, $this_sort." DESC") === FALSE) {
					$final_sort.=$this_sort." DESC, ";
				} else {
					$final_sort.=$this_sort.", ";
				}
			}
		}

		$this->options['sort_by']=substr($final_sort, 0, strlen($final_sort)-2);

	
	}






}

?>
