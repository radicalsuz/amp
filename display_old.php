<?php

include("AMP/BaseDB.php"); 


function parse_display($R,$html,$fields) {
	$fields = explode(",", $fields);
	for ($x=0; $x<sizeof($fields); $x++){
			$html = eregi_replace("\[".$fields[$x]."\]",$R->Fields($fields[$x]),$html);
		}	
	$html = ereg_replace("\[[A-Z\. ]+\]","",$html);
	return $html;
}

$sql = "select * from display where id = ".$_GET['did'];
$S=$dbcon->CacheExecute($sql) or DIE($sql.$dbcon->ErrorMsg());

if ($_GET['detail']) {
	$extra = " and id = ".$_GET['detail'];
}
if ($S->Fields("sql_order")){
	$order = ' order by '.$S->Fields("sql_order");
}

$sql = $S->Fields("sql").$extra.$order;

$R=$dbcon->CacheExecute($sql) or DIE($sql.$dbcon->ErrorMsg());

$modid =$S->Fields("mod_intro_list_id");; 
if ($_GET['detail']) {
	$modid =$S->Fields("mod_detail_list_id");
}
$mod_id = $S->Fields("mod_id"); 

include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 

if ($_GET['detail']) {
	echo parse_display($R,$S->Fields("detail_html"),$S->Fields("display_fields"));
} 
else {
	$Sort = '';
	$Sort2 = '';
	while (!$R->EOF) {
		if ($S->Fields("sort_field")) {
			$sort_field = $S->Fields("sort_field") ;
			if ($R->Fields($sort_field) != $Sort) {
				echo parse_display($R,$S->Fields("sort_class"),$S->Fields("sort_field"));
			}
			$Sort = trim($R->Fields($sort_field)); 
		}
		if ($S->Fields("sort_field2")) {
			$sort_field2 = $S->Fields("sort_field2") ;
			if ($R->Fields($sort_field2) != $Sort2) {
				echo parse_display($R,$S->Fields("sort_class2"),$S->Fields("sort_field2"));
			}
			$Sort2 = trim($R->Fields($sort_field)); 
		}

		echo parse_display($R,$S->Fields("list_html"),$S->Fields("display_fields"));
		$R->MoveNext();
	}

}

include("AMP/BaseFooter.php");

?>