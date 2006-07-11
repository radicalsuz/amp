<?php

require_once( 'utility.base.functions.inc.php');

if (!function_exists( 'array_intersect_key' ) ) {

    function array_intersect_key() {

        $numArgs = func_num_args();

        if (2 <= $numArgs) {

            $arrays =& func_get_args();

            for ($idx = 0; $idx < $numArgs; $idx++) {
                if (! is_array($arrays[$idx])) {
                    trigger_error('Parameter ' . ($idx+1) . ' is not an array', E_USER_ERROR);
                    return false;
                }
            }

            foreach ($arrays[0] as $key => $val) {
                for ($idx = 1; $idx < $numArgs; $idx++) {
                    if (! array_key_exists($key, $arrays[$idx])) {
                        unset($arrays[0][$key]);
                    }
                }
            }

            return $arrays[0];
        }

        trigger_error('Not enough parameters; two arrays expected', E_USER_ERROR);
        return false;
    }

}

if ( !function_exists( 'makesmall' ) ) {
	function makesmall($text,$maxTextLenght=9000) {
		$aspace=" ";
		if(strlen($text) > $maxTextLenght ) {
			$text = substr(trim($text),0,$maxTextLenght); 
			$text = substr($text,0,strlen($text)-strpos(strrev($text),$aspace));
			$text = $text.'...';
		  }
		return $text;
	}
}

if ( !function_exists( 'ampredirect' ) ) {

    function ampredirect($url) {
        $target_url = $url;
        if ( isset( $_REQUEST[ 'pageredirect' ] ) && $_REQUEST['pageredirect'] ) {
            $target_url = $_REQUEST['pageredirect'];
        }
        if ( !defined( 'AMP_CONTENT_PAGE_REDIRECT'))  define( 'AMP_CONTENT_PAGE_REDIRECT', $target_url );
        header("Location: $target_url");
    }

}

/* this function is causing trouble by conflicting with PHPList /
/* it is no longer permitted in AMP 3.5.3 Bugfix 5 
if ( !function_exists( 'redirect' ) ) {

    function redirect($url) {
        ampredirect($url);
    }

}
*/

if ( !function_exists( 'DoDateTime' ) ) {

    //Date functions
    function DoDateTime($theObject, $NamedFormat) {
        if (!isset($theObject))  return '';

        ereg("([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $theObject, $tgRegs);
        $parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1])); 

        if ($parsedDate == "12/31/69") return '';
        return $parsedDate;
    }

}

if ( !function_exists( 'DoTimeStamp' ) ) {

    function DoTimeStamp($theObject, $NamedFormat) {

        if (!$theObject) {
            $parsedDate = '';
        } else {
            ereg("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})", $theObject, $tgRegs);
            $parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1]));
        }

        if ($parsedDate == "12/31/69") $parsedDate = null;

        return $parsedDate;

    }

}

if ( !function_exists( 'DoDate' ) ) {

    function DoDate($theObject, $NamedFormat) {

        if (!$theObject || $theObject == '0000-00-00') {
            $parsedDate = '';
        } else {
            ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $theObject, $tgRegs);
            if (($NamedFormat == "F, Y") && ($tgRegs[2] == "00")) {
                $NamedFormat = "Y";
                $parsedDate = $tgRegs[1];
            } else {
                $parsedDate=date($NamedFormat, mktime(0,0,0,$tgRegs[2],$tgRegs[3],$tgRegs[1])); 
            }
        }

        if ($parsedDate == "12/31/69") $parsedDate = null;

        return $parsedDate;
    }
}


if ( !function_exists( 'DateConvertIn' ) ) {

    function DateConvertIn($date) {

    	$date = preg_replace("/\//", "-", $date);

        if (ereg ("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $date, $regs) || !$date) {
            $date = "$regs[3]-$regs[1]-$regs[2]";
        } else {
            die( "Invalid date format: $date" );
        }
	
        return $date;
    }
}

if ( !function_exists( 'DateConvertOut' ) ) {

    function DateConvertOut($date) {
    	$date = preg_replace("/\//", "-", $date);
        if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
            echo "$regs[2]-$regs[3]-$regs[1]";
        }
    }
}

if ( !function_exists( 'converttext' ) ) {

    function converttext($text) {

        $text = ereg_replace("(([a-zA-Z0-9_\.-]+)(\@)[a-z0-9_-]+([\.][a-z0-9_-]+)+)", "<a href=\"mailto:\\0\">\\0</a>", $text);
        $text = ereg_replace(" [[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" target=_offsite>\\0</a>", $text); 
        $text = nl2br($text);
        return $text;

    }
}

if ( !function_exists( 'hotword' ) ) {
        
    function hotword($text) {

        global $dbcon;
        $getwordlist = $dbcon->CacheExecute("SELECT word, url FROM hotwords WHERE publish=1 ")
                            or die( $dbcon->ErrorMsg() ); 

        while (!$getwordlist->EOF) {
            $word = " " . $getwordlist->Fields("word") . " ";
            $url = $getwordlist->Fields("url");
            $text = ereg_replace("$word", " <a href=\"$url\">" . $getwordlist->Fields("word") . "</a> ", $text);
            $getwordlist->MoveNext();
        }

        return $text;
    }
}
	   
if ( !function_exists( 'statelist' ) ) {

    function statelist($selectname) {

        global $dbcon;

        echo "<select name=\"$selectname\">";
        $state = $dbcon->CacheExecute("SELECT * FROM states")
                    or die("Couldn't find state list: " . $dbcon->ErrorMsg());
        $state_numRows=0;
        $state__totalRows=$state->RecordCount();

        echo  "<option value=\"\">Select State</option>";

        if ($state__totalRows > 0) {

            $state__index=0;
            $state->MoveFirst();

            while ($state__index < $state__totalRows) {

                echo "<option value=\"".$state->Fields("id")."\">".$state->Fields("statename")."</option>";

                $state->MoveNext();
                $state__index++;
            }

            $state__index=0;  
            $state->MoveFirst();
        }

        echo "</select>";
        $state->Close();
    }
}
if ( !function_exists( 'AMP_buildSelect' )) {

    function AMP_buildSelect( $name, $values, $selected = null, $attr = false ) {
        return '<select name="'. $name . "\"$attr>\n".
                AMP_buildSelectOptions( $values, $selected). "\n</select>";
    }
}
if (!function_exists( 'AMP_buildSelectOptions' )) {
    function AMP_buildSelectOptions( $values, $selected=null ) {
        $option_set = array();
        foreach ($values as $value => $text ) {
            $selected_flag = "";
            if (isset($selected) && $selected == $value ) $selected_flag = " selected";
            $option_set[] = "<option value=\"$value\"$selected_flag>$text</option>";
        }
        return join( "\n", $option_set );
    }
}

if ( !function_exists( 'sectionimage' ) ) {
        
    function sectionimage($string) {

        global $dbcon, $MM_type; 
        $sectionimg = $dbcon->CacheExecute("Select flash from articletype where id=$MM_type")
                        or die( "Couldn't find section image: " . $dbcon->ErrorMsg() );

        if ($sectionimg->Fields("flash") != null) {
            echo $sectionimg->Fields("flash");
        } else { 
            echo $string;
        }

    }
}

if ( !function_exists( 'evalhtml' ) ) {
    
    function evalhtml($string){

        global $dbcon, $MM_type, $MM_parent, $MM_typename, $HTTP_GET_VARS, $list, $id, $MM_issue, $MM_region, $navalign;

        $start = 0;

        /* Loop through to find the php code in html...  */
        $pos = strpos( $string, '<?php', $start ) ; 
        while ( !($pos === FALSE)) { 

            /* Find the end of the php code.. */
            $pos2 = strpos( $string, "?>", $pos + 5);

            /* Eval outputs directly to the buffer. Catch / Clean it */ 
            ob_start();
            eval( substr( $string, $pos + 5, $pos2 - $pos - 5) );
            $value = ob_get_contents();
            ob_end_clean();

            /* Grab that chunk!  */
            $start = $pos + strlen($value);
            $string = substr( $string, 0, $pos ) . $value . substr( $string, $pos2 + 2);
            $pos = strpos( $string, '<?php', $start ) ; 
        }

        return $string;
    }
}

if ( !function_exists( 'eval_includes' ) ) {
    //evaluates php include files contained within the given text
    function eval_includes ($text, $basedir=null) {
        $pos = strpos ( $text, '<?php');
        if ($pos!==FALSE) {
			$endpos = 0;
	
			$result = substr($text, 0, $pos);
			while (!($pos===FALSE)) {
	
				//find the end of the block
				$endpos = strpos($text, '?>', $pos);
				if ($endpos === FALSE) return $result;
				$code = substr($text, $pos+5, $endpos);
	
				//Get the include
				$include_start = strpos($code, 'include')+7;
				$include_start = strpos($code, '"', $include_start)+1;
				$include_stop = strpos($code, '"', $include_start+1);
	
				$include_args = substr($code, $include_start, $include_stop-$include_start);
				#$include_args = preg_replace("/.*include\s*[\(\s*]?\s*\"?([^\)\"\s]*)\"?[\)\s*]?.*/", "\$1", $code );
				$incl = trim(str_replace('"','',$include_args));
	
				//catch the include
				ob_start();
				if (file_exists_incpath($incl)) {
					include($incl);
				} elseif (isset($basedir)) {
					$newfile = $basedir.$incl;
					if (file_exists_incpath($newfile)) include($newfile);
				}
			
				
				$value = ob_get_contents();
				ob_end_clean();
				$result .= $value;
				$pos = strpos( $text, '<?php', $endpos);
				
				//add the last chunk to the result
				if ($pos === FALSE) {
					$result .= substr($text, $endpos+2);
				} else {
					$result .= substr($text, $endpos+2, $pos);
				}
	
			}
		}
		 
		$pos = strpos ( $text, '{{');
        if ($pos===FALSE) return $text;
        $endpos = 0;

        $result = substr($text, 0, $pos);
        while (!($pos===FALSE)) {

            //find the end of the block
            $endpos = strpos($text, '}}', $pos);
            if ($endpos === FALSE) return $result;
            $code = substr($text, $pos+2, $endpos-($pos+2));

            $incl = trim(str_replace('"','',$code));

            //catch the include
			ob_start();
			
            if (file_exists_incpath($incl)) {
                include($incl);
            } elseif (isset($basedir)) {
                $newfile = $basedir.$incl;
                if (file_exists_incpath($newfile)) include($newfile);
            }
        
			
            $value = ob_get_contents();
            ob_end_clean();
            $result .= $value;
            $pos = strpos( $text, '{{', $endpos);
            
            //add the last chunk to the result
            if ($pos === FALSE) {
                $result .= substr($text, $endpos+2);
            } else {
                $result .= substr($text, $endpos+2, $pos);
            }

        }
        return $result;
    }
}
            



            
if ( !function_exists( 'email_is_valid' ) ) {
	
    function email_is_valid($email) {
#	return ereg("[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+(.[a-zA-Z0-9-]+)", $email);

        $valid_email = eregi(
            "^" .                               // start of line
            "[_a-z0-9]+([_\\.-][_a-z0-9]+)*" .    // user
            "@" .                               // @
            "([a-z0-9]+([\.-][a-z0-9]+)*)+" .   // domain
            "\\.[a-z]{2,}" .                    // sld, tld
            "$",                                // end of line
            $email
        );

        return $valid_email;
    }			
}

if ( !function_exists('ob_get_clean') ) {
   function ob_get_clean() {
       $ob_contents = ob_get_contents();
       ob_end_clean();
       return $ob_contents;
   }
}


if (!function_exists('randomid')) {
function randomid() {

	$random_id_length = 10;
	$rnd_id = crypt(uniqid(rand(),1));
	$rnd_id = strip_tags(stripslashes($rnd_id));
	$rnd_id = str_replace(".","",$rnd_id);
	$rnd_id = strrev(str_replace("/","",$rnd_id));
	$rnd_id = substr($rnd_id,0,$random_id_length);
	return $rnd_id;

}
}

if (!function_exists('pagination')) {
function pagination($count,$offset,$limit) {
	
	$total = ($offset +$limit);
	if ($total > $count) {$total = $count ;}
	echo "Displaying ".($offset +1)."-".$total." of ".$count."  <b>".$q."</b> <br>";
	$pages = ceil(($count/$limit));
	if ($pages > 1) {
		$i = 0;
		$io =0;
		echo "<b>Pages:</b>&nbsp;";
		$pos =strpos($_SERVER['QUERY_STRING'],'offset');

		if ($pos) {
            $qs = substr($_SERVER['QUERY_STRING'],$pos);
			$qs= str_replace($qs,"", $_SERVER['QUERY_STRING']);
        } else {
            $qs = $_SERVER['QUERY_STRING'];
        }

		if ($_SERVER['QUERY_STRING']) {
            $qs= "?".$qs."&";
        } else {
            $qs="?";
        }

		while ($i != $pages) {

			if ($io == $offset) {
                echo "<strong>";
            }

			print '<a  href="' . $_SERVER['PHP_SELF'] . $qs . "offset=$io" . '">' .
			      ($i +1) . '</a> ';

			if ($io == $offset) echo "</strong>";

			$io = ($io+$limit);
			$i++;
		}

		echo '<br/><br/>'; 
	}
}
}


if (!function_exists('setBrowser')) {
function setBrowser() {
    if ( defined( 'AMP_SYSTEM_USER_BROWSER_TYPE' )) return AMP_SYSTEM_USER_BROWSER_TYPE;

    //global $browser_ie, $browser_win, $browser_mo, $browser_checked;
    $browser_ie =  strstr(getenv('HTTP_USER_AGENT'), 'MSIE') ;
    $browser_win =  strstr(getenv('HTTP_USER_AGENT'), 'Win') ;
    if (!strstr(getenv('HTTP_USER_AGENT'), 'Safari')){
        $browser_mo =  strstr(getenv('HTTP_USER_AGENT'), 'Mozilla/5') ;
    }
    if (strstr(getenv('HTTP_USER_AGENT'), '2002')){
        $browser_mo =  false;
    }
    $browser_value = false;
    if ( $browser_mo  ) $browser_value = "mozilla";
    if ( $browser_win ) $browser_valu  = "win";
    if ( $browser_ie  ) $browser_value = "ie";
    if ( $browser_ie && $browser_win ) $browser_value =  "win/ie";
    define( 'AMP_SYSTEM_USER_BROWSER_TYPE', $browser_value );
    return AMP_SYSTEM_USER_BROWSER_TYPE;

    //    return  $browser_ie?"ie":$browser_win?"win":$browser_mo?"mozilla":false;
    //$browser_checked = true;
    

}
}

if (!function_exists('getBrowser')) {
function getBrowser() {
    if ( defined( 'AMP_SYSTEM_USER_BROWSER_TYPE' )) return AMP_SYSTEM_USER_BROWSER_TYPE;
    return setBrowser( );
    global $browser_ie, $browser_win, $browser_mo, $browser_checked;
    /*
    if ($browser_checked) {
        if ($browser_ie&&$browser_win) return "win/ie";
        return  $browser_ie?"ie":$browser_win?"win":$browser_mo?"mozilla":false;
    } else {
        return setBrowser();
    }
    */
}
}
  
if (!function_exists('array_combine_key')) {
    function array_combine_key(&$arr1, &$arr2) {
        if (!is_array($arr1) || !is_array($arr2)) return false;
        $result = array();
        foreach ($arr1 as $key => $value) {
            if (isset($arr2[$value])) $result[$value]=$arr2[$value];
        }
        return $result;
    }
}
if (!function_exists('AMPfile_list')) {
		function AMPfile_list($file,$ext=NULL) { 
            $dir_name= AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.$file;  
            $dir = opendir($dir_name);
            $basename = basename($dir_name);
            $fileArr = array();
            $fileArr[''] = 'Select';
            while ($file_name = readdir($dir)) {
                if ( is_dir( $dir_name . DIRECTORY_SEPARATOR . $file_name )) continue; 
                
                if ( isset( $ext) && $ext ) {
                    $file_ext = false;
                    if ($dotspot = strrpos( $file_name, "." )) {
                        $file_ext = strtolower( substr( $file_name, $dotspot+1) );
                    }
                    if ( !$file_ext || $ext != $file_ext ) continue;
                }
                $fileArr[$file_name] = $file_name;
            }	
            uksort($fileArr, "strnatcasecmp");
				return $fileArr;
		} 
}

if (!function_exists('AMPbacktrace')) {

    function AMPbacktrace() {
        $output = "<div style='text-align: left; font-family: monospace;'>\n";
        $output .= "<b>Backtrace:</b><br />\n";
        $backtrace = debug_backtrace();

        foreach ($backtrace as $bt) {
            $args = '';
            if (isset($bt['args'])) {;
                foreach ($bt['args'] as $a) {
                    if (!empty($args)) {
                        $args .= ', ';
                    }
                    switch (gettype($a)) {
                        case 'integer':
                        case 'double':
                            $args .= $a;
                            break;
                        case 'string':
                            $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
                            $args .= "\"$a\"";
                            break;
                        case 'array':
                            $args .= 'Array('.count($a).')';
                            break;
                        case 'object':
                            $args .= 'Object('.get_class($a).')';
                            break;
                        case 'resource':
                            $args .= 'Resource('.strstr($a, '#').')';
                            break;
                        case 'boolean':
                        $args .= $a ? 'True' : 'False';
                            break;
                        case 'NULL':
                        $args .= 'Null';
                            break;
                        default:
                        $args .= 'Unknown';
                    }
                }
            }
            $output .= "<br />\n";
            $local_line = isset( $bt['line'] )? $bt['line']: false;
            $local_file = isset( $bt['file'] )? $bt['file']: false;
            $local_class = isset( $bt['class'] )? $bt['class']: false;
            $local_type = isset( $bt['type'] )? $bt['type']: false;
            $local_func = isset( $bt['function'] )? $bt['function']: false;
            $output .= "<b>file:</b> {$local_line} - {$local_file}<br />\n";
            $output .= "<b>call:</b> {$local_class}{$local_type}{$local_func}($args)<br />\n";
        }
        $output .= "</div>\n";
        return $output;
    }
}

if (!function_exists( 'lowerlimitInsertID' )) {
    function lowerlimitInsertID( $table, $num ) {
		global $dbcon;
		$getid=$dbcon->Execute( "SELECT id FROM $table ORDER BY id DESC LIMIT 1") or die($dbcon->ErrorMsg());
		if ($getid->Fields("id") < $num) { $id = $num; } else { $id = NULL;} 
		return $id;
	}
}

if (!function_exists( 'AMP_URL_Values' ) ) {
    function AMP_URL_Values() {
        if( !($url_criteria_set = AMP_URL_Read())) return false;
        $url_criteria = array();

        foreach($url_criteria_set as $ukey=>$uvalue) {
            $valueset = $ukey."=".$uvalue;
            if (is_array($uvalue)) $valueset = urlencode_array( $uvalue, $ukey );
            $url_criteria[$ukey] = $valueset;
        }

        return $url_criteria;
    }
}

if (!function_exists( 'AMP_URL_Read' )) {

    function AMP_URL_Read() {
        parse_str($_SERVER['QUERY_STRING'], $url_criteria_set );
        if (empty($url_criteria_set)) return false;
        return $url_criteria_set;
    }
}

if (!function_exists( 'urlencode_array' )) {

    function urlencode_array(
        $var,                // the array value
        $varName,            // variable name to be used in the query string
        $separator = '&'    // what separating character to use in the query string
        ) {
        $toImplode = array();
        foreach ($var as $key => $value) {
            if (is_array($value)) {
                $toImplode[] = urlencode_array($value, "{$varName}[{$key}]", $separator);
            } else {
                $toImplode[] = "{$varName}[{$key}]=".urlencode($value);
            }
        }
        return implode($separator, $toImplode);
    }
}

if (!function_exists('PHP_SELF_QUERY')) {
    function PHP_SELF_QUERY() {
        if (!( isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'])) return $_SERVER['PHP_SELF'];
        return $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];

    }
}

if (!function_exists('AMP_DebugSQL')) {
    function AMP_DebugSQL( $sql, $source_object ) {
        print $source_object . ":<BR>\n". $sql . "<P>";
        trigger_error( $source_object . " debug: ". $sql );
    }
}

if (!function_exists('filterConstants')) {
	function filterConstants( $prefix ) {
		$constant_set = get_defined_constants();
		$result_set = array();
		$local_prefix = $prefix . ((substr($prefix, -1) != '_') ? '_': '');
		
		foreach ( $constant_set as $name => $value ) {
			if ( strpos( $name, $local_prefix )!==0 ) continue;
            if (!isset( $value )) continue;

			$desc = substr( $name, strlen( $local_prefix ) );
			$result_set[ $desc ] = $value;
		}

		return $result_set;
	}
}

if (!function_exists( 'AMP_PastParticiple' )) {
    function AMP_PastParticiple( $word ) {
        if (substr($word, -1) == "y" ) return substr( $word, 0, strlen( $word) -1 ) ."ied";
        if (substr($word, -1) != "e" ) return $word ."ed";
        return $word."d";
    }
}
if (!function_exists( 'AMP_Pluralize' )) {
    function AMP_Pluralize( $word ) {
        $term_end = substr( $word, -1 );
        // ending in Z
        if ("z" == $term_end ){
            $term_end = substr( $word, -2 );
            if ("zz" == $term_end ) return $word . 'es';
            return $word . 'zes';
        }
        // ending in Y
        if ("y" == $term_end ) return substr( $word, 0, strlen( $word )-1 ). "ies" ;
        // ending in Default 
        if ($term_end != "s" ) return $word ."s";
        else return $word .'es';
        return $word;
    }
}


if (!function_exists ('AMP_varDump' )) {
    function AMP_varDump ( &$var ) {
        print '<pre>';
        var_dump( $var );
        print '</pre>';
    }
}
if (!function_exists ('AMP_jsAlert' )) {
    function AMP_jsAlert( $alert ) {
        print '<script type="text/javascript">';
        print ' alert ( "'.$alert.'");';
        print '</script>';
    }
}

if (!function_exists('AMP_Url_AddVars')) {
    function AMP_Url_AddVars ( $current_value, $new_vars ) {
        if (!is_array( $new_vars)) $new_vars = array( $new_vars );
        if (strpos( $current_value, '?') === FALSE )  return $current_value . '?' . implode( '&', $new_vars );

        return $current_value . '&' . implode( '&', $new_vars );
    }
}
if (!function_exists('AMP_Url_AddAnchor')) {
    function AMP_Url_AddAnchor ( $current_value, $anchor_name) {
        return $current_value . '#' . $anchor_name;
    }
}

if (!function_exists('AMP_makeMergeFields')) {
    function AMP_makeMergeFields( $fieldnames ) {
        if (!is_array( $fieldnames)) $fieldnames = array( $fieldnames );
        $result= array();
        foreach ($fieldnames as $fieldname ) {
            $result[] = '%' . $fieldname . '%';
        }
        return $result;
    }
}

if (!function_exists('AMP_trimText')) {
    function AMP_trimText( $text, $max_length, $preserve_tags=true ) {
        $trimmed = ($preserve_tags ? $text : strip_tags( $text ));
        if (! (strlen( $trimmed ) > $max_length) ) return $trimmed; 

        $end_item = " ...";
        $trimmed = substr( trim($trimmed), 0, $max_length );
        if ( !($pos = strrpos( $trimmed, " " ))) return $trimmed . $end_item;

        return substr( $trimmed, 0, $pos ) . $end_item;
    }
}

if (!function_exists( 'AMP_getCachedSiteItem' )) {
    function &AMP_getCachedSiteItem( $item_key ) {
        if (!( $memcache = &AMPSystem_Memcache::instance() )) return false;
        return $memcache->getSiteItem( $item_key );
    }
}

function &AMP_cache_get( $key ){
    $cache = &AMP_get_cache( );
    return $cache->retrieve( $key );
}

function &AMP_cache_delete( $key ){
    $cache = &AMP_get_cache( );
    return $cache->delete( $key );
}

if (!function_exists( 'AMP_cacheSiteItem' )) {
    function AMP_cacheSiteItem( $item_key, $item ) {
        if (!( $memcache = &AMPSystem_Memcache::instance() )) return false;
        return $memcache->setSiteItem( $item_key, $item );
    }
}

if (!function_exists( 'AMP_getCachedPageItem' )) {
    function &AMP_getCachedPageItem( $item_key ) {
        if (!( $memcache = &AMPSystem_Memcache::instance() )) return false;
        return $memcache->getPageItem( $item_key );
    }
}

if (!function_exists( 'AMP_cachePageItem' )) {
    function AMP_cachePageItem( $item_key, $item ) {
        if (!( $memcache = &AMPSystem_Memcache::instance() )) return false;
        return $memcache->setPageItem( $item_key, $item );
    }
}
if (!function_exists( 'AMP_cacheFlush' )) {
    function AMP_cacheFlush() {
        if (!( $memcache = &AMPSystem_Memcache::instance() )) return false;
        return $memcache->flushSite();
    }
}

if (!function_exists( 'AMP_removeExtension' )) {
    function AMP_removeExtension( $filename ) {
        if (!($dotpoint = strrpos( $filename, "." ) )) return $filename;
        return substr( $filename, 0, $dotpoint);
    }
}

if ( !function_exists( 'mime_content_type')) {
    function mime_content_type($filepath) {
         $f = escapeshellarg($filepath);
         return trim( `file -bi $f` );
   }
}

if ( !function_exists( 'AMP_directDisplay')) {
    function AMP_directDisplay( $html, $display_name = null ) {
        $direct_display = &new AMPDisplay_HTML( );
        $direct_display->setContent( $html );
        $currentPage = &AMPContent_Page::instance( );
        $currentPage->contentManager->addDisplay( $direct_display, $display_name );
    }
}
if ( !function_exists( 'AMP_removeBlankElements')) {
    function AMP_removeBlankElements( $value_array ) {
        if ( empty ( $value_array ) ) return $value_array;
        $results = array();
        foreach( $value_array as $key => $value ) {
            if ( !( trim( $value ))) continue;
            $results[ $key ] = $value;
        }
        return $results;
    }
}

if (  !function_exists(  'AMP_clearSpecialChars')) {
    function AMP_clearSpecialChars( $text ) {
        $special_chars = array ( '&' => '&amp;' , "'" => '&rsquo;');
        return str_replace( array_keys( $special_chars ), array_values( $special_chars ), $text );
    }
}

if (!function_exists('amp_writecsv')) {
	function amp_writecsv($rows, $delimiter = ',', $enclosure = '"') {
		foreach($rows as $row) {
			$strings = array();	
			foreach($row as $field) {
				$strings[] = $enclosure.str_replace('"','""',$field).$enclosure;
			}
			$lines[] = join($delimiter, $strings);
		}
		return join("\n", $lines);
	}
}

if ( !function_exists( 'AMP_Authorized')) {

    function AMP_Authorized( $id ) {
        static $permissions = false;
        if ( !$permissions ) {
            require_once( 'AMP/System/Permission/Manager.inc.php');
            $permissions = & AMPSystem_PermissionManager::instance();
        }
        return $permissions->authorized ($id);
    }

}
if ( !function_exists( 'AMP_mkdir')) {
    function AMP_mkdir( $new_path, $per_level = 0775 ){
        if ( file_exists( $new_path )) return true;
        $dir_set = split( DIRECTORY_SEPARATOR, $new_path );

        $child_folder = array_pop( $dir_set );
        $parent_path = join( DIRECTORY_SEPARATOR, $dir_set );

        if ( AMP_mkdir( $parent_path )){
            mkdir( $new_path );
            return true;
        }
        return false;
    }
}

if ( !function_exists( 'AMP_hasTable')) {

    function AMP_hasTable( $table_name ){
        static $tablenames = false;
        if ( !$tablenames ) {
            $dbcon = &AMP_Registry::getDbcon( );
            $tablenames = $dbcon->MetaTables( );
        }
        return ( array_search( $table_name, $tablenames ) !== FALSE );
    }
}

if ( !function_exists( 'AMP_getHeader')){
    function &AMP_getHeader( ){
        if ( defined( 'AMP_USERMODE_ADMIN') && AMP_USERMODE_ADMIN ) {
            require_once( 'AMP/System/Header.inc.php');
            return AMPSystem_Header::instance( );
        }
        require_once( 'AMP/Content/Header.inc.php');
        require_once( 'AMP/Content/Page.inc.php');
        return AMPContent_Header::instance( AMPContent_Page::instance( ) );
    }


}

if ( !function_exists( 'AMP_Authenticate')){

    function AMP_Authenticate( $loginType = 'content', $do_login = false ){
        static $auth_status = array( );
        if ( isset( $auth_status[$loginType]) && $auth_status[$loginType] ) return $auth_status[ $loginType ];

        require_once( 'AMP/Auth/Handler.inc.php');
        $AMP_Authen_Handler = &new AMP_Authentication_Handler( AMP_Registry::getDbcon(), $loginType );

        if ( !( $auth_status[ $loginType ] = $AMP_Authen_Handler->is_authenticated() )) {
            if ( $do_login ) $AMP_Authen_Handler->do_login();
        }
        return $auth_status[ $loginType ];
        
    }
}

if ( !function_exists( 'AMP_initBuffer')){
    function &AMP_initBuffer( $header = '', $footer = '', $delimiter = "\n\n") {
        require_once( 'AMP/Content/Buffer.php');
        $buffer = &new AMP_Content_Buffer( );
        if ( $header ) $buffer->set_header( $header );
        if ( $footer ) $buffer->set_footer( $footer );
        if ( $delimiter ) $buffer->set_delimiter( $delimiter );
        return $buffer;
    }

}

if ( !function_exists( 'AMP_getClassAncestors')){
    function AMP_getClassAncestors( $start_class, $check_parent = null ) {
        $classes = array($start_class);
        $check_parent = strtolower($check_parent);
        $current_class = $start_class;

        while( $current_class = get_parent_class( $current_class )) { 
            $classes[] = $current_class; 
            if (isset( $check_parent ) && $check_parent == $current_class ) return true;
        }
        return $classes;
    }
}

if ( !function_exists( 'AMP_evalLookup')){
    function AMP_evalLookup( $lookup_def ){
        if ( is_object( $lookup_def )){
            return $lookup_def->dataset;
        }
        if ( !is_array( $lookup_def )) return AMPSystem_Lookup::instance( $lookup_def );
        if ( isset( $lookup_def['module'])){
            return AMPSystem_Lookup::locate( $lookup_def );
        }
        return array( );
    }
}

if ( ! function_exists( 'AMP_navCountDisplay_Section')) {
    function AMP_navCountDisplay_Section( $section_id ){
        if ( !$section_id ) return false;
        if ( is_array( $section_id )) return false;

        static $renderer = false;
        static $layout_lists = false;
        static $layout_content = false;
        static $navcount_layouts = false;
        if ( !$renderer ) $renderer = &new AMPDisplay_HTML;
        if ( !$layout_lists )
            $layout_lists = &AMPContent_Lookup::instance( 'navLayoutsBySectionList' );
        if ( !$layout_content )
            $layout_content = &AMPContent_Lookup::instance( 'navLayoutsBySection' );
        if ( !$navcount_layouts )
            $navcount_layouts = &AMPContent_Lookup::instance( 'navLayoutLocationCount' );

        $count_lists = false;
        $count_content = false;
        $layout_id_content = $layout_content ? array_search( $section_id, $layout_content ) : false;
        $layout_id_lists = $layout_lists ? array_search( $section_id, $layout_lists ) : false;
        $url_vars_lists   = array( 'action=add', 'section_id_list='.$section_id );
        $url_vars_content = array( 'action=add', 'section_id='.$section_id );

        if ( $layout_id_lists ){
            $count_lists = "( " . $navcount_layouts[ $layout_id_lists   ] . " )";
            $url_vars_lists = array( 'id='.$layout_id_lists );
        }

        if ( $layout_id_content ){
            $count_content = "( " . $navcount_layouts[ $layout_id_content ] . " )";
            $url_vars_content = array( 'id='.$layout_id_content );
        }

        $navlink_lists = 
            $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_NAV_LAYOUT, $url_vars_lists ),
                             AMP_TEXT_LIST_PAGES . $count_lists );
        $navlink_content = 
            $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_NAV_LAYOUT, $url_vars_content ),
                             AMP_TEXT_CONTENT_PAGES . $count_content );

        return  $renderer->in_P( 
                    $navlink_lists 
                    . $renderer->newline( )
                    . $navlink_content
                );
    
    }
}
			
if ( ! function_exists( 'AMP_navCountDisplay_Class')) {
    function AMP_navCountDisplay_Class( $class_id ){
        if ( !$class_id ) return false;
        static $renderer = false;
        static $layout_lists = false;
        if ( !$renderer ) $renderer = &new AMPDisplay_HTML;
        if ( !$layout_lists )
            $layout_lists = &AMPContent_Lookup::instance( 'navLayoutsByClass' );
        if ( !$navcount_layouts )
            $navcount_layouts = &AMPContent_Lookup::instance( 'navLayoutLocationCount' );

        $count_lists = false;
        $url_vars_lists = array( 'action=add', 'class_id='. $class_id);
        $layout_id_lists = $layout_lists ? array_search( $class_id, $layout_lists ) : false;

        if ( $layout_id_lists ) {
            $count_lists = "( " . $navcount_layouts[ $layout_id_lists   ] . " )";
            $url_vars_lists = array( 'id='.$layout_id_lists );
        }

        $navlink_lists = 
            $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_NAV_LAYOUT, $url_vars_lists ),
                             AMP_TEXT_LIST_PAGES . $count_lists );

        return  $renderer->in_P( 
                    $navlink_lists 
                );
    
    }
}

if ( ! function_exists( 'AMP_navCountDisplay_Introtext')) {
    function AMP_navCountDisplay_Introtext( $introtext_id ){
        if ( !$introtext_id ) return false;
        static $renderer = false;
        static $layout_lists = false;
        static $navcount_layouts = false;

        if ( !$renderer ) $renderer = &new AMPDisplay_HTML;
        if ( !$layout_lists )
            $layout_lists = &AMPContent_Lookup::instance( 'navLayoutsByIntrotext' );
        if ( !$navcount_layouts )
            $navcount_layouts = &AMPContent_Lookup::instance( 'navLayoutLocationCount' );

        $count_lists = false;
        $url_vars_lists = array( 'action=add', 'introtext_id='. $introtext_id );
        $layout_id_lists = $layout_lists ? array_search( $introtext_id, $layout_lists ) : false;

        if ( $layout_id_lists ){
            $count_lists = "( " . $navcount_layouts[ $layout_id_lists   ] . " )";
            $url_vars_lists = array( 'id='.$layout_id_lists );
        }

        $navlink_lists = 
            $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_NAV_LAYOUT, $url_vars_lists ),
                             AMP_TEXT_CONTENT_PAGES . $count_lists );

        return  $renderer->in_P( 
                    $navlink_lists 
                );
    
    }
}

if ( !function_exists( 'AMP_openFile')){
    function &AMP_openFile( $filename, $path = null ){
        if ( !isset( $path )) $path = AMP_LOCAL_PATH . '/custom/';
        print $path . $filename; 
        return fopen( $path.$filename, 'a');
    }
}

if ( !function_exists( 'AMP_cleanPhoneNumber')){
    function AMP_cleanPhoneNumber( $number ){
        $remove = array("-","(",")"," ");
        $number = trim(str_replace($remove,'',$number));
        if (substr($number, 0, 1)=='1') {$number=substr($number, 1);}
        $ct = strlen($number);
        if ($ct == 10) {
            return $number;
        }
        return false;
    }
}

if ( !function_exists( 'AMP_get_cache')){
    function &AMP_get_cache( ){
        if ( !AMP_SYSTEM_CACHE ) return false;
        static $cache = false;
        if ( $cache ) return $cache;

        require_once( 'AMP/System/Cache/'.ucfirst( AMP_SYSTEM_CACHE ).'.php');
        $cache_class = 'AMP_System_Cache_' . ucfirst( AMP_SYSTEM_CACHE );
        $cache = call_user_func_array( array( $cache_class, 'instance'), array( ));
        return $cache;
    }
}

if ( !function_exists( 'AMP_validate_url')){
    function AMP_validate_url( $test_url ){
        if( preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'
                   .'((:[0-9]{1,5})?\/.*)?$/i' ,$test_url)) return $test_url;
        return false;
    }
}

if ( !function_exists( 'AMP_verifyDateValue')){
    function AMP_verifyDateValue( $date_value ){
        if ( !$date_value ) return false;
        $null_dates = &AMPConstant_Lookup::instance( 'nullDates');
        if ( array_search( $date_value, $null_dates )!==FALSE) return false;
        return $date_value;
    }
}

if ( !function_exists( 'AMP_publicPagePublishButton')){
    function AMP_publicPagePublishButton( $id, $linkfield ){
        static $renderer = false;
        if ( !$renderer ){
            require_once( 'AMP/Content/Display/HTML.inc.php');
            $renderer = &new AMPDisplay_HTML( );
        }
        return $renderer->link( AMP_Url_AddVars( AMP_SYSTEM_URL_PUBLIC_PAGE_PUBLISH, array( $linkfield . '=' . $id )), ucfirst( AMP_TEXT_PUBLISH ) ) ;
        
        /* using POST is not required , and is kinda messy
        return '<form name="publish_Page_'.$id .'" method="POST" action="/system/module_contentadd.php">'
                . '<input type="hidden" name="'.$linkfield.'" value="'. $id . '">'
                . '<input type="submit" value="Publish" class="searchform_element"></form>';
                */

    }
}

if ( !function_exists( 'AMP_flashMessage')){
    function AMP_flashMessage( $message, $is_error = false ){
        require_once( 'AMP/System/Flash.php');
        $flash = & AMP_System_Flash::instance( );
        if ( $is_error ) return $flash->add_error( $message );
        $flash->add_message( $message );
    }
}

if ( !function_exists( 'AMP_defineLegacyCustomField')){
    function AMP_defineLegacyCustomField( $number ){
        $legacy_field_name = 'AMP_customartfield'. $number;
        $legacy_field_map = array( 
            0   => 'NAME',
            1   => 'LABEL',
            2   => 'TYPE',
            3   => 'DEFAULT'
        );
        if ( !( isset( $GLOBALS[$legacy_field_name]) && $GLOBALS[$legacy_field_name])) return false;
        foreach( $GLOBALS[$legacy_field_name] as $key => $value ){
            if ( !isset( $legacy_field_map[ $key ])) continue;
            $constant_name = 'AMP_CONTENT_ARTICLE_CUSTOMFIELD_' . $legacy_field_map[ $key ] . '_' . $number;
            if ( !defined( $constant_name )) define( $constant_name, $value );
        }
        return true;
    }
}

if ( !function_exists( 'AMP_make_404' )){
    function AMP_make_404( ){
        ampredirect( AMP_CONTENT_URL_404_CORE );
    }
}
			

?>
