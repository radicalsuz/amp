<?php
if (!function_exists('groups_layout_display')) {
	function groups_layout_display($id,$Organization,$City=NULL,$State=NULL,$Country=NULL,$First_Name=NULL,$Last_Name=NULL,$Email=NULL,$Phone=NULL,$Web_Page=NULL,$About=NULL,$Details=NULL,$image=NULL) {
		
		if ($image) {
			$start = "<table width= \"100%\"><tr><td width = 100><img src =\"img/thumb/$image\"></td><td valign=\"top\">";
			$end = "</td></tr></table>\n";
		}
		$html .= $start;

		$html .= "<span class =\"eventtitle\"> \n";
		if (($Web_Page != NULL) && ($Web_Page != 'http://')) {
			 $html .= '<a href="'.$Web_Page.'" target="_blank" class ="eventtitle" >';
			 $endlink = "</a>";
		}
		else {
			 $html .= '<a href="groups.php?gid='.$id.'" class ="eventtitle" >';
			 $endlink = "</a>";
		}
		$html .= $Organization.$endlink."</span><br>";
		
		if ($City && $State) {
			$html .= "<span class=\"eventsubtitle\">$City, ";
			if ($State =='Intl') { 
				$html .= $Country;
			} else {
				$html .= state_convert($State);
			}		
			$html .="</span><br>\n";
		}
	
		if ( ($First_Name) & ($Last_Name) ) {
			$html .= "<span class=\"bodygrey\">". $First_Name . "&nbsp;" . $Last_Name. "</span><br>\n";
		}
		if ($Email) {
			$html .= "<span class=\"bodygrey\"><a href=\"mailto:$Email\">$Email</a></span><br>\n";
		}
		if ($Phone) {
			$html .= "<span class=\"bodygrey\">". $Phone . "</span><br>\n";
		}
		if ($About) {
			$html .= "<span class=\"bodygrey\">". converttext($About) . "</span><br>\n";
		}
		# $html .= events_groups($id);
		$html .= "<br>\n";
		$html .= $end;

		return $html;
	}
}

if (!function_exists('groups_detail_display')) {
	function groups_detail_display($id,$Organization,$City=NULL,$State=NULL,$Country=NULL,$First_Name=NULL,$Last_Name=NULL,$Email=NULL,$Phone=NULL,$Web_Page=NULL,$About=NULL,$Details=NULL,$image=NULL) {
	
		$html .= '<p class ="title">'.$Organization.'</p>';
		if ($Web_Page && ($Web_Page != 'http://')) {
			 $html .= '<a href="'.$Web_Page.'" target="_blank" class ="bodygrey" >'.$Web_Page.'</a><br>';
		}
		
		if ($City && $State) {
			$html .= "<span class=\"eventsubtitle\">$City, ";
			if ($State =='Intl') { 
				$html .= $Country;
			} else {
				$html .= state_convert($State);
			}		
			$html .="</span><br>\n";
		}
	
		if ( ($First_Name) & ($Last_Name) ) {
			$html .= "<span class=\"bodygrey\">". $First_Name . "&nbsp;" . $Last_Name. "</span><br>\n";
		}
		if ($Email) {
			$html .= "<span class=\"bodygrey\"><a href=\"mailto:$Email\">$Email</a></span><br>\n";
		}
		if ($Phone) {
			$html .= "<span class=\"bodygrey\">". $Phone . "</span><br><br><br>\n";
		}
		if ($image) {
			$html .= "<img src =\"img/pic/$image\" align = left>\n";
		}
		if ($About) {
			$html .= "<span class=\"bodygrey\">". converttext($About) . "</span><br><br>\n";
		}
		if ($Details) {
			$html .= "<span class=\"text\">". converttext($Details) . "</span><br>\n";
		}
		$html .= "<br>\n";

		return $html;
	}
}

if ($_GET["gid"]) {
	echo  groups_detail_display( $groups->Fields("id"), $groups->Fields("Company"), $groups->Fields("City"), $groups->Fields("State") , $groups->Fields("Country"), $groups->Fields("First_Name"), $groups->Fields("Last_Name"), $groups->Fields("Email"), $groups->Fields("Phone"), $groups->Fields("Web_Page"), $groups->Fields("custom1"),$groups->Fields("custom18"),$groups->Fields("custom19"));
} else {
	echo  groups_layout_display( $groups->Fields("id"), $groups->Fields("Company"), $groups->Fields("City"), $groups->Fields("State") , $groups->Fields("Country"), $groups->Fields("First_Name"), $groups->Fields("Last_Name"), $groups->Fields("Email"), $groups->Fields("Phone"), $groups->Fields("Web_Page"), $groups->Fields("custom1"),$groups->Fields("custom18"),$groups->Fields("custom19"));
}



?>
