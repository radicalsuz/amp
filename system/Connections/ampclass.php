<?php

if ( !function_exists( 'autoinc_check' ) ) {
	function autoinc_check ($table,$num) {
		global $dbcon;
		$getid=$dbcon->Execute( "SELECT id FROM $table ORDER BY id DESC LIMIT 1") or die($dbcon->ErrorMsg());
		if ($getid->Fields("id") < $num) { $id = $num; } else { $id = NULL;} 
		return $id;
	}
}
if ( !function_exists( 'helpme2' ) ) {
	function helpme2($link) {
		$output = "<a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=$link','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\"></a>&nbsp;";
		return $output;
	}
}	 
if ( !function_exists( 'helpme' ) ) {

	function helpme($link) {
	
		global $PHP_SELF;
		$output="<table width=\"15\" border=\"0\" align=\"right\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a href=\"javascript:void(0)\" ONCLICK=\"open('help.php?file=";
		
		$pos = strrpos($PHP_SELF, "/");
		$pos = substr($PHP_SELF, ($pos + 1), -4);
		$output.= $pos;
		$output.= "#";
		$output.= $link;
		$output.="','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400')\"><img src=\"images/help.png\" border=\"0\" align=\"absmiddle\"></a></td></tr></table>";
		return $output;
	
	}
}

if ( !function_exists( 'DateOut' ) ) {

    function DateOut($date) {
        if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
            $date = "$regs[2]-$regs[3]-$regs[1]";
        }
		return $date;
    }
}

function listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby=null,$sort=null,$extra=null,$extramap=NULL) {
	global $dbcon;
	if ($sort) { $orderby =" order by $sort asc ";}
	$query=$dbcon->Execute($listsql.$orderby) or DIE($dbcon->ErrorMsg());
	
	echo "<h2>".$listtitle."</h2>";
	echo "\n<div class='list_table'> \n	<table class='list_table'>\n		<tr class='intitle'> ";
	echo "\n			<td>&nbsp;</td>";
	foreach ($fieldsarray as $k=>$v) {
		echo "\n			<td><b><a href='".$_SERVER['PHP_SELF']."?action=list&sort=".$v."' class='intitle'>".$k."</a></b></td>";
	}
	
	if ($extra) {
		for ($i = 1; $i <= sizeof($extra); $i++) {
			echo "\n			<td>&nbsp;</td>";
		}
	}
	echo "\n		</tr>";
	$i= 0;
	while (!$query->EOF) {
		 $i++;
		 $bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
	
		echo "\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
		echo "\n			<td> <div align='center'><A HREF='".$filename."?id=".$query->Fields("id")."'><img src=\"images/edit.png\" alt=\"Edit\" width=\"16\" height=\"16\" border=0></A></div></td>";
		foreach ($fieldsarray as $k=>$v) {
			if ($v =='publish' ) {
				if ($query->Fields($v) == 1) { $live= "live";}
				else { $live= "draft";}
				echo "\n			<td> $live </td>";
			}
			else {
				echo "\n			<td> ".$query->Fields($v)." </td>";
			}
		}
		
		if ($extra) {
			
			
			foreach ($extra as $k=>$v) {
				$id=NULL;
				if ($extramap[$k] != NULL) {
					$id= $extramap[$k];
				}else {
					$id= "id";
				}
				echo " \n			<td> <div align='right'>";
				echo "<A HREF='".$v.$query->Fields($id)."'>$k</A>";
				echo "</div></td>";
			}
			
		}
		echo "\n		</tr>";
	
		$query->MoveNext();
	}		
	
	echo "\n	</table>\n</div>\n<br>&nbsp;&nbsp;<a href=\"$filename\">Add new record</a> ";

}

function listpage_basic($listtitle,$fieldsarray,$filename) {
	echo "<h2>".$listtitle."</h2>";
	echo "<div class='list_table'> \n	<table class='list_table'> \n		<tr class='intitle' > ";
	$r=0;
	foreach ($fieldsarray[0] as $k=>$v) {
		echo "\n			<td><b><a href='".$_SERVER['PHP_SELF']."?action=list&sort=".$k."' class='intitle'>".$k."</a></b></td>";
		$f[$r]=$k;
		$r++;
	}
	echo "\n		</tr>";
	$i=0;
	for($x=0;$x<sizeof($fieldsarray);$x++){
		$i++;
		$bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
		echo "\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
		foreach ($f as $k=>$v) {
			echo "\n			<td> ".$fieldsarray[$x][$v]." </td>";
		}
		echo "\n		</tr>";
	}
	echo "\n	</table> \n</div>";
}

function WYSIWYG($value,$html){
	global $browser_mo, $browser_ie, $browser_win;
	if ($browser_mo && ($_COOKIE["AMPWYSIWYG"] != 'none'))  {
		$output = launch_htmlarea($value,$html);
	}
	elseif (($browser_ie) && ($browser_win) && ($_COOKIE["AMPWYSIWYG"] != 'none')) { 
		$output = launch_win($value,$html);
	}
	else {
		$output = launch_nowysiwyg($value,$html);
	}	
	return $output;
}

function launch_htmlarea($value,$html=NULL) {
	$output = '
	<script type="text/javascript">
		_editor_url = "htmlarea/";
		_editor_lang = "en";
	</script> 
	<script type="text/javascript" src="htmlarea/htmlarea.js"></script> 
	<script type="text/javascript">
    	// WARNING: using this interface to load plugin
      	// will _NOT_ work if plugins do not have the language
      	// loaded by HTMLArea.

      	// In other words, this function generates SCRIPT tags
      	// that load the plugin and the language file, based on the
      	// global variable HTMLArea.I18N.lang (defined in the lang file,
      	// in our case "lang/en.js" loaded above).

      	// If this lang file is not found the plugin will fail to
      	// load correctly and nothing will work.

      	HTMLArea.loadPlugin("TableOperations");
      	HTMLArea.loadPlugin("SpellChecker");
      	HTMLArea.loadPlugin("FullPage");
      	HTMLArea.loadPlugin("CSS");
      	HTMLArea.loadPlugin("ContextMenu");
	</script>
	<script type="text/javascript">
		var editor = null;
		function initEditor() {
  			// create an editor for the "ta" textbox
  			editor = new HTMLArea("articlemo");
			// register the FullPage plugin
			editor.registerPlugin(FullPage);
			// register the SpellChecker plugin
			editor.registerPlugin(TableOperations);
			// register the SpellChecker plugin
			//editor.registerPlugin(SpellChecker);
			setTimeout(function() {
				editor.generate();
			}, 500);
  		return false;
		}
	</script> 
	<textarea id = "articlemo" name="article" cols="80" rows="60" wrap="VIRTUAL" style="width:100%">';
	if ($html != "1") {
		$output .= nl2br($value);
	} else {
		$output .= $value;
	} 
	$output .= '</textarea> <input name="html" type="hidden" value="1">'; 
	return $output;
}
 
function launch_win($value,$html=NULL) {
	$leadin = '';
	if ($html != "1") {
		$textvalue = nl2br($value);
	} else {
		$textvalue = $value;
	}

	$oFCKeditor = new FCKeditor ;
	$oFCKeditor->Value = $textvalue  ;
	ob_start();
	$oFCKeditor->CreateFCKeditor( 'article', '500', 500 ) ;
	$output = ob_get_contents();
    ob_end_clean();
    $output .= '<input name="html" type="hidden" value="1"> ';
	return $output;
} 

function launch_nowysiwyg($value,$html=NULL) {
    $output = '<input name="html" type="checkbox" value="1" ' ;  
	if ($html == "1") { $output .= "CHECKED";} 
    $output .= '>HTML Override <br> <textarea name="article" cols="65" rows="20" wrap="VIRTUAL">';
	$text2 = $value;
	if ($html == "1"){
		$text2 = str_replace("<BR>", "<BR>\r\n", $text2);
	} 
	$output .= $text2 .'</textarea> ';
	return $output;
} 



/**
 * Base RICE class.
 * All classes should extend this for common functionality.
 */
class Base {
	var $db;  /// adodb object

	var $error   = array(); /// holds the last error
	var $options = array(); /// associative array of all options
	
	function Base() {
		global $dbcon;

		$this->db  = &$dbcon;
		#$this->tpl = &$tpl;
	}

	/**
	 * Sets a class option.  This can be anything useful to your object.
	 * Meant to be a public interface so options can be set at runtime.
	 *
	 * @param string $option the option name
	 * @param mixed  $value  the option value
	 */
	function set_option($option, $value) {
		$this->options[$option] = $value;
	}

	/**
	 * Returns a specific option.  This is designed so when you render() or
	 * output anything, you can test to see what options are set and act on
	 * them.  For instance, a date format, paging options, whatever.
	 *
	 * @param string $option the option name
	 */
	function get_option($option) {
		return isset($this->options[$option]) ? $this->options[$option] : false;
	}

	/**
	 * A function to add an error to the error stack.
	 *
	 * @param string $error error message
	 */
	function add_error($error) {
		$this->error[] = $error;
	}

	/**
	 * Returns the last error.
	 */
	function get_error() {
		$form = '<ul>%s</ul>';
		$item = '<li>%s';

		$output = null;

		if(is_array($this->error)) {
			foreach($this->error as $msg) {
				$output .= sprintf($item, $msg);
			}

			$output = sprintf($form, $output);
		}

		return $output;
	}
	
	/**
	 * turns a date and time array into a mySQL datetime correct field. time may be optionally included in the date array.
	 * the date array looks like $d['year'], $d['month'], $d['day'] and the time array looks like $t['hour'], $t['min'], $t['ext']
	 * you may optionaly include the time in the same array as date with the same names...
	 * @param $d date array
	 * @param $t time array
	 */	
	function make_datetime($d, $t=null, $s=null) {
		$datetime = $d['year'] . '-' . $d['month'] . '-' . $d['day'];
		if ($t != null) {
			if(strtolower($t['ext']) == 'am') {
				$t['hour'] += 12;
				if ($t['hour'] == 24) $t['hour'] = '00';
			} else {
				if ($t['hour'] == 12) $t['hour'] = '00';	
			}
			$datetime .= ' '. $t['hour'] . ':' . $t['min'] . ':00';
		} else if ($d['ext'] != '') {
			if(strtolower($d['ext']) == 'pm') {
				$d['hour'] += 12;
				if ($d['hour'] == 24) $d['hour'] = '12';
			} else {
				if ($d['hour'] == 12) $d['hour'] = '00';	
			}
			$datetime .= ' '. $d['hour'] . ':' . $d['min'] . ':00';
		}
		return $datetime;
	}
	
	/**
	 * turn a mysql datetime stamp into an array for use by the date and time classes
	 *
	 * @param $dt the datetime to convert to an array
	 */
	function make_datearray($dt) {
		$dt = strtotime($dt);
		$v['day'] = date("d", $dt);
		$v['month'] = date("m", $dt);
		$v['year'] = date("Y", $dt);
		return $v;
	}
	
	function make_timearray($dt) {
		$dt = strtotime($dt);
		$v['hour'] = date("h", $dt);
		$v['min'] = date("i", $dt);
		$v['ext'] = date("a", $dt);
		return $v;
	}

	/**
	 * Return a list of results keyed by the id_field.
	 *
	 * @param string $q the query
	 * @param string $id_field the field to use for keys
	 * @param string $field if present, what field to use as the value
	 */
	function _build_list($q, $id_field, $field = null) {
		$r = $this->db->execute($q);

		if(!$r) {
			die($this->db->errorMsg());
		}

		$list   = array();
		$single = (bool) $field;

		while(!$r->EOF) {
			if($single) {
				$list[$r->fields[$id_field]] = $r->fields[$field];
			}
			else {
				$list[$r->fields[$id_field]] = $r->fields;
			}

			$r->moveNext();
		}

		return $list;
	}
}

class BuildForm {

	function db_out($m) {
		$m = stripslashes($m);
		return $m;
	}
	
	function db_in($m) {
		#$m = htmlspecialchars(stripslashes($m), ENT_QUOTES); 
		$m = stripslashes($m); 
		return $m;
	}

	//html table functions
	function start_table($class = 'form') {
		return '<table width="100%" align="center" cellpadding="3" class="'. $class .'">';
	}
	
	function end_table() {
		return '</table>';
	}
	
	function del_btn() {
		return "<input name=\"MM_delete\" type=\"submit\" value=\"Delete Record\" onclick=\"return confirmSubmit('Are you sure you want to DELETE this record?')\">";
	
	}
	
	function add_btn() {
		$html = '<input type="submit" name="';
		if (empty($_REQUEST["id"])== TRUE) {
			$html .=  "MM_insert";
		} else {
			$html .= "MM_update";
		}
		$html .="\" value='Save Changes'> ";
		return $html;
	}
	
	function add_row($label=null, $field=null, $req=null, $right_text=null) {
		$html = '
		<tr valign="top">
			<td class="name">'. $label .'</td>
			<td>';
		$html .= (is_object($field)) ? $field->fetch(): $field;
		$html .= ' '. $right_text .'</td></tr>';
		return $html;
	}
	
	function add_colspan_obj($label = null, $object = null, $req = null) {
		$html = '
		<tr valign="top"><td class="form" colspan="2">';
		$html .= ($label != null) ? $label .'<br clear="all" />': '';
		$html .=  $object->fetch() .'<br clear="all" />'. $right_text .'</td></tr>';
		return $html;
	}
	
	function add_colspan($label = null, $object = null, $req = null) {
		$html = '
		<tr valign="top"><td class="form" colspan="2">'.$label.'</td></tr>';
		return $html;
	}

	function add_header($header, $class='intitle') {
		if ($class) {
			$html .= '
			<tr valign="top"><td colspan="2" class="'. $class .'">'. $header .'</td></tr>';
		} else {
			$html .= '
			<tr valign="top"'. $class .'><td colspan="2"><br /><strong>'. $header .'</strong></td></tr>';
		}
		return $html;	
	}
	
	function add_content($content) {
		$html .= '
		<tr valign="top"><td colspan="2" class="form">'. $content .'</td></tr>';
		return $html;
	}
}

class Input {
	function Input($type=null,  $name=null, $value=null, $class=null,
	               $label=null, $check=null, $size=null, $maxlength=null) {
		$this->type  = $type;
		$this->name  = $name;
		$this->value = htmlentities($value);
		$this->class = $class;
		$this->label = $label;
		$this->check = $check;
		$this->size  = $size;
		$this->maxlength = $maxlength;
	}

	/**
	 * Return the completed element.
	 */
	function fetch() {
		$btn = '<input type="%s" value="%s" name="%s" class="%s" size="%d" maxlength="%s" %s>%s';
		return sprintf($btn, $this->type, $this->value, $this->name, $this->class,
		               $this->size, $this->maxlength, $this->check, $this->label);
	}
}

class Text {
	function Text($name=null, $value=null, $size=30, $maxlength=null, $class=null) {
		$this->name = $name;
		$this->value = $value;
		$this->size = $size;
		$this->maxlength = $maxlength;
		$this->class = $class;
	}
	
	function fetch() {
		$text = & new Input('text', $this->name, $this->value, $this->class, null, null, $this->size, $this->maxlength);
		return $text->fetch();
	}
}

/**
 * RadioList class.
 *
 * Usage:<code>
 *     $list = & new RadioList('my_list', array('dog', 'cat', 'bird'));
 *     echo $list->fetch();</code>
 */
class RadioList {
	/**
	 * Start a new RadioList.
	 *
	 * @param string $name the name of the radiolist
	 * @param array  $list list of choices keyed by values
	 * @param int    $selected which key is selected by default
	 */
	function RadioList($name, $list, $selected=null) {
		$this->name     = $name;
		$this->list     = $list;
		$this->selected = $selected;
	}

	/**
	 * Return the completed element.
	 */
	function fetch() {
		$buf = null;

		foreach($this->list as $key => $value) {
			$sel  = $key == $this->selected ? 'checked' : null;
			$item = & new Input('radio', $this->name, $key, $sel, $value, $sel);
			
			$buf .= $item->fetch() . '<br>';
		}

		return $buf;
	}
}

/**
 * CheckList class.
 */
class CheckList {
	/**
	 * Constructor
	 *
	 * @param string $name the name of the list (array[] syntax!)
	 * @param array  $list the list
	 * @param array  $selected an array of items that are selected
	 */
	function CheckList($name, $list, $selected = null) {
		$this->name = $name;
		$this->list = $list;
		$this->selected = $selected ? $selected : array();
	}

	function fetch() {
		if(!($cnt = count($this->list))) return false;

		// Loop through and make all the widgets
		$buf = null;

		foreach($this->list as $key => $item) {
			#echo $item . in_array($item, $this->selected);
			$checked = in_array($item, $this->selected) ? 'checked' : null;
			$widget = new Input('checkbox', $this->name, $item, null, $item, $checked);
			$buf .= $widget->fetch() . '<br>';
		}

		return $buf;
	}
}

/**
 * Textarea class
 */
class TextArea {
	/**
	 * Start a new TextArea.
	 *
	 * @param string $name the name of the textarea
	 * @param string $contents contents of the textarea
	 * @param int    $rows the number of rows
	 * @param int    $cols the number of cols
	 * @param string $class the css class
	 */
	function TextArea($name, $contents = null, $rows=10, $cols=45, $class=null) {
		$this->name     = $name;
		$this->rows     = $rows;
		$this->cols     = $cols;
		$this->contents = $contents;
		$this->class    = $class;
	}

	function fetch() {
		$area = '<textarea name="%s" cols="%d" rows="%d" class="%s">%s</textarea>';
		return sprintf($area, $this->name, $this->cols, $this->rows, $this->class, $this->contents);
	}
}

/**
 * Select class.
 *
 * Usage:<code>
 *     $sel = & new Select('name', $options);
 *     echo $sel->fetch();</code>
 */
class Select {
	/**
	 * Constructor.  Accepts select box options.
	 *
	 * @param string $name name of the select box
	 * @param array  $options list of options (<option value="$key">$value</option>)
	 * @param int    $selected id of the selected option (should match key in $options)
	 * @param bool   $multiple whether to allow mulitple selections
	 * @param int    $size size of the select box
	 * @param string $special any special text for processing the value
	 */
	function Select($name, $options, $selected=null, $multiple=false, $size=1, $special=null, $class=null) {
		$this->name     = $name;
		$this->options  = $options;
		$this->selected = $selected;
		$this->multiple = $multiple ? 'multiple' : null;
		$this->size     = $size;
		$this->special  = $special;
		$this->class    = $class;
	}

	/**
	 * Return the completed select box.
	 */
	function fetch() {
		$box = '<select name="%s" size="%s" class="%s" %s>%s</select>';
		$opt = '<option value="%s" %s>%s</option>';
		$buf = null;

 		foreach($this->options as $key => $value) {
			$key = !empty($this->special) ? sprintf($this->special, $key) : $key;
			if (is_array($this->selected)) {//selected is an array, for multiselects
				$sel = (in_array($key, $this->selected)) ? 'selected' : null; 
			} else {
				$sel = ($this->selected == $key) ? 'selected' : null;
			}
			$buf .= sprintf($opt, $key, $sel, $value);
		}

		return sprintf($box, $this->name, $this->size, $this->class, $this->multiple, $buf);
	}
}
class Form extends Base {
	/**
	 * Constructor
	 *
	 * @param string $method post|get
	 * @param string $action url to post|get to
	 * @param string $name the name of the form (useful if you use js)
	 * @param string $enctype enctype
	 */
	function Form($method='POST', $action=null, $name=null, $enctype=null) {
		if ($action == null) $action = $_SERVER['PHP_SELF'];

		$this->method  = $method;
		$this->action  = $action;
		$this->name    = $name;
		$this->enctype = $enctype;
	}

	/**
	 * Set the contents of the form.  This function can take any number of
	 * arguments.  The results of each argument will simply be concatenated
	 * together.  If any of the arguments are objects, the objects fecth() method
	 * will be called.  PHP will generate a warning if you pass an object with no
	 * fetch method.
	 */
	function set_contents() {
		$args = func_get_args();
		foreach($args as $arg) {
			$this->contents .= is_object($arg) ? $arg->fetch() : $arg;
		}
	}

	/**
	 * Return the completed form.
	 */
	function fetch() {
		$form = '<form method="%s" action="%s" name="%s" %s enctype="%s">%s</form>';
		return sprintf($form, $this->method, $this->action, $this->name,
		               $this->get_option('submit_check'), $this->enctype,
					   $this->contents);
	}
}


/**
 * Date class.
 * Renders a group of selection fields related to the date.
 */
class Date {
	var $days;
	var $years;

	var $months = array(
		1  => 'January',
		2  => 'February',
		3  => 'March',
		4  => 'April',
		5  => 'May',
		6  => 'June',
		7  => 'July',
		8  => 'August',
		9  => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December'
	);
	
	/**
	 * Constructor.
	 *
	 * @param string $name   the name of the date array
	 * @param int    $mon    default month (1-12) to select
	 * @param int    $day    default day (1-31) to select
	 * @param int    $yr     default year to select
	 * @param string $class  the css class to apply to all fields
	 * @param bool   $select whether to show the select subscript [and key of zero]
	 */
	function Date($name, $mon=null, $day=null, $yr=null, $class=null) {
		$this->name  = $name;
		$this->mon   = $mon;
		$this->day   = $day;
		$this->yr    = $yr;
		$this->class = $class;

		$this->days  = array_smear(lpad(range(1, 31)));

		// Make the keys not padded with zeros
		$days = array();
		foreach($this->days as $key => $value) {
			if(substr($value, 0, 1) == '0') {
				$days[substr($value, 1, 1)] = $value;
			}
			else {
				$days[$key] = $value;
			}
		}

		$this->days  = $days;
		$this->years = array_smear(range(2003, 2013));
	}

	function get_month($m) {
		return $this->months[$m];
	}

	function get_months() {
		return $this->months;
	}

	function fetch() {
		$name = '%s[%s]';

		$n['m'] = sprintf($name, $this->name, 'month');
		$n['d'] = sprintf($name, $this->name, 'day');
		$n['y'] = sprintf($name, $this->name, 'year');
		
		$mon  = & new Select($n['m'], $this->months, $this->mon, false, 1, null, $this->class);
		$day  = & new Select($n['d'], $this->days,   $this->day, false, 1, null, $this->class);
		$year = & new Select($n['y'], $this->years,  $this->yr,  false, 1, null, $this->class);

		return $mon->fetch() . $day->fetch() . $year->fetch();
	}
}

/**
 * Time class.
 * Renders a group of selection boxes relating to the time.
 */
class Time {
	var $h;
	var $m;
	var $e;

	/**
	 * Constructor
	 *
	 * @param string $name name of the time array
	 * @param int    $hour selected hour
	 * @param int    $min  select minute
	 * @param string $ext  am|pm
	 * @param string $class css class
	 */
	function Time($name, $hour=null, $min=null, $ext=null, $class=null) {
		$this->name  = $name;
		$this->hour  = $hour;
		$this->min   = $min;
		$this->ext   = $ext;
		$this->class = $class;
		

		
		$this->h = array_smear(lpad(range(0, 12)));
		$this->m = array_smear(lpad(range(0, 59)));
		$this->e = array('am' => 'AM', 'pm' => 'PM');
	}

	/**
	 * Return the completed time widgets.
	 */
	function fetch() {
		$name = '%s[%s]';

		$n['h'] = sprintf($name, $this->name, 'hour');
		$n['m'] = sprintf($name, $this->name, 'min');
		$n['e'] = sprintf($name, $this->name, 'ext');

		$h = & new Select($n['h'], $this->h, $this->hour, false, 1, null, $this->class);
		$m = & new Select($n['m'], $this->m, $this->min,  false, 1, null, $this->class);
		$e = & new Select($n['e'], $this->e, $this->ext,  false, 1, null, $this->class);

		return $h->fetch() . '&nbsp;:&nbsp;' . $m->fetch() . $e->fetch();
	}
} 


function addfield($name,$label=NULL,$fieldtype='text',$value=NULL,$defualt=NULL,$size=45,$height=4,$right_text=NULL) {
	global $buildform,$R;
	if ($label == NULL) { $label = $name; }
	if (($R) &&  $value == NULL ) { $value  = $R->Fields($name); }
	
	$label = str_replace('_',' ',$label); 
	$label = ucwords($label);
	
	if ((!$_GET['id']) && (!$value)) {$value = $defualt;}
	if ($fieldtype == 'text') {
		$field = & new Text($name, $value, $size);
	}
	if ($fieldtype == 'checkbox') {
		if ($value == 1 or $defualt == 1) $value = 'checked';
		$field = & new Input('checkbox', $name, '1', null, null, $value); 
	}
	if ($fieldtype == 'textarea') {
		$field = & new Textarea($name, $value, $height , $size);
	}
	if ($fieldtype == 'file') {
		$field =  & new Input($fieldtype,$name,'','','', '', $size);
	}
	
	if ($fieldtype == 'hidden') {
		$field =  & new Input($fieldtype,$name,$value,'','', '', '');
	}

	$output = $buildform->add_row($label, $field,'',$right_text);
	
	return $output;
}

function makelistarray($q,$key,$value,$label='Select') {
	global $dbcon;
	$list = array(''=>$label);
	while (!$q->EOF) {
			$list[$q->Fields($key)] =$q->Fields($value);
		$q->MoveNext();
	}
	return $list;
}

function upload_image($newname=NULL,$wwidth,$lwidth,$thumbwidth,$hide_display=NULL){
	global $base_path_amp,$gd_version;

	$picdir = $base_path_amp."img/original";
	$thumbdir = $base_path_amp."img/thumb";
	$usedir = $base_path_amp."img/pic"; 
	$addition = "";
 	$newext = "jpg";

	$array = explode (".",$_FILES['file']['name']);
	$filename = $array[0];
	$extension = strtolower($array[1]);
    if ($_FILES['file']['name'] == "")	{
    } else {
		if(!(($extension == jpe) or ($extension == jpg) or ($extension == jpeg))) {
			$response = "<b>The attached file is not a jpeg!</b>";
        } else {
			if($newname){
				 $filename = $newname; 
			}
            	$smallimage = "$thumbdir"."/"."$filename"."$addition"."."."$newext";
				$useimage = "$usedir"."/"."$filename"."$addition"."."."$newext";
                $original = "$picdir"."/"."$filename"."."."$newext";

			if(file_exists($original)) {
				$response = "<b>A file with this name already exists  on the server</b>";
			} else {
				if (move_uploaded_file($_FILES['file']['tmp_name'], $original)) {  
					$response = "<b>File is valid, and was successfully uploaded.</b>"; 
				} else { 
					$response = "<b>File uploaded failed.</b>";
				}
				if (!copy($original, $useimage)) {
  					echo "<b>failed to copy $useimage...\n</b>";
				}
				if (!copy($original, $smallimage)) {
  					echo "<b>failed to copy $smallimage...\n</b>";
				}
				chmod($smallimage,0755);
				chmod($useimage,0755);
				chmod($original,0755);
				if(file_exists($smallimage)) {
                	$image = imagecreatefromjpeg("$smallimage");
                    $ywert=imagesy($image);
					$xwert=imagesx($image);
					if($xwert > $ywert){
						$verh = $xwert / $ywert;
						$newwidth = $thumbwidth;
						$newheight = $newwidth / $verh;
					} else  {
						$verh = $ywert / $xwert;
                        $newwidth = $thumbwidth;
                        $newheight= $newwidth * $verh;
                   	}
					if ($gd_version >= 2.0) {
            			$destimage = ImageCreateTrueColor($newwidth,$newheight);
                        ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					} else {
						$destimage = ImageCreate($newwidth,$newheight);
                        ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					}
                    imagejpeg($destimage,$smallimage);
				}
				if(file_exists($useimage)) {
					$image = imagecreatefromjpeg("$useimage");
                    $ywert=imagesy($image);
                    $xwert=imagesx($image);
					if($xwert > $ywert) {
						$verh = $xwert / $ywert;
                        $newwidth = $wwidth;
                        $newheight = $newwidth / $verh;
					} else  {
                        $verh = $ywert / $xwert;
                        $newwidth = $lwidth;
                        $newheight= $newwidth * $verh;
               		}
					if ($gd_version >= 2.0) {
           				$destimage = ImageCreateTrueColor($newwidth,$newheight);
                    	ImageCopyResampled($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					} else {
           				$destimage = ImageCreate($newwidth,$newheight);
                        ImageCopyResized($destimage, $image, 0,   0,   0,   0, $newwidth, $newheight,$xwert,$ywert); 
					}
                    imagejpeg($destimage,$useimage);
				}
  			}
		}
	}
	
	if (isset($original)) {
		$response .= '>hr><table>';
        $response .= '<tr><td>Thumbnail:<td><td>'.$smallimage.'</td><td><img src="../img/thumb/'.$filename.$addition.".".$newext."\"></td></tr>";
		$response .= '<tr><td>Optimized:<td><td>'.$useimage.'</td><td><img src="../img/pic/'.$filename.$addition.".".$newext."\"></td></tr>";
		$response .= '<tr><td>Original:<td><td>'.$original.'</td><td><img src="../img/original/'.$filename.$addition.".".$newext."\"></td></tr>";
		$response .= '</table><hr><br>';
	}
	if (!$hide_display) {
		echo $response;
	}
	$image =$filename.$addition.".".$newext;
	return $image;
}


?>