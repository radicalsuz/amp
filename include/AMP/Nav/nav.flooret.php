<?php

// function that makes a nav that shows sub sections if in that section

function nav_subs($type,$list) {
	global $dbcon;
	$html .= '<ul class= nav_sub_list>';
		$sql = "select type, id from articletype where parent = $type and usenav =1 order by textorder, id asc";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			$html .= '<li><a href="section.php?id='.$R->Fields("id").'">'.$R->Fields("type").'</a></li>';
			$R->MoveNext();
	} 
	$html .= '</ul>';
	return $html;
}

// function that builds Nav that shows top level sections
function nav_menu_dd($type){
	global $dbcon, $MM_type;
	$sql = "select type, id, listtype  from articletype where parent = 1 and usenav =1 order by textorder, id asc";
		$subsql = "select type, parent, id, listtype  from articletype where id = $type and usenav =1 order by textorder, id asc";
	
     $R=$dbcon->Execute($sql) or DIE('Could not load the navigation information'.$sql.$dbcon->ErrorMsg());	
	$subsections=$dbcon->Execute($subsql) or DIE('Could not load the navigation information'.$sql.$dbcon->ErrorMsg());	
	$html .= '<ul class="nav_list">' ;
	while (!$R->EOF) {

		$html .= '<li><a href="section.php?id='.$R->Fields("id").'">'.$R->Fields("type").'</a></li>';
		if (($type == $R->Fields("id")) ||  ($R->Fields("id") == $subsections->Fields("parent")) ) {
			$html .= nav_subs($R->Fields("id"),$R->Fields("listtype"));
		}
		$R->MoveNext();
	}
	$html .= '</ul>';

	return $html;
}



echo nav_menu_dd($MM_type);

?>
