<?php

/**
 * Check if a file exists in the include path
 *
 * @version      1.2
 * @author       Aidan Lister <aidan@php.net>
 * @param        string $file The name of the file to look for
 * @return       bool True if the file exists, False if it does not
 */

if ( !function_exists( 'file_exists_incpath' ) ) {

    function file_exists_incpath ($file) {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach ($paths as $path)
        {
            // Formulate the absolute path
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;

            // Check it
            if (file_exists($fullpath)) {
                return true;
            }
        }

        return false;
    }
}

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

if ( !function_exists( 'ampredirect' ) ) {

    function ampredirect($url) {
        if ($_REQUEST[pageredirect]) {
            header("Location: $_REQUEST[pageredirect]");
        } else {
            header("Location: $url");
        }
    }

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
        if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
            echo "$regs[2]-$regs[3]-$regs[1]";
        }
    }
}

if ( !function_exists( 'converttext' ) ) {

    function converttext($text) {

        $text = ereg_replace("(([a-z0-9_\.-]+)(\@)[a-z0-9_-]+([\.][a-z0-9_-]+)+)", "<a href=\"mailto:\\0\">\\0</a>", $text);
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

        global $dbcon, $MM_type, $MM_parent, $MM_typename, $HTTP_GET_VARS, $list, $id, $MM_issue, $userper, $MM_region, $navalign;

        $pos = 0;
        $start = 0;

        /* Loop through to find the php code in html...  */
        while ( $pos = strpos( $string, '<?php', $start ) ) {

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
        }

        return $string;
    }
}

if ( !function_exists( 'getnavs' ) ) {

    function getnavs($sql,$navside=l) {

        global $dbcon, $MM_type, $mod_id, $modtemplate;
        $navsqlsel="SELECT navid FROM nav  ";
        $navsqlend =" and position like  '%$navside%' order by position asc";
        $navsql =$navsqlsel.$sql.$navsqlend;
        $navcalled=$dbcon->CacheExecute("$navsql") or die($navsql);
                
        return $navcalled;
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

if ( !function_exists( 'buildheader' ) ) {
		
    function buildheader() {
        
        global $AmpPath, $MM_title, $MM_shortdesc, $MM_id, $_GET, $meta_description, $meta_content, $mod_name, $SiteName, $Web_url,$css;
        $htmlheader .= "<html>
        <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">";
        
        //build header title
        if ($MM_id) {
            $headertitle = $MM_title;
            $meta_description = substr(trim($MM_shortdesc),0,250); 
            $meta_description = ereg_replace ("\"", "", $meta_description);
        } elseif ($_GET["list"] == "type") {
            $headertitle = $MM_typename;
        } else {
            $headertitle = $mod_name;
        }

        if ($mod_id != 2) {
            $headertitle = ":&nbsp;".$headertitle ;
        } else {
            $headertitle = "";
        }

        if ($headertitle =="Article") $headertitle = "";
        
        $htmlheader.="<meta http-equiv=\"Description\" content=\"$meta_description\">" .
                     "<meta name=\"Keywords\" content=\"$meta_content\">" .
                     "<link rel=\"Search\" href=\"/search.php\">";

        if ( file_exists( $AmpPath . "img/favicon.ico" ) ) {
            $htmlheader .= '<link rel="icon" href="' . $AmpPath . 'img/favicon.ico" type="image/x-icon" />';
        }

        $htmlheader.="<title>".$SiteName.$headertitle."</title>";

        $allsheets=explode(", ", $css);

        for ($i=0;  $i<count($allsheets);$i++) {
                $htmlheader.="<link href=\"".$Web_url.trim($allsheets[$i])."\" rel=\"stylesheet\" type=\"text/css\">";
        }

        $htmlheader.="<script language=\"JavaScript\" src=\"".$Web_url."Connections/functions.js\"></script>
        </head>";

        return $htmlheader;

    }
}

function randomid() {

	$random_id_length = 10;
	$rnd_id = crypt(uniqid(rand(),1));
	$rnd_id = strip_tags(stripslashes($rnd_id));
	$rnd_id = str_replace(".","",$rnd_id);
	$rnd_id = strrev(str_replace("/","",$rnd_id));
	$rnd_id = substr($rnd_id,0,$random_id_length);
	return $rnd_id;

}

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

function find_local_path () {

    if (function_exists('apache_lookup_uri')) {

        $localInfo = apache_lookup_uri( '/custom' );
        $localPath = preg_replace( "/(.*)\/custom$/", "\$1", $localInfo->filename );
        
    }

    if (isset($localPath)) $customPath = $localPath . '/custom';

    $searchPath = '.';
    $depth = 0;
    while ( !is_dir($customPath) && $depth++ < 4 ) {
        $customPath = $searchPath . '/custom'; //realpath($searchPath) . '/custom';
        $localPath = realpath( $searchPath );
        $searchPath = '../' . $searchPath;
    }

    if ($depth >= 4) return null;

    return $localPath;
}

?>
