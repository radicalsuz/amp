<?php
##region stuf


#get calendar events

$mod_id = 61;
include("sysfiles.php");
include("header.php"); 
$area= $_GET[area]; 
$sql=" select * from articles WHERE state  = $area  and publish=1 and  Class =  8 Limit 1 ";
$sql = $sqlsel.$sql.$sqlorder.$sqloffset;
$rheader=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
 if ($rheader->Fields("id")){
 $MM_id = $rheader->Fields("id");
 include ("article.inc.php");
 }
 else {
 $rname=$dbcon->CacheExecute("select  title from region where id = $area ;")or DIE($dbcon->ErrorMsg());
echo "<p class= title>".$rname->Fields("title")."</p>";}

//events
 if ($nonstateregion !=1) {
  $sqlarea = " and lstate = ".$area." " ;
   }
   else {  
  $sqlarea = " and region = ".$area." " ;
   }
 $event=$dbcon->CacheExecute("SELECT eventtype.name, calendar.id, calendar.shortdesc  ,calendar.contact1  , calendar.event ,calendar.time ,calendar.date ,calendar.fulldesc ,calendar.email1 ,calendar.location ,calendar.org ,calendar.url ,calendar.typeid ,calendar.lcity ,calendar.lstate, calendar.lzip, calendar.phone1, calendar.laddress ,calendar.lcountry, states.statename  FROM calendar, states, eventtype where calendar.lstate = states.id and calendar.typeid = eventtype.id and   publish=1  and calendar.date >=  CURDATE()   $sqlarea  order by calendar.date asc ") or DIE($dbcon->ErrorMsg());
 if ($event->Fields("id")){
 echo "<hr><p class=subtitle>Events in this Region</p>";
 while (!$event->EOF) 
   { 
?>
<a href="calendar.php?calid=<?php echo $event->Fields("id")?>" class="eventtitle"><?php echo $event->Fields("lcity")?>,&nbsp;<?php echo $event->Fields("statename")?>: <?php echo $event->Fields("event")?></a><br>
 </b>
  <span class="eventsubtitle"><?php echo DoDate( $event->Fields("date"), 'l, F jS Y') ?>&nbsp;<?php echo $event->Fields("time")?></span> 
  <span class="text"> 
  <?php if ($event->Fields("shortdesc") != (NULL)) { ?>
  <br>
  <?php echo converttext( $event->Fields("shortdesc")); }?></span><br>
<?php

  $event->MoveNext();
} }


$sqlsel = "SELECT id, link, linkover, shortdesc, date, usedate, author, source, source, sourceurl, picuse, picture, title FROM articles ";
$sqlorder= "  Order by date desc, id desc ";
$sqloffset = " LIMIT 3 ";

$sql=" WHERE state  = $area  and publish=1 and  Class =  5 ";
$sql = $sqlsel.$sql.$sqlorder.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
 if ($list->Fields("id")){
 echo "<hr><p class= subtitle>Action Alerts</p>";
 include ("list.layout.inc.php");}
//groups
if (!$groupslayout) {$groupslayout="groups.layout.php";}
 if ($nonstateregion !=1) {
  $sqlarea = " and  moduserdata.State = ".$area." " ;
   }
   else {  
  $sqlarea = " and  moduserdata.region = ".$area." " ;
   }
 $groups=$dbcon->CacheExecute("SELECT moduserdata.*, states.state FROM moduserdata, states  WHERE moduserdata.publish = '1' and moduserdata.modinid=2  and states.id=moduserdata.State $sqlarea   ORDER BY moduserdata.Organization ASC ") or DIE($dbcon->ErrorMsg());  
  if ($groups->Fields("id")){
 echo "<hr><p class= subtitle>Local Groups</p>";
 while (!$groups->EOF)
   { 
include ("$groupslayout");
  $groups->MoveNext();
}}
//content
 
$sql="WHERE state = $area and publish=1 and  (class !=2 && class !=8 && class !=9 && class !=3 && class !=5)  ";
 $sql = $sqlsel.$sql.$sqlorder.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
 if ($list->Fields("id")){
 echo "<hr><p class= subtitle>Information</p>";
 include ("list.layout.inc.php");}
 
$sql="WHERE state = $area and publish=1 and  Class =  3 ";
 $sql = $sqlsel.$sql.$sqlorder.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
 if ($list->Fields("id")){
 echo "<hr><p class= subtitle>News</p>";
 include ("list.layout.inc.php");}
 
$sql="WHERE state = $area and publish=1 and  Class =  10" ;
 $sql = $sqlsel.$sql.$sqlorder.$sqloffset;
$list=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
 if ($list->Fields("id")){
 echo "<hr><p class= subtitle>Press Releases</p>";
 include ("list.layout.inc.php");}

include("footer.php"); 
?>