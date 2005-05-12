<?php

/*********************
03-16-2005  v3.01
Module:  Navigation
Description:  displays an expanding navigation menu that lists 
top level sections and one level of subsections
CSS: nav_sub_list, nav_list, nav_active, nav_sub_active
SYS VARS: $MM_type
To Do: 
Touch: Margot
*********************/ 



// function that makes a nav that shows sub sections if in that section

function nav_sub_content($type) {
	global $dbcon;
	$html = '<ul class="nav_sub_list">';
	$sql = "select title,id, linktext from articles where type = $type and publish =1 and (class !=2 and class !=8 ) order by pageorder, id asc";
	$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
	while (!$R->EOF) {
		if ($R->Fields("linktext")) {
			$link = $R->Fields("linktext");
		}
		else {
			$link = $R->Fields("title");
		}
		$html .= '<li class="nav_sub_list"><a href="article.php?id='.$R->Fields("id").'" class="nav_sub_list">'.$link.'</a></li>';
		$R->MoveNext();
	}

	$html .= '</ul>';
	return $html;
}

function nav_sub_section($type) {
	global $dbcon;
	$html = '<ul class="nav_sub_list">';
		$sql = "select type, id from articletype where parent = $type and usenav =1 order by textorder, id asc";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			$html .= '<li class="nav_sub_list"><a href="section.php?id='.$R->Fields("id").'" class="nav_sub_list" >'.$R->Fields("type").'</a></li>'; 
			$R->MoveNext();
	} 
	$html .= '</ul>';
	return $html;
}



function nav_sub_both($type) {
	global $dbcon;
	$html .= '<ul class="nav_sub_list">';
		$sql = "select type, id from articletype where parent = $type and usenav =1 order by textorder, id asc";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			$html .= '<li class="nav_sub_list"><a href="section.php?id='.$R->Fields("id").'" class="nav_sub_list" >'.$R->Fields("type").'</a></li>'; 
			$R->MoveNext();
	} 


	$sql = "select title,id, linktext from articles where type = $type and publish =1 and (class !=2 and class !=8 ) orderby pageorder, id asc";
	$C=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
	while (!$C->EOF) {
		if ($C->Fields("linktext")) {
			$link = $C->Fields("linktext");
		}
		else {
			$link = $C->Fields("title");
		}
		$html .= '<li class="nav_sub_list"><a href="article.php?id='.$C->Fields("id").'" class="nav_sub_list">'.$link.'</a></li>';
		$C->MoveNext();
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

    if (!isset($html)) $html = '';
	
    $html .= '<ul class="nav_list">' ;

	while (!$R->EOF) {
        if (($type == $R->Fields("id")) ||  ($R->Fields("id") == $subsections->Fields("parent")) ) {
            $html .= '<li ><a href="section.php?id='.$R->Fields("id").'" class="nav_active" >'.$R->Fields("type").'</a></li>';
        } else { 
		    $html .= '<li class="nav_list"><a href="section.php?id='.$R->Fields("id").'" class="nav_list">'.$R->Fields("type").'</a></li>';
        }
		if (($type == $R->Fields("id")) ||  ($R->Fields("id") == $subsections->Fields("parent")) ) {
			if ($R->Fields("listtype")  == 5) {
				$html .= nav_sub_section($R->Fields("id"));
			} else if ($R->Fields("listtype")  == 1)  {
				$html .= nav_sub_content($R->Fields("id"));
			} else {
				$html .= nav_sub_both($R->Fields("id"));
			}
		}
		$R->MoveNext();
	}
	$html .= '</ul>';

	return $html;
}



echo nav_menu_dd($MM_type);

?>
