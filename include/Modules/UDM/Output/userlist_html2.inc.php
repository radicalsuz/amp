<?php
require_once('HTML/List2.inc.php');

class UserList_HTML extends HTML_List {

var $udm;
var $menu;


	function define_options($options) {
			/* Setup the SQL used by the list
			  */

			//Display Fields for list
			$default_options['display_fields']="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone, publish";
			//Source Table
			$default_options['datatable']="userdata";
			//Criteria
			$default_options['list_criteria']="modin=".$this->udm->instance;
			//Sort By
			$default_options['sort_by']="Last_Name, First_Name";

			//Page Name and Form Name defaults
			$default_options['page_name']=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
			$default_options['list_form']="UDM_Listing";

			//variables assigned to the query string by the 'edit' link
			$default_options['editlink_fields']="modin,id";
			$default_options['editlink_id_type']="uid";

			//target for the edit link
			$default_options['editlink_action']="modinput4_view.php";

			$default_options['hidden_fields'].=$this->setupEditLink($default_options);


			//Pagination Defaults
			$default_options['qty_displayed']=50;


			/* Permissions 
			  */

			//show edit links and selectable listitems
			$default_options['allow_edit']=FALSE;  
			$default_options['allow_select']=TRUE;
			//allow list to look up values in other tables
			$default_options['allow_lookups']=TRUE;
			//allow publish, unpublish, and delete
			$default_options['allow_publish']=FALSE;
			//allow e-mail blaster
			$default_options['allow_email']=FALSE;
			//allow multi-source listings
			$default_options['allow_include_modins']=FALSE;

			/* Formatting
			  */
			$default_options['show_headers']=TRUE;  //show column headers
			$default_options['show_action_bar']=FALSE;  //action bar is turned off without this
			$default_options['show_advanced_modin']=FALSE; //load code for the modin Sources listing
			$default_options['is_dynamic']=TRUE; //read display values passed from one listpage to the next

			/* HTML Templating
			  */
			$default_options['control_class']="side";
			$default_options['list_row_start_template']="<tr id=\"listrow_%s\" bordercolor=\"#333333\" bgcolor=\"%s\" class=\"results\" onMouseover=\"this.bgColor='#CCFFCC';\" onMouseout=\"this.bgColor='%s';\" onClick=\"select_id(this.id.substring(8));\">";
			$default_options['list_row_end']="</tr>\n";
			$default_options['list_item_start']="<td class=list_column_%s>";
			$default_options['list_item_end']="</td>";
			$default_options['list_html_start']='<center><table cellpadding="1" cellspacing="1" width="95%"><tr class="toplinks">';
			$default_options['list_html_footer']="</table></center>";
			$default_options['list_html_header_column_start']="<td align=\"left\">";
			$default_options['list_html_header_template']="<b><a href=\"javascript: document.forms['%1\$s'].elements['sort_by'].value = '%2\$s '+document.forms['%1\$s'].elements['sort_by'].value; document.forms['%1\$s'].submit();\">%3\$s</a></b>";


			/* Lookups and Aliases
			  *  Lookups show values from other tables or from a passed set
			  *  Aliases are MySQL 'AS' constructions in display_fields - these need the base field info
			  *  for column sorting to work correctly
			  *  All but the most common of these are commented out by default
			  *  And should be called from the modules where they are needed
			  */
			#$default_options['Aliases']['Name']['sort']='First_Name, Last_Name';
			$default_options['Aliases']['Changed']['sort']='timestamp';
			#$default_options['Lookups'][]=array("fieldname"=>"modin", "LookupTable"=>"userdata_fields", "LookupField"=>"name");
			$default_options['Lookups']['publish']=array("LookupSet"=>array("0"=>"draft" , "1"=>"live"), 'LookupName'=>'status');


			/*Email Blast Options
			  */
			$default_options['email_action']="udm_mailblast.php";
			
			/*Function Definitions for base List Class
			*/
			$default_options['header_display_function']="output_UDM_header_html";
			$default_options['footer_display_function']="output_UDM_footer_html";
			$default_options['item_display_function']="showrow";

			$default_options['persistent']="List_Action,editlink_action,show_advanced_menu";

			
			return array_merge($default_options, $options);
	}


	function UserList_HTML (&$udm, $options) {
		$this->udm=&$udm;
		$this->dbcon=&$udm->dbcon;
		$this->options=$this->define_options($options);
		if ($udm->authorized) {
			$this->HTML_List($this->options);
		} else {
			$this->error='You do not have permission to view this list';
			return false;
		}
		$this->hold('modin', $udm->instance);
		$this->readAction();
		if ($this->options['show_advanced_menu']) {
			require_once('Modules/UDM/Output/userlist_menu.inc.php');
			$this->menu=new UserList_Menu($this);
		}
	}


		
	////// Creates the listing of results
	function showrow(&$dataitem, &$options){
		$list_row_start_template=$options['list_row_start_template'];
		$list_row_end=$options['list_row_end'];
		$list_item_start=$options['list_item_start'];
		$list_item_end=$options['list_item_end'];
		
		//allow selectboxes
		if ($options['allow_select']) {
				$list_row_select.=sprintf($list_item_start, 'ROWSELECT_box')."<input name=\"id[]\" type=\"checkbox\" value=\"%s\" onclick=\"this.checked=!this.checked;\">".$list_item_end;}
		
		//show Edit Link
		if ($options['allow_edit']){ //edit offered to admin users
				$list_row_edit=sprintf($list_item_start,'editlink')."<a href=\"%s\">edit</a>".$list_item_end;
				#$list_row_edit="<TD><a href=\"%s\">edit</a>".$list_item_end;
		}

		
		//begin Row output loop
			$current_row=$dataitem;
			//Alternates the background color
			$options['html_rowcount']++;
			$bgcolor =($options['html_rowcount'] % 2) ? "#D5D5D5" : "#E5E5E5";
			//assigns an id and background color to each row
			$list_row="";
			$list_row_start=sprintf($list_row_start_template, $current_row['hide_id'], $bgcolor, $bgcolor);
			
			//Field Output Loop
			foreach($current_row as $key=>$current_field) {
				//Check if this is a hidden field
				if (!(substr($key, 0, 5)=="hide_")&&!is_numeric($key)) { 
					//Check for lookup field
					if ($lookup=$options['Lookups'][$key]) {
						if (isset($lookup['LookupSet'])) {
							$current_field=$lookup['LookupSet'][$current_field];
						} 
					}
					
					$list_row.=sprintf($list_item_start, $key).$current_field.$list_item_end;
				}
			}
			
			if ($options['allow_select']) {
				$list_row=sprintf($list_row_select, $current_row['hide_id']).$list_row;
			}
			if ($options['allow_edit']) $list_row.=sprintf($list_row_edit, $current_row['hide_editlink']);

			$list_row=$list_row_start.$list_row.$list_row_end;
			//append row to html var
			$list_html.=$list_row;
		
			return $list_html;	
		}
	


//////Wrapper for results list - formats the container and title row
//// includes control bars
	function output_UDM_header_html () {
		$udm=&$this->udm;
		$options=&$this->options;
		$list_html_start.=$options['list_html_start'];
		if ($this->dataset != NULL) {
			if ($options['show_headers']) {
				//DISPLAY COLUMN HEADERS
				$column_count=0;
				foreach($this->dataset[0] as $current_field=>$field_value) {
					if (substr($current_field, 0, 5)!="hide_"&&!is_numeric($current_field)) { //hide sort fields and editlink fields
						$list_html_headers.=$options['list_html_header_column_start'];
						//Each field is linked to change the sort_by value and submit the form 
						if (isset($options['Aliases'][$current_field]['sort'])) { 
							
							$list_html_headers.=sprintf($options['list_html_header_template'], $options['list_form'], ($options['Aliases'][$current_field]['sort'].", ") , $this->translateFields($current_field));
						} else {
							//standard column header
							$list_html_headers.=sprintf($options['list_html_header_template'], $options['list_form'], ($current_field.", "), $this->translateFields($current_field));
							}
						$column_count++;
						$list_html_headers.=$options['list_item_end'];
					}
				}
				
				
				if ($options['allow_select']) {
					//include columns for checkbox and editlink values
					$list_html_headers=$options['list_html_header_column_start']."<a href=\"javascript: list_selectall();\"><B>All</B></a>".$options['list_item_end'].$list_html_headers;
					$column_count++;
					
				}
				
				if ($options['allow_edit']) {
					//include columns for checkbox and editlink values
					$list_html_headers.=$options['list_item_start'].$options['list_item_end'];
					$column_count++;
				}
				$list_html_start.=$list_html_headers.$options['list_row_end'];
			}
			
			//pagination controls
			if ($this->recordcount>$options['qty_displayed']) {
				$list_html_start=$this->page_controls().$list_html_start;
			}	

			//INSERT action bar
			if ($options['show_action_bar']) {
				$list_html_start=$this->action_bar()."\n  ".$list_html_start;
				$list_html_start=$this->header_script().$list_html_start;
			}

		} else {
			//dataset is null
		}
		
		return $list_html_start;
	}
			

		function output_UDM_footer_html() {
			$list_html_footer = $this->options['list_html_footer'];
			
			if ($this->recordcount>$options['qty_displayed']) {
			
				$list_html_footer.=$this->page_controls();
			}
			if ($this->udm->admin) {
				if ($this->options['show_advanced_menu']) {
					$list_html_footer=$this->output_UDM_advanced_menu($list_html_footer);
				} else {
					$list_html_footer.="<a href=\"javascript: document.forms['".$this->options['list_form']."'].elements['show_advanced_menu'].value=1; document.forms['".$this->options['list_form']."'].submit();\" class=side>Advanced Options </a>";
				}
			}
			

		
			return $list_html_footer;
		}

		
		function output_UDM_advanced_menu($footer_html){
			require_once ('Modules/UDM/Output/userlist_menu.inc.php');
			if (!isset($this->menu)) {
				$this->menu=new UserList_Menu($this);
			}
			//INSERT modin selectbox
			#if ($this->options['show_source_select']) {
				$list_html_menulinks.="<a href=\"javascript: change_any('List_menu_controls'); \" class=\"".$this->options['control_class']."\">Select sources and fields</a>&nbsp;&nbsp; <a href=\"#searchform\" onclick=\"change_any('UDM_search_box');\" class=\"".$this->options['control_class']."\">Advanced Search</a><BR><div id=\"List_menu_controls\" style=\"display: none; float:left;\"><table cellpadding=10><tr><td class=\"".$this->options['control_class']."\" valign=\"top\">". $this->menu->include_modin_box()."</td><td class=\"".$this->options['control_class']."\" valign=\"top\">".$this->menu->display_fieldselect_box()."</td><td class=\"".$this->options['control_class']."\" valign=\"top\"> <br><input type=\"submit\" value=\"Go\"></td></tr></table></div>";
			#} 
			
			//Display fields selection
			#if ($this->options['show_field_select']) {
				#$list_html_menulinks.="&nbsp;&nbsp;<a href=\"javascript: \"  class=\"".$this->options['control_class']."\">Fields to show</a>" 
			#} 
			
			//Advanced Search

			//Save this List
			
			//insert menulinks
			$div_end=strpos($footer_html, "</div>");
			if ($div_end) {
				$footer_html=str_replace("</div>", "<BR>".$list_html_menulinks."</div>", $footer_html);
			} else {
				$footer_html.=$list_html_menulinks."<BR>";
			}
			$footer_html.=$this->menu->output_SearchForm();

			//INSERT LIST ACTION OPTIONS
			#$options_html="<div class=\"side\" style=\"float:right;\"><form name='export_button' action='export4.php?id=".$udm->instance."' method='POST'><input type=\"hidden\" name=\"sqlsend\" value=\" FROM userdata WHERE modin=".$udm->instance."\"><a href=\"#\" onclick=\"checkSave();\">Save This Search</a> &nbsp;| &nbsp;<a href=\"#\" onclick=\"document.forms['export_button'].submit();\">Export List</a></form></div><BR>";
			#$list_html_start=$options_html.$list_html_start;

			return $footer_html;
	}



	function check_fields() {
		$udm=&$this->udm;
		$options=&$this->options;
		$display_fields =str_replace("Concat(First_Name, \" \", Last_Name) as Name,", "Name,", $options['display_fields']);
		$display_fieldset=split(",", $display_fields);
		foreach ($display_fieldset as $current_field) {
			$current_field=trim($current_field);
			if (isset($udm->fields[$current_field])) {
				if (!($udm->fields[$current_field]['public']==false&&$udm->admin==false)) {
					$return_fieldset[]=$current_field;
				}
			} else {
				switch ($current_field) {
					case "Name":
					if ($udm->admin) { $return_fieldset[]="Name";}
						elseif ($udm->fields['Last_Name']['public']&&$udm->fields['First_Name']['public']) {
							$return_fieldset[]="Name";
						} 
						break;
					case "id":
						if ($udm->admin) { $return_fieldset[]="id";}
						break;
					case "timestamp":
						$return_fieldset[]=$current_field;
						break;
					default:
						if (isset($options['Lookups'][$current_field])) {
							$return_fieldset[]=$current_field;
					}
				}
			}
		}
		if (is_array($return_fieldset)) {
			foreach ($return_fieldset as $key=>$current_field) {
				if ($current_field=='Name') {
					$return_fields.="Concat(First_Name, \" \", Last_Name) as Name, ";
				} else {
					$return_fields.=$current_field.", ";
				}
			}
		}
		$options['display_fields']=substr($return_fields, 0, strlen($return_fields)-2);
		return $options;
	}

	// converts system fieldnames to UDM-assigned fieldnames

	function translateFields($fieldname) {
		$udm=&$this->udm;
		$options=&$this->options;
		$returnField=strip_tags($udm->fields[$fieldname]['label']);
		if ($options['allow_lookups']) {
			if (isset($options['Lookups'][$fieldname]['LookupName'])){
				$returnField=$options['Lookups'][$fieldname]['LookupName'];
			}
		}

		if ($returnField==NULL) {$returnField=$fieldname;}
		return $returnField;
	}


	//retrieves Lookup values from database tables and stores them in the options array
	function setupLookups($which=null) {
		$udm=&$this->udm;
		$options=&$this->options;
		if (is_array($options['Lookups'])) {
			if (isset($which)&&$which) {
				$this_lookup=$options['Lookups'][$which];
				if (isset($this_lookup['LookupTable'])) {
						if (isset($this_lookup['LookupSearchby'])) {
							$id_field = $this_lookup['LookupSearchby']. " AS id";
						} else { $id_field="id";}
						$options['Lookups'][$which]['LookupSet']=$udm->dbcon->GetAssoc( "Select $id_field, ".$this_lookup['LookupField']." FROM ".$this_lookup['LookupTable']);
				}
			} else {
				foreach($options['Lookups'] as $key=>$this_lookup) {
					if (isset($this_lookup['LookupTable'])) {
						if (isset($this_lookup['LookupSearchby'])) {
							$id_field = $this_lookup['LookupSearchby']. " AS id";
						} else { $id_field="id";}
						$options['Lookups'][$key]['LookupSet']=$udm->dbcon->GetAssoc( "Select $id_field, ".$this_lookup['LookupField']." FROM ".$this_lookup['LookupTable']);
					}
				}
			}
		}
		return $options;
	}

///CREATES HTML for PUBLISH, UNPUBLISH, DELETE, SELECT-ALL Buttons
	function action_bar() {
		$options=&$this->options;
		if ($options['allow_publish']) {

			//Each of these sets the List_Action value of the form and submits the form
			//Selected items are posted in List_id
			//Publish Button
			$publish_btn="<input type=\"button\" name=\"Publish\" value=\"Publish\" class=\"name\" onclick=\"document.forms['".$options['list_form']."'].elements['List_Action'].value='Publish'; document.forms['".$options['list_form']."'].submit();\">";
			//Unpublish Button
			$unpublish_btn="<input type=\"button\" name=\"UnPublish\" value=\"Unpublish\" class=\"name\"  onclick=\"document.forms['".$options['list_form']."'].elements['List_Action'].value='Unpublish'; document.forms['".$options['list_form']."'].submit();\">";
			//Delete Button
			$delete_btn="<input type=\"button\" name=\"Delete\" value=\"Delete\" class=\"name\"  onclick=\"if (confirm('Are you sure you want to DELETE these records?')) {document.forms['".$options['list_form']."'].elements['List_Action'].value='Delete'; document.forms['".$options['list_form']."'].submit();}\">";
		}
		//Email Button
		if ($options['allow_email']) {
			$email_btn="<input type=\"button\" class=\"name\" value=\"Send Email\" name=\"list_email_btn\" onclick=\"setup_Email();\">";
		}	
		///Export Button
		if ($options['allow_export']) {
			$export_btn="<input type=\"button\" class=\"name\" value=\"Export\" name=\"list_export\" onclick=\"setup_Export();\">";
		}
		$select_all_btn="<input type=\"hidden\" name=\"list_select\" class=\"name\"  value=\"Select All\" onclick=\"list_selectall()\">";

		$output ="<div class=".$options['control_class']." id = \"list_action_bar\" style=\"width:100%;text-align:left;vertical-align: top;background-color:#FFFFFF;padding: 0 5px 5px 10px;\"><B>With Selected:</b> ".	$select_all_btn.$publish_btn."&nbsp;&nbsp;".$unpublish_btn."&nbsp;&nbsp;".$delete_btn."&nbsp;&nbsp;".$email_btn."&nbsp;&nbsp;".$export_btn."</div>";
		return $output;
	
	}



	//returns the editlink value  for inclusion in the SQL query
	function setupEditlink($options) {
		$editlink_set=split(",", $options['editlink_fields']);
		if (is_array($editlink_set)) {
			$editlink="Concat( '".$options['editlink_action']."?";
		
			foreach ($editlink_set as $current_link) {
				if ($current_link=='id') { //hack to change id to uid
					$editlink.=$options['editlink_id_type']."=', $current_link, '&";
				} else {
					$editlink.=$current_link."=', $current_link, '&";
				}
			}
			$editlink=substr($editlink, 0, strlen($editlink)-4);
			$editlink.=") as hide_editlink, id as hide_id ";
		} else {
			$editlink=$options['editlink_action']." AS hide_editlink, id as hide_id ";
		}
		$editlink= ", ".$editlink;
		return $editlink;
	
	}

//reads the requested action from the form
	function readAction() {
		if (isset($this->options['List_Action'])) {
			switch ($this->options['List_Action']) {
				case 'Publish' :
					$this->publish_set($_REQUEST['id']); break;
				case 'Unpublish': 
					$this->unpublish_set($_REQUEST['id']); 
				break;
				case 'Delete':
					$this->delete_set($_REQUEST['id']);break;
				case 'Advanced_Modin':
					$this->options['show_advanced_modin']=true;
				break;
				/*case 'Search': $options['list_criteria']=(isset($_REQUEST['list_criteria_sql']))?$_REQUEST['List_criteria_sql']:$options['list_criteria'];
				break;*/
			}
			$this->options['List_Action']='';
		}
		return $options;
	}

	function header_script() {
		$options=&$this->options;
		
				
				
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

		
		
		
		$script="<script type=\"text/javascript\">
		
		var sform=document.forms['".$options['list_form']."'];";

		if ($options['allow_select']) { $script.="
		
		//Javascript function to select all/deselect all on a given page
		var sel_action='Select All';

		function list_selectall() {
			sform=document.forms['".$options['list_form']."']; 
			t=document.forms['".$options['list_form']."'].length;
			if (sel_action=='Select All') {
				sel_action='Unselect All';
				var tvalue=true;
			} else {
				sel_action='Select All';
				var tvalue=false;
			}
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id') {
					sform.elements[n].checked=tvalue;
				}
			}
		}
		//Javascript - returns the currently selected ids as a comma separated string
		function list_return_selected() {
			sform=document.forms['".$options['list_form']."']; 
			t=sform.length;
			var selected_list='';
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id') {
					if (sform.elements[n].checked==true) {
						selected_list=selected_list+','+sform.elements[n].value;
					}
				}
			}
			selected_list=selected_list.substring(1);
			return selected_list;
		}

		//Javascript - selects an ID - used by table row_select
		function select_id(find_id) {
			sform=document.forms['".$options['list_form']."'];
			t = sform.length;
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id'&&sform.elements[n].value==find_id) {
					sform.elements[n].checked=!sform.elements[n].checked;
					return;
				}
			}
		}
		";
		}



		if ($options['allow_email']&&!$options['use_email_form']) { 
			
					$script.="
			
				//Javascript - checks current status and updates SQL before starting Email Blast
				function setup_Email() {
					var eform=document.forms['List_email'];
					var mylist=list_return_selected();
					if (mylist=='') {
						var reply= confirm('you have not selected any names\\nSend e-mail to entire list?');
						if (reply) { 
							eform.elements['sqlp'].value=eform.elements['start_sql'].value;
							//alert( eform.elements['sqlp'].value);
							eform.submit(); 
						}
					} else {
						eform.elements['sqlp'].value=eform.elements['start_sql'].value+'AND id IN('+mylist+')'; 
						//alert( eform.elements['sqlp'].value);
						eform.submit();
					}
				}";  //end jscript
			
		}
		
		if ($options['allow_export']) {$script.="

		//Javascript - checks current status and updates SQL before starting Export
		function setup_Export() {
			var sform=document.forms['".$options['list_form']."'];
			var mylist=list_return_selected();
			if (mylist=='') {
				var reply= confirm('you have not selected any names\\nExport entire list?');
				if (reply) { 
					sform.action='modinput4_export.php?modin=".$this->udm->instance."';
					//alert (sform.action+'\\n'+sform.elements['sqlp'].value);
					sform.submit(); 
				}
			} else {
				sform.action='modinput4_export.php?modin=".$udm->instance."';
				sform.submit();
			}
		}";
		
		}

		$script.="</script>";
		if ($options['allow_email']) { 		
			$script.=$this->emailBlastConnect($udm, $options);
		}
		
		return $script;
	}




	//publish a set of ids
	function publish_set($ids) {
		$options=&$this->options;
		if (is_array($ids)) {
			$id_set=join(',', $ids);
			$q = "update ".$options['datatable']." set publish=1 where id IN($id_set)";
			$this->dbcon->execute($q) or die($dbcon->errorMsg());
			$options['message']='Selected items posted live.';

		}
		return $options;
	}

	function unpublish_set($ids) {
		$options=&$this->options;

		if (is_array($ids)) {
			$id_set=join(',', $ids);
			$q = "update ".$options['datatable']." set publish=0 where id IN($id_set)";
			$this->dbcon->execute($q) or die($dbcon->errorMsg());
			$options['message']='Selected items unpublished.';
		}
		return $options;
	}


	/**
	 * a function to take an array of IDs and delete them
	 */
	function delete_set($ids) {
		$options=&$this->options;

		if (is_array($ids)) {
			$id_set=join(',', $ids);
			$q = "delete from ".$options['datatable']." where id IN($id_set)";
			$this->dbcon->execute($q) or die($dbcon->errorMsg());
			$options['message']='Selected items deleted.';
		}
		return $options;
	}






}




function udm_output_userlist_html_old($udm, $options=null) {
		global $ID, $MM_email_from; // - returns current user variable
		
		//List Vars
		//create default options array
		
		/*Setup the SQL used by the list
		  */
		
		//Display Fields for list
		$default_options['display_fields']="id, Concat(First_Name, \" \", Last_Name) as Name, Company, State, Phone, publish";
		//Source Table
		$default_options['datatable']="userdata";
		//Criteria
		$default_options['list_criteria']="modin=".$udm->instance;
		//Sort By
		$default_options['sort_by']="Last_Name, First_Name";
		
		//Page Name and Form Name defaults
		$default_options['page_name']=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$default_options['list_form']="UDM_Listing";

		//variables assigned to the query string by the 'edit' link
		$default_options['editlink_fields']="modin,id";
		$default_options['editlink_id_type']="uid";

		//target for the edit link
		$default_options['editlink_action']="modinput4_view.php";

		
		//Pagination Defaults
		$default_options['default_qty']=50;
		$default_options['current_offset']=0;
		$default_options['qty_displayed']=$default_options['default_qty'];

		
		/* Permissions 
		  */

		//show edit links and selectable listitems
		$default_options['allow_edit']=TRUE;  
		$default_options['allow_select']=TRUE;
		//allow list to look up values in other tables
		$default_options['allow_lookups']=TRUE;
		//allow publish, unpublish, and delete
		$default_options['allow_publish']=TRUE;
		//allow e-mail blaster
		$default_options['allow_email']=TRUE;
		//allow multi-source listings
		$default_options['allow_include_modins']=FALSE;
		
		/* Formatting
		  */
  		$default_options['show_headers']=TRUE;  //this doesn't work yet
		$default_options['show_action_bar']=TRUE;  //action bar is turned off without this
		$default_options['show_advanced_modin']=FALSE; //load code for the modin Sources listing
		$default_options['is_dynamic']=TRUE; //read display values passed from one listpage to the next

		/* HTML Templating
		  */
		$default_options['controls_class']="side";
  		$default_options['list_row_start_template']="<tr id=\"listrow_%s\" bordercolor=\"#333333\" bgcolor=\"%s\" class=\"results\" onMouseover=\"this.bgColor='#CCFFCC';\" onMouseout=\"this.bgColor='%s';\" onClick=\"select_id(this.id.substring(8));\">";
		$default_options['list_row_end']="</tr>\n";
		$default_options['list_item_start']="<td class=list_column_%s>";
		$default_options['list_item_end']="</td>";
		$default_options['list_html_start']='<center><table cellpadding="1" cellspacing="1" width="95%"><tr class="toplinks">';
		$default_options['list_html_footer']="</table></center>";
		$default_options['list_html_header_column_start']="<td align=\"left\">";
		$default_options['list_html_header_template']="<b><a href=\"javascript: document.forms['%1\$s'].elements['sort_by'].value = '%2\$s '+document.forms['%1\$s'].elements['sort_by'].value; document.forms['%1\$s'].submit();\">%3\$s</a></b>";


		/* Lookups and Aliases
		  *  Lookups show values from other tables or from a passed set
		  *  Aliases are MySQL 'AS' constructions in display_fields - these need the base field info
		  *  for column sorting to work correctly
		  */
		$default_options['Aliases']['Name']['sort']='First_Name, Last_Name';
		$default_options['Aliases']['Changed']['sort']='timestamp';
		#$default_options['Lookups'][]=array("fieldname"=>"modin", "LookupTable"=>"userdata_fields", "LookupField"=>"name");
		$default_options['Lookups']['publish']=array("LookupSet"=>array("0"=>"draft" , "1"=>"live"), 'LookupName'=>'status');

		
		/*Email Blast Options
		  */
		$default_options['email_action']="udm_mailblast.php";

		if ($default_email=$udm->dbcon->GetAssoc("SELECT id, name, email from users where id=$ID")){
			if (isset($default_email[$ID]['email'])) {
				$default_options['user_email']=$default_email[$ID]['email'];
				$default_options['user_name']=$default_email[$ID]['name'];
			}
		} else { 
			$default_options['user_email']=$MM_email_from;
		}
		
		
		$options= array_merge($default_options, $options);

		//Initialize the list
		$userlist=new UserList_HTML($options);
	
		//check display fields for admin/enabled
		$options=$userlist->check_fields($udm, $options);

	if ($options['allow_include_modins']&&$options['show_advanced_modin']) {
		$options['Lookups']['modin']['LookupName']="Source";
		$options['Lookups']['modin']['LookupTable']="userdata_fields";
		$options['Lookups']['modin']['LookupField']="name";
	}

	if ($options['allow_lookups']) {
		$options=$userlist->setupLookups($udm, $options);
	}
	
	$options['hidden_fields'].=$userlist->setupEditLink($options);

	if (!strpos($options['display_fields'], "timestamp")===FALSE) {
		$options['display_fields']=str_replace("timestamp", "DATE_FORMAT(timestamp, \"%m-%d\") as Changed", $options['display_fields']);
	}


	if ($udm->authorized) {
		return $output;
	} else {
		$this->error='You do not have permission to view this list';
	}
}




?>