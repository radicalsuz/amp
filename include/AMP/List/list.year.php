<?php

function article_year_list($type=NULL,$class=NULL) {
	global $dbcon;
	
	if ($type) {$wtype ='class= $class';}
	if ($class) {$wclass= 'type= $type';}
	if ($type && $class){
	$sql ='select distinct YEAR(date) as date from articles where $wtype $and $wclass ';
	
	echo 


}



?>