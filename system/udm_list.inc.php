<?php
class UserList {
	var $display_fields="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone";
	var $current_fieldset;
	var $search_criteria;
	var $search_tables="userdata";
	var $search_logic;
	var $search_hier;
	var $qty_displayed;
	var $include_modin;
	var $sort_by="Last_Name, First_Name";
	var $current_sql;
	var $current_list;

	#function UserList () {
		//Constructor
	#}
	function showlist($action_field, $link_action, $allow_edit=FALSE){
		$default_modin=3;
		$list_row_start='<tr bordercolor="#333333" bgcolor="#CCCCCC" class="results">';
		$list_row_end="</tr>\n";
		$list_item_start="<td>";
		$list_item_end="</td>";
		
		//begin Row output loop
		if ($this->current_list!=NULL) {
		    
			foreach ($this->current_list as $current_row){
				$list_row="";
				#$debug_list=array_flip($current_row);
				foreach($this->current_fieldset as $current_field) {
				 $list_row.=$list_item_start.$current_row[$current_field].$list_item_end;
				}
				//insert edit link
				$list_row.=$list_item_start;

				if ($allow_edit){ //edit offered to admin users
					if(!strpos($this->include_modin, ",")===FALSE) 
						{$action_modin=$default_modin;} 
					else {$action_modin=$this->include_modin;}
					$list_row.="<a href=\"$link_action?modin=$action_modin&uid=".$current_row[$action_field]."\">edit</a>";
				}
				$list_row=$list_row_start.$list_row.$list_item_end.$list_row_end;
				//append row to html var
				$list_html.=$list_row;
			
			}
		} else { //No Records Found
			$list_html=$list_row_start."<td>No Records Found</td>".$list_row_end;
	
		}	
		return $list_html;	
	}

	
	function output_list ($link_action="modinput4_view.php", $current_page=1) {
		global $userper, $standalone;
		$list_html_start='<table cellpadding="1" cellspacing="1" width="95%">';
		$list_html_start.='<tr class="toplinks">';
		if ($this->current_list!=NULL) {
			//DISPLAY COLUMN HEADERS
			foreach($this->current_fieldset as $current_field) {
				$list_html_start.="<td align=\"left\"><b>$current_field</b></td>";
			}
			$list_html_start.="<td><!--editlink column--></td>";
			$list_html_start.=$list_row_end;
			$allow_edit=($userper[87] == 1 || $standalone == 1);
			$list_html=$this->showlist("id", $link_action, $allow_edit);			
		}
		$list_html_footer = "</table>";
		return $list_html_start.$list_html.$list_html_footer;
	}

	function tab_navs() {
		$page_tabs="Results,Add Search";
		//Create tabbed div wrapper
		$tabnav_html='<ul id="topnav">';
		$page_tabset = explode(",", $page_tabs);
		foreach ($page_tabset as $tabname){
			$pagecount++;
			$tabnav_html.="<li class=\"tab$pagecount\"><a href=\"#\" id=\"a$pagecount\" onclick=\"change('tabpage_$tabname', 'tabpage');\" >$tabname</a></li>";
		}
		$tabnav_html.="</ul>";
		return $tabnav_html;
	}
	
	
	
	function addModule ($modin) {
		$current_modules=explode(",", $this->include_modin);
		if (!isset($this->include_modin)) {
			$this->include_modin=$modin;
		} elseif (array_search($modin, $current_modules)===FALSE) {
			$this->include_modin.=",".$modin;
		}
	}

	function addCriteria ($field, $operator, $value, $search_set=0) {
		$this->search_criteria[$search_set][$field]=$operator.$value;
		echo "Added Criteria in Search[$search_set]:$field$operator$value<BR>";
	}

	function setLogic ($logic, $search_set=0, $target=NULL) {
		if (!isset($target)) {
			$this->search_logic[$search_set]['internal']=$logic;
			$this->search_logic[$search_set]['external']=$logic;
		} else {
			$this->search_logic[$search_set][$target]=$logic;
		}
	}

	function setupSearch ($search_set_count=0,$search_set=NULL) {
		if (!isset($search_set)) {
		    $search_set=array();
			for ($i=0; $i<=$search_set_count; $i++) {
				$search_set[$i]=$i;
			}
		}
		//CYCLE THROUGH SEARCH SETS
		foreach($search_set as $current_set) {
			$setup_sql="";
			if(!isset($this->search_logic[$current_set]['internal'])) {$this->search_logic[$current_set]['internal']="AND";}
			if(!isset($this->search_logic[$current_set]['external'])) {$this->search_logic[$current_set]['external']="AND";}
			//ADD EACH CRITERIA  WITHIN SET
			$working_fieldset=array_keys($this->search_criteria[$current_set]);
			foreach ($working_fieldset as $currentField) {
				#echo "#".$currentField."/<BR>";
				if ($setup_sql !="") { $setup_sql .= " ".$this->search_logic[$current_set]['internal']; 
				} 
				$setup_sql.=" ".$currentField." ".$this->search_criteria[$current_set][$currentField];
			}
			$criteria_sql.="(".$setup_sql.") ".$this->search_logic[$current_set]['external']." ";
		}
		//STRIP OUT FINAL LOGIC CALL
		$lastcall=strpos(strrev($criteria_sql), ")");
		$criteria_sql = substr($criteria_sql, 0, strlen($criteria_sql)-$lastcall);
		//INCLUDE MODIN RESTRICTION
		$criteria_sql .= " AND modin IN(".$this->include_modin.")";
		//ASSEMBLE SQL statment
		$this->current_sql = "SELECT DISTINCTROW ".$this->display_fields." FROM ".$this->search_tables." WHERE ".$criteria_sql." ORDER BY ".$this->sort_by;
		echo $this->current_sql."<BR>";
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
		} else {
			$this->current_list=NULL;
		}
	}


} ?>