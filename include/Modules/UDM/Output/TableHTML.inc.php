<?php
require_once ('AMP/UserData/Plugin.inc.php');
require_once ('AMP/Region.inc.php');

class UserDataPlugin_TableHTML_Output extends UserDataPlugin {
    
    var $options= array( 
		'list_row_start_template'=>array('value'=>"<tr id=\"listrow_%s\" bordercolor=\"#333333\" bgcolor=\"%s\" class=\"results\" onMouseover=\"this.bgColor='#CCFFCC';\" onMouseout=\"this.bgColor='%s';\" onClick=\"select_id(this.id.substring(8));\">"),
		'list_row_end'=>array('value'=>"</tr>\n"),
		'list_item_start'=>array('value'=>"<td class=list_column_%s>"),
		'list_item_end'=>array('value'=>"</td>"),
		'list_html_start'=>array('value'=>'<center><table cellpadding="1" cellspacing="1" width="95%"><tr class="toplinks">'),
		'list_html_footer'=>array('value'=>"</table></center></form>"),
		'list_html_header_column_start'=>array('value'=>"<td align=\"left\">"),
		'list_html_header_template'=>array('value'=>'<td align="left"><b>%s</b></td>'),
                #"<td align=\"left\"><b><a href=\"javascript: document.forms['%1\$s'].elements['sortby'].value = '%2\$s '+document.forms['%1\$s'].elements['sortby'].value; document.forms['%1\$s'].submit();\">%3\$s</a></b></td>"),
        'display_fields'=>array('value'=>'id,Name,Company,State,Phone,Status'),
        'display_format'=>array('value'=>'table_format'),
        'form_name'=>array('value'=>'udm_list'),
        'editlink'=>array('value'=>'modinput4_view.php')
        );
    var $html_rowcount=0;
    var $display_fieldset;
    var $Lookups;
    var $list_row_select;
    var $list_row_edit;

    function UserDataPlugin_TableHTML_Output (&$udm, $options=null, $instance=null) {   
        $this->init($udm, $options, $instance);
    }


    function execute ($options=null) {
        //create fieldset
        $this->display_fieldset=split(',', $this->options['display_fields']['value']);

        //Print the current results list
        if (!($dataset=$this->udm->getData())) return false;
        
        $display_function=isset($this->options['display_format']['value'])?($this->options['display_format']['value']):"display_item";
        $inclass=method_exists($this, $display_function);

        //Start Output
        $output='<FORM name="'.$this->options['form_name']['value'].'" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
        if ($this->udm->admin) $output.=$this->select_script();
        $output.=$this->column_headers();

        //output display format
        foreach ($dataset as $dataitem) {
            if (isset($this->options['subheader'])) $output.=$this->subheader($dataitem);
            if($inclass) $output.=$this->$display_function($dataitem);
            else $output.=$display_function($dataitem, $this->options);
        }

		return $this->options['list_html_start']['value'].$output.$this->options['list_html_footer']['value'];
    }

    function _register_options_dynamic(){
        if ($this->udm->admin) { //make editing permission available
            $this->list_row_select.=sprintf($this->options['list_item_start']['value'], 'ROWSELECT_box').
                "<input name=\"id[]\" type=\"checkbox\" value=\"%s\" onclick=\"this.checked=!this.checked;\">".
                $this->options['list_item_end']['value'];
            $this->list_row_edit=sprintf($this->options['list_item_start']['value'],'editlink').
                "<a href=\"".$this->options['editlink']['value']."?uid=%s&modin=".$this->udm->instance."\">edit</a>".$this->options['list_item_end']['value'];
        }
    }


    function column_headers() {
        foreach ($this->display_fieldset as $key) {
            if ($sort_obj=&$this->udm->getPlugin('AMP', 'Sort')) {
                $key=$sort_obj->makelink($key);
            }
            $list_html_headers.=sprintf($this->options['list_html_header_template']['value'], $key);
        }
        if ($this->udm->admin) { //include columns headers for select and edit cols
            $list_html_headers=$this->options['list_html_header_column_start']['value'].
                    "<a href=\"javascript: list_selectall();\"><B>All</B></a>".
                    $this->options['list_item_end']['value'].$list_html_headers;
            $list_html_headers.=$this->options['list_item_start']['value'].$this->options['list_item_end']['value'];
        }
        return $list_html_headers;
    }

    function table_format($current_row) {
        $this->html_rowcount++;

        //assigns an id and background color to each row
        $bgcolor =($this->html_rowcount % 2) ? "#D5D5D5" : "#E5E5E5";
        $list_row="";
        $list_row_start=sprintf($this->options['list_row_start_template']['value'], $current_row['id'], $bgcolor, $bgcolor);

        foreach($this->display_fieldset as $key) {
            //Check for values swapped by Lookup
            if (isset($this->Lookups[$key])) {
                $kvalue=$this->Lookups[$key]['Set'][$current_row[$key]];
            } else {
                $kvalue=$current_row[$key];
            }
            //Main Field Format Statement
            $list_row.=sprintf($this->options['list_item_start']['value'], $key).
                        $kvalue.$this->options['list_item_end']['value'];
        }
        //Admin View Formatting
        if ($this->udm->admin) {
            $list_row=sprintf($this->list_row_select, $current_row['id']).$list_row;
            $list_row.=sprintf($this->list_row_edit, $current_row['id']);
        }
        return $list_row_start.$list_row.$this->options['list_row_end']['value'];

    }

    function select_script() {

        $form_name=$this->options['form_name']['value'];
		$script="<script type=\"text/javascript\">
		
		var sform=document.forms['".$form_name."'];";

		if ($this->udm->admin) { $script.="
		
		//Javascript function to select all/deselect all on a given page
		var sel_action='Select All';

		function list_selectall() {
			sform=document.forms['".$form_name."']; 
			t=document.forms['".$form_name."'].length;
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
			sform=document.forms['".$form_name."']; 
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
			sform=document.forms['".$form_name."'];
			t = sform.length;
			for (n=0; n<t; n++){
				//alert(sform.elements[n].name);
				if (sform.elements[n].name.substring(0,2)=='id'&&sform.elements[n].value==find_id) {
					sform.elements[n].checked=!sform.elements[n].checked;
					return;
				}
			}
		}
		</script>";

		}
        return $script;
    }
}



?>
