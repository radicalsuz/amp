<?php 
#include_once($base_path."adodb/adodb.inc.php");
#include_once($base_path_amp."custom/config.php");
#ADOLoadCode($MM_DBTYPE);
#$dbcon=&ADONewConnection($MM_DBTYPE);
#$dbcon->Connect($MM_HOSTNAME,$MM_USERNAME,$MM_PASSWORD,$MM_DATABASE);

#$website = "http://vevo.verifiedvoting.org/";
include_once("AMP/BaseDB.php");

function errorre($org,$target) {
        global $Web_url;
        if (strstr($_SERVER['REQUEST_URI'], "$org")) {
          if (substr($target, 0,4)=="http"){
	    header ("Location: $target");
	  } else {
	   header ("Location: $Web_url"."$target");
	  } 
	  return true;
        }
        else return false;
}

function errorred($org,$target,$num) {
        global $Web_url;
        $get = strstr($_SERVER['REQUEST_URI'], $org);
        $go = substr($get, $num);
        if ($go) {
          if (substr($target, 0, 4)=="http"){
	    header ("Location: ".$target.$go);
	  } else {
	    header ("Location: ".$Web_url.$target.$go);
	  }
	  return true;
        }
        else return false;
}

$go= false;
$myURI = $dbcon->qstr(substr($_SERVER['REQUEST_URI'], 1));
$R=$dbcon->Execute("select * from redirect where publish =1 and old=$myURI") or DIE('404 query'.$dbcon->ErrorMsg());

while (!$R->EOF) {
	if ($R->Fields("conditional")) {
		if ($go == false) {$go = errorred($R->Fields("old"),$R->Fields("new"),$R->Fields("num"));}
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



if ($go == false) { header ("Location:  $Web_url"."search.php");}






?>

