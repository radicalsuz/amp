<?php
/*********************
10-30-2002  v3.01
Module:  Calendar
Description:  generates rss feed from calendar
To Do:  make work, it is busted

*********************/ 
require_once("AMP/BaseDB.php");

 $event=$dbcon->CacheExecute("SELECT calendar.*, states.statename, eventtype.name  FROM calendar LEFT JOIN states ON states.id = calendar.lstate left join eventtype on calendar.typeid=eventtype.id WHERE publish=1 and calendar.date > CURDATE()-1  order by calendar.date asc") or DIE($dbcon->ErrorMsg());
 ?>
<?php
   $Repeat2__numRows = 4;
   $Repeat2__index= 0;
   $event_numRows = $event_numRows + $Repeat2__numRows;
?>
<?php //header ("Content-Type: text/xml; charset=\"utf-8\"")?>
<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">"; ?>
<rdf:RDF
 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
 xmlns="http://purl.org/rss/1.0/"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:ev="http://purl.org/rss/1.0/modules/event/"
 xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
 xmlns:syn="http://purl.org/rss/1.0/modules/syndication/"
>
  <channel rdf:about="http://www.unitedforpeace.org/new/calendar.php">
    <title>United For Peace Events</title>
    <link>http://www.unitedforpeace.org/new/calendar.php</link>
    <description>Calendar of Peace Events</description>
    <items>
      <rdf:Seq>
<?php while (($Repeat2__numRows-- != 0) && (!$event->EOF)) 
   { 
?>
        <rdf:li rdf:resource="http://www.unitedoforpeace.org/new/calendar.php?id=<?php echo $event->Fields("id")?>" />
		<?php
  $Repeat2__index++;
  $event->MoveNext();
} ?>
      </rdf:Seq>
    </items>
  </channel>

<?php 
$Repeat2__numRows = 4;
   $Repeat2__index= 0;
   $event_numRows = $event_numRows + $Repeat2__numRows;
while (($Repeat2__numRows-- != 0) && (!$event->EOF)) 
   { 
?>
  <item rdf:about="http://www.unitedforpeace.org/new/calendar.php?id=<?php echo $event->Fields("id")?>">
    <title><?php echo htmlspecialchars($event->Fields("event"))?></title> 
    <link>http://www.unitedforpeace.org/new/calendar.php?id=<?php echo $event->Fields("id")?></link>
    <ev:type><?php echo $event->Fields("name")?></ev:type>
    <ev:organizer><?php echo $event->Fields("org")?></ev:organizer>
    <ev:location><?php echo $event->Fields("lcity")?>, <?php echo $event->Fields("statename")?> <?php echo $event->Fields("location")?></ev:location>
    <ev:startdate><?php echo $event->Fields("date")?></ev:startdate>
  </item> 
<?php
  $Repeat2__index++;
  $event->MoveNext();
} ?>
  </rdf:RDF>
