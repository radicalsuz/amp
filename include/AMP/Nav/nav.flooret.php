<?php
#function that makes a nav that shows sub content if in that section

function nav_subs($type,$list) {
	global $dbcon;
	$html .= '<ul class= nav_sub_list>';
	if ($list  == 5) {
		$sql = "select type, id from articletype where parent = $type and usenav =1 order by textorder, id asc";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			$html .= '<li><a href="section.php?id='.$R->Fields("id").'">'.$R->Fields("type").'</a></li>';
			$R->MoveNext();
		}

	}
	else {
		$sql = "select title,id, linktext from articles where type = $type and publish =1 and (class !=2 and class !=8 )";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			if ($R->Fields("linktext")) {
				$link = $R->Fields("linktext");
			}
			else {
				$link = $R->Fields("title");
			}
			$html .= '<li><a href="article.php?id='.$R->Fields("id").'">'.$link.'</a></li>';
			$R->MoveNext();
		}
		

	}
	$html .= '</ul>';
	return $html;
}

function nav_menu_dd($type){
	global $dbcon, $MM_type;
	$sql = "select type, id, listtype from articletype where parent = 1 and usenav =1 order by textorder, id asc";
	$R=$dbcon->Execute($sql) or DIE('Could not load the navigation information'.$sql.$dbcon->ErrorMsg());	
	$html .= '<ul class="nav_list">';
	while (!$R->EOF) {

		$html .= '<li><a href="section.php?id='.$R->Fields("id").'">'.$R->Fields("type").'</a></li>';
		if (($type == $R->Fields("id")) || ( nav_subs($R->Fields("id") )) {
			$html .= nav_subs($R->Fields("id"),$R->Fields("listtype"));
		}
		$R->MoveNext();
	}
	$html .= '</ul>';

	return $html;
}



echo nav_menu_dd($MM_type);

?>
