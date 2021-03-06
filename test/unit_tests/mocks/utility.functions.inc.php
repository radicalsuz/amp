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

//mock function
    function ampredirect($url) {
		return true;
	}
//end mock function

/*** real function
    function ampredirect($url) {
        if ($_REQUEST['pageredirect']) {
            header("Location: ".$_REQUEST['pageredirect']);
        } else {
            header("Location: $url");
        }
    }
*** end real function */

}

if ( !function_exists( 'redirect' ) ) {

    function redirect($url) {
        ampredirect($url);
    }

}

if ( !function_exists( 'DoDateTime' ) ) {

    //Date functions
    function DoDateTime($theObject, $NamedFormat) {
    if ($theObject == ($null)){ $parsedDate = '';}
        else {
        ereg("([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $theObject, $tgRegs);
        $parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1])); }
        if ($parsedDate == "12/31/69") { $parsedDate = NULL;}
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
        
//mock function
    function hotword($text) {
		return $text;
	}
//end mock function

/*** real function
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

*** end real function */
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
    return setBrowser();
    /*
    global $browser_ie, $browser_win, $browser_mo, $browser_checked;
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
		function AMPfile_list($file){ 
				$dir_name= AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.$file;  
				//die($dir_name);
				$dir = opendir($dir_name);
				$basename = basename($dir_name);
				$fileArr = array();
				$fileArr[''] = 'Select';
				while ($file_name = readdir($dir))
				{
						if (($file_name !=".") && ($file_name != "..")) {
						$fileArr[$file_name] = $file_name;
						}
				}
				uksort($fileArr, "strnatcasecmp");
				return $fileArr;
		} 
}
 
?>
