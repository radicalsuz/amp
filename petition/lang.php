<?php

//  phpetition v0.3, An easy to use PHP/MySQL Petition Script
//  Copyright (C) 2001,  Mike Gifford, http://openconcept.ca
//
//  This script is free software; you can redistribute it and/or
//  modify it under the terms of the GNU General Public License
//  as published by the Free Software Foundation; either version 2
//  of the License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License http://www.gnu.org/copyleft/ for more details. 
//
//  If you distribute this code, please maintain this header.
//  Links are always appreciated!
//
// This file is used to configure your script

echo "<!-- START: lang.php -->\n";

// Available languages

  $avail = array( 
	"en" => "eng"
		);
/*
	"es" => "esl",
	"pt" => "por",
	"eo" => "epo",
	"ja" => "jpn",
	"pl" => "pol",
	"nl" => "dut",
	"de" => "deu",
	"it" => "ita",
	"ro" => "rom",
	"mk" => "mac",
	"zh" => "chi-t",
	"hr" => "hrv",
	"sr" => "scr",
	"el" => "ell",
	"et" => "est",
	"cs" => "ces"
*/

  $avail_rev = array( 
	"eng" => "en"
		);	
/*
	"esl" => "es",
	"por" => "pt",
	"epo" => "eo",
	"jpn" => "ja",
	"pol" => "pl",
	"ita" => "it",
	"rom" => "ro",
	"deu" => "de",
	"mac" => "mk",
	"hrv" => "hr",
	"scr" => "sr",
	"ell" => "el",
	"est" => "et",
	"chi-t" => "zh",
	"chi-s" => "zh",
	"ces" => "cs"
	);
	*/
	
  $lang_avail = array( 
	"eng" => "english" 
	
	);
/*
	"esl" => "español - spanish",
	"por" => "portuguese",
	"epo" => "esperanto",
	"dut" => "dutch",
	"jpn" => "japanese",
	"pol" => "polska - polish",
	"ita" => "italiana - italian",
	"rom" => "româneascã - romanian",
	"deu" => "deutsch - german",
	"mac" => "macedonian",
	"hrv" => "croatian",
	"scr" => "serbian",
	"ell" => "greek",
	"est" => "eesti - estonian",
	"chi-t" => "traditional chinese",
	"chi-s" => "simplified chinese",
	"ces" => "Èeská - czech"
	);
*/

// check preferred language

/* generalized language selection. make this into a function... */

  /* ignore browser prefs if language set in GET. */
  if (isset($lang) && !empty($lang)) {
    $lang2 = $avail_rev[$lang];
  } else {
    /* figure out if preferred language is available. */
//error_log("prefs=" . $HTTP_SERVER_VARS["HTTP_ACCEPT_LANGUAGE"], 0);
//error_log("envpr=" . getenv("HTTP_ACCEPT_LANGUAGE"), 0);
    //if (isset($HTTP_SERVER_VARS)) {
    //  $prefs = explode(', ', $HTTP_SERVER_VARS["HTTP_ACCEPT_LANGUAGE"]);
    //} else {
      $prefs = split(',[ ]*', getenv('HTTP_ACCEPT_LANGUAGE'));
      //$prefs = explode(', ', getenv('HTTP_ACCEPT_LANGUAGE'));
    //}

    /* match preferences with available languages. */
    if (sizeof($prefs) == 0) {
      error_log("Warning: Client set no preferred language!", 0);
      $lang = "eng";
      $lang2 = "en";
    } else {
      /* match preferred language with available languages */
      reset($avail);
      reset($prefs);
      $lang = "";
      while (list($key,$val) = each($prefs)) {
	$x = substr($val,0,2);
        if (!empty($x) && $avail[$x]) {
          $lang = $avail[$x];
          $lang2 = $val;
          break;
        }
      }
    if (empty($lang)) {
        error_log("Warning: Preferred languages not available: " . implode($prefs, ','), 0);
        $lang = "eng"; 
        $lang2 = "en"; 
      }
    }
  }

/* language dependent constants. */
  include ("$base_path"."$petdir"."/lang/$lang.php");
  
echo "<!-- END: lang.php -->\n";
?>
