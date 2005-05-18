<?php
$mod_name="email";
require_once("Connections/freedomrising.php");
require_once("WYSIWYG/FCKeditor/fckeditor.php");
$buildform = new BuildForm;

include("AMP/Blast/Reporting.php");

include ("header.php");

echo "<h2>Message Report</h2>";
echo blast_report($_REQUEST['blast_ID']);
echo '<br><a href="blast_control.php?type=Email&blast_failed='.$_REQUEST['blast_ID'].'">Resend Failed</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
echo '<a href="blast_control.php?type=Email&blast_bounced='.$_REQUEST['blast_ID'].'">Resend Bounced</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
echo '<a href="blast_control.php?type=Email&blast_stale='.$_REQUEST['blast_ID'].'">Resend Stale</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
echo '<a href="blast_control.php?type=Email&blast_new='.$_REQUEST['blast_ID'].'">Reset Blast as New</a><br>';

//delivery_chart($blast_ID);
echo "<h2>Open Rate</h2>";
echo open_rate($_REQUEST['blast_ID']);
echo "<h2>Message Details</h2>";
echo blast_details($blast_ID);

include ("footer.php");
?>
