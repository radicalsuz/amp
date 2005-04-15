<?php 
#print ini_get('include_path');
if ( !function_exists( 'evalnavhtml' ) ) {
    
    function evalnavhtml($string){

        global $base_path, $dbcon, $MM_type, $MM_parent, $MM_typename, $list, $id, $MM_issue, $userper, $MM_region, $navalign;

        $pos = strpos( $string, '<?php', $start );
        $start = 0;

        /* Loop through to find the php code in html...  */
        while (!($pos === FALSE)  ) {

            /* Find the end of the php code.. */
            $pos2 = strpos( $string, "?>", $pos + 5);

            /* Eval outputs directly to the buffer. Catch / Clean it */ 
            $code = substr($string, $pos + 5, $pos2 - $pos - 5);
			$variables =  preg_replace( '/.*\$([^\s]*)\s*=\s*["\']{0,1}([^"\']*)["\']{0,1}.*/', "\$1 \$2", $code );
			$va_args = split( " ", $variables );
			
			if ($va_args[0] == 'regionlink') {
				$regionlink = $va_args[1];
			}
			if ($va_args[0] == 'navalign') {
				$navalign = $va_args[1];
			}
			//echo $regionlink.'<br>';
			
			$include_args = preg_replace("/.*include\s*[\(\s*]?\s*\"?([^\)\"\s]*)\"?[\)\s*]?.*/", "\$1", $code );
			$incl = str_replace('"','',$include_args);
			//echo $incl.'<br>';
			ob_start();
            $customfile = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . $incl;
            if (file_exists($customfile)) {
                include ($customfile);
            } else {
                $basefile = 'AMP/Nav/'.$incl;
                if (file_exists_incpath($basefile)) {
                    include($basefile);
                } elseif (file_exists_incpath($incl)) {		
                    $file = $incl;
                    include($file);
                }
            }
			
            $value = ob_get_contents();
            ob_end_clean();

            /* Grab that chunk!  */
            $start = $pos + strlen($value);
            $string = substr( $string, 0, $pos ) . $value . substr( $string, $pos2 + 2);
            $pos = strpos( $string, '<?php', $start );
        }

        return $string;
    }
}


if ( !function_exists( 'getnavs' ) ) {

    function getnavs($sql,$navside=l) {

        global $dbcon, $MM_type, $intro_id, $modtemplate;
        $navsqlsel="SELECT navid FROM nav  ";
        $navsqlend =" and position like  '%$navside%' order by position asc";
        $navsql =$navsqlsel.$sql.$navsqlend;
        $navcalled=$dbcon->CacheExecute("$navsql") or die($navsql);
                
        return $navcalled;
    }
}

if ( !function_exists( 'magpienav' ) ) {
	function magpienav($url,$num_items=NULL,$title=NULL,$html1=NULL,$html2=NULL,$html3=NULL,$html4=NULL,$html5=NULL) {
		global $base_path;
	
	
		define('MAGPIE_DIR', 'Magpie/');
		require_once(MAGPIE_DIR.'rss_fetch.inc');
	
		$error_level_tmp = error_reporting();
		error_reporting( E_ERROR );
		$rss = fetch_rss( $url );
		error_reporting( $error_level_tmp );
	
	
		if ( $rss ) {
			if (!$num_items) {$num_items=5;}
			$items = array_slice($rss->items, 0, $num_items);
			if (!$title) {$title = $rss->channel['title'];}
			$shownav.= $html1.$title.$html2;
			foreach ($items as $item) {
			$href = $item['link'];
			$title = $item['title'];
			$shownav.= $html3."<a href=$href class=sidelist>$title</a>".$html4;
		}
		} else {
			$shownav .= $html1 . $html2;
		}
		$shownav.= $html5;
		return $shownav;
	}
}

function getthenavs($navside) {

    global $dbcon, $MM_type, $MX_top, $obj, $intro_id, $mod_template, $modtemplate, $sidelistcss;
    global $rNAV_HTML_1, $rNAV_HTML_2, $rNAV_HTML_3,$rNAV_HTML_4,$rNAV_HTML_5;
    global $lNAV_HTML_1, $lNAV_HTML_2, $lNAV_HTML_3,$lNAV_HTML_4,$lNAV_HTML_5;
    

    if ($_GET["list"] ) {
        
        ##GET TYPE NAV FILES ####
        if ( $_GET["type"]) {

            $navsql="  WHERE typelist= $MM_type " ;
            $navcalled = getnavs($navsql, $navside);
            
            //work up the hierarchy
            $nparent = $MM_type;
            $nnnavid = $navcalled->Fields("navid");

            while (!$nnnavid && ($nparent != $MX_top)) {
                    $nparent=$obj->get_parent($nparent);
                    $navcalled = getnavs("  WHERE typelist=$nparent  ", $navside);
                    $nnnavid = $navcalled->Fields("navid");
            } 

              }//end type
        
        ##GET CLASS NAV FILES ####
        elseif ( $_GET["class"]) {
            $navsql="  WHERE classlist=".$_GET["class"] ;
            $navcalled = getnavs($navsql, $navside);
            $nnnavid = $navcalled->Fields("navid");
        }

    } //end list
    
    ##GET ID NAV FILES ####
    elseif ($_GET['id']) {
        $navcalled = getnavs("  WHERE typeid=$MM_type ", $navside);
        $nparent =$MM_type;
        $nnnavid = $navcalled->Fields("navid");
        while (!$nnnavid && ($nparent != $MX_top)) {
                $nparent=$obj->get_parent($nparent);
                $navcalled = getnavs("  WHERE typeid=$nparent  ", $navside);
                $nnnavid = $navcalled->Fields("navid");
        }

        if (!$nnnavid) {
            $navcalled = getnavs(" WHERE moduleid = $intro_id ", $navside);  
            $nnnavid  = $navcalled->Fields("navid");
        }
    }

    ## GET MODULES AND DEFULAT ########
    
    if (!$nnnavid ) {
        $navcalled = getnavs(" WHERE moduleid = $intro_id ", $navside);
            $nnnavid  = $navcalled->Fields("navid");
    }

    if (!$nnnavid ) {
		if ($modtemplate) {
        	$navcalled = getnavs(" WHERE moduleid = $modtemplate ",$navside);
            $nnnavid  = $navcalled->Fields("navid"); 
		}		
    }

	if (!$nnnavid ) {
        $navcalled = getnavs(" WHERE moduleid = 1 ",$navside);
        $nnnavid  = $navcalled->Fields("navid"); 
    }

    ##cycle through list #####
    $rowx_count=0;
    while (!$navcalled->EOF) {
        
        $nav_var = $navcalled->Fields("navid") ; //set nav id 
        $settemplatex= ($rowx_count % 2) ? $temp1 : $temp2;
        
        ###################################################################
        
        $nav=$dbcon->CacheExecute("SELECT * FROM navtbl WHERE id = " .$nav_var. "") or DIE($dbcon->ErrorMsg());
        
        if ($navside == "l") {
            $NAV_HTML_1 = $lNAV_HTML_1;
            $NAV_HTML_2 = $lNAV_HTML_2;
            $NAV_HTML_3 = $lNAV_HTML_3;
            $NAV_HTML_4 = $lNAV_HTML_4;
            $NAV_HTML_5 = $lNAV_HTML_5;
        } elseif ($navside == "r") {
            $NAV_HTML_1 = $rNAV_HTML_1;
            $NAV_HTML_2 = $rNAV_HTML_2;
            $NAV_HTML_3 = $rNAV_HTML_3;
            $NAV_HTML_4 = $rNAV_HTML_4;
            $NAV_HTML_5 = $rNAV_HTML_5;
        }
            
        ##  GET DIFFERENT NAV TEMPLATES (Overridden) ####
        if ($settemplatex  or (($nav->Fields("templateid") != $template_id) && ($nav->Fields("templateid") != 0) ) ) {

            if ($settemplatex ) {
                $template_id2 =  $settemplatex;
            } else {
                $template_id2 = $nav->Fields("templateid");
            }
            
            $settemplate=$dbcon->CacheExecute("SELECT * FROM template WHERE id = $template_id2") or DIE($dbcon->ErrorMsg());

            if ($navside == l) {
                $NAV_HTML_1 = $settemplate->Fields("lnav3");        //heading row
                $NAV_HTML_2 = $settemplate->Fields("lnav4");        //close heading row
                $NAV_HTML_3 = $settemplate->Fields("lnav7");                //start content table row
                $NAV_HTML_4 = $settemplate->Fields("lnav8");                //end content table row
                $NAV_HTML_5 = $settemplate->Fields("lnav9");                // content table row spacer

            } elseif ($navside == r) {
                $NAV_HTML_1 = $settemplate->Fields("rnav3");        //heading row
                $NAV_HTML_2 = $settemplate->Fields("rnav4");        //close heading row
                $NAV_HTML_3 = $settemplate->Fields("rnav7");                //start content table row
                $NAV_HTML_4 = $settemplate->Fields("rnav8");                //end content table row
                $NAV_HTML_5 = $settemplate->Fields("rnav9");                // content table row spacer
            }
        }
        
        ###DEFINE NON SQL NAVIGATION
        
        //TITLE AS IMAGE 
		if ($nav->Fields("rss")==1) {
			$shownav.= magpienav($nav->Fields("rss"),$nav->Fields("repeat"),$nav->Fields("titletext"),$NAV_HTML_1,$NAV_HTML_2,$NAV_HTML_3,$NAV_HTML_4,$NAV_HTML_5);
		}
        elseif ($nav->Fields("nosql") == 1 &&  $nav->Fields("titletext") != (NULL)  ) { 
            if ($nav->Fields("titleti") == 1) { //start image
                $shownav.= $NAV_HTML_1 ;
				$shownav.="<img src=\"".$NAV_IMG_PATH ; 
                $shownav.= $nav->Fields("titleimg")."\">";
				$shownav.=$NAV_HTML_2 ; 
            } 

            //TITLE AS TEXT        
            if ($nav->Fields("titleti") != ('1')) { 
                $shownav.= $NAV_HTML_1 ; 
                $shownav.= nl2br($nav->Fields("titletext")); 
                $shownav.=$NAV_HTML_2 ; 
            }

            //BODY
            $shownav.= $NAV_HTML_3 ;
            $nonsqlreturn = $nav->Fields("nosqlcode");
            
            $shownav.= evalnavhtml($nonsqlreturn);
            $shownav.= $NAV_HTML_4 ; 
            $shownav.= $NAV_HTML_5 ;

        }
	
		elseif ($nav->Fields("nosql") == 1 && $nav->Fields("titletext") != (NULL)) {
                //start nonsql
                $shownav.= $nav->Fields("nosqlcode");
            } elseif (trim($nav->Fields("sql"))>"") {
    	
            ###DEFINE SQL GENERATED NAVIGATION
            //deal with database php
            $sqlx = $nav->Fields("sql") ;
            $sqlx = addslashes($sqlx) ;
            eval("\$sqlx = \"$sqlx\";");
            $sqlx = stripslashes($sqlx);
            $limit = $nav->Fields("repeat");
            $alimit = $limit + 1;
            if ($limit != 700) {
                    $sqlx = $sqlx." Limit ".$alimit;
            }
            $nested=$dbcon->CacheExecute($sqlx) or die ($dbcon->ErrorMsg());
            $nested_numRows=0;
            $nested__totalRows=$nested->RecordCount();
            
            $Repeat2__numRows = $nav->Fields("repeat");
            $Repeat2__index= 0;
            $nested_numRows = $nested_numRows + $Repeat2__numRows;
            
            if ($nested->Fields("id") == (NULL)) {
                //start navigation
                $rowx_count++;
            } else {

                ##### TITLE ROW ###########
                if ($nav->Fields("titleti") == ('1')) { //start image
                    $shownav.= $NAV_HTML_1;
                    $shownav.="<img src=\"".$NAV_IMG_PATH .$nav->Fields("titleimg")."\">";
                    $shownav.= $NAV_HTML_2;
				} //end image 
                
                if ($nav->Fields("titleti") != ('1')) { //start title text
                    $shownav.= $NAV_HTML_1 ; 
                    
                    $title = $nav->Fields("titletext") ;
                    if (ereg('^z{3}',$title )) {
                        $title = ereg_replace ("zzz", "", $title);
                        $title = $nested->Fields($title) ;
                        $shownav.= $title;
                        $shownav.= $NAV_HTML_2 ;
                    } else {
                        $shownav.= $title ;
                        $shownav.= $NAV_HTML_2 ;
                    }
                } //end title text 

                ##### REPEATING MIDDLE CONTENT###########
                while (($Repeat2__numRows-- != 0) && (!$nested->EOF)) { 
                    $shownav.=$NAV_HTML_3 ; //start link text
                    $shownav.= "<a href=\"";
                    $shownav.= $nav->Fields("linkfile") ;
                    $shownav.= "?";

                    if ($nav->Fields("mvar1")) {
                        $shownav.=$nav->Fields("mvar1");
                    } else { 
                        $shownav.= "id";
                    }

                    $shownav.="=";
                    if ($nav->Fields("mvar1val")) {
                        $shownav.= $nested->Fields($nav->Fields("mvar1val"));
                    } else {
                        $shownav.= $nested->Fields("id");
                    }

                    $shownav.="\"";   
                    $shownav.=" class=\"";
                    if ($settemplatex ==$temp1 && $settemplatex ) {
                        $shownav.= "sidelist2";
                    } elseif ($nav->Fields("linkextra")) {
                        $shownav.= $nav->Fields("linkextra");
                    } else {
                        $shownav.=$sidelistcss;
                    }

                    $shownav.="\">";
                    if ($nav->Fields("linkfield")) {
                        $shownav.= $nested->Fields($nav->Fields("linkfield"));
                    } else {
                        $shownav.= $nested->Fields("title");
                    }

                    $shownav.="</a>";
                    $shownav.= $NAV_HTML_4;
                    
                    $Repeat2__index++;
                    $nested->MoveNext();
                } 

                ######### REPEAT ROW ###############
                if (($nested->RecordCount()) > ($nav->Fields("repeat"))) {                                 //start more
                    $shownav.= $NAV_HTML_3 ;                                                                         
                    $shownav.="<A HREF=\"";
                    $shownav.= $nav->Fields("mfile")."?list=".$nav->Fields("mcall1");
                    if  ($nav->Fields("mcall1") == "classt") $shownav.= "&type=$MM_type";

                    $shownav.="&".$nav->Fields("mvar2")."=".$nested->Fields($nav->Fields("mcall2"))."\" class=\"go\">More&nbsp;&#187;</a>";
                    $shownav.= $NAV_HTML_4 ;
				} //end more
                
                $shownav.=$NAV_HTML_5 ;         //end navigation
                $nested->Close();
            } 
        }
        
        ###################################################################
        
        $rowx_count++;
        $navcalled->MoveNext();
    }

    return $shownav; 
}
?>
