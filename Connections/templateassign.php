<?php

/*****
 *
 * this file loads the system vars and builds the template by working up the
 * hierarchy unitll it finds an assigned template
 *
 *****/

//set system vars from database
$setvar = $dbcon->CacheExecute("SELECT template, emendorse, emfrom, metadescription, metacontent FROM sysvar WHERE id = 1")
                or die("Couldn't find template definition: " . $dbcon->ErrorMsg());

//work down the hierarchy to set the template id
$template_id = $mod_template;  

if ( (!$template_id) && $MM_type != 1 && $MM_type ) { 

    $gettemplate = $dbcon->CacheExecute("SELECT templateid, css FROM articletype WHERE id = $MM_type")
                        or die( "Couldn't find template id: " . $dbcon->ErrorMsg());

    $template_id = $gettemplate->Fields("templateid");
    $css = $gettemplate->Fields("css");
    $tparent = $MM_type;

	if (isset($css_inherit)) {

		while ((!$template_id||!$css) && ($tparent != $MX_top)) {
			$tparent=$obj->get_parent($tparent);
			$gettemplate=$dbcon->CacheExecute("SELECT templateid, css FROM articletype WHERE id = $tparent")
                            or die( "Couldn't find inherited template style id: " . $dbcon->ErrorMsg());

			if (!$template_id) $template_id = $gettemplate->Fields("templateid");
			if (!$css) $css = $gettemplate->Fields("css");
		}

	} else {

		while (!$template_id && ($tparent != $MX_top)) {
			$tparent=$obj->get_parent($tparent);
			$gettemplate = $dbcon->CacheExecute("SELECT templateid FROM articletype WHERE id = $tparent")
                            or die( "Couldn't find inherited template id: " . $dbcon->ErrorMsg());

			$template_id = $gettemplate->Fields("templateid");
		}
	}
  
  
}

if (!$template_id) $template_id = $setvar->Fields("template");

//load the template data 	
$settemplate = $dbcon->CacheExecute("SELECT * FROM template WHERE id = $template_id")
                    or die("Couldn't fetch template data: " . $dbcon->ErrorMsg());

###SET SYSTEM AND TEMPALTE VARS#####

##Email VARS
$MM_email_usersubmit = $setvar->Fields("emendorse");		//User Submitted Article
$MM_email_from = $setvar->Fields("emfrom");				//return email web sent emails

$meta_description= $setvar->Fields("metadescription");	//meta desc
$meta_content = $setvar->Fields("metacontent");			//meta content
$NAV_IMG_PATH = $settemplate->Fields("imgpath");
$NAV_REPEAT = $settemplate->Fields("repeat");
$htmltemplate =$settemplate->Fields("header2");
//$header2 = $settemplate->Fields("header2");
//$header3 = $settemplate->Fields("header3");
//$header4 = evalhtml($settemplate->Fields("header4"));
//$header5 = evalhtml($settemplate->Fields("header5"));
//$header6 = $settemplate->Fields("header6");
//$footer = evalhtml($settemplate->Fields("footer"));

if (!$css) $css = $settemplate->Fields("css");
   
#set defual template

$lNAV_HTML_1 = $settemplate->Fields("lnav3");	//heading row
$lNAV_HTML_2 = $settemplate->Fields("lnav4");	//close heading row
$lNAV_HTML_3 = $settemplate->Fields("lnav7");		//start content table row
$lNAV_HTML_4 = $settemplate->Fields("lnav8");		//end content table row
$lNAV_HTML_5 = $settemplate->Fields("lnav9");		// content table row spacer
$rNAV_HTML_1 = $settemplate->Fields("rnav3");	//heading row
$rNAV_HTML_2 = $settemplate->Fields("rnav4");	//close heading row
$rNAV_HTML_3 = $settemplate->Fields("rnav7");		//start content table row
$rNAV_HTML_4 = $settemplate->Fields("rnav8");		//end content table row
$rNAV_HTML_5 = $settemplate->Fields("rnav9");		// content table row spacer

?>
