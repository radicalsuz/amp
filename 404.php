<?php    

/***************Redirection Page
Displayed when a user queries an AMP website
and no resulting page is returned from the database.
Searches redirect table for matching pages, then sends 
the user to search page if no matches are found.*/

include_once("AMP/BaseDB.php");

// Check for a custom handler.
$uri = $_SERVER['REQUEST_URI'];
$pos = strpos( $uri, '?' );
$PHP_SELF = $_SERVER['PHP_SELF'] = substr( $uri, 1, ($pos) ? $pos - 1 : strlen( $uri ) - 1 );

parse_str( $_SERVER['REDIRECT_QUERY_STRING'], $_GET );

$customHandler = AMP_LOCAL_PATH . "/custom/" . $_SERVER['PHP_SELF'];

if (file_exists($customHandler)) { 

	include( $customHandler );

    // Set response header to reflect the actual status of our request.
    //
    // if we made it this far, I'm going to assume that everything is just
    // fine. Custom scripts that want to redirect must exit() before reaching
    // here.
    header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 200 OK' );

} else {

    header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );

    $myURI = $dbcon->qstr(substr($_SERVER['REQUEST_URI'], 1));
    $R=$dbcon->Execute("select * from redirect where publish =1 and old=$myURI or conditional =1") or DIE('404 query'.$dbcon->ErrorMsg());

    $go= false;
	
	while (!$R->EOF) {
		if ($R->Fields("conditional")) {
			if ($go == false) {
				$go = errorred($R->Fields("old"),$R->Fields("new"),$R->Fields("num"));
				}
		}
		else {
			if ($go == false) {$go = errorre($R->Fields("old"),$R->Fields("new"));}
		}
		$R->MoveNext();
	}
	
	if ($go == false) {
	  $sql = "select * from redirect where publish =1 and $myURI like Concat(old, '%')";
	  $R=$dbcon->Execute($sql) or DIE('404 query'.$dbcon->ErrorMsg());
	
	  while (!$R->EOF) {
		if ($R->Fields("conditional")) {
		  
		  if ($go == false) {$go = errorred($R->Fields("old"),$R->Fields("new"),$R->Fields("num"));}
		  
		}
		else {
		  
		  if ($go == false) {$go = errorre($R->Fields("old"),$R->Fields("new"));}
		}
		$R->MoveNext();
	  }
	}
	
	if ($go == false) { 	   //ampredirect ("$Web_url"."search.php");
	}
}

function errorre($org,$target) {
        global $Web_url;
        if (strstr($_SERVER['REQUEST_URI'], "$org")) {
          if (substr($target, 0,4)=="http"){
	   ampredirect ("$target");
	  } else {
	   ampredirect("$Web_url"."$target");
	  } 
	  return true;
        }
        else return false;
}

function errorred($org,$target,$num) {
        global $Web_url;
        $get = strstr($_SERVER['REQUEST_URI'], $org);
		//die($org);
		//die($_SERVER['REQUEST_URI'].'ll');
        $go = substr($get, $num);
		//die($go);
        if ($go) {
          if (substr($target, 0, 4)=="http"){
	    	   ampredirect($target.$go);
	  } else {
	   ampredirect($Web_url.$target.$go);
	  }
	  return true;
        }
        else return false;
}



?>
