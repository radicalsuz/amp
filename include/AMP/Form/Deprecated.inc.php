<?php

class BuildForm {
    var $first_copy_btn=true;

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
    
	function copy_btn($namefield, $formname, $idfield='MM_recordId') {
		if (empty($_REQUEST["id"])== TRUE) return "";
		$html = '<input type="button" name= "MM_copy" value="Save As ..." onclick="save_getName()"> ';
        if ($this->first_copy_btn) {
            $html .= '<input type="hidden" name="MM_insert" value = "">';
            $html .= '<script type="text/javascript"> 
                function save_getName() {
                    pform = document.forms["'.$formname.'"];
                    copyname = prompt ("What would you like to name this new item?");
                    
                    if (copyname != "" && copyname) {
                        pform.elements["MM_insert"].value=\'MM_insert\';
                        pform.elements["'.$namefield.'"].value=copyname;
                        pform.elements["'.$idfield.'"].value=null;
                        pform.submit();
                    } else {
                        pform.elements["MM_insert"].value=0;
                        return false;
                    }
                }
            </script>';
            $this->first_copy_btn=false;
        } 
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
			$html = '
			<tr valign="top"><td colspan="2" class="'. $class .'">'. $header .'</td></tr>';
		} else {
			$html .= '
			<tr valign="top"'. $class .'><td colspan="2"><br /><strong>'. $header .'</strong></td></tr>';
		}
		return $html;	
	}
	
	function add_content($content) {
		$html = '
		<tr valign="top"><td colspan="2" class="form">'. $content .'</td></tr>';
		return $html;
	}
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
		$area = '<textarea name="%s" cols="%d" rows="%d" class="%s" id="%s">%s</textarea>';
		return sprintf($area, $this->name, $this->cols, $this->rows, $this->class, $this->name, $this->contents);
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
	function Select($name, $options, $selected=null, $multiple=false, $size=1, $special=null, $class=null, $attr=null) {
		$this->name     = $name;
		$this->options  = $options;
		$this->selected = $selected;
		$this->multiple = $multiple ? 'multiple' : null;
		$this->size     = $size;
		$this->special  = $special;
		$this->class    = $class;
        $this->attr     = $attr;
	}

	/**
	 * Return the completed select box.
	 */
	function fetch() {
		$box = '<select name="%s" size="%s" class="%s" %s %s>%s</select>';
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

		return sprintf($box, $this->name, $this->size, $this->class, $this->multiple, $this->attr, $buf);
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

    var $contents;

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

if ( !function_exists( 'DateOut' ) ) {

    function DateOut($date) {
        if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
            $date = "$regs[2]-$regs[3]-$regs[1]";
        }
		return $date;
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

function add_view_row($label,$value=NULL,$db=NULL) {
	global $buildform,$R;
	if (!$db) {$db = $R;}
	if (($db) && ($value == NULL)) { 
		$value= $db->Fields($label) ;
	} 
	$output = $buildform->add_row($label, $value);
	return $output; 
}

function addfield($name,$label=NULL,$fieldtype='text',$value=NULL,$defualt=NULL,$size=45,$height=4,$right_text=NULL) {
	global $buildform,$R;
	if ($label == NULL) { $label = $name; }
	if (($R) &&  $value == NULL ) { $value  = $R->Fields($name); }
	
	$label = str_replace('_',' ',$label); 
	$label = ucwords($label);
	
	if ((!isset($_GET['id']) || !$_GET['id']) && (!$value)) {$value = $defualt;}
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
?>
