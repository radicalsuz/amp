<?php 
$navside="l";

$modtemplate = 1;

if ($HTTP_GET_VARS[type] ){
$calledvar = $HTTP_GET_VARS[type] ;
if ($calledvar) {
$navcalled=$dbcon->CacheExecute("SELECT * FROM nav WHERE typelist=$calledvar and position like '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());}
$nparent =$calledvar;
$nnnavid = $navcalled->Fields("navid");
 while (!$nnnavid && ($nparent != $MX_top)) {
	$nparent=$obj->get_parent($nparent);
	$navcalled=$dbcon->CacheExecute("SELECT * FROM nav WHERE typelist=$nparent and position like '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());
	$nnnavid = $navcalled->Fields("navid");
	}
if (!$navcalled->Fields("navid") ){
 $navcalled=$dbcon->CacheExecute("SELECT navid FROM nav WHERE moduleid = $mod_id and position like  '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());}
}
elseif ($HTTP_GET_VARS["list"] == ("class") ){
$calledvar =  $HTTP_GET_VARS["class"] ;
if ($calledvar) {
$navcalled=$dbcon->CacheExecute("SELECT * FROM nav WHERE classlist=$calledvar and position like '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());}
if (!$navcalled->Fields("navid") ){
 $navcalled=$dbcon->CacheExecute("SELECT navid FROM nav WHERE moduleid = $mod_id and position like  '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());}
}
elseif (isset($HTTP_GET_VARS[id])){
$navcalled=$dbcon->CacheExecute("SELECT * FROM nav WHERE typeid=$MM_type and position like '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());
$nparent =$MM_type;
$nnnavid = $navcalled->Fields("navid");
 while (!$nnnavid && ($nparent != $MX_top)) {
	$nparent=$obj->get_parent($nparent);
	$navcalled=$dbcon->CacheExecute("SELECT * FROM nav WHERE typeid=$nparent and position like '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());
	$nnnavid = $navcalled->Fields("navid");
	}
if (!$navcalled->Fields("navid")){
 $navcalled=$dbcon->CacheExecute("SELECT navid FROM nav WHERE moduleid = $mod_id and position like  '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());}
}

else {
 $navcalled=$dbcon->CacheExecute("SELECT navid FROM nav WHERE moduleid = $mod_id and position like  '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());
 if ($navcalled->RecordCount() == 0 ) {
  $navcalled=$dbcon->CacheExecute("SELECT navid FROM nav WHERE moduleid = $modtemplate and position like  '%$navside%' order by position asc") or DIE($dbcon->ErrorMsg());
 }}

    $navcalled_numRows=0;
   $navcalled__totalRows=$navcalled->RecordCount();
    $Repeat7__numRows = -1;
   $Repeat7__index= 0;
   $navcalled_numRows = $navcalled_numRows + $Repeat7__numRows;
    while (($Repeat7__numRows-- != 0) && (!$navcalled->EOF)) 
   { 
		$nav_var = $navcalled->Fields("navid") ; //set nav id 
		include ("nav.php"); 
	$Repeat7__index++;
 	$navcalled->MoveNext();
}
 $navcalled->Close();
		?>