<?php
if (!function_exists('groups_layout_display')) {
	function groups_layout_display($Organization,$City=NULL,$State=NULL,$Country=NULL,$First_Name=NULL,$Last_Name=NULL,$Email=NULL,$Phone=NULL,$Web_Page=NULL,$About=NULL) {
		$html .= "<span class =\"eventtitle\"> \n";
		$html .= "<a";
		if ($Web_Page && ($Web_Page != 'http://')) {
			 $html .= " href=\"$Web_Page\" ";
		}
		$html .= " class =\"eventtitle\" target=\"_blank\">".$Organization;
		 
		$html .= "</a></span><br>";
		
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
		$html .= "<br>\n";
		return $html;
	}
}

echo  groups_layout_display( $groups->Fields("Company"), $groups->Fields("City"), $groups->Fields("State") , $groups->Fields("Country"), $groups->Fields("First_Name"), $groups->Fields("Last_Name"), $groups->Fields("Email"), $groups->Fields("Phone"), $groups->Fields("Web_Page"), $groups->Fields("custom1"));


?>