<?php
/*****
 *
 * AMP UserData Search Object
 *
 * (c) 2004 Radical Designs
 * 
 *****/

require_once( 'AMP/UserData/Set.inc.php' );

class UserDataSearch extends UserDataSet {

    function UserDataSearch( &$dbcon, $instance, $admin = false ) {

        $this->UserDataSet( $dbcon, $instance, $admin );

        $this->results = array();

    }

    function doSearch () {

        $this->_build_search();

    }

    function saveSearch () {

    }

    function output ( $format = 'html', $options = null ) {

        if ( strpos( $format, 'search_' ) === false ) {
            if ( count( $this->results ) > 0 ) {
                $format = 'search_results_' . $format;
            } else {
                $format = 'search_form_' . $format;
            }
        }

        $parent = get_parent_class( $this );
//        return $parent::output( $format, $options );

    }

    function showForm ( $format = 'html', $options ) {

        return $this->output( 'search_form_' . $format, $options );

    }

    function _build_search () {

    }

}

class UserList {
	var $action_fields="modin";
	var $display_fields="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone, modin";
	var $current_fieldset;
	var $search_criteria;
	var $page_name="modinput4_search.php";
	var $search_tables="userdata";
	var $search_logic;
	var $is_parent;
	var $is_included;
	var $search_hier;
	var $default_qty=50;
	var $current_offset;
	var $qty_displayed;
	var $include_modin;
	var $base_modin;
	var $sort_by="Last_Name, First_Name";
	var $criteria_sql;
	var $current_sql;
	var $current_list;
	var $search_count;
	var $current_list_action;
	var $message;
	var $udm;

	function list_isActionField($field) {
		if ($this->action_fields==$field) {
			$this->current_list_action=$field;
			return $field;
		}
		if ($action_fieldset=explode(",", $this->action_fields)){
			$this->current_list_action= array_search($field, $action_fieldset);
			return $this->current_list_action;
		}
		else return FALSE;
	}
	function showlist($action_field, $link_action, $allow_edit=FALSE){
		$default_modin=1;
		$list_row_start='<tr bordercolor="#333333" bgcolor="#CCCCCC" class="results">';
		$list_row_end="</tr>\n";
		$list_item_start="<td>";
		$list_item_end="</td>";
		//begin Row output loop
		if ($this->current_list!=NULL) {
		    
			for ($n=$this->current_offset; ($n<($this->current_offset+$this->qty_displayed)&&$n<count($this->current_list)); $n++){
				$current_row=$this->current_list[$n];
				$bgcolor =($n % 2) ? "#D5D5D5" : "#E5E5E5";
				$list_row_start='<tr bordercolor="#333333" bgcolor="'.$bgcolor.'" class="results" '." onMouseover=\"this.bgColor='#CCFFCC';\" onMouseout=\"this.bgColor='$bgcolor';\">";
				$list_row="";
				foreach($this->current_fieldset as $current_field) {
				$showfield=TRUE;
					//Check if this is an action field
					
					if (substr($current_field, 0, 5)=="sort_") { $showfield=FALSE; }
					if ($current_action=$this->list_isActionField($current_field)) {
						
							if ($allow_edit){ //edit offered to admin users
								$list_row.=$list_item_start."<a href=\"$link_action?".$current_action."=".$current_row[$current_action]."&uid=".$current_row[$action_field]."\">edit</a>".$list_item_end;
							} else {
								$showfield=FALSE;
								$list_row.=$list_item_start.$list_item_end;
							}
					} else {
						if ($showfield)  $list_row.=$list_item_start.$current_row[$current_field].$list_item_end;
					}
				}

				$list_row=$list_row_start.$list_row.$list_row_end;
				//append row to html var
				$list_html.=$list_row;
			}
		} else { //No Records Found
			$list_html=$list_row_start."<td>No Records Found</td>".$list_row_end;
	
		}	
		return $list_html;	
	}

	function display_qty_choice() {
		$output="<SELECT name=\"page_qty\">";
		$display_options['50']='50';
		$display_options['100']='100';
		$display_options['*']='All';
		foreach ($display_options as $dvalue => $dtext) {
			$output .= "<option value=\"".$dvalue."\"";
			if ($dvalue==$this->qty_displayed) {
				$output .= " selected";
			}
			$output .=">".$dtext."</option>";
		}
		
		$output.="</select>";
		return $output;
	}

	function paged_list_header () {
		$output ="<div class=side>";
		if ($this->current_offset>0) {
			$output .= "&nbsp;<a href=\"javascript:void(0);\" onclick=\"sform.elements['offset'].value='";
			if ($this->current_offset>$this->qty_displayed) {
				$output.= $this->current_offset-$this->qty_displayed; 
			} else {
				$output.="0";
			}
			$output.="'; sform.submit();\"><< Prev </a>";
		}
		if (count($this->current_list) > ($this->current_offset+$this->qty_displayed)){
			$output .= "&nbsp;&nbsp;<a href=\"#\" onclick=\"sform.elements['offset'].value=".($this->current_offset+$this->qty_displayed)."; sform.submit();\">Next >></a>";
		}
		$output.="&nbsp;&nbsp;Displaying ".$this->current_offset."-";
		if (count($this->current_list) < ($this->current_offset+$this->qty_displayed)) {
			$output .= count($this->current_list);
		} else {
			$output .= ($this->current_offset+$this->qty_displayed);
		}
		
		$output.="&nbsp;".$this->jumpto_box();
		$output.="</div>";
		return $output;
	}
	

	function jumpto_box () {
		$output="&nbsp;Go: <SELECT name=\"UDM_offset\" class=side onchange=\"sform.elements['offset'].value=this.value; sform.submit();\">";
		$sortfields = explode(",", $this->sort_by);
		$sortfields[0]=str_replace(" DESC", " ", $sortfields[0]);
		$sortfields[0]=str_replace(" ASC", " ", $sortfields[0]);
		$mysort=trim($sortfields[0]);
		for ($n=0;$n<=count($this->current_list); $n=$n+$this->qty_displayed) {
			$output.="<option value=\"$n\"";
			if ($n==$this->current_offset) {
				$output.=" selected";
			}
			$output.=">".$this->translateFields($mysort, $this->udm).": ".$this->current_list[$n]["sort_".$mysort]."</option>";
		}
		$output.="</select>";
		return $output;
	}

	function output_list ($link_action="modinput4_view.php") {
		$list_html_start.='<table cellpadding="1" cellspacing="1" width="95%">';
		$list_html_start.='<tr class="toplinks">';
		if ($this->current_list!=NULL) {
			//DISPLAY COLUMN HEADERS
			foreach($this->current_fieldset as $current_field) {
				if (substr($current_field, 0, 5)!="sort_") { //hide sort fields
					$list_html_start.="<td align=\"left\">";
					if (!$this->list_isActionField($current_field)){
						if ($current_field=="Name") { //hack for name field
							$list_html_start.="<b><a href=\"#\" onclick=\"sform.elements['UDM_sort'].value = 'Last_Name, First_Name, '+sform.elements['UDM_sort'].value; sform.submit();\">".$this->translateFields($current_field, $this->udm)."</a></b>";
						} else {
							$list_html_start.="<b><a href=\"#\" onclick=\"sform.elements['UDM_sort'].value = '$current_field, '+sform.elements['UDM_sort'].value; sform.submit();\">".$this->translateFields($current_field, $this->udm)."</a></b>";
						}
					}
					$list_html_start.="</td>";
				}
			}
			#$list_html_start.="<td><!--editlink column--></td>";
			$list_html_start.=$list_row_end;
			$allow_edit=true;
			$list_html=$this->showlist("id", $link_action, $allow_edit);			
		}
		$list_html_footer = "</table>";
		//INSERT PAGINATION
		if (count($this->current_list)>$this->qty_displayed) {
			$list_html_start=$this->paged_list_header().$list_html_start;
			$list_html_footer.=$this->paged_list_header();
		}
		//INSERT LIST ACTION OPTIONS
		$options_html="<div class=\"side\" style=\"float:right;\"><form name='export_button' action='export4.php?id=".$this->base_modin."' method='POST'><input type=\"hidden\" name=\"sqlsend\" value=\" FROM userdata WHERE ".$this->criteria_sql."\"><a href=\"#\" onclick=\"checkSave();\">Save This Search</a> &nbsp;| &nbsp;<a href=\"#\" onclick=\"document.forms['export_button'].submit();\">Export List</a></form></div><BR>";
		$list_html_start=$options_html.$list_html_start;

		return $list_html_start.$list_html.$list_html_footer;
	}

	function set_offset($offset) {
		$this->current_offset=$offset;
	}


	function tab_navs() {
		$page_tabs="Results,Search Options";
		//Create tabbed div wrapper
		$tabnav_html='<ul id="topnav">';
		$page_tabset = explode(",", $page_tabs);
		foreach ($page_tabset as $tabname){
			$pagecount++;
			$tabnav_html.="<li class=\"tab$pagecount\"><a href=\"#\" id=\"a$pagecount\" onclick=\"change_any('tabpage_$tabname', 'tabpage');\" >$tabname</a></li>";
		}
		$tabnav_html.="</ul>";
		return $tabnav_html;
	}
	
	
	function saveSearch($searchname, $id=NULL) {
		global $dbcon;
		$output=array();
		$output['name']=$searchname;
		$output['criteria_sql'].=$this->criteria_sql;
		
		foreach ($this->search_criteria as $searchset=>$currentset) {
			foreach ($currentset as $currentitem=>$currentoptions) {
				$output['fields'] .= $currentoptions['fieldname'].",";
				$output['operators'].=$currentoptions['operator'].",";
				//watch out for commas in user input criteria
				$output['criteria'] .=str_replace(",", "&#044;", $currentoptions['value']).",";
				$output['sets'].= $searchset.",";
			}
			$output['set_logic'] .= $this->search_logic[$searchset]['internal'].",";
			$output['set_logic'] .=$this->search_logic[$searchset]['external'].",";
		}
		$output['base_modin']=$this->base_modin;
		$output['include_modin']=$this->include_modin;

		//kill final commas and format for insert
		foreach ($output as $key=>$outputset) {
			if (substr($outputset, strlen($outputset)-1,1)==',') {
			$output[$key]=substr($outputset, 0, strlen($outputset)-1);}
			if ($outputset==',') { $outputset='';}
			
		}

		if (isset($id)) {  //overwrite an existing saved search
			$save_sql="UPDATE userdata_search set  ";
			foreach ($output as $outputname => $outputset) {
				$save_sql.= "`".$outputname."` = ".$dbcon->qstr($outputset, true).", ";
			}
			$save_sql=substr($save_sql, 0, strlen($save_sql)-2);
			$save_sql.=" WHERE id = ".$id;
		} else { //save as new search
			$save_sql_start="INSERT INTO userdata_search ( ";
			foreach ($output as $fieldname=>$outputset) {
				$save_sql_start.="`".$fieldname."`, ";
				if ($fieldname=='criteria_sql') {
					$save_sql_values .= $dbcon->qstr($outputset).", ";
				} else {
					$save_sql_values .= $dbcon->qstr($outputset, true).", ";
				}
			}
			$save_sql=substr($save_sql_start, 0, strlen($save_sql_start)-2)." ) VALUES ( ".substr($save_sql_values, 0, strlen($save_sql_values)-2)." )";
		}
		//echo $save_sql."<BR>";
		if ($dbcon->Execute($save_sql)) {
			$this->message.="Search <B>".$searchname."</b> saved.";
		} else {
			$this->message.="Save failed: ".$dbcon->ErrorMsg();
		}



	}
	
	
	function loadSearch($id, $logictype="0", $start_set=0) {
		global $dbcon;
		$load_sql="SELECT * from userdata_search where id = $id";
		if($loaded_search=$dbcon->Execute($load_sql)) {
			$search_list=$this->getSavedSearches();
			$this->message="Loaded Search: ".$search_list[$id];
			$input['fieldset']=explode(",", $loaded_search->Fields("fields"));
			$input['opset']=explode(",", $loaded_search->Fields("operators"));
			$input['valueset']=explode(",", $loaded_search->Fields("criteria"));
			$input['searchset']=explode(",", $loaded_search->Fields("sets"));
			$input['logicset']=explode(",", $loaded_search->Fields("set_logic"));
			$input['base_modin']=$loaded_search->Fields("base_modin");
			$input['include_modin']=explode(",", $loaded_search->Fields("include_modin"));

			//criteria creation loop
			foreach($input['fieldset'] as $currentkey=>$currentField) {
				$op=$input['opset'][$currentkey];
				//restore commas to normal
				$crit_value=str_replace("&#044;", ",", $input['valueset'][$currentkey]);
				$searchset=$input['searchset'][$currentkey]+$start_set;
				$this->addCriteria($currentField, $op, $crit_value, $searchset);
			}
			foreach($input['searchset'] as $currentset) {
				$this->setLogic($input['logicset'][$currentset*2], $currentset+$start_set, 'internal');
				$this->setLogic($input['logicset'][$currentset*2+1], $currentset+$start_set, 'external');
			}
			foreach($input['include_modin'] as $current_mod) {
					$this->addModule($current_mod);
			}
		
		
			if (!$logictype==0) { //search must be combined with current search
				if ($this->base_modin!=$input['base_modin']) {
						$this->addModule($input['base_modin']);
				}
				$this->setLogic($logictype, $start_set-1, 'external');
			
			} else { //search is being loaded fresh
				$this->base_modin=$input['base_modin'];
				$this->readPagination();
				$this->readDisplayFields();
				$this->readSort();

				$this->setupSearch($dbcon);
				#$this->runSearch($dbcon);
			}


		} else {
			$this->message="Requested Search Record Not Found";
		}
	}
	
	
	
	
	
	function readSearch($udm) {
		global $_REQUEST;
		$searchset_count=0;
		$searchitems_count=1;

		//check for a newly loaded search
		if ($_REQUEST['UDM_load_searchnum']!=''&&($_REQUEST['UDM_load_search_logic']=='0')) {
			//Loaded search wipes out existing search
			//new udm for mapping new module
			$this->loadSearch($_REQUEST['UDM_load_searchnum']);
			$udm=new UserDataInput ($udm->dbcon, $this->base_modin);

			return $udm;
		}

		$this->udm=$udm;
		$modin=$udm->instance;
		//Find modules specified for inclusion in Search
		$this->base_modin=$modin;
		if($_REQUEST['UDM_Search_all_mods']) {//user checked 'Search in all' box
			$this->include_modin="*";  //flag to indicate all modules are included
		} elseif(isset($_REQUEST['UDM_Search_modin'])) {
			$mod_set=$_REQUEST['UDM_Search_modin'];
			if (is_array($mod_set)){
				foreach ($mod_set as $this_mod) {
					if ($this_mod!=$modin) { $this->addModule($this_mod);}
				} 
			}
		}


		//Check for old searches stored on form
		//Prev_Search_action selectbox gives "Narrow/Expand/New" Search options
		if (isset($_REQUEST['UDM_Prev_Search_action'])) {
			if ($_REQUEST['UDM_Prev_Search_action']!='0') { //New Search=0; ignore previous searches

				while ($field_choice=$_REQUEST['UDM_Prev_Search_field'.$searchitems_count]) {
					$op_choice=$_REQUEST['UDM_Prev_Search_compare'.$searchitems_count];
					$value_choice=$_REQUEST['UDM_Prev_Search_value'.$searchitems_count];
					$set_choice=$_REQUEST['UDM_Prev_Search_set'.$searchitems_count];
					$this->addCriteria($field_choice, $op_choice, $value_choice, $set_choice);
					if ($set_choice>$searchset_count) { $searchset_count=$set_choice; }
					$searchitems_count++;
				}
				for ($n=0; $n<=$searchset_count; $n++) {
					$this->setLogic($_REQUEST['UDM_Prev_Search_set_logic_int'.$n], $n, 'internal');
					$this->setLogic($_REQUEST['UDM_Prev_Search_set_logic_ext'.$n], $n, 'external');
				}
				$this->setLogic($_REQUEST['UDM_Prev_Search_action'], $searchset_count, 'external');
				$this->setLogic($_REQUEST['UDM_Prev_Search_action'], $searchset_count+1, 'external');
				$searchitems_count=1;
				$searchset_count++;
			} 
		}  

		//LOAD NEW SEARCH criteria
		while (isset($_REQUEST['choose_field'.$searchitems_count])){

				$field_choice=$_REQUEST['choose_field'.$searchitems_count];
				$op_choice=$_REQUEST['choose_comparison'.$searchitems_count];
				#$logic_choice=$_REQUEST['choose_logic'.$searchitems_count];
				$value_choice=$_REQUEST['value_field'.$searchitems_count];
				if (($value_choice!=NULL && $value_choice!='')||(substr($op_choice, strlen($op_choice)-5)== "EMPTY")||(substr($op_choice, strlen($op_choice)-4)== "TRUE")) {
					$this->addCriteria($field_choice, $op_choice, $value_choice, $searchset_count);
				}
				$searchitems_count++;


		}
		//Set the LOGIC for the set
		#$this->search_count=$searchset_count;
		$this->setLogic($_REQUEST['choose_logic'], $searchset_count, 'internal');
	

		//LOAD SAVED SEARCH combined with current search

		if ($_REQUEST['UDM_load_searchnum']!='') {
			if (count($this->search_criteria[$searchset_count])>0) { $searchset_count++; }
			$this->loadSearch($_REQUEST['UDM_load_searchnum'], $_REQUEST['UDM_load_search_logic'], $searchset_count);
		}

		$this->readPagination();
		$this->readDisplayFields();
		$this->readSort();


		$this->setupSearch($udm->dbcon);
		//SAVE CURRENT SEARCH upon request
		if ($_REQUEST['UDM_save_searchname']!=''&&$_REQUEST['UDM_save_searchnum']==''){
			//INSERT new search record
			$this->saveSearch($_REQUEST['UDM_save_searchname']);
		}
		if ($_REQUEST['UDM_save_searchnum']!='') {  
			
			//UPDATE previously saved search 
			$this->saveSearch($_REQUEST['UDM_save_searchname'], $_REQUEST['UDM_save_searchnum']);
		}

		return $udm;

	}

	function readPagination() {
		//set page offset and display qty from form data
		global $_REQUEST;
		if (isset($_REQUEST['page_qty'])) {
			$this->qty_displayed=$_REQUEST['page_qty'];
		} else {
			$this->qty_displayed=$this->default_qty;
		}
		if (isset($_REQUEST['offset'])) {
			$this->current_offset=$_REQUEST['offset'];
		} else {
			$this->current_offset=0;
		}
	}


	function readDisplayFields() {
		//set fields for search results list from form data
		global $_REQUEST;
		if (isset($_REQUEST['UDM_display_fields'])) {
			$this->display_fields=stripslashes($_REQUEST['UDM_display_fields']);
		}
		if (isset($_REQUEST['UDM_list_fields'])) {
			$list_field_list="id, Concat(First_Name, \" \", Last_Name) as Name";
			foreach ($_REQUEST['UDM_list_fields'] as $current_field) {
				$list_field_list.=", ".$current_field;
			}
			$list_field_list.=", ".$this->action_fields;
			$this->display_fields=$list_field_list;
		}
	}

	function readSort() {
		global $_REQUEST;
		//set sort fields from form data
		if (isset($_REQUEST['UDM_sort'])) {
			$sort_set=explode(",", $_REQUEST['UDM_sort']);
			$this->sort_by="";
			foreach ($sort_set as $this_sort) {
				$this_sort=str_replace(" DESC", " " , $this_sort);
				$this_sort=str_replace(" ASC", " ", $this_sort);
				$this_sort=trim($this_sort);
				if (strpos($this->sort_by, $this_sort)===FALSE) {
					//sort descending when sortfield appears in form data twice
					//but not if the sortfield is already DESC
					if (substr_count($_REQUEST['UDM_sort'], $this_sort)>1&&strpos($_REQUEST['UDM_sort'], $this_sort." DESC") === FALSE) {
						$this->sort_by.=$this_sort." DESC, ";
					} else {
						$this->sort_by.=$this_sort.", ";
					}
				}
			
			}
				
			$this->sort_by=substr($this->sort_by, 0, strlen($this->sort_by)-2);
			
		}

	}

	function connect_SearchOperators() {
		$compare_operators=array();
		$compare_operators['contains']="LIKE";
		$compare_operators['does not contain']="!LIKE";
		$compare_operators['starts with']="_LIKE";
		$compare_operators['ends with']="LIKE_";
		$compare_operators['equals']="=";
		$compare_operators['does not equal']="!=";
		$compare_operators['is Empty']="EMPTY";
		$compare_operators['not Empty']="NOT EMPTY";
		$compare_operators['is True']="TRUE";
		$compare_operators['is False']="NOT TRUE";
		$compare_operators['greater than']=">";
		$compare_operators['>=']=">=";
		$compare_operators['less than']="<";
		$compare_operators['<=']="<=";
		return $compare_operators;
	}

 function labels_box() {
	$_Avery_Labels = array (
        '5160'=>'Avery 5160',
        '5161'=>'Avery 5161',        
		'5162'=>'Avery 5162',
        '5163'=>'Avery 5163',
        '5164'=>'Avery 5164',
        '8600'=>'Avery 8600',
        'L7163'=>'Avery L7163');
	$output.="<div id=\"UDM_Label_Options\" style=\"display:none;\">Select Label Type<BR><form name='labels_button' action='make_labels.php' method='POST'><select name=\"UDM_label_type\">".$this->makeSelbox($_Avery_Labels, '5160')."</select><BR><input type=\"hidden\" name=\"sqlsend\" value=\" FROM userdata WHERE ".$this->criteria_sql."\"><a href=\"#\" onclick=\"document.forms['labels_button'].submit();\">Make Labels</a></form></div>";
	return $output;
 }

	
	function translateLogic($current_set) {
		if ($this->search_logic[$current_set]['internal']=="OR") {
			$returnLogic="any";
		} else {
			$returnLogic="all";
		}
		return $returnLogic;

	}

	function translateCompare($compare_term) {
		$compare_options=$this->connect_SearchOperators();
		return array_search($compare_term, $compare_options);
	}

	function translateFields($fieldname, $udm) {
		$returnField=strip_tags($udm->fields[$fieldname]['label']);
		if ($returnField==NULL) {$returnField=$fieldname;}
		return $returnField;
	}


	function translateMods($udm) {
		$output = "Searched ";
		if($mymodules=$this->GetModsArray($udm)){
			if ($this->include_modin=="*") {
				$output.= "all modules";
			} elseif (!isset($this->include_modin)) {
				$output.= $udm->name;
			} else {
				$current_mods=explode(",", $this->include_modin);
				foreach ($current_mods as $this_mod) {
					$output.=$mymodules[$this_mod].", ";
				}
				$output.=$udm->name;
			}
		
		} else {
			$output="No Modules Defined";
		}
		return ($output."<BR>");

	}
	
	function translateSearch($udm) {
		//Creates an explanation of the search process
		//for user display
		$output = "<div id=\"div_search_details\" style=\"display: none\">";
		$output .= "Searched for ";
		for ($current_set=0; $current_set<=$this->search_count; $current_set++) {
			if (count($this->search_criteria[$current_set])>1) {
				$output.=$this->translateLogic($current_set)." of these:<BR>";
				$startline="<li>";
				$endline="</LI>";
				$endlist="";
			}	 else {
				$startline="";
				$endline="<BR>";
				$endlist="";
			}
			foreach($this->search_criteria[$current_set] as $item => $searchdef) {
				$fieldname=$searchdef['fieldname'];
				$output.=$startline."<b>".$this->translateFields($fieldname, $udm)." ".$this->translateCompare($searchdef['operator'])." ".$searchdef['value']."</b>";
				if (isset($this->include_modin)&&(substr($fieldname,0,6)=="custom")) { $output.= " in ".$udm->name." only";}
				$output.=$endline;
			}
			$output.=$endlist;
			if ($this->search_count>$current_set) {
				switch ($this->search_logic[$current_set]['external']) {
					case "AND":	$output.="Narrowed to records where:<BR>";break;
					case "OR": $output.="Expanded to also include records where:<BR>";break;
				}

			}

		}
		$output.="</div>";
		if (isset($this->message)) {$output.=$this->message."<BR>"; }
		$output.=$this->translateMods($udm);
		$output.="Your search returned ".count($this->current_list)." results &nbsp;&nbsp;<a href=\"javascript:void(0);\" onclick=\"change_any('div_search_details')\">details</a><BR>";
		return $output;
	}
			





	//JAVASCRIPT FOR ADDING AND SUBTRACTING ROWS OF SEARCH CRITERIA
	//There must be a better place to put this

	function searchForm_Jscript() {
		
		$script = "<script type=\"text/javascript\">\r\n
	var SearchLines=new Array(); //Holds pointers to search criteria form elements
	var searchitems=1;  //the number of search lines being displayed on the form
	var sform=document.forms['UDM_Advanced_Search'];
	var searchtable=document.getElementById('UDM_Search_tbl');
		
	
//This is A Javascript Function
//to create a new row of search criteria
	function AddItem() { 
		sform=document.forms['UDM_Advanced_Search'];
		searchtable=document.getElementById('UDM_Search_tbl');

		searchitems++; 
		
		var newfieldbox=SetupSelect('field');
		//var newlogicbox=SetupSelect('logic');
		var newcomparebox=SetupSelect('comparison');
		var newvaluebox=document.createElement('input');
		newvaluebox.name='value_field'+searchitems;
		newvaluebox.type='text';
		newvaluebox.size='20';
		var newaddbtn=document.createElement('input');
		newaddbtn.type='button';
		newaddbtn.value='+';
		event(newaddbtn, 'onclick', 'AddItem();');
		var newrmvbtn=document.createElement('input');
		newrmvbtn.type='button';
		newrmvbtn.value='-';
		event(newrmvbtn, 'onclick', ('RemoveItem('+searchitems+');'));
		var newrow=searchtable.tBodies[0].appendChild(document.createElement('tr'));
		var newcell=document.createElement('td');
		newcell.appendChild(newfieldbox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newcomparebox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newvaluebox);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newaddbtn);
		newrow.appendChild(newcell);
		newcell=document.createElement('td');
		newcell.appendChild(newrmvbtn);
		newrow.appendChild(newcell);
		
		SearchLines[searchitems]=new Array();
		SearchLines[searchitems][0]=newfieldbox;
		SearchLines[searchitems][1]=newcomparebox;
		SearchLines[searchitems][2]=newvaluebox;
		//SearchLines[searchitems][3]=newlogicbox;

		
		
	} 

	function SaveRow (rowindex) { //This is A Javascript Function
		//won't work in IE for rows generated by script
		//used to commit the first row, which serves as a template
		sform=document.forms['UDM_Advanced_Search'];
		SearchLines[rowindex]=new Array();
		SearchLines[rowindex][0]=sform.elements['choose_field'+rowindex];
		SearchLines[rowindex][1]=sform.elements['choose_comparison'+rowindex];
		SearchLines[rowindex][2]=sform.elements['value_field'+rowindex];
		//SearchLines[rowindex][3]=sform.elements['choose_logic'+rowindex];
	}

	function RemoveItem(which) { //This is A Javascript Function
		if (searchitems>1) {
			for (n=which; n<searchitems; n++) {
				MoveRow(n+1, n);
			}
			searchitems=searchitems-1;
			searchtable.deleteRow(searchitems);
		} else {
			sform.elements['choose_field1'].selectedIndex=0;
			sform.elements['choose_comparison1'].selectedIndex=0;
			sform.elements['value_field1'].value = '';
			sform.elements['choose_field1'].focus;
		}
	} 

//This is A Javascript Function
//Which Moves values *from* one set of select boxes *to* another

	function MoveRow (from,to) {
		//if (to==1) { //First field not stored in array
		//	sform.elements['choose_field1'].selectedIndex=SearchLines[from][0].selectedIndex;
		//	sform.elements['choose_comparison1'].selectedIndex=SearchLines[from][1].selectedIndex;
		//	sform.elements['choose_logic1'].selectedIndex=SearchLines[from][3].selectedIndex;
		//	sform.elements['value_field1'].value = SearchLines[from][2].value;
		//} else { //retrieve values using array data
			SearchLines[to][0].selectedIndex=SearchLines[from][0].selectedIndex;
			SearchLines[to][1].selectedIndex=SearchLines[from][1].selectedIndex;
			//SearchLines[to][3].selectedIndex=SearchLines[from][3].selectedIndex;
			
			SearchLines[to][2].value = SearchLines[from][2].value;
		//}

	}	

	//This is A Javascript Function
	//To Ensure IE Compliance for assigning onClick action
	function event(elem,handler,funct) {//This is A Javascript Function
		if(document.all) {
			elem[handler] = new Function(funct);
		} else {
			elem.setAttribute(handler,funct);
		}
	}

//This is A Javascript Function
//To Create a Selectbox by copying the options from an Existing Select

	function SetupSelect(selecttype) {
		var newselect=document.createElement('select');
		var selbox = SearchLines[1][convertSelectType(selecttype)];
		//var selbox = sform.elements['choose_'+selecttype+'1'];
		newselect.name='choose_'+selecttype+searchitems;
		for (n=0; n<selbox.options.length; n++) {
			newselect.options[n] = new Option(selbox.options[n].text, selbox.options[n].value);
		}
		
		return(newselect);
	}

	//this is a javascript function
	//for putting select boxes into an array
	function convertSelectType(selecttype) {
		var numindex;
		switch (selecttype) {
			case('logic'): numindex=3; break;
			case('field'):numindex=0; break;
			case('comparison'): numindex=1;break;
			case('value'):numindex=2;break;
		}
		return numindex;
	}

//This is A Javascript Function
//which assesses the entries on the search form before the form submits

	function goSearch() {
		var searchflag=false;
		for (n=1;n<=searchitems;n++){
				if ( (SearchLines[n][2].value != '')|| (SearchLines[n][1].value=='EMPTY')||(SearchLines[n][1].value=='NOT EMPTY')||(SearchLines[n][1].value=='TRUE')||(SearchLines[n][1].value=='NOT TRUE')) {
					searchflag=true;
				}
				if (sform.elements['UDM_Prev_Search_field1']) {
					searchflag=true;
				}
		}
		if (searchflag) {
			sform.elements['UDM_search_items'].value=searchitems;
			sform.submit();
		} else {
			alert ('No search criteria have been selected');
		}
	}
	
	//JAVASCRIPT FUNCTION
	//checks current searchlist before saving search
	function checkSave() {
		var searchname=prompt('Please enter a name for this search:');
		var selbox= sform.elements['UDM_saved_search_list'];
		var searchflag=0;
		var searchitem=0;
		if (searchname> '' ) { //do nothing if no search name is entered
			for (n=0; n<selbox.length; n++) {
				if (selbox.options[n].text==searchname) {
					searchflag=1;
					searchitem=n;
				}
			}
			if (searchflag) {
				var reply=confirm('The name you have chosen matches an existing saved search.\\nDo you wish to overwrite it?');
				if (reply) {
					sform.elements['UDM_save_searchname'].value=searchname; 
					sform.elements['UDM_save_searchnum'].value=searchitem;
					sform.submit();
				}
			} else {
				sform.elements['UDM_save_searchname'].value=searchname; 
				sform.submit();
			}
		} else return false;
	}

	function checkLoad() {
		var searchnum=sform.elements['UDM_saved_search_list'].value;
		if (searchnum!='') {
			sform.elements['UDM_load_searchnum'].value=searchnum;
			sform.submit();
		} else {
			alert ('Sorry, a saved search must be selected first');
		}
	}
	function makeMail() {
		//var searchnum=sform.elements['UDM_saved_search_list'].value;
		//if (searchnum!='') {
			sform.elements['UDM_make_labels'].value='5160';
			sform.submit();
		//} else {
		//	alert ('Sorry, a saved search must be selected first');
		//}
	}

	</script>";
	return $script;
	}
	
	//BACK TO PHP
	//Stores the previous search on the form
	function storeSearch() {
		$fieldcount=0;
		if (count($this->search_criteria)>0) {
		for ($n=0; $n<=$this->search_count;$n++) {
			foreach ($this->search_criteria[$n] as $fitem => $fdef) {
				$fieldcount++;
				$output.="<input type='hidden' name=\"UDM_Prev_Search_field$fieldcount\"	  value=\"".$fdef['fieldname']."\">";
				$output.="<input type='hidden' name=\"UDM_Prev_Search_compare$fieldcount\" value=\"".$fdef['operator']."\">";
				$output.="<input type='hidden' name=\"UDM_Prev_Search_value$fieldcount\" value=\"".$fdef['value']."\">";
				$output.="<input type='hidden' name=\"UDM_Prev_Search_set$fieldcount\" value=\"".$n."\">";
			}	
			$output.="<input type='hidden' name=\"UDM_Prev_Search_set_logic_int$n\" value=\"".$this->search_logic[$n]['internal']."\">";
			$output.="<input type='hidden' name=\"UDM_Prev_Search_set_logic_ext$n\" value=\"".$this->search_logic[$n]['external']."\">";
		}
		$output.="<table width=\"100%\"><tr><td  align=\"left\" valign=\"bottom\" nowrap><select  name=\"UDM_Prev_Search_action\" onchange=\"change_any(('prev_action_option'+this.value), 'prev_action_option');\"  style=\"font-weight: bold;\"><option value='AND' selected>Focus</option><option value='OR'>Expand </option><option value='0'>New </option></select></td><td align=\"left\"><div id=\"prev_action_optionAND\" class=\"prev_action_option\" style=\"display: block; font-size:14px; \"><B>Narrow the current search</b> using the criteria below </div><div id=\"prev_action_optionOR\" class=\"prev_action_option\" style=\"display: none;font-size:14px;\"><B>Widen the current search</b> to include results which match: </div><div id=\"prev_action_option0\" class=\"prev_action_option\" style=\"display: none;font-size:14px;\">Start a <B>New search </b></div></td></tr></table>";
		}
		return $output;
	}


	//Returns an array of existing Saved Searches from the database
	//can be restricted to show only those from the current modin
	function getSavedSearches ($modin='') {
		global $dbcon;
		$load_sql="SELECT id, name from userdata_search";
		if ($modin!='') { $load_sql.=" WHERE base_modin = $modin";}
		$output = $dbcon->GetAssoc($load_sql);
		return $output;
	}

	//outputSelbox utility function
	//returns option list from an array
	//until we get this hooked up to new dropdown.php
	function makeSelbox ($data, $selected='') {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$output.="<option value='$key'";
				if ($key==$selected&&($selected!='')){
					$output.= " selected";
				}
				$output.=">$value</option>\n";
			}
		} else {
			$output="<option value=''>No Items Found</option>";
		}
		return $output;
	}


	//Master Search Form output method

	function SearchForm($udm, $action="modinput4_search.php") {
		
		//SET VALUES for User Interface
		//FIELDS in current udm instance
		foreach ($udm->fields as $fieldname => $fdef) {
			$thislabel=strip_tags($fdef['label']);
			if (strlen($thislabel)>25) {
					$thislabel=substr($thislabel, 0, 25);
			} 
			$fieldselect.="<option value=\"$fieldname\">".$thislabel."</option>";
		}
		
		//OPERATOR CHOICES
		$compare_operators=$this->connect_SearchOperators();
		$compare_select=$this->makeSelbox(array_flip($compare_operators));

		
		//LOGIC Options for this search
		$logic_set['all']="AND";
		$logic_set['any']="OR";
		$logic_select=$this->makeSelbox(array_flip($logic_set));

		//GET Javascript form engine
		$control_script= $this->SearchForm_Jscript();

		//ROW template for input fields on Search Form
		$new_row="<tr><td><select name = 'choose_field%1\$s' width='25'>".$fieldselect."</select></td><td><select name='choose_comparison%1\$s' width='15'>".$compare_select."</select></td><td><input name='value_field%1\$s' type='text' size='20'></td><td><input name='add_criteria%1\$s' type='button' value='+'  onclick='AddItem();'></td><td><input name='remove_criteria%1\$s' type='button' value='-'  onclick='RemoveItem(%1\$s);'></td></tr>";
		


		//THE BIG BUTTON
		$search_button_go="<input name=\"btnUdmSubmit\" value=\"Search\" type=\"button\" onclick=\"goSearch();\" id=\"UDM_Search_btn\" style=\"font-size: 20px;\">";
		
		//HIDDEN values which govern form behavior
		$hidden_values="<input name=\"UDM_search_items\" type=\"hidden\" value=\"1\"><input name=\"modin\" value=\"".$udm->instance."\" type=\"hidden\"><input name=\"offset\" value=\"0\" type=\"hidden\"><input name=\"UDM_display_fields\" value='".$this->display_fields."' type=\"hidden\"><input name=\"UDM_sort\" value='".$this->sort_by."' type=\"hidden\"><input name=\"UDM_load_searchnum\" value='' type=\"hidden\"><input name=\"UDM_save_searchname\" value='' type=\"hidden\"><input name=\"UDM_save_searchnum\" value='' type=\"hidden\">";

		//LOGIC CONTROL PANEL and STORING VALUES from previous searches
		$logic_box='<div style ="background-color:E3E3E3; min-height=50px; vertical-align:center; text-align:left; padding: 5px;">'.$this->storeSearch().'Find results which match <select name="choose_logic">'.$logic_select.'</select> of the following:</div>';
		
		//UDM INSTANCE CONTROL PANEL
		if (isset($this->include_modin)) {$modlist_style="block"; } else {$modlist_style="none";}
		if ($this->include_modin=="*") {$is_selected=" CHECKED"; $modlist_style="none";}

		$modinbox="Currently searching <B>".$udm->name."</b><BR><a href=\"javascript: void(0);\" onclick=\"change_any('UDM_search_modules')\">Select sources</a><BR><input name=\"UDM_Search_all_mods\" type=\"checkbox\" value=\"1\" $is_selected>Search users from all sources<br><div id=\"UDM_search_modules\" style=\"display: $modlist_style;\">Also include:<BR><SELECT MULTIPLE name=\"UDM_Search_modin[]\" size=\"6\">".$this->GetMods($udm)."</select></DIV>";
		
		//LOADING/SAVING SEARCHES CONTROL PANEL
		$searchlist=$this->makeSelbox($this->getSavedSearches($this->base_modin), '0');
		$saved_search_box="<a href=\"javascript: void(0);\" onclick=\"change_any('UDM_saved_searches');\"> Open A Search</a><BR>";
		$load_search_logic="<option value='0'>Open saved search";
		if (isset($this->current_sql)) {
			$saved_search_box.="<a href=\"javascript: void(0);\"  onclick=\"checkSave();\" >Save this Search</a><BR><a href=\"javascript: void(0);\"  onclick=\"change_any('UDM_Label_Options');\" >Mailing labels</a><BR>";
			$load_search_logic.=" and ignore current search</option><option value=\"AND\">Narrow current search to show only users in both groups</option><option value=\"OR\">Expand current search to also include users from:";
		}
		$load_search_logic.="</option>";
		$saved_search_box.="<div id=\"UDM_saved_searches\" style=\"display: none;\">When loading:<BR><select name=\"UDM_load_search_logic\">$load_search_logic</select><BR><select name=\"UDM_saved_search_list\" size=\"8\">$searchlist</select><BR><input name=\"UDM_loadsearch_submit\" value=\"Open Search\" type=\"button\" onclick=\"checkLoad();\"></div>";

		//DISPLAY FIELDS CONTROL PANEL
		$fieldselect_no_custom=substr($fieldselect, 0, strpos($fieldselect, "<option value=\"custom"));
		$display_fields_box="<a href=\"javascript: void(0);\" onclick=\"change_any('UDM_search_listfields');\">Fields to show</a><BR><div id=\"UDM_search_listfields\" style=\"display: none;\">Name will show in list automatically<BR> Please select other fields to include:<BR><select multiple name=\"UDM_list_fields[]\" size=\"8\">$fieldselect_no_custom</select></div><BR> ".$this->display_qty_choice()." matches per page of results";
		
		
		//ASSEMBLE FORM
		$form_header=$control_script."	<form name=\"UDM_Advanced_Search\" action=\"$action\" method=\"POST\">$hidden_values $logic_box<table id=\"UDM_Search_tbl\">";
				
		$controls_html="<div>".sprintf($new_row, '1')."</div><BR>";
		//Save First Row to script when form loads
		$controls_html.="<script type=\"text/javascript\">SaveRow(1);</script>";
		
		//footer
		$form_footer="</table><P><center>".$search_button_go."<div style=\"width:400px; text-align:left;\"><H3>Options</H3><b>Sources</b><P>".$modinbox."<P><b>Display</b><p>".$display_fields_box."<P><b>Actions</b><P>".$saved_search_box."</form></div><center><div style=\"width:400px; text-align:left;\">".$this->labels_box()."</div></center>";

		$form_html= $form_header.$controls_html.$form_footer;
		return $form_html;
		
	}	

	//Make an option list of modules
	function GetMods($udm) {
		if($modlist=$udm->dbcon->Execute("SELECT id, name from userdata_fields")) {
			while (!$modlist->EOF) {
				$output.="<option value=\"".$modlist->Fields("id")."\"";
				if ($this->isModule($modlist->Fields("id"))) {$output.=" selected";}
				$output.=">".$modlist->Fields("name")."</option>";
				$modlist->MoveNext();
			}
		} else {
			$output = "<option value=\"\">No Modules Defined</option>";
		}
		return $output;
	}

	//Make an array list of modules
	function GetModsArray($udm) {
		
		return $udm->dbcon->GetAssoc("SELECT id, name from userdata_fields");
	}
	
	
	function addModule ($modin) {
		$current_modules=explode(",", $this->include_modin);
		if (!isset($this->base_modin)) {
			$this->base_modin=$modin;
			} elseif (array_search($modin, $current_modules)===FALSE&& $modin!=$this->base_modin) {
			if (isset($this->include_modin)) {
				$this->include_modin.=",".$modin;
			} else {$this->include_modin=$modin;}
		}
		
	}

	function isModule($modin) {
		if (!strpos($this->include_modin, ",")===FALSE) {
			$current_modules=explode(",", $this->include_modin);
			$result = array_search($modin, $current_modules);
			if($result===FALSE) {
				$output=FALSE;
			} else { $output=TRUE; }
		} else {
			if ($modin==$this->include_modin){
				$output=TRUE;
			}else { $output=FALSE;}
		}
		if ($modin==$this->base_modin) { $output=TRUE; }
		return $output;
	}

	function addCriteria ($field, $operator, $value, $search_set=0, $logic="AND") {
		#$search_set=$this->setSearchParent($search_set);
		if ($search_set>$this->search_count) {$this->search_count=$search_set;}
		$item=count($this->search_criteria[$search_set]);
		$this->search_criteria[$search_set][$item]['fieldname']=$field;
		$this->search_criteria[$search_set][$item]['operator']=$operator;
		$this->search_criteria[$search_set][$item]['logic']=$logic;
		$this->search_criteria[$search_set][$item]['value']=$value;
		//echo "Added Criteria in Search[$search_set]:$field$operator$value<BR>";
		if ($this->search_count<$search_set||!isset($this->search_count)) { $this->search_count=$search_set;}
	}

	function setLogic ($logic, $search_set=0, $target=NULL) {
		if (!isset($target)) {
			$this->search_logic[$search_set]['internal']=$logic;
			$this->search_logic[$search_set]['external']=$logic;
		} else {
			$this->search_logic[$search_set][$target]=$logic;
		}
	}

	function setSearchParent($parent_search, $child_set) {
		$this->is_parent[$parent_search]=$child_set;
	}


	function setup_subSearch ($dbcon, $current_set, $avoid_custom=FALSE) {
			$this->is_included[$current_set]=TRUE;
			//echo "starting subsearch $current_set<BR>";
			//ADD EACH CRITERIA  WITHIN SET
			#$working_fieldset=array_keys($this->search_criteria[$current_set]);
			foreach ($this->search_criteria[$current_set] as $item=>$itemdef) {
				$currentField=$itemdef['fieldname'];
				//Check for custom fields in multi-module searches
				if (!(substr($currentField,0,6)=="custom" && $avoid_custom)) { 
					//Convert special operators to sql and add quotes to values
					$op_prepped=$itemdef['operator'];
					//LIKE-type exceptions
					if (!(strpos($op_prepped, "LIKE")===FALSE)) {
						$value_prepped=$itemdef['value'];
						if ($op_prepped<>"_LIKE") {
							$value_prepped="%".$value_prepped;	}
						if ($op_prepped<>"LIKE_") {
							$value_prepped=$value_prepped."%";}
						if ($itemdef['operator']=="!LIKE") {
							$clause_start="!(";
							$clause_end=")";
						} else { 
							$clause_start="";		
							$clause_end="";
						}
						$op_prepped="LIKE";

						$value_prepped =$dbcon->qstr($value_prepped);
					} else {
						$value_prepped = $dbcon->qstr($itemdef['value']);
					}
					//TRUE/ NOT TRUE Exceptions
					if (strpos($op_prepped, "EMPTY")===FALSE) {
						if (strpos($op_prepped, "TRUE")===FALSE){
							$setup_sql.=" ".$clause_start.$currentField." ".$op_prepped;
							$setup_sql.=" ".$value_prepped.$clause_end;
						} else {
							if ($op_prepped=="TRUE") {
								$setup_sql.=" (".$currentField.") ";
							} else { //operator is NOT TRUE
								$setup_sql.=" (!".$currentField.") ";
							}
						}
					//EMPTY/NOT EMPTY Exceptions
					} elseif ($op_prepped=="EMPTY") {
						$setup_sql.=" (IsNull($currentField) OR $currentField='') ";
					} else { //NOT EMPTY
						$setup_sql.=" (!IsNull($currentField) AND $currentField!='') ";
					}
					if (isset($this->search_logic[$current_set]['internal'])) {
						$logic_prepped =$this->search_logic[$current_set]['internal'];
					} else {
						$logic_prepped =$this->$itemdef['logic'];
					}
					$setup_sql .= " ".$logic_prepped; 
				}
			}//End Clause Loop
			$lastcall=strpos(strrev($setup_sql), " ");
			if ($lastcall) {
				$setup_sql2 = "((".substr($setup_sql, 0, strlen($setup_sql)-$lastcall).")";
				if(isset($this->is_parent[$current_set])) {
					$all_children=explode(",", $this->is_parent[$current_set]);
					foreach ($all_children as $this_child) {
						#echo "child $this_child found<BR>";
						$setup_sql2.=" ".$this->search_logic[$this_child]['external']." ";
						$setup_sql2 .= $this->setup_subSearch($dbcon, $this_child, $avoid_custom);
						$lastcall2=strpos(strrev($setup_sql2), ")");
						$setup_sql2 = substr($setup_sql2, 0, strlen($setup_sql2)-$lastcall2);
					}
				}
				$setup_sql2.=")".substr($setup_sql, strlen($setup_sql)-$lastcall);
			}
		return $setup_sql2;
	}

	function setupSearch ($dbcon, $avoid_custom=FALSE, $search_set=NULL) {
		if (!isset($search_set)) {
		    $search_set=array();
			for ($i=$this->search_count; $i>=0; $i--) {
				$search_set[$i]=$i;
				if ($i>0) {
					$this->setSearchParent($i, $i-1);
				}
			}
		}

		//CYCLE THROUGH SEARCH SETS
		foreach($search_set as $current_set) {
			if (!$this->is_included[$current_set]) {
				$criteria_sql.=$this->setup_subSearch($dbcon, $current_set, $avoid_custom);
			}
		}
		
		//STRIP OUT FINAL LOGIC CALL
		$lastcall=strpos(strrev($criteria_sql), ")");
		$criteria_sql = substr($criteria_sql, 0, strlen($criteria_sql)-$lastcall);
		//INCLUDE MODIN RESTRICTION
		$criteria_sql = "(".$criteria_sql." AND modin = ".$this->base_modin.")";
		
		//CHECK for additional Modules Defined
		if (isset($this->include_modin)) {
			$this->is_included= array();
			foreach($search_set as $current_set) {
				if (!$this->is_included[$current_set]) {
					$criteria_sql2.=$this->setup_subSearch($dbcon, $current_set, TRUE);
				}
			}
			//STRIP OUT FINAL LOGIC CALL
			$lastcall=strpos(strrev($criteria_sql2), ")");
			$criteria_sql2 = substr($criteria_sql2, 0, strlen($criteria_sql2)-$lastcall);
			//INCLUDE MODIN RESTRICTION
			if ($this->include_modin!="*") {
				$criteria_sql2 .= " AND modin IN(".$this->include_modin.")";
			} else {
				$criteria_sql2.=" AND modin != ".$this->base_modin;
			}
			$criteria_sql.=" OR (".$criteria_sql2.") ";
		}

		$this->criteria_sql=$criteria_sql;
		//ASSEMBLE SQL statment
		$this->current_sql = "SELECT DISTINCTROW ".$this->display_fields.$this->setupSort()." FROM ".$this->search_tables." WHERE ".$criteria_sql." ORDER BY ".$this->sort_by;
		//echo $this->current_sql."<BR>";
	}

	function setupSort() {
		if($sort_set = explode(',', $this->sort_by)) {
			$sort_set[0]=str_replace(" DESC", " " , $sort_set[0]);
			$sort_set[0]=str_replace(" ASC", " ", $sort_set[0]);
			$primary_sort=trim($sort_set[0]);
			$output = ", ".$primary_sort." as sort_".$primary_sort;
		}
		return $output;
	}
		

	function changeDisplay($showFields) {
		$this->display_fields = $showFields;
	}
	function changeSort($sortFields) {
		$this->sort_by = $sortFields;
	}
	function runSearch($dbcon) {
		if ($templist = $dbcon->Execute($this->current_sql)) {
			for ($i=0; $i<=$templist->FieldCount(); $i++) {
				$fieldobj=$templist->FetchField($i);
				$this->current_fieldset[$i]=$fieldobj->name;
			}
			$this->current_list=$templist->GetArray();
			if ($this->qty_displayed=="*") {$this->qty_displayed=count($this->current_list);}
		
		} else {
			$this->current_list=NULL;
		}
	}


} ?>
