<?php

require_once( 'utility.system.functions.inc.php');

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
        //this is a massive security hole AP 2007-06-25
        //if ( isset( $_REQUEST[ 'pageredirect' ] ) && $_REQUEST['pageredirect'] ) {
        //    $target_url = $_REQUEST['pageredirect'];
        //}
        if ( defined( 'AMP_USERMODE_ADMIN' ) && AMP_DISPLAYMODE_DEBUG ) trigger_error ( 'redirect is for ' . $target_url );
        if ( !defined( 'AMP_CONTENT_PAGE_REDIRECT' ))  define( 'AMP_CONTENT_PAGE_REDIRECT', $target_url );
        header("Location: $target_url");
    }

}

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
        $cache_key = false;

        // pull large sets of options from cache
        if ( count( $values ) > 100 ) {
            $basic_text = join( '__K__', array_keys( $values ))
                     . '===>>>'
                     . join ( '__V__', $values )
                     . '__S__'
                     . ( isset( $selected ) ? $selected : '');
            $enc_text = sha1( $basic_text );
            $cache_key = sprintf( AMP_CACHE_TOKEN_COMPONENT, 'options') . $enc_text;
            $result = AMP_cache_get( $cache_key );
            if ( $result ) return $result;
        }
        
        

        foreach ($values as $value => $text ) {
            $selected_flag = "";
            if ( $value ) {
                if (isset($selected) && $selected == $value ) $selected_flag = " selected";
            } else {
                //value is zero-equivalent, test for type
                if (isset($selected) && $selected === $value ) $selected_flag = " selected";
            }
            $option_set[] = "<option value=\"$value\"$selected_flag>$text</option>";
        }
        $result = join( "\n", $option_set );

        //store large sets to cache
        if ( $cache_key ) {
            AMP_cache_set( $cache_key, $result );
        }
        return $result ;
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

if ( !function_exists( 'AMP_get_include_output' ) ) {
    function AMP_get_include_output( $match_value) {
        $filename = $match_value[1];
        if (!file_exists_incpath($filename)) {
            trigger_error( sprintf( AMP_TEXT_ERROR_FILE_EXISTS_NOT, $filename ));
            return false;
        }
		$dbcon = AMP_Registry::getDbcon();	
        ob_start();
        extract( $GLOBALS );
        include($filename);
        $value = ob_get_contents();
        ob_end_clean();
        return $value;
    }
}

if ( !function_exists( 'eval_includes' ) ) {
    //evaluates php include files contained within the given text
    function eval_includes ( $text ) {
        $php_pattern = '/<\?php[^(?:?>)]+include(?:_once)?\(?\s*["\']([^"\']+)["\']\)?\s*;\s*\?>/'; 
        $alternate_pattern = sprintf( '/%1$s\s*([^(?:%2$s)]+)\s*%2$s/', AMP_INCLUDE_START_TAG, AMP_INCLUDE_END_TAG );
        $result = preg_replace_callback( $php_pattern, 'AMP_get_include_output', $text);
        return preg_replace_callback( $alternate_pattern, 'AMP_get_include_output', $result);

        /**
         * long, regex-free way to do this 
         * 
         *
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
        */
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
    $browser_mo = false;
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
    function &array_combine_key( $arr1, &$arr2) {
        $empty_value = false;
        if (!is_array($arr1) || !is_array($arr2)) return $empty_value;
        $result = array();
        foreach ($arr1 as $key => $value) {
            if (isset($arr2[$value])) {
                if( is_object( $arr2[$value]))  {
                    $result[$value] = &$arr2[$value];
                } else {
                    $result[$value] =  $arr2[$value];
                }
            }
        }
        return $result;
    }
}
function array_elements_by_key( $keys, $data ) {
    $super_data = array_fill( 0, count( $keys ), $data );
    $set = array_map( 'array_find_element', $keys, $super_data );
    if ( function_exists( 'array_combine')) {
        $new_data = array_combine( $keys, $set );
    } else {
        $new_data = array( );
        $index = 0;
        while ( $index < count( $set )) {
            $new_data[ $keys[ $index ]] = $set[ $index ];
            ++$index;
        }
    }

    return array_filter( $new_data );
}

function &array_find_element( $key, $data ) {
    $false = false;
    if( !isset( $data[ $key ])) return $false;
    return( $data[$key]);
}

if (!function_exists('AMPfile_list')) {
		function AMPfile_list($file,$ext=NULL) { 
            if (( strpos( $file, AMP_LOCAL_PATH ) === FALSE )
                && ( substr( $file, 0, 1 ) != DIRECTORY_SEPARATOR )) {
                $dir_name= AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.$file;  
            } else {
                $dir_name = $file;
            }

            $dir = opendir($dir_name);
            if ( !$dir ) {
                return false;
            }
            $basename = basename($dir_name);
            $fileArr = array();
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
            //uksort($fileArr, "strnatcasecmp");
            natcasesort( $fileArr );
            $final_list = array( '' => 'Select') + $fileArr;
            return $final_list;
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
		$dbcon = AMP_Registry::getDbcon( );
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

    function AMP_url_read( ) {
        parse_str($_SERVER['QUERY_STRING'], $url_criteria_set );
        if (empty($url_criteria_set)) return false;
        return $url_criteria_set;
    }

if (!function_exists( 'AMP_URL_Read' )) {
    function AMP_URL_Read( ) {
        return AMP_url_read( );
    }

}

if (!function_exists( 'urlencode_array' )) {

    function urlencode_array(
        $var,                // the array value
        $varName,            // variable name to be used in the query string
        $separator = '&amp;'    // what separating character to use in the query string
        ) {
        if (!is_array($var)) return ( $varName . '=' . $var );
        $toImplode = array();

        foreach ($var as $key => $value) {
            if (is_array($value)) {
                $toImplode[] = urlencode_array($value, "{$varName}[{$key}]", $separator);
            } else {
                if ( strip_tags( $value ) != $value ) return false;
                $toImplode[] = "{$varName}[{$key}]=".urlencode($value);
            }
        }
        return implode($separator, $toImplode);
    }
}

if (!function_exists('PHP_SELF_QUERY')) {
    function PHP_SELF_QUERY() {
        if (!( isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'])) {
            $php_self_query = $_SERVER['PHP_SELF'];
        } else {
            $php_self_query = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        }
        //strip extra slashes
        while(0 === strpos($php_self_query, '//')) {
            $php_self_query = substr($php_self_query, 1);
        }
        return $php_self_query;
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
        $double_consonants = array( 'tag');
        if ( array_search( $word, $double_consonants ) !== FALSE ) return $word . substr( $word, strlen( $word )-1 ) . 'ed' ;
        if (substr($word, -1) == "y" ) return substr( $word, 0, strlen( $word) -1 ) ."ied";
        if (substr($word, -1) != "e" ) return $word ."ed";
        return $word."d";
    }
    function AMP_past_participle( $word ) {
        return AMP_PastParticiple( $word );
    }
}
if (!function_exists( 'AMP_pluralize' )) {
    function AMP_pluralize( $word ) {
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
    function AMP_url_add_vars( $current_value, $new_vars ) {
        return AMP_Url_AddVars( $current_value, $new_vars );
    }
}
if (!function_exists( 'AMP_Url_AddAnchor' )) {
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
        //cant do this because of php4
        //$no_tags_version = strip_tags( html_entity_decode( $text, ENT_COMPAT, strtoupper( AMP_SITE_CONTENT_ENCODING ) ));
        $no_tags_version = strip_tags( html_entity_decode( $text ));
        $tag_length = mb_strlen( $text , AMP_SITE_CONTENT_ENCODING ) - mb_strlen( $no_tags_version , AMP_SITE_CONTENT_ENCODING );
        $trimmed = $text;
        if ( !$preserve_tags ) {
            $trimmed = $no_tags_version;
            $tag_length = 0;
        }
        $max_length = $max_length+$tag_length;
        if (! (mb_strlen( $trimmed, AMP_SITE_CONTENT_ENCODING ) > $max_length ) ) return $trimmed; 

        $end_item = " ...";
        $trimmed = mb_substr( trim($trimmed), 0, $max_length, AMP_SITE_CONTENT_ENCODING );
        if ( !($pos = mb_strrpos( $trimmed, " ", AMP_SITE_CONTENT_ENCODING  ))) return $trimmed . $end_item;

        return mb_substr( $trimmed, 0, $pos, AMP_SITE_CONTENT_ENCODING ) . $end_item;
    }
}

function AMP_cache_close( ){
    $cache = &AMP_get_cache( );
    if ( !$cache ) return false;
    return $cache->shutdown( );
}

function &AMP_cache_get( $key, $id = null ){
    $false = false;
    $cache = &AMP_get_cache( );
    if ( !$cache ) return $false;
    if ( isset( $id ) && $id ) $key = $cache->identify( $key, $id );
    return $cache->retrieve( $key );
}

function AMP_cache_delete( $key, $id = null ){
    $cache = &AMP_get_cache( );
    if ( !$cache ) return false;
    if ( isset( $id ) && $id ) $key = $cache->identify( $key, $id );
    return $cache->delete( $key );
}

function AMP_cache_set( $key, &$item, $id = null ){
    $cache = &AMP_get_cache( );
    if ( !$cache ) return false;
    if ( isset( $id ) && $id ) $key = $cache->identify( $key, $id );
    return $cache->add( $item, $key );
}

function AMP_is_cacheable_url( ) {
    $cache = &AMP_get_cache( );
    $flash = &AMP_System_Flash::instance( );
    $registry = &AMP_Registry::instance( );

    return 
        //is this page a valid caching candidate
        (  defined( 'AMP_CONTENT_PAGE_CACHE_ALLOWED' ) 

        //was a form submitted to get here
        && empty( $_POST) 

        // is a redirect scheduled to occur
        && ( ! defined( 'AMP_CONTENT_PAGE_REDIRECT' ))

        //did the flash display a value on this page
        && ( ! defined( 'AMP_SYSTEM_FLASH_OUTPUT')) )

        //is the cache active
        && ( $cache ) 
        
        //does the flash contain messages for the user
        && ( !$flash->active( )) 

        //is the user viewing protected content
        && ( !AMP_Authenticate( 'content' ))
        && ( !$registry->getEntry( AMP_REGISTRY_CONTENT_SECURE ))
        
        ; 

}

function AMP_cached_image_request( ) {
    require_once( 'AMP/System/Cache/File.php');
    $cache_key = AMP_CACHE_TOKEN_IMAGE . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    if ( defined( 'AMP_SYSTEM_USER_ID') && AMP_SYSTEM_USER_ID ) {
        $cache_key = AMP_System_Cache::identify( $cache_key, AMP_SYSTEM_USER_ID );
    }
    $file_cache =  new AMP_System_Cache_File( );
    $file_name = $file_cache->authorize( $cache_key );
    $file_path = $file_cache->path( $file_name );
    if ( !file_exists( $file_path )) {
        return false;
    }

    header( "Content-Type: " . mime_content_type( $file_path ));
    $fRef = fopen( $file_path, 'r');
    fpassthru( $fRef );
    fclose( $fRef );
    return true;

}

function AMP_assert_var( $varname ) {
    if ( !isset( $_REQUEST['varname'])) {
        return false;
    }
    return $_REQUEST['varname'];
}

function AMP_cached_request( $timeout = null  ){
    //signal that the current request is cacheable
    //because it has requested a cached copy of itself
    if ( AMP_DISPLAYMODE_CACHE_OFF ) return false;
    if ( !defined( 'AMP_CONTENT_PAGE_CACHE_ALLOWED')) define( 'AMP_CONTENT_PAGE_CACHE_ALLOWED', true );

    if ( !( $cache = &AMP_get_cache( ) && AMP_is_cacheable_url( )) ) return false; 
    $cache_key = AMP_CACHE_TOKEN_URL_CONTENT . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    if ( defined( 'AMP_SYSTEM_USER_ID') && AMP_SYSTEM_USER_ID ) {
        $cache_key = $cache->identify( $cache_key, AMP_SYSTEM_USER_ID );
    }
    //if ( isset( $timeout ) && ( $cache->age( $cache_key ) > $timeout )) return false;

    return $cache->retrieve( $cache_key ) ;
}

if (!function_exists( 'AMP_cacheFlush' )) {
    function AMP_cacheFlush( $key_token = null ) {
        $cache = &AMP_get_cache( );
        if ( $cache && ( strtolower( get_class( $cache ) == 'AMP_System_Cache_File' ) ) 
             && !is_dir( AMP_pathFlip( AMP_SYSTEM_CACHE_PATH ) )) {
            //don't endlessly repeat file cache clearing
            return;
        }

        if ( isset( $key_token )) {
            $flush_command = "find ". AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . " -name " . $key_token ."\* | xargs rm &"; 
        } else {
            $dbcon = AMP_Registry::getDbcon(  );
            $dbcon->CacheFlush(  );
            $flush_command = "rm -rf ". AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . "* &";
        }
        $command_result = false;
        system( $flush_command, $command_result );

        if ( !isset( $key_token ) || $key_token == AMP_CACHE_TOKEN_ADODB ) {
            //$flush_command = "rm -f `find ". AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'cache' ." -name adodb_*.cache` &"; 
            $flush_command = "find ". AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'cache' ." -name ?? | xargs rm -rf &"; 
            //$flush_command = "find ". AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'cache' ." -name adodb_\*.cache | xargs rm &"; 
            $command_result = false;
            system($flush_command, $command_result );
        }

        if ( $command_result ) {
            //unix systems should return 0 on success, try a DOS filesystem command
            //this function will not flush memcache on windows, probably no one cares
            $flush_command = "rmdir /S /Q " . AMP_pathFlip(  AMP_SYSTEM_CACHE_PATH );
            system($flush_command, $command_result );
            trigger_error( 'Win flush result was ' . $command_result );
            return;
        }

        if ( !$cache )  {
            return false;
        }
        $cache->clear( $key_token );

        $flash = AMP_System_Flash::instance( );
        $flash->restore_cache( );
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
         if( function_exists( 'finfo_open')) {
             $finfo = finfo_open( FILEINFO_MIME );
             $ftype = finfo_file( $finfo, $filepath );
             finfo_close( $finfo );
             return $ftype;
         }
         if( function_exists( 'exif_imagetype') && ( $ftype = exif_imagetype( $filepath ))) {
             return image_type_to_mime_type( $ftype );
         }
         $f = escapeshellarg($filepath);
         return trim( `file -bi $f` );
   }
}

if ( !function_exists( 'AMP_directDisplay')) {
    function AMP_directDisplay( $html, $display_name = null ) {
        $direct_display = AMP_to_buffer( $html );
        $currentPage = &AMPContent_Page::instance( );
        $currentPage->contentManager->addDisplay( $direct_display, $display_name );
    }
}
if ( !function_exists( 'AMP_to_buffer')) {
    function AMP_to_buffer( $content ) {
        require_once( 'AMP/Content/Buffer.php' );
        $direct_display = new AMP_Content_Buffer( );
        $direct_display->add( $content );
        return $direct_display;
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
            $permissions = AMPSystem_PermissionManager::instance();
        }
        return $permissions->authorized ($id);
    }

    function AMP_allow( $action, $item_type, $id, $user_id = null ) {
        if ( $action == 'cancel' ) return true;
        /*
        $action = 'access';

        if ( !$user_id ) $user_id = AMP_SYSTEM_USER_ID;
        $crit_array = array( 'target_id' => $id, 'user_id' => $user_id, 'action' => $action, 'allow' => 1 );
        $target_permission_lookup = AMP_lookup( 'permissionTarget'.ucfirst( $item_type ), $id );

        if ( !$permission_lookup ) return true;
        return ( isset( $permission_lookup[$id]) && $permission_lookup[$id] == $action) ;
        */

        //this code applies only in ACL mode. not currently in use
        $action_translations = array( 
            'edit'      => 'access',
            'publish'   => 'publish',
            'unpublish' => 'publish',
            'move'      => 'save',
            'reorder'   => 'save',
            'list'      => 'access',
            'copy'      => 'create',
            'trash'     => 'delete',
            );

        if ( defined( 'AMP_SYSTEM_PERMISSIONS_LOADING') || !defined( 'AMP_SYSTEM_USER_ID_ACL')) {
            return true;
        }
        if ( !defined( $user_id )) $user_id = AMP_SYSTEM_USER_ID_ACL;
        $gacl = &AMP_acl( );
        if ( isset( $action_translations[ $action ])) {
            $action = $action_translations[ $action ];
        }
        #trigger_error( 'checking ' . $action . ' for ' . AMP_SYSTEM_USER_TYPE . ' #' . $user_id . ' on ' . AMP_pluralize( $item_type ) . ' or ' . $item_type . '_' . $id );

        return $gacl->acl_check( 'commands', $action, AMP_SYSTEM_USER_TYPE, $user_id, AMP_pluralize( $item_type ), $item_type . '_' . $id );
    }

    function &AMP_acl( $api = false ) {
        static $gacl = false;
        static $gacl_api  = false;
        //trigger_error( AMP_SYSTEM_USER_ID );
        if ( ( !$gacl && !$api) || ( !$gacl_api && $api ) ) {
            $gacl_options = array( 
                'smarty_dir' => 'phpgacl/admin/smarty/libs',
                'smarty_template_dir' => 'phpgacl/admin/templates',
                'smarty_compile_dir'  => AMP_SYSTEM_CACHE_PATH,
                'db_type' 		    => AMP_DB_TYPE,
                'db_host'			=> AMP_DB_HOST,
                'db_user'			=> AMP_DB_USER,
                'db_password'		=> AMP_DB_PASS, 
                'db_name'			=> AMP_DB_NAME, 
                'db_table_prefix'   => 'acl_',
            //    'debug' => 1

                );

            if ( $api ) {
                require_once( 'phpgacl/gacl_api.class.php');
                $gacl_api = new gacl_api( $gacl_options );
            } else {
                require_once( 'phpgacl/gacl.class.php');
                $gacl = new gacl( $gacl_options );
            }
            $reg = AMP_Registry::instance( );
            $reg->setEntry( AMP_REGISTRY_PERMISSION_MANAGER, $gacl );
        }

        if ( $api ) {
            return $gacl_api;
        }
        return $gacl;

    }


}
if ( !function_exists( 'AMP_mkdir')) {
    function AMP_mkdir( $new_path, $per_level = 0775 ){
        if ( file_exists( $new_path )) return true;
		$split_pattern = ( DIRECTORY_SEPARATOR == '\\' ? '\\\\' : DIRECTORY_SEPARATOR );
        $dir_set = split( $split_pattern, $new_path );

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

    function &AMP_get_header( ) {
        $header = & AMP_getHeader( );
        return $header;
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
        if ( !is_array( $lookup_def )) return AMP_lookup( $lookup_def );
        if ( isset( $lookup_def['name']) && isset( $lookup_def['var'])) {
            return AMP_lookup( $lookup_def['name'], $lookup_def['var']);
        }
        if ( isset( $lookup_def['instance'])){
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
            //$count_lists = "( " . $navcount_layouts[ $layout_id_lists   ] . " )";
            $count_lists =  $navcount_layouts[ $layout_id_lists ];
            $url_vars_lists = array( 'id='.$layout_id_lists );
        }

        if ( $layout_id_content ){
            //$count_content = "(&nbsp;" . $navcount_layouts[ $layout_id_content ] . "&nbsp;)";
            $count_content = $navcount_layouts[ $layout_id_content ];
            $url_vars_content = array( 'id='.$layout_id_content );
        }

        $navlink_lists = 
            $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_NAV_LAYOUT, $url_vars_lists ),
                             $count_lists 
                             . $renderer->image( '/img/list_page'.( $count_lists?'':'_create').'.png' ),
                             array( 'title' => 
                                sprintf( AMP_TEXT_WHAT_FOR_WHAT, 
                                         ( $count_content ? AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT : AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_CREATE ), 
                                         AMP_TEXT_LIST_PAGES )));
        $navlink_content = 
            $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_NAV_LAYOUT, $url_vars_content ),
                             $count_content 
                             . $renderer->image( '/img/content_page'.( $count_content?'':'_create').'.png' ),
                             array( 'title' => 
                                sprintf( AMP_TEXT_WHAT_FOR_WHAT, 
                                         ( $count_content ? AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT : AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_CREATE ), 
                                         AMP_TEXT_CONTENT_PAGES )));
        return $renderer->div( $navlink_lists . $navlink_content, array( 'class' => 'icon' ));

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
        static $navcount_layouts = false;

        if ( !$renderer ) $renderer = &new AMPDisplay_HTML;
        if ( !$layout_lists )
            $layout_lists = &AMPContent_Lookup::instance( 'navLayoutsByClass' );
        if ( !$navcount_layouts )
            $navcount_layouts = &AMPContent_Lookup::instance( 'navLayoutLocationCount' );

        $count_lists = false;
        $url_vars_lists = array( 'action=add', 'class_id='. $class_id);
        $layout_id_lists = $layout_lists ? array_search( $class_id, $layout_lists ) : false;

        if ( $layout_id_lists ) {
            $count_lists = "(&nbsp;" . $navcount_layouts[ $layout_id_lists   ] . "&nbsp;)";
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
            $count_lists = "(&nbsp;" . $navcount_layouts[ $layout_id_lists   ] . "&nbsp;)";
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
//        trigger_error( 'getting cache');
        $empty_value = false;
        if ( !AMP_SYSTEM_CACHE ) {
            require_once( 'AMP/System/Cache/Cache.php');
            return $empty_value;
        }
//        trigger_error( 'cache is on');
        static $cache = false;
        static $cache_failure = false;
        if ( $cache ) {
            return $cache;
        }
		if ( $cache_failure ) return $empty_value;

        $cache_filename = ( 'AMP/System/Cache/'.ucfirst( AMP_SYSTEM_CACHE ).'.php');
        require_once( 'AMP/System/Cache/'.ucfirst( AMP_SYSTEM_CACHE ).'.php');
        $cache_class = 'AMP_System_Cache_' . ucfirst( AMP_SYSTEM_CACHE );
        $cache = new $cache_class;
        if ( !$cache->has_connection( ) ) {

            if ( $failover = $cache->failover( )) {
                trigger_error('MEMCACHE FAILED, attempting '.$failover.' cacheing for ' . $_SERVER['REQUEST_URI']);
                require_once( 'AMP/System/Cache/'.ucfirst( $failover ).'.php');
                $cache_class = 'AMP_System_Cache_' . ucfirst( $failover );

                $cache = false;
                $cache = new $cache_class;
            }

            if ( !$cache->has_connection( )) {
                trigger_error('cache failure for ' . $_SERVER['REQUEST_URI']);
                $cache = false;
                $cache_failure = true;
            }
        } 

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
        $null_dates = AMP_lookup( 'null_dates');
        if ( isset( $null_dates[ $date_value ])) return false;
        //if ( array_search( $date_value, $null_dates )!==FALSE) return false;
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

function AMP_log_error( $error_number, $error_text, $error_file, $error_line ) {
    $localized_error = sprintf( AMP_TEXT_ERROR_LOG_FORMAT, $error_text, $error_file, $error_line );
    $output_log = &AMP_openFile( 'error_log.txt');
    fwrite( $output_log, $localized_error ); 
    fclose( $output_log );
}

function AMP_verifyDateTimeValue( $date_value ){
    if ( !$date_value ) return false;
    $null_dates = AMP_lookup( 'null_datetimes');
    if ( isset( $null_dates[ $date_value ])) return false;
    return $date_value;
}

function AMP_pathFlip( $path ) {
	if ( DIRECTORY_SEPARATOR != '/' ) {
		return str_replace( '/', DIRECTORY_SEPARATOR, $path );
	}
	return $path;
}

function AMP_urlFlip( $path ) {
	if ( DIRECTORY_SEPARATOR != '/' ) {
		return str_replace( DIRECTORY_SEPARATOR, '/', $path );
	}
	return $path;
}

function AMP_javascript_envelope( $script ) {
    return AMP_HTML_JAVASCRIPT_START . $script . AMP_HTML_JAVASCRIPT_END;
}

if (version_compare(phpversion(), '5.0') < 0) {
    eval( 
    'function clone($object) {
      return $object;
    }');
}

function &AMP_get_renderer( $type = 'HTML'){
    static $renderer = array( );
    if ( isset( $renderer[$type] )) return $renderer[$type];

    $class = 'AMP_Renderer_'.strtoupper( $type);
    $file = str_replace( '_', '/', $class ) . '.php';
    require_once( $file );
    $item = &new $class( );
    $renderer[$type] = $item;
    return $item;

}

function AMP_update_tags( $tag_ids = false, $tag_names = false, $item_id, $item_type ) {
    if ( $tag_names ) {
        require_once( 'AMP/Content/Tag/Tag.php');
        $new_tag_set = AMP_Content_Tag::create_many( $tag_names );
        if ( $tag_ids ) {
            $complete_tags = array_merge( $tag_ids, $new_tag_set );
        } else {
            $complete_tags = $new_tag_set;
        }
        return AMP_update_tags( $complete_tags, false, $item_id, $item_type );
    }

    $tag_lookup = 'tagsBy' . ucfirst( $item_type );
    $existing_tags = AMPSystem_Lookup::instance( $tag_lookup, $item_id );

    if ( ( !$tag_ids || empty( $tag_ids )) && !$existing_tags ) return true;
    if ( !( $existing_tags )) $existing_tags = array( );
    if ( !( $tag_ids )) $tag_ids = array( );

    $existing_tag_ids = array_keys( $existing_tags );

    if ( $existing_tag_ids ) {
        $deleted_items = array_diff( $existing_tag_ids, $tag_ids );
        $new_items = array_diff( $tag_ids, $existing_tag_ids );
    } else {
        $deleted_items = array( );
        $new_items = $tag_ids;
    }

    if ( empty( $deleted_items ) && empty( $new_items )) return false;

    require_once( 'AMP/Content/Tag/Item/Item.php');
    $action_item = &new AMP_Content_Tag_Item( AMP_Registry::getDbcon( ));

    //remove existing tags
    if ( !empty( $deleted_items )) {
        foreach ( $deleted_items as $tag_item_id ) {
            $action_item->deleteByCriteria( array( 'tag_id' => $tag_item_id, 'item' => $item_id, 'itemtype' => $item_type ));
        }
    }

    //add new tag designations
    if ( !empty( $new_items )) {
        $create_values = array( 'item_type' => $item_type, 'item_id' => $item_id, 'user_id' => AMP_SYSTEM_USER_ID );
        foreach( $new_items as $tag_id ) {
            $action_item->dropID( ) ;
            $tag_values = $create_values + array( 'tag_id' => $tag_id );
            $action_item->setData( $tag_values );
            $action_item->save( );
        }
    }

}

function AMP_add_tags( $tag_ids = false, $tag_names = false, $item_id, $item_type ) {
    if ( $tag_names ) {
        require_once( 'AMP/Content/Tag/Tag.php');
        $new_tag_set = AMP_Content_Tag::create_many( $tag_names );
        $new_tag_results = AMP_add_tags( $new_tag_set, false, $item_id, $item_type );
        if ( !$tag_ids ) return $new_tag_results;
    }

    if ( !$tag_ids ) return false;

    if ( !is_array( $tag_ids )) {
        $tag_id_set = array( $tag_ids );
    } else {
        $tag_id_set = $tag_ids;
    }
    $related_tags = AMPSystem_Lookup::instance( 'tagsBy' . ucfirst( $item_type ), $item_id );
    if ( $related_tags ) {
        $new_tags = array_diff( $tag_id_set, array_keys( $related_tags ));
    } else {
        $new_tags = $tag_id_set;
    }

    if ( !$new_tags || empty( $new_tags )) return false;

    require_once( 'AMP/Content/Tag/Item/Item.php');
    $action_item = &new AMP_Content_Tag_Item( AMP_Registry::getDbcon( ));
    $create_values = array( 'item_type' => $item_type, 'item_id' => $item_id, 'user_id' => AMP_SYSTEM_USER_ID );
    $results = 0;
    foreach( $new_tags as $new_tag_id ) {
        $create_values['tag_id'] = $new_tag_id;
        $action_item->dropID( );
        $action_item->setData( $create_values );
        $results += $action_item->save( );
    }
    return $results;

}

function AMP_local_request( ){
    return ( strpos( $_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'] ));
}

function AMP_lookup( $lookup_type, $lookup_var = null ) {
    $lookup_types = array( 'AMPSystem_Lookup' => 'AMPSystemLookup_', 
                            'AMPContent_Lookup' => 'AMPContentLookup_', 
                            'FormLookup' => 'FormLookup_');
    //the whole class name is passed
    if ( class_exists( $lookup_type )) {
        foreach ( $lookup_types as $base_type => $prefix ) {
            if ( strpos( $lookup_type, $prefix ) === 0) {
                $instance = str_replace( $prefix, '', $lookup_type );
                return call_user_func_array( array( $base_type, 'instance'), array( $instance, $lookup_var ));
            }
        }
        return new $lookup_type( $lookup_var );
    }

    //just the instance is passed
    $instance = AMP_to_camelcase( $lookup_type );
    $value = false;
    foreach( $lookup_types as $base_type => $prefix ) {
        if ( !class_exists( $prefix . ucfirst( $instance ))) continue;
        $values = call_user_func_array( array( $base_type, 'instance'), array( $instance, $lookup_var ));
        if ( $values ) return $values;
    }
    if ( !isset( $values )) {
        trigger_error( sprintf( AMP_TEXT_ERROR_LOOKUP_NOT_FOUND, $lookup_type . ' / ' . $instance . ( isset( $lookup_var ) ? ' / ' . $lookup_var :  '')));
        return false;
    }
    return $values;
}

function AMP_lookup_clear_cached( $type, $instance_var = null ) {
    require_once( "AMP/System/Lookups.inc.php");
    $key = AMPSystem_Lookup::cache_key( AMP_to_camelcase( $type), $instance_var );
    AMP_cache_delete( $key );
}

function AMP_permission_update( ) {
    AMP_cacheFlush( AMP_CACHE_TOKEN_ADODB );
    AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );
    require_once( 'AMP/System/Permission/ACL/Controller.php');
    $controller = &new AMP_System_Permission_ACL_Controller( );
    $controller->request( 'update');
    $controller->execute( false );
    AMP_cacheFlush( );
}

function AMP_s3_save( $file_path ) {
    if ( !AMP_SYSTEM_FILE_S3_KEY ) return false; 
    if ( !( $data = file_get_contents( $file_path ))) return false; 

    static $s3_connection = false;   

    $type = mime_content_type($file_path);

    $file_name = basename( $file_path );
    $clean_file = str_replace(" ", "%20",  $file_name );
    $file_path =  str_replace( $file_name, $clean_file, $file_path );
    $object_id  = str_replace( AMP_pathFlip( AMP_LOCAL_PATH . '/' ), '', $file_path );
    $bucket = AMP_SYSTEM_FILE_S3_BUCKET;

    if ( !$s3_connection ) {
        require_once("s3/s3.class.php");
        $s3_connection = &new s3( );
    }

    return $s3_connection->putObject( $object_id, $data, $bucket, NULL, $type );

}

function AMP_s3_delete( $file_path ) {
    if ( !AMP_SYSTEM_FILE_S3_KEY ) return false; 
    if ( !( $data = file_get_contents( $file_path ))) return false; 

    static $s3_connection = false;   

    $file_name = basename( $file_path );
    $clean_file = str_replace(" ", "%20", $file_path );
    $file_path =  str_replace( $file_name, $clean_file, $file_path );
    $object_id  = str_replace( AMP_pathFlip( AMP_LOCAL_PATH . '/' ), '', $file_path );
    $bucket = AMP_SYSTEM_FILE_S3_BUCKET;

    if ( !$s3_connection ) {
        require_once("s3/s3.class.php");
        $s3_connection = &new s3( );
    }

    return $s3->deleteObject( $object_id, $bucket );

}

function AMP_absolute_urls( $html ) {

    $url = AMP_SITE_URL;

    $pattern = '/(href|src|background|action)\s?=\s?["\']((?!http)[]\w\d\.\/?=&[+% -]*)["\']/i';
    $replace = '$1="'.$url.'/$2"';
    $data =  preg_replace($pattern, $replace, $html);
    /*

    $pattern = '/href\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
    $replace = 'href="'.$url.'/$1"';
    $data =  preg_replace($pattern, $replace, $data);

    $pattern = '/src\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
    $replace = 'src="'.$url.'/$1"';
    $data =  preg_replace($pattern, $replace, $data);

    $pattern = '/src\s?=\s?\'((?!http)[\w\d\.\/?=& -]*)\'/i';
    $replace = 'src="'.$url.'/$1"';
    $data =  preg_replace($pattern, $replace, $data);

    $pattern = '/background\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
    $replace = 'background="'.$url.'/$1"';
    $data =  preg_replace($pattern, $replace, $data);

    $pattern = '/action\s?=\s?"((?!http)[\w\d\.\/?=& -]*)"/i';
    $replace = 'action="'.$url.'/$1"';
    $data =  preg_replace($pattern, $replace, $data);
    */

    $pattern = '/,\'\',\'((?!http)[\w\d\.\/?=& -]*)\'/i';
    $replace = ',\'\',\''.$url.'/$1\'';
    $data =  preg_replace($pattern, $replace, $data);

    return $data;

}

function AMP_js_write( $data, $uniq_id = null ) {
    
    $pattern = array( "\r", "\n" );
    $output =  str_replace($pattern, '', $data);

    if ( !isset( $uniq_id )) {
        $uniq_id = str_replace( '.', '_', $_SERVER['SERVER_NAME'] ) . mt_rand( 1000,10000 );
    }

    return 'var '.$uniq_id.'=  { value: \''. str_replace( "'", "\'", $output) . "'};\ndocument.write( ".$uniq_id.".value );";
}

function acl_identify( &$obj ) {
    $object_types = AMP_lookup( 'objects' );
    $designator = strtolower( current( array_keys( $object_types, get_class( $obj ))));
    if ( !$designator ) {
        trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED ), 'ACL', get_class( $obj ));
        return false;
    }
    if ( isset( $obj->id )) {
        return $designator . '_' . $id;
    }
    return $designator;
}

function AMP_base_select_options( $options, $default_text = null ) {
    if ( !isset( $default_text )) $default_text = sprintf( AMP_TEXT_SELECT, AMP_TEXT_ONE );
    if ( !$options || empty( $options )) {
        return array( '' => $default_text );
    }
    return array( '' => $default_text ) + $options;
}

function &AMP_current_user( ) {
    $false = false;
    if ( !defined( 'AMP_SYSTEM_USER_ID' )) {
        return $false;
    }
    static $current_user = false;
    if ( $current_user ) {
        return $current_user;
    }
    require_once( 'AMP/System/User/User.php');
    $current_user = new AMPSystem_User( AMP_Registry::getDbcon( ), AMP_SYSTEM_USER_ID );

    return $current_user;

}

function AMP_mailto( $address ) {
    $renderer = AMP_get_renderer( );
    return $renderer->link( 'mailto:' . $address, $address );
}

/*
function urlencode_array( $field_value, $fieldname ) {
    if (!is_array($field_value)) return ( $fieldname . '=' . $field_value);
    $separator = '&';
    $toImplode = array();
    foreach ($field_value as $key => $value) {
        if (is_array($value)) {
            $toImplode[] = urlencode_array($value, "{$fieldname}[{$key}]" );
        } else {
            $toImplode[] = "{$fieldname}[{$key}]=".urlencode($value);
        }
    }

    return implode($separator, $toImplode);

}
*/

function AMP_flush_apache_cache_folders() {
	$flush_command = "rm -rf ". AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . "article";
	system($flush_command);
	$flush_command = "rm -rf ". AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . "section";
	system($flush_command);
	$flush_command = "rm  ". AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . "index.html";
	system($flush_command);
}

function AMP_find_banner_image( ) {
    $banner_image = false;
    $currentPage = &AMPContent_Page::instance();

    //if ( $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION ) || $currentPage->isArticle( )) {
    //    $map = &AMPContent_Map::instance();
    //    $banner_image = $map->readAncestors( $currentPage->getSectionId(), 'flash' );
    //} else
    if ( $currentPage->isList( AMP_CONTENT_LISTTYPE_CLASS )) {
        $current_class = $currentPage->getClass( );
        $banner_image = $current_class->get_image_banner( ) ;
    } elseif ( $currentPage->getSectionId( )) {
        $map = &AMPContent_Map::instance();
        $banner_image = $map->readAncestors( $currentPage->getSectionId(), 'flash' );
    }
    return $banner_image;

}

function AMP_get_column_names( $table_name ) {
    //caches calls to dbcon::metacolumns, which are expensive
    $reg = &AMP_Registry::instance();
    $definedSources = &$reg->getEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
    if ( !$definedSources ) {
        $definedSources = AMP_cache_get( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
        if ( $definedSources ) {
            $reg->setEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );
        }
    }
    if ($definedSources && isset($definedSources[ $table_name ])) return $definedSources[ $table_name ];

    $dbcon = AMP_Registry::getDbcon(  );
    $colNames = $dbcon->MetaColumnNames( $table_name );
    $definedSources[ $table_name ] = $colNames;
    $reg->setEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );
    AMP_cache_set( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );

    return $colNames;
}

/**
 * AMP_array_splice 
 *
 * preserves associative keys from a replacement array
 * 
 * @param mixed $target 
 * @param int $offset 
 * @param int $length 
 * @param array $replacement 
 * @access public
 * @return void
 */
function AMP_array_splice( $target, $offset =0, $length = null, $replacement = array(  ) ) {
    if (!isset( $target[$offset] )) {
        trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, 'Array', $offset ));
        return array_merge(  $target, $replacement );
    }
    if ( is_string( $offset ) ) {
        $keys = array_keys( $target );
        $offset = current( array_keys( $keys, $offset ));
    }
    if ( empty( $replacement ) ) {
        return array_splice( $target, $offset, $length );
    }

    $second_offset = isset( $length ) ? $offset+$length : $offset;

    $first_chunk = array_slice( $target, 0, $offset);
    $second_chunk = array_slice( $target, $second_offset );

    foreach( $replacement as $key => $value ) {
        if ( isset( $first_chunk[$key] ) ) {
            if ( is_numeric( $key ) ) continue;
            unset( $first_chunk[$key] );
        }
    }

    return array_merge( $first_chunk, $replacement, $second_chunk );


}

function AMP_url_update( $url, $attr = array( )) {

    if ( empty( $attr ) || !$attr ) return $url;
    $url_segments = split( '\?', $url );
    $base_url = $url_segments[0];
    $updated_values = AMP_url_build_query( $attr );
    if ( !isset( $url_segments[1])) {
        return $url. '?' . join( '&', $updated_values );
    }

    parse_str($url_segments['1'], $start_values );
    $url_start_values = AMP_url_build_query( $start_values );
    $final_values = array_merge( $url_start_values, $updated_values );
    return ( $base_url . '?' . join( '&', $final_values ));

}

function AMP_url_print( $value, $key ) {
    if ( is_array( $value )) return urlencode_array( $value, $key );
    if ( strpos( $value, $key.'=') === 0 ) return $value;
    return $key.'='.urlencode( $value );
}

function AMP_url_build_query( $attr = array( )) {
    if ( empty( $attr )) return array( );
    $complete = array( );
    foreach( $attr as $key => $value ) {
        if ( is_array( $value )) {
            $complete[$key]= urlencode_array( $value, $key );
            continue;
        } elseif ( strip_tags( $value ) != $value ) {
            continue;
        }
        $complete[$key] = AMP_url_print( $value, $key );
    }
    return $complete;
}

function AMP_protect_email( $address, $text='' ) {
    require_once( 'enkoder/enkoder.php');
    return enkode_mailto( $address, ( $text?$text:$address ) );
}

function AMP_to_camelcase( $value ) {
    return str_replace( ' ', '', ucwords( str_replace( '_', ' ', $value )));
}

function AMP_from_camelcase( $value ) {
    $start = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
    $end = "_a,_b,_c,_d,_e,_f,_g,_h,_i,_j,_k,_l,_m,_n,_o,_p,_q,_r,_s,_t,_u,_v,_w,_x,_y,_z";
    $start_set = split( ',', $start );
    $end_set = split( ',', $end );
    return str_replace( $start_set, $end_set, $value );
}


function AMP_flush_common_cache ( ) {
    require_once( 'AMP/System/Cache/Config.inc.php');
    AMP_cacheFlush( AMP_CACHE_TOKEN_URL_CONTENT );
    AMP_cacheFlush( AMP_CACHE_TOKEN_ADODB );
    AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );
	AMP_flush_apache_cache_folders();
}

function AMP_subscribe_to_list( $addresses, $list_id ) {
    if(AMP_MODULE_BLAST == 'PHPlist') {
        require_once( 'Modules/Blast/API.inc.php');
        $_PHPlist = &new PHPlist_API( $this->dbcon );
        return $_PHPlist->add_subscribers( $addresses, $list_id );
    } 
    
    if(AMP_MODULE_BLAST == 'DIA') {
        require_once('DIA/API.php');
        if(!isset($api)) {
            $api =& DIA_API::create();
        }
        $result = $api->addMembersByEmail($addresses, $list_id);
        return sizeof($result);
    }

    return false;
}

function AMP_config_load( $file, $prefix='AMP', $cache=true ) {
    if ( !$file ) return array( );
    static $loaded = array( );
    $prefix = strtoupper( $prefix );
    if ( !isset( $loaded[$prefix])) {
        $loaded[$prefix] = array( );
    }
    if ( isset( $loaded[$prefix][$file]) && $cache ) {
        return $loaded[$prefix][$file];
    }

    //parse values in the custom folder
    $custom_ini = array();
    $custom_file_name = AMP_pathFlip( AMP_LOCAL_PATH . '/custom/' . $file . '.ini.php' );
    if ( file_exists ( $custom_file_name )){
        $custom_ini = parse_ini_file( $custom_file_name, true );
    }

    //parse the base config
    $base_ini = array();
    $base_file_name = AMP_pathFlip( AMP_BASE_INCLUDE_PATH . 'Config/' . $file . '.ini.php' );
    if ( file_exists ( $base_file_name ) ) {
        $base_ini = parse_ini_file( $base_file_name , true );
    }
    if (empty($custom_ini) && empty($base_ini)) return array();

    //this part merges the custom settings with the base settings
    $loaded[$prefix][$file]= array( );
    foreach( $base_ini as $block_key => $block ) {
        if( !isset( $custom_ini[$block_key])) $custom_ini[$block_key] = array( );
        $loaded[$prefix][$file][$block_key]= array_merge( $base_ini[$block_key], $custom_ini[$block_key]);
    }
    $loaded[$prefix][$file] = array_merge( $loaded[$prefix][$file], array_diff( $custom_ini, $base_ini ));
    #$loaded[$prefix][$file]= array_merge( $base_ini, $custom_ini );
    AMP_set_constants( $loaded[$prefix][$file], $prefix );
    return $loaded[$prefix][$file];
}


function AMP_set_constants( $values, $prefix = '' ) {
    foreach( $values as $label => $value ) {
        $full_label = $prefix ? $prefix . ' ' . $label : $label;
        if ( is_array( $value )) {
            AMP_set_constants( $value, $full_label );
            continue;
        }

        $full_label = strtoupper( str_replace( ' ', '_', $full_label ));
        if ( !defined( $full_label )) {
            define( $full_label, $value );
        }
    }

}

function AMP_config_update( $values, $file='custom/site', $prefix='AMP') {
    if ( !$values || empty( $values )) return false;
    $config_filename = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR. $file . '.ini.php';

    $config_file = fopen( $config_filename, 'w+');
    $config_data = AMP_config_format( $values );
    $formatted_config_data = ";<?php\n" . $config_data . "\n\n;?>" ;
    $result = fwrite( $config_file, $formatted_config_data );
    fclose( $config_file );
    return $result;

}

function AMP_config_format( $values ) {
    $output = array( );
    $blocks_output = array( );
    ksort( $values );
    foreach( $values as $key => $value ) {
        if ( is_array( $value )) {
            $blocks_output[] = "\n[".strtolower( str_replace( '_', ' ', $key))."]";
            $blocks_output[] = AMP_config_format( $value ) . "\n";
            continue;
        }


        $formatted_value = is_numeric( $value ) ? $value : ( '"'.$value.'"' );
        $formatted_key = str_pad( strtolower( $key ) . "=", 15 );
        $output[] = $formatted_key . $formatted_value;
    }
    $output = array_merge( $output, $blocks_output );
    return join( "\n", $output );
}

function AMP_config_write( $constant_name, $write_value, $prefix = 'AMP' ) {
    $config_id = strtolower( $constant_name );
    if( $prefix && strpos( strtolower( $constant_name ), strtolower( $prefix) . '_') === 0) {
        $config_id = strtolower( substr( $constant_name, strlen( $prefix ) +1 ));
    }
    $current_config = AMP_config_load( 'site', 'AMP', $cache=false );
    if( empty( $current_config )) return AMP_config_update( array( $config_id => $write_value ));
    foreach( $current_config as $id => $value ) {
        if( is_array( $value ) && strpos( $config_id, str_replace( ' ', '_', $id ) . '_') === 0 ) {
            unset( $current_config[$config_id]);
            $current_config[$id][substr( $config_id, strlen( $id ) + 1 )] = $write_value;
            return AMP_config_update( $current_config );
        }
    }

    $current_config[ $config_id ] = $write_value;
    return AMP_config_update( $current_config );
    

}

function AMP_date_from_url( ) {
    if ( isset( $_GET['date']) && is_array( $_GET['date'])) {
        return $_GET['date'];
    }

    $result = array( );
    $month =( isset( $_GET['month']) && $_GET['month'])         ? $_GET['month']        : false;
    if ( $month ) {
        $result['M'] = $month;
    }
    $year = ( isset( $_GET['year']) && $_GET['year'] )          ? $_GET['year']         : false;
    if ( $year ) {
        $result['Y'] = $year;
    }
    $day = ( isset( $_GET['day']) && $_GET['day'])         ? $_GET['day']        : false;
    if ( $day ) {
        $result['d'] = $day;
    }
    return $result;
}

function AMP_format_date_from_array( $date_values ) {
    $real_day=false;

    if ( !( isset( $date_values['Y']) || isset( $date_values['M']))) return false;
    if ( !isset( $date_values['d']))  {
        $date_values['d'] = 1;
    } else {
        $real_day = true;
    }

    $separator = ': ';
    if ( !isset( $date_values['Y']) && isset( $date_values['M'])) {
        $example_date = strtotime( date( 'Y') . '-' . $date_values['M'] . '-' . $date_values['d'] );
        return $separator . date( 'F', $example_date );
    }
    if ( !isset( $date_values['M']) && isset( $date_values['Y'])) {
        $example_date = strtotime( $date_values['Y'] . '-' . date( 'm') . '-' . $date_values['d'] );
        return $separator . date( 'Y', $example_date );
    }
    $date_stamp = mktime( 0,0,0, $date_values['M'], $date_values['d'], $date_values['Y']);

    if( $real_day ) {
        return $separator . date( 'F j, Y', $date_stamp );
    }

    return $separator . date( 'F Y', $date_stamp );
}

function AMP_cache_this_request( $finalPageHtml ) {
    if ( !AMP_is_cacheable_url( ) ) return; 

    $cache_key = AMP_CACHE_TOKEN_URL_CONTENT . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $user_id =  ( defined( 'AMP_SYSTEM_USER_ID' ) && AMP_SYSTEM_USER_ID ) ? AMP_SYSTEM_USER_ID : null; 
    AMP_cache_set( $cache_key, $finalPageHtml, $user_id );

    //HTML caching code for apache redirection
    $url_values = AMP_url_read(  );
    if ( $url_values ) {
        $section_okay = ( ( count( $url_values ) == 2) && isset( $url_values['list'] ) && isset( $url_values['type'] ));
        $class_okay =   ( ( count( $url_values ) == 2) && isset( $url_values['list'] ) && isset( $url_values['class'] ));
        $article_okay = ( ( count( $url_values ) == 1) && isset( $url_values['id'] ));
        if ( !( $section_okay || $article_okay || $class_okay ) ) {
            //don't cache pages with any funny vars on them
            return;
        }
    }
    $cache_file = false;
    $cache_folder = false;
    $currentPage = AMPContent_Page::instance( );

    if ( $currentPage->isArticle()) {
        $cache_folder = AMP_pathFlip(AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . 'article');
        AMP_mkdir($cache_folder );
        $cache_file = $cache_folder . DIRECTORY_SEPARATOR . $currentPage->getArticleId(). '.html'; 
    }
    if ( $currentPage->isList('type') ) {
        $cache_folder = AMP_pathFlip(AMP_SYSTEM_CACHE_PATH . DIRECTORY_SEPARATOR . 'section') ;
        AMP_mkdir($cache_folder );
        $cache_file = $cache_folder . DIRECTORY_SEPARATOR . $currentPage->getSectionId(). '.html'; 
    }
    if ( $currentPage->isList('index' ) ) {
        $cache_folder = AMP_pathFlip( AMP_SYSTEM_CACHE_PATH );
        AMP_mkdir( $cache_folder );
        $cache_file = $cache_folder . DIRECTORY_SEPARATOR . 'index.html'; 
    }
    if ($cache_file && !file_exists($cache_file) ) {
        $cache_out = fopen( $cache_file, 'w' );
        fwrite($cache_out, $finalPageHtml );
        fclose( $cache_out );	
    }

}

function AMP_dump( $var ) {
    return AMP_varDump( $var );
}

function &AMP_current_section( ) {
    $page = AMPContent_Page::instance( );
    $current_section = $page->getSection( );
    if ( $current_section ) return $current_section;

    if ( !$current_section && ( $current_section_id = AMP_current_section_id( ))) {
        require_once('AMP/Content/Section.inc.php');
        $section = new Section( AMP_Registry::getDbcon( ), $current_section_id );
        return $section;
    }

    $false = false ;
    return $false;
}

function AMP_current_section_id( ) {
    $page = AMPContent_Page::instance( );
    $current_section = $page->getSection( );
    if ( $current_section ) return $current_section->id;
    if ( $current_article = $page->getArticle( )) {
        return $current_article->getParent( );
    }
    if ( $current_intro = $page->getIntroText( )) {
        return $current_intro->getSection( );
    }
    return AMP_CONTENT_SECTION_ID_ROOT;

}

function &AMP_searchform_xml( $xml_filename, $action = false ) {
    require_once( 'AMP/Display/Form/Search.php') ;
    $form = &new AMP_Display_Form_Search( );
    if ( $action ) {
        $form->action = $action;
    }
    $form->read_xml_fields( $xml_filename );
    return $form;

}

function AMP_image_path( $filename, $img_class = AMP_IMAGE_CLASS_OPTIMIZED ) {
    return AMP_LOCAL_PATH . AMP_IMAGE_PATH . $img_class . DIRECTORY_SEPARATOR . $filename;
}

function AMP_request_to_include( $request ) {
    $routes = array(
        '/^articles\/(\d*)/' => 'article.php',
        '/^sections\/(\d*)/' => 'article.php',
        '/^list/' => 'list.php',
        '/^(\d{4})\/(\d{1,2})\/(\d{1,2})\/(\w+)/' => 'articles.php',
        );

    foreach( $routes as $pattern => $target ) {
        if( preg_match( $pattern, $request )) return $target;
    }
    return AMP_CONTENT_URL_404_CORE;
}

function AMP_request_to_vars( $request ) {
    $routes = array(
        '/^articles\/(\d*)/' => 'id=%s',
        '/^sections\/(\d*)/' => 'list=type&type=%s',
        '/^(\d{4})\/(\d{1,2})\/(\d{1,2})\/(\w+)/' => 'year=%s&month=%s&day=%s&permalink=%s',
        );
    foreach( $routes as $pattern => $target ) {
        $found = array( );
        if( preg_match( $pattern, $request, $found )) {
            array_shift( $found );
            parse_str( vsprintf( $target, $found), $new_vars);
            return $new_vars;

        }
    }
    return array( );

}

function AMP_block_frequent_requesters( ) {
    if ( !( defined( 'AMP_BLOCK_FREQUENT_REQUESTERS') && AMP_BLOCK_FREQUENT_REQUESTERS)) return;
    $key = 'REQUESTED_BY_' . $_SERVER['REMOTE_ADDR'] ;
    if( !( $value = AMP_cache_get( $key ))) $value = 0;
    ++$value;
    AMP_cache_set( $key, $value );
    if ( $value > 200 && !AMP_Authenticate( 'admin')) {
        trigger_error( 'Blocking further requests from '. $_SERVER['REMOTE_ADDR']);
        exit;
    }
}

?>
