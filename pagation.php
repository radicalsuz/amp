<?php

  // *** Recordset Stats, Move To Record, and Go To Record: declare stats variables

  

  // set the record count

  

  

  // set the number of rows displayed on this page

  if ($page_numRows < 0) {            // if repeat region set to all records

    $page_numRows = $page_total;

  } else if ($page_numRows == 0) {    // if no repeat regions

    $page_numRows = 1;

  }

  

  // set the first and last displayed record

  $$page_first = 1;

  $page_last  = $$page_first + $page_numRows - 1;

  

  // if we have the correct record count, check the other stats

  if ($page_total != -1) {

    $page_numRows = min($page_numRows, $page_total);

    $$page_first  = min($$page_first, $page_total);

    $page_last  = min($page_last, $page_total);

  }

  ?><?php $MM_paramName = ""; ?><?php

// *** Move To Record and Go To Record: declare variables



$MM_rs	  = &$list;

$MM_rsCount   = $page_total;

$MM_size      = $page_numRows;

$MM_uniqueCol = "";

$MM_paramName = "";

$MM_offset = 0;

$MM_atTotal = false;

$MM_paramIsDefined = ($MM_paramName != "" && isset($$MM_paramName));

?><?php

// *** Move To Record: handle 'index' or 'offset' parameter



if (!$MM_paramIsDefined && $MM_rsCount != 0) {



	// use index parameter if defined, otherwise use offset parameter

	if(isset($index)){

		$r = $index;

	} else {

		if(isset($offset)) {

			$r = $offset;

		} else {

			$r = 0;

		}

	}

	$MM_offset = $r;



	// if we have a record count, check if we are past the end of the recordset

	if ($MM_rsCount != -1) {

		if ($MM_offset >= $MM_rsCount || $MM_offset == -1) {  // past end or move last

			if (($MM_rsCount % $MM_size) != 0) {  // last page not a full repeat region

				$MM_offset = $MM_rsCount - ($MM_rsCount % $MM_size);

			}

			else {

				$MM_offset = $MM_rsCount - $MM_size;

			}

		}

	}



	// move the cursor to the selected record

	for ($i=0;!$MM_rs->EOF && ($i < $MM_offset || $MM_offset == -1); $i++) {

		$MM_rs->MoveNext();

	}

	if ($MM_rs->EOF) $MM_offset = $i;  // set MM_offset to the last possible record

}

?><?php

// *** Move To Record: if we dont know the record count, check the display range



if ($MM_rsCount == -1) {



  // walk to the end of the display range for this page

  for ($i=$MM_offset; !$MM_rs->EOF && ($MM_size < 0 || $i < $MM_offset + $MM_size); $i++) {

    $MM_rs->MoveNext();

  }



  // if we walked off the end of the recordset, set MM_rsCount and MM_size

  if ($MM_rs->EOF) {

    $MM_rsCount = $i;

    if ($MM_size < 0 || $MM_size > $MM_rsCount) $MM_size = $MM_rsCount;

  }



  // if we walked off the end, set the offset based on page size

  if ($MM_rs->EOF && !$MM_paramIsDefined) {

    if (($MM_rsCount % $MM_size) != 0) {  // last page not a full repeat region

      $MM_offset = $MM_rsCount - ($MM_rsCount % $MM_size);

    } else {

      $MM_offset = $MM_rsCount - $MM_size;

    }

  }



  // reset the cursor to the beginning

  $MM_rs->MoveFirst();



  // move the cursor to the selected record

  for ($i=0; !$MM_rs->EOF && $i < $MM_offset; $i++) {

    $MM_rs->MoveNext();

  }

}

?><?php

// *** Move To Record: update recordset stats



// set the first and last displayed record

$$page_first = $MM_offset + 1;

$page_last  = $MM_offset + $MM_size;

if ($MM_rsCount != -1) {

  $$page_first = $$page_first<$MM_rsCount?$$page_first:$MM_rsCount;

  $page_last  = $page_last<$MM_rsCount?$page_last:$MM_rsCount;

}



// set the boolean used by hide region to check if we are on the last record

$MM_atTotal = ($MM_rsCount != -1 && $MM_offset + $MM_size >= $MM_rsCount);

?><?php

// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters



// create the list of parameters which should not be maintained

$MM_removeList = "&index=";

if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";

$MM_keepURL="";

$MM_keepForm="";

$MM_keepBoth="";

$MM_keepNone="";



// add the URL parameters to the MM_keepURL string

reset ($HTTP_GET_VARS);

while (list ($key, $val) = each ($HTTP_GET_VARS)) {

	$nextItem = "&".strtolower($key)."=";

	if (!stristr($MM_removeList, $nextItem)) {

		$MM_keepURL .= "&".$key."=".urlencode($val);

	}

}



// add the URL parameters to the MM_keepURL string

if(isset($HTTP_POST_VARS)){

	reset ($HTTP_POST_VARS);

	while (list ($key, $val) = each ($HTTP_POST_VARS)) {

		$nextItem = "&".strtolower($key)."=";

		if (!stristr($MM_removeList, $nextItem)) {

			$MM_keepForm .= "&".$key."=".urlencode($val);

		}

	}

}



// create the Form + URL string and remove the intial '&' from each of the strings

$MM_keepBoth = $MM_keepURL."&".$MM_keepForm;

if (strlen($MM_keepBoth) > 0) $MM_keepBoth = substr($MM_keepBoth, 1);

if (strlen($MM_keepURL) > 0)  $MM_keepURL = substr($MM_keepURL, 1);

if (strlen($MM_keepForm) > 0) $MM_keepForm = substr($MM_keepForm, 1);

?><?php

// *** Move To Record: set the strings for the first, last, next, and previous links



$MM_moveFirst="";

$MM_moveLast="";

$MM_moveNext="";

$MM_movePrev="";

$MM_keepMove = $MM_keepBoth;  // keep both Form and URL parameters for moves

$MM_moveParam = "index";



// if the page has a repeated region, remove 'offset' from the maintained parameters

if ($MM_size > 1) {

  $MM_moveParam = "offset";

  if (strlen($MM_keepMove)> 0) {

    $params = explode("&", $MM_keepMove);

    $MM_keepMove = "";

    for ($i=0; $i < sizeof($params); $i++) {

      list($nextItem) = explode("=", $params[$i]);

      if (strtolower($nextItem) != $MM_moveParam)  {

        $MM_keepMove.="&".$params[$i];

      }

    }

    if (strlen($MM_keepMove) > 0) $MM_keepMove = substr($MM_keepMove, 1);

  }

}



// set the strings for the move to links

if (strlen($MM_keepMove) > 0) $MM_keepMove.="&";

$urlStr = $PHP_SELF."?".$MM_keepMove.$MM_moveParam."=";

$MM_moveFirst = $urlStr."0";

$MM_moveLast  = $urlStr."-1";

$MM_moveNext  = $urlStr.($MM_offset + $MM_size);

$MM_movePrev  = $urlStr.(max($MM_offset - $MM_size,0));

?>



