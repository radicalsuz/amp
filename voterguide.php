<?php

$modid = 109;
$mod_id = 74;

require_once("AMP/BaseDB.php"); 
require_once("AMP/BaseTemplate.php"); 
require_once("AMP/BaseModuleIntro.php");
require_once( "Modules/VoterGuide/VoterGuide.php");
require_once( "Modules/VoterGuide/SetDisplay.inc.php");

$currentPage = &AMPContent_Page::instance( );

if ( isset( $_GET['id']) && $_GET['id']) {
    $guide = &new VoterGuide( $dbcon, $_GET['id']);
    $currentPage->contentManager->addDisplay( $guide->getDisplay( ));
} else {
    $display = &new VoterGuideSet_Display( $dbcon );
    $currentPage->contentManager->addDisplay( $display );
}

include("AMP/BaseFooter.php"); 
/*
function vg_postition($can,$pos,$reason=NULL) {
		$position = "<p><i>Candidate/Ballot Item</i>:&nbsp;<B>$can</b><br><i>Position:</i>&nbsp;<B>$pos</B><br><i>Reason:</i>&nbsp;$reason</p><BR>";
		return $position;
}

function vg_list($id,$state,$name,$city,$date,$intro) {
	$layout = "<p><span class='eventtitle'><a href='voterguide.php?detail=$id' class='vguidelink1'>$name</a><BR> <b>$city, $state<BR> $date</span> </b><br> $intro<br><center><span style='text-align: center; font-style:italic;'><a href='voterguide.php?detail=$id' class='vguidelink2'>view guide</a></span></center><P>&nbsp;<P>";
	return $layout;
}



function vg_detail($R) { 
	global $dbcon;
	$L= $dbcon->Execute("select * from voterguide where guide_id =".$_GET['detail']." ORDER BY textorder");
	echo "<P class=title>".$R->Fields("custom1")."<br><span class=subtitle>".$R->Fields("custom2").", ".$R->Fields("State").", ".$R->Fields("custom3")."</span></p>";
	//echo "<table width=\"100%\" border=\"0\"><tr><td align=\"right\">";
	if ($R->Fields("MI")) {echo "<a href ='downloads/".$R->Fields("MI")."'>Download the Voter Guide as a PDF</a><br>"; }
	else { echo "<a href=\"voterguide.php?detail=".$_GET[detail]."&printsafe=1\">Printer Safe Voter Guide</a><br>";}
	//echo "</td></tr></table>";
	if ($R->Fields("Suffix")) {echo "<br><a href ='modinput4.php?modin=".$R->Fields("Suffix")."' class=\"joinlink\">JOIN THIS VOTER BLOC -- ENDORSE THIS VOTER GUIDE!</a><br>"; }
	echo "<p>".$R->Fields("custom4")."</p><BR><BR>";
	if ($L->Fields("id") ) {
		while (!$L->EOF) {
			echo vg_postition($L->Fields("item"),$L->Fields("position"),$L->Fields("reason"));
		$L->MoveNext();
		}
	}
	if ($R->Fields("Suffix")) {echo "<a href ='modinput4.php?modin=".$R->Fields("Suffix")."' class=\"joinlink\">JOIN THIS VOTER BLOC -- ENDORSE THIS VOTER GUIDE!</a><br>"; }

}

if ($_GET[detail]) {
	$sql = "select * from userdata where id = ".$_GET['detail'];
	$d=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
	vg_detail($d);
}

else {
	if ($_GET['area']) {
		$st=$dbcon->CacheExecute("select state, statename from states where id = ".$_GET['area'])or DIE($dbcon->ErrorMsg());
		$where = " and State ='".$st->Fields("state")."'";
		$statetitle = "<p class=title>".$st->Fields("statename")."</p>";
	}
	$sql = "select id, State, custom1, custom2, custom3, custom4 from userdata where publish =1 and modin =52 $where order by State asc";
	$d=$dbcon->CacheExecute("$sql")or DIE($sql.$dbcon->ErrorMsg());
	echo $statetitle;
	while (!$d->EOF) {
		echo vg_list($d->Fields("id"),$d->Fields("State"),$d->Fields("custom1"),$d->Fields("custom2"),$d->Fields("custom3"),$d->Fields("custom4"),$d->Fields("custom5"));
		$d->MoveNext();
	}
	if ($d->RecordCount() ==0) {echo "<p>There are no voter guides in this area. Why don't you <a href=\"voterguide_add.php?modin=52\">create one</a>...?&nbsp;--&nbsp;&nbsp;<a href=\"article.php?id=34\">(how?)</a>";} 
	else {
		echo "<p><a href=\"voterguide_add.php?modin=52\">Create a local voter guide</a>&nbsp;--&nbsp;&nbsp;<a href=\"article.php?id=34\">(how?)</a></P>";
	}
}

 include("AMP/BaseFooter.php"); 
 */
?>
