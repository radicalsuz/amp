<?php
#needs to add date function, random selection, and sectional selection
# assocated class styles : quoteframe, quote, quoteby

function display_quote($type=NULL) {
	global $dbcon;
	if ($type) {$where_type = "type=$type and";}
	$where_date = "date= now() and";
	$Q=$dbcon->CacheExecute("Select * from quotes where $where_date $where_type publish =1 order by rand() limit 1");
	if (!$Q->Fields("quote")) {
			$Q=$dbcon->CacheExecute("Select * from quotes where $where_type publish =1 order by rand() limit 1");
	}
	$html .= "<div class=\"quoteframe\">";
	if ($Q->Fields("quote")) {
		$html .= "<div class=\"quote\">\"".$Q->Fields("quote")."\"</div>";
		if ($Q->Fields("source")) { 
			$html .= "<div class=\"quoteby\">-".$Q->Fields("source")."</div>";
		}
	}
	$html .= "</div>";
	return $html;
} 

echo display_quote();
?>
