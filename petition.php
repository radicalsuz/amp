<?php
##to do :
#  duplicat checking against email
#  email verifaction
# email dns lookup
# required fields
#  response pages 

# modinput2 needs to be changed to modinput 4 and not break

$modid = 7;
$mod_id = 42;
include_once("AMP/BaseDB.php");
include_once("AMP/BaseTemplate.php");
include_once("AMP/BaseModuleIntro.php"); 
require_once( 'AMP/UserData/Input.inc.php' );

function progressBox($petmod, $petition_started=NULL,  $petition_ends=NULL) {
	global $dbcon;
	$sql="SELECT  COUNT(DISTINCT id) FROM userdata  where modin = $petmod ";
	$ptct= $dbcon->CacheExecute($sql) or DIE("could not get count: ".$sql.$dbcon->ErrorMsg());
	$count = $ptct->fields[0];
	 
	$html .= "<table cellpadding=0 cellspacing=0 border=1 align=center bgcolor=\"#CCCCCC\" width=\"100%\"><tr><td>";
	$html .= "\n\t<table border=0 cellspacing=0 cellpadding=0 width=\"100%\"><tr>";
	if  ($petition_started){
		$html .= "\n\t\t<td align=center class=form><small><B>Posted:<br>$petition_started</B></small></td>";
	}
	if  ($petition_ends){
		$html .= "\n\t\t<td align=center class=form><B><small>Petition Ends:<br>$petition_ends</small></B></td>";
	}
	$html .= "\n\t\t<td align=center class=form><small><B>Petition Signatures:&nbsp; $count</b></small></td>";
	$html .= "\n\t</tr></table>";
	$html .= "</td></tr></table>";
	return $html;
}

function petition_signers($petmod,$limit=25){
	global $dbcon, $pid;
	if (!$_REQUEST["offset"]) {$offset= 0;}
  	else {$offset=$_REQUEST["offset"];}
	$sql="SELECT First_Name, Last_Name, Company,Notes, City,  State  FROM userdata  where  modin = $petmod and custom19 = 1 order by id desc  Limit $offset, $limit";
	$P=$dbcon->CacheExecute($sql) or DIE("could not find signers ".$sql.$dbcon->ErrorMsg());
	$sql="SELECT  COUNT(DISTINCT id) FROM userdata  where modin = $petmod and custom19 =1";
	$ptct= $dbcon->CacheExecute($sql) or DIE("could not get count: ".$sql.$dbcon->ErrorMsg());
	$count = $ptct->fields[0];

	$html .='<a name="namelist"></a>
			<p class="title">Recent Petition Signers</p>
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
			  <tr bgcolor="#CCCCCC"> 
				<td class="text">Name</td>
				<td class="text">Organization</td>
				<td class="text">Location</td>
				<td class="text">Comment</td>
			  </tr>';
	while (!$P->EOF) { 
		$html .= '
				  <tr> 
					<td class="text">'. trim($P->Fields("First_Name")).'&nbsp;'. trim($P->Fields("Last_Name")).'</td>
					<td class="text">'. $P->Fields("Company") .'</td>
					<td class="text">'. $P->Fields("City").'&nbsp;'.$P->Fields("State").'</td>
					<td class="text">'. $P->Fields("Notes").'</td>
				  </tr>';
		$P->MoveNext();
	}
	if ($count > $limit) {
		$html .= '<tr><div align=right><td colspan=4 class="text"><a href="petition.php?pid='.$pid.'&signers=1&offset='.($offset + $limit).'#namelist">Next Page</a></div></td></tr>';
	} 
	$html .= '</table><P><a href="petition.php?pid='. $pid.'">Sign the Petition</a></P><br><br>';
	return $html;
}

if ($_REQUEST['pid'] or $_REQUEST['modin']) {

	if ($_REQUEST['modin']) {$where = 'udmid = '. $_REQUEST['modin']; }
	else {$where = "id = ".$_REQUEST["pid"];}
	$petitontx=$dbcon->Execute("SELECT * FROM petition where $where  ") or DIE("could not find petition".$dbcon->ErrorMsg());
	$petmod  =  $petitontx->Fields("udmid");
	$pid  =  $petitontx->Fields("id");
	
	if ($petitontx->Fields("datestarted") !="0000-00-00" or $petitontx->Fields("datestarted") != NULL ){
	$petition_started = DoDate($petitontx->Fields("datestarted"),"M, j Y");}
	if ($petitontx->Fields("dateended") !="0000-00-00" or $petitontx->Fields("dateended") != NULL){
	$petition_ends= DoDate($petitontx->Fields("dateended"),"M, j Y");}
	
	//OUTPUT THE PAGE
	echo progressBox($petmod,$petition_started, $petition_ends);
	echo "<P align=center><a href=\"petition.php?pid=".$pid."&signers=1\">View Signatures</a></p>";
	
	if(!$_REQUEST['btnUdmSubmit'] and (!$_REQUEST["signers"]) and  (!$_REQUEST["uid"])){
	
	?>
		  <p class="title"> 
			<?php echo $petitontx->Fields("title")?>
		  </p>
	 
	<?php if ($petitontx->Fields("addressedto") != NULL) {?><p><B><span class="bodystrong">To:</span> <span class="text"> 
	  <?php echo $petitontx->Fields("addressedto")?>
	  </span></B></p><?php } ?>
	<p class="text"> 
	  <?php echo converttext( $petitontx->Fields("text")) ?>
	</p><?php if ($petitontx->Fields("intsigner") != NULL) {?>
	<p><B><span class="bodystrong">Initiated By:</span>  
	  <?php echo $petitontx->Fields("intsigner")?>
	  , 
	  <?php echo $petitontx->Fields("org")?>
	  <a href="http://<?php echo $petitontx->Fields("url");?>"> 
	  <?php echo $petitontx->Fields("url");?>
	  </a><br>
	  <?php echo $petitontx->Fields("intsignerad")?>
	  <a href="mailto:<?php echo $petitontx->Fields("intsignerem")?>"> 
	  <?php echo $petitontx->Fields("intsignerem")?>
	  </a></span></B></p><?php } ?><br>
	<?php
	
	
	}
	
	
	// Fetch the form instance specified by submitted modin value.
	$udm = new UserDataInput( $dbcon, $petmod );
	
	// User ID.
	$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
	$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;
	
	// Was data submitted via the web?
	$sub = isset($_REQUEST['btnUdmSubmit']);
	
	// Check fo rduplicates, setting $uid if found.
	if ( !$uid ) {
		$uid = $udm->findDuplicates();
	} 
	
	// Check for authentication, sending authentication mail if necessary.
	if ( $uid ) {
		// Set authentication token if uid present
		$auth = $udm->authenticate( $uid, $otp );
	}
	
	// Fetch or save user data.
	if ( ( !$uid || $auth ) && $sub ) {
		// Save only if submitted data is present, and the user is
		// authenticated, or if the submission is anonymous (i.e., !$uid)
		$udm->saveUser();
		//echo "<p class=title>Thank you for signing this petition.</p><br><br><br><br><br>";
	} elseif ( $uid && $auth && !$sub ) {
		// Fetch the user data for $uid if there is no submitted data
		// and the user is authenticated.
		$udm->submitted = false;
		$udm->getUser( $uid ); 
	}
			
	
	
	//$_REQUEST['modin'] =$petmod  ;
	//die($_REQUEST['modin']);
	
	
	$options['frmAction'] ="petition.php?pid=".$pid;
	
	if (!$_REQUEST['btnUdmSubmit']) {
		echo '<p class="title">Sign Petition</p>';
	}
	print $udm->output();
	#$udm->registerPlugin ('Output', 'html', $options) ;
	#$out=$udm->doPlugin( 'Output', 'html', $options );
	#print $out;
	
	
	if ($_REQUEST["signers"]  or $_REQUEST['btnUdmSubmit']) {
		echo petition_signers($petmod,$limit=25);
	}
}
else {




}

include_once("AMP/BaseFooter.php");
?>
