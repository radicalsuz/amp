<?php
function evalhtml($string){
global $dbcon;
   $pos = 0;
   $start = 0;

   /* Loop through to find the php code in html...  */
  while ( ($pos = strpos( $string, '<?php', $start )) != FALSE ) {
      /* Find the end of the php code.. */
       $pos2 = strpos( $string, "?>", $pos + 5);

       /* Eval outputs directly to the buffer. Catch / Clean it */ 
       ob_start();
     eval( substr( $string, $pos + 5, $pos2 - $pos - 5) );
      $value = ob_get_contents();
       ob_end_clean();

       /* Grab that chunk!  */
       $start = $pos + strlen($value);
      $string = substr( $string, 0, $pos ) . $value . substr( $string,
$pos2 + 2);
       }
echo $string;
}

function DoDateTime($theObject, $NamedFormat) {
  if ($theObject == ($null)){ $parsedDate = '';}
	else {
	ereg("([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $theObject, $tgRegs);
	$parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1])); }
	return $parsedDate;
}
function converttext($text){
   $text = ereg_replace(" [_a-zA-Z0-9-]+(\.[_a-zA_Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA_Z0-9-]+)", 
       "<a href=\"mailto:\\0\">\\0</a>", $text);
	   $text = ereg_replace(" [[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
                     "<a href=\"\\0\">\\0</a>", $text); 
					 $text= nl2br($text) ;
	   return $text;}
	   
function DoDate($theObject, $NamedFormat) {
	if ($theObject == ($null)){ $parsedDate = '';}
	elseif ($theObject == ('0000-00-00')){ $parsedDate = '';}
	else {
	ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $theObject, $tgRegs);
	$parsedDate=date($NamedFormat, mktime($tgRegs[4],$tgRegs[5],$tgRegs[6],$tgRegs[2],$tgRegs[3],$tgRegs[1])); }
	return $parsedDate;
}
  

?>