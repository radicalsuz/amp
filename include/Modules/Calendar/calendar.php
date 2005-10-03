<?php 
/*********************
05-06-2003  v3.01
Module:  Calendar
Description:  displays the calendar
CSS: go, title, text, eventsubtitle, eventtitle, 
To Do: 

*********************/ 

$mod_id = 57;
$modid = 1;
require_once("AMP/BaseDB.php"); 
require_once("AMP/BaseTemplate.php"); 
require_once("AMP/BaseModuleIntro.php");

//$bydate= $_GET[bydate];
//$caltype = $caltype;
//$area = $_GET['area'];
//$calid = $_GET['calid'];
if (isset($_GET['area'])) {
	if ($nonstateregion !=1) {
		$sqlarea = " and lstate = ".$_GET['area']." " ;
		$area=$dbcon->CacheExecute("SELECT statename from states where id = ".$_GET['area']."") or DIE($dbcon->ErrorMsg());
	} else {  
		$area=$dbcon->CacheExecute("SELECT title as statename from region where id = ".$_GET['area']."") or DIE($dbcon->ErrorMsg());
		$sqlarea = " and region = ".$_GET['area']." " ;
	}
}

if (isset($caltype)  and ($caltype != "By Event Type") and ($caltype != "student")) {
	$sqltype = "and typeid = $caltype";
}

if ($caltype == "student") {
	$sqltype = "and student = 1";
}

if (1 == $_REQUEST['old']) {
	$sqldate = " and calendar.date <=  CURDATE()" ;
} else if ("all" == $_REQUEST['old']) {
	$sqldate = "" ;
} else if ($caltype == "8") {
	$sqldate = "" ;
} else {
	$sqldate = " and calendar.date >=  CURDATE()" ;
}
 
$repeatvar = "repeat = 0 and";
if (isset($_GET['calid'])) {
	$sqlid = "and calendar.id = $calid" ;
	$sqldate = " " ;
	$repeatvar ="";
}
 
if (isset($bydate) and ($bydate != "By Date (ex 01-28-03)")) {
	if ((ereg ("([0-9]{1,2})-([0-9]{1,2})-([0-9]{2})", $bydate, $regs)) ) {
		$bydate2 = $bydate;
		$bydate = "$regs[3]$regs[1]$regs[2]";
	}
	$sqldate = "and calendar.date = $bydate" ;
	$sqldate2 =$sqldate;
}

$event=$dbcon->CacheExecute("SELECT eventtype.name, calendar.id, calendar.shortdesc  ,calendar.contact1  , calendar.event ,calendar.time ,calendar.date ,calendar.fulldesc ,calendar.email1 ,calendar.location ,calendar.org ,calendar.url ,calendar.typeid ,calendar.lcity ,calendar.lstate, calendar.lzip, calendar.phone1, calendar.laddress ,calendar.lcountry, states.statename  FROM calendar, states, eventtype where calendar.lstate = states.id and calendar.typeid = eventtype.id and  $repeatvar publish=1 $sqldate  $sqlarea $sqltype $sqlid order by calendar.date asc") or DIE($dbcon->ErrorMsg());

$revent=$dbcon->CacheExecute("SELECT eventtype.name, calendar.id, calendar.shortdesc, calendar.event ,calendar.time ,calendar.date ,calendar.typeid ,calendar.lcity ,calendar.lcountry, states.statename  FROM calendar, states, eventtype where calendar.lstate = states.id and calendar.typeid = eventtype.id and repeat=1 and publish=1 $sqldate2 $sqlarea  $sqltype $sqlid order by calendar.date asc") or DIE($dbcon->ErrorMsg());

$event_numRows=0;
$event__totalRows=$event->RecordCount();
$revent_numRows=0;
$revent__totalRows=$revent->RecordCount();

$typelist=$dbcon->CacheExecute("SELECT id, name from eventtype order by name asc");

if ($searchon == 1) {
?>
 <form name="form1" method="post" action="calendar.php<?php if  (isset($_GET["area"])){?>?area=<?php echo $_GET['area']; }?>" class="go">Search the Calendar<br> &nbsp;&nbsp;&nbsp;<select name="caltype" id="bytype" class="go">
<option selected>By Event Type </option>
<?php while (!$typelist->EOF) { ?>
<option value="<?php echo $typelist->Fields("id"); ?>"><?php echo $typelist->Fields("name"); ?></option>
  <?php $typelist->MoveNext();}?>
  </option>
<?php if ($studenton == "1") { ?>
<option value="student">Student</option>
<?php } ?>
  </select>
  &nbsp; or&nbsp;&nbsp; 
  <input name="bydate" type="text" id="bydate" value="By Date (ex 01-28-03)" size="25" class="go">
  <input name="Search" type="submit" id="Search" value="Search" class="go">
     
</form>
<p> 
<?php } ?>
  <?php if  (isset($_GET["area"])) { ?>
  <span class="title"><?php echo $area->Fields("statename"); ?> Events</span><br>
  <br>
  <?php
$area->Close();}
if  (isset($caltype) and ($caltype == "student")) { ?><span class="title">Student Events</span><br>
  <br><?php }
  elseif  (isset($caltype) && ($caltype != "By Event Type")) { ?><span class="title"><?php echo $event->Fields("name"); ?></span><br>
  <br><?php }
    elseif  (isset($bydate) && ($bydate != "By Date (ex 01-28-03)")) { ?><span class="title"><?php echo "Events On ".$bydate2 ; ?></span><br>
  <br> <?php }


 if  (isset($_GET["area"])) { //start area called
 if (($revent__totalRows == 0) && ($event__totalRows == 0)){ //start failed area called?>
</p>
<p class="text">There are currently no events planned in this area.</p>
<?php }//end failed area called
 } //end areacalled?>
 
 
<?php if  (isset($caltype) && ($caltype != "By Event Type")) { //start type called
if ($event->Fields("event") == ($null)) { //start failed type called?>
<p class="text">There are currently no events of this type planned.</p>
<?php }//end failed type called
 } //end typecalled
//start calendar 
####################################called event#######################################
if (isset($calid)) { ?>
<p><span class="title"><?php echo $event->Fields("event")?></span><br>
 <span class="eventsubtitle"><?php echo DoDate( $event->Fields("date"), 'l, F jS Y') ?>&nbsp;<?php echo $event->Fields("time")?> <br>
 <?php echo $event->Fields("lcity")?>,&nbsp;<?php echo $event->Fields("statename")?> </span><br><br>
  <span class="text"> <?php if (($event->Fields("shortdesc") != ($null)) && ($event->Fields("fulldesc") == ($null))) { ?>
  <?php echo converttext( $event->Fields("shortdesc"));?><br><?php }?>
  <?php echo (converttext($event->Fields("fulldesc"))); ?><br>
  <?php if ($event->Fields("location") != ($null)) { ?>
  <br><br><b>Location:&nbsp;</b><br><?php echo $event->Fields("location")?>&nbsp;<?php echo $event->Fields("laddress")?>&nbsp;<?php echo $event->Fields("lcity")?>&nbsp;<?php echo $event->Fields("statename")?>&nbsp;<?php echo $event->Fields("lzip")?>&nbsp; 
  <?php }?>
  <?php if (($event->Fields("contact1") != ($null)) or ($event->Fields("phone1") != ($null)) or ($event->Fields("email1") != ($null)) ) { ?>
  <br><br>
  <b>Contact:</b>
  <?php }?>
  <?php if ($event->Fields("contact1") != ($null)) { ?><br><?php echo $event->Fields("contact1")?><?php } ?>
  <?php if ($event->Fields("email1") != ($null)) { ?><br><a href="mailto:<?php echo $event->Fields("email1")?>"> 
  <?php echo $event->Fields("email1")?></a> <?php } ?>
   <?php if ($event->Fields("phone1") != ($null)) { ?><br><?php echo $event->Fields("phone1")?><?php } ?> 
   
  <?php if ($event->Fields("org") != ($null)) { ?>
    <br><br><b>Sponsored By:</b><br><?php echo $event->Fields("org")?> 
  <?php }?>
  <?php if (($event->Fields("url") != ($null)) and ($event->Fields("url") != ("http://")))  { ?>
  <a href="<?php echo $event->Fields("url")?>"><?php echo $event->Fields("url")?></a> 
  <?php }?>
  </span></p>
<?php } 
###############################sorted by area ################################################
elseif (isset($area)) { 
 
 $eventcountry=$dbcon->CacheExecute("SELECT distinct calendar.lcountry  FROM calendar  where repeat!=1 and publish=1 $sqldate $sqlarea  $sqltype $sqlid  order by calendar.lcountry asc") or DIE($dbcon->ErrorMsg());
   while (!$eventcountry->EOF) 
   { 
     $calledcountry = $eventcountry->Fields("lcountry");
   if ( $calledcountry != "USA") {  
   echo "<br><b><big><font color=red>".$eventcountry->Fields("lcountry")."</font></big></b><br>";
 }
 
 
   $eventcity=$dbcon->CacheExecute("SELECT distinct calendar.lcity, calendar.lcountry FROM calendar  where repeat!=1 and publish=1  $sqldate $sqlarea  $sqltype $sqlid and calendar.lcountry='$calledcountry' order by calendar.lcountry, calendar.lcity asc") or DIE($dbcon->ErrorMsg());
   
       while (!$eventcity->EOF) 
   { 
   $calledcity = $eventcity->Fields("lcity");
   $event2=$dbcon->CacheExecute("SELECT eventtype.name , calendar.id,
calendar.shortdesc, calendar.event ,calendar.time ,calendar.date ,calendar.typeid ,calendar.lcity ,calendar.lcountry,  calendar.lstate, states.statename   FROM calendar, states, eventtype where calendar.lstate = states.id and calendar.typeid = eventtype.id and $repeatvar publish=1 $sqldate  $sqlarea $sqltype $sqlid and calendar.lcity = '".$calledcity."'  order by calendar.date asc") or DIE($dbcon->ErrorMsg());

   echo "<br><b><big>".$event2->Fields("lcity");
     if ($event2->Fields("lstate") == "53") { echo ",&nbsp;".$event2->Fields("lcountry");  }
	  echo "</big></b><br>";
   while (!$event2->EOF) 
   { 
?>
<br><a href="calendar.php?calid=<?php echo $event2->Fields("id")?>" class="eventtitle"><?php echo $event2->Fields("event")?></a><br>
 </b>
  <span class="eventsubtitle"><?php echo DoDate( $event2->Fields("date"), 'l, F jS Y') ?>&nbsp;<?php echo $event2->Fields("time")?></span> 
  <span class="text"> 
  <?php if ($event2->Fields("shortdesc") != (NULL)) { ?>
  <br>
  <?php echo converttext( $event2->Fields("shortdesc")); }?></span><br>
<?php

  $event2->MoveNext();
} 
  $eventcity->MoveNext();
} 
   $eventcountry->MoveNext();
}  ?>
<?php if ($revent__totalRows != 0 ){?><h3>Weekly, Monthly or other Repeating Events</h3><?php } ?>
<?php 
$reventcountry=$dbcon->CacheExecute("SELECT distinct calendar.lcountry  FROM calendar  where repeat=1 and publish=1 $sqldate2 $sqlarea  $sqltype $sqlid  order by calendar.lcountry asc") or DIE($dbcon->ErrorMsg());
    while (!$reventcountry->EOF) 
   { 
     $rcalledcountry = $reventcountry->Fields("lcountry");
   if ( $rcalledcountry != "USA") {  
   echo "<br><b><big><font color=red>".$reventcountry->Fields("lcountry")."</font></big></b><br>";
 }
   $reventcity=$dbcon->CacheExecute("SELECT distinct calendar.lcity, calendar.lcountry FROM calendar  where repeat=1 and publish=1 and calendar.lcountry='$rcalledcountry' $sqldate2 $sqlarea  $sqltype $sqlid order by calendar.lcountry, calendar.lcity asc") or DIE($dbcon->ErrorMsg());
   
    while (!$reventcity->EOF) 
   { 
   $rcalledcity = $reventcity->Fields("lcity");
    echo "<br><b><big>".$rcalledcity."</big></b><br>";
   $revent2=$dbcon->CacheExecute("SELECT eventtype.name, calendar.id,
calendar.shortdesc, calendar.event ,calendar.time ,calendar.date ,calendar.typeid ,calendar.lcity ,calendar.lcountry,  states.statename  FROM calendar, states, eventtype where calendar.lstate = states.id and calendar.typeid = eventtype.id and repeat=1 and publish=1 $sqldate2 $sqlarea  $sqltype $sqlid and calendar.lcity = '".$rcalledcity."'  order by calendar.date asc") or DIE($dbcon->ErrorMsg());
  
   while (!$revent2->EOF) 
   { 
   
?>
<br><a href="calendar.php?calid=<?php echo $revent2->Fields("id")?>" class="eventtitle"><?php echo $revent2->Fields("event")?></a><br>
 </b>
  <span class="eventsubtitle"><?php echo $revent2->Fields("time")?></span> 
  <span class="text"> 
  <?php if ($revent2->Fields("shortdesc") != (NULL)) { ?>
  <br>
  <?php echo converttext( $revent2->Fields("shortdesc")); }?></span><br>
<?php
 
  $revent2->MoveNext();
}
$reventcity->MoveNext();
} 
  $reventcountry->MoveNext();
} 
}
###############################DEFUALT LAYOUT #############################################
else {
 while (!$event->EOF) 
   { 
?>
<br><a href="calendar.php?calid=<?php echo $event->Fields("id")?>" class="eventtitle"><?php echo $event->Fields("lcity")?>,&nbsp;<?php echo $event->Fields("statename")?>: <?php echo $event->Fields("event")?></a><br>
 </b>
  <span class="eventsubtitle"><?php echo DoDate( $event->Fields("date"), 'l, F jS Y') ?>&nbsp;<?php echo $event->Fields("time")?></span> 
  <span class="text"> 
  <?php if ($event->Fields("shortdesc") != (NULL)) { ?>
  <br>
  <?php echo converttext( $event->Fields("shortdesc")); }?></span><br>
<?php

  $event->MoveNext();
} ?>
<?php if ($revent__totalRows != 0 ){?><h3>Weekly, Monthly or other Repeating Events</h3><?php } ?>
<?php while (!$revent->EOF) 
   { 
   
?>
<br><a href="calendar.php?calid=<?php echo $revent->Fields("id")?>" class="eventtitle"><?php echo $revent->Fields("lcity")?>,&nbsp;<?php echo $revent->Fields("statename")?>: <?php echo $revent->Fields("event")?></a><br>
 </b>
  <span class="eventsubtitle"><?php echo $revent->Fields("time")?></span> 
  <span class="text"> 
  <?php if ($revent->Fields("shortdesc") != (NULL)) { ?>
  <br>
  <?php echo converttext( $revent->Fields("shortdesc")); }?></span><br>
<?php
 
  $revent->MoveNext();
}
   
}//end not called
#################################################################################################

  $event->Close();
  $revent->Close();
   
 require_once("AMP/BaseFooter.php"); ?>
