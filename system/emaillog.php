<?php
$modid=9;
  require("Connections/freedomrising.php");
  error_reporting  (E_ALL);

function lastmonth($add){
global $dbcon;
if ($add == '1') {$asql= "and eadd = 1";}
if ($add == '2') {$asql= "and remove = 1";}
$dsql = "TO_DAYS(NOW()) - TO_DAYS(date) <= 30";
$lastmonth=$dbcon->Execute("SELECT * FROM emaillog WHERE $dsql $asql") or DIE($dbcon->ErrorMsg());
$count = $lastmonth->RecordCount();;
return $count;
}

function namedate($add, $dsql){
global $dbcon;
if ($add == '1') {$asql= "and eadd = 1";}
if ($add == '2') {$asql= "and remove = 1";}
$lastmonth=$dbcon->Execute("SELECT * FROM emaillog WHERE $dsql $asql") or DIE($dbcon->ErrorMsg());
$count = $lastmonth->RecordCount();;
return $count;
}
$january = "MONTH(date) =1"; 
$lastweek ="TO_DAYS(NOW()) - TO_DAYS(date) <= 7";
$yesterday = "TO_DAYS(NOW()) - TO_DAYS(date) >= 1";
$today = "TO_DAYS(NOW()) - TO_DAYS(date) <= 1" ;
$lasthour = "HOUR(date) =  HOUR(DATE_SUB(NOW(),INTERVAL 1 HOUR))  and CURDATE() = date" ;
$thishour = "HOUR(date) = HOUR(date) and CURDATE() = date";
//$lasthour = "HOUR(date) =  HOUR(DATE_SUB(NOW(),INTERVAL 1 HOUR))" ;


$time2=$dbcon->Execute("SELECT CURDATE() as time") or DIE($dbcon->ErrorMsg());
//$time=$dbcon->Execute("SELECT HOUR(date) as time from emaillog") or DIE($dbcon->ErrorMsg());
//$timen = $time2->Fields("time");
//$time=$dbcon->Execute("SELECT HOUR(DATE_SUB(NOW(),INTERVAL 1 HOUR)) as time") or DIE($dbcon->ErrorMsg());
 ?> 
 <?php //echo $time->Fields("time")?>
 <?php echo $time2->Fields("time")?>
<table width="80%" border="1" cellspacing="0" cellpadding="1">
  <tr> 
    <td>Time Frame</td>
    <td># of new subscribers</td>
    <td># of removed subscribers</td>
  </tr>
  <tr> 
    <td>January</td>
    <td><?php echo namedate(1,$january)?></td>
    <td><?php echo namedate(2,$january)?></td>
  </tr>
  <tr> 
    <td>Last 30 days</td>
    <td><?php echo lastmonth(1)?></td>
    <td><?php echo lastmonth(2)?></td>
  </tr>
  <tr> 
    <td>Last Hour</td>
    <td><?php echo namedate(1,$lasthour)?></td>
    <td><?php echo namedate(2,$lasthour)?></td>
  </tr>
  <tr> 
    <td>Yesterday</td>
    <td><?php echo namedate(1,$yesterday)?></td>
    <td><?php echo namedate(2,$yesterday)?></td>
  </tr>
  <tr> 
    <td>last 60 minuets</td>
    <td><?php // echo namedate(1,$60min)?></td>
    <td><?php //echo namedate(2,$60min)?></td>
  </tr>
  <tr> 
    <td>This Hour</td>
    <td><?php echo namedate(1,$thishour)?></td>
    <td><?php echo namedate(2,$thishour)?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
