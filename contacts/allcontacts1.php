<?php
     
  
  require_once("Connections/freedomrising.php");  


 $allusers=$dbcon->Execute("SELECT distinct users.id, users.name, contacts2.enteredby FROM users, contacts2 where contacts2.enteredby =users.id and contacts2.enteredby  is not null ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $allusers_numRows=0;
   $allusers__totalRows=$allusers->RecordCount(); 
   
$alltypes=$dbcon->Execute("SELECT distinct contacts_class.id, contacts_class.title, contacts2.classid FROM contacts_class, contacts2 where contacts2.classid =contacts_class.id  and contacts2.classid  is not null ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $alltypes_numRows=0;
   $alltypes__totalRows=$alltypes->RecordCount();
   
   $source=$dbcon->Execute("SELECT distinct source.id, source.title, contacts2.source FROM source, contacts2 where contacts2.source =source.id and contacts2.source  is not null ORDER BY source.title ASC") or DIE($dbcon->ErrorMsg());
   $source_numRows=0;
   $source__totalRows=$source->RecordCount();

$region=$dbcon->Execute("SELECT distinct region.id, region.title, contacts2.regionid FROM region, contacts2 where contacts2.regionid =region.id and contacts2.regionid  is not null ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $region_numRows=0;
   $region__totalRows=$region->RecordCount();
    ?>
   <?php
   $Repeat2__numRows = -1;
   $Repeat2__index= 0;
   $allusers_numRows = $allusers_numRows + $Repeat2__numRows;
   $Repeat3__numRows = -1;
   $Repeat3__index= 0;
   $alltypes_numRows = $alltypes_numRows + $Repeat3__numRows;
   $Repeat4__numRows = -1;
   $Repeat4__index= 0;
   $source_numRows = $source_numRows + $Repeat4__numRows;
   $Repeat5__numRows = -1;
   $Repeat5__index= 0;
   $region_numRows = $region_numRows + $Repeat5__numRows;
?>
 <?php include("header.php"); ?>
<p></p>
<table width="90%" border="0" align="center" cellspacing="3">
  <tr> 
    <td width="50%"><h3><a href="allcontacts.php">View All Contacts</a></h3> </td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td width="50%" class="toplinks">View by Entered By</td>
    <td class="toplinks">View by Type</td>
  </tr>
  <tr> 
    <td width="50%" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="0">
       <?php while (($Repeat2__numRows-- != 0) && (!$allusers->EOF)) 
   { 
?>
	    <tr class="results"> 
          <td><a href="allcontacts.php?&enteredby=<?php echo $allusers->Fields("id")?>"><?php echo $allusers->Fields("name")?></a></td>
        </tr>
		<?php
  $Repeat2__index++;
  $allusers->MoveNext();
}
?>
      </table></td>
    <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="0">
        <?php while (($Repeat3__numRows-- != 0) && (!$alltypes->EOF)) 
   { 
?>
		<tr class="results"> 
          <td><a href="allcontacts.php?&type=<?php echo $alltypes->Fields("id")?>"><?php echo $alltypes->Fields("title")?></a></td>
        </tr>
			<?php
  $Repeat3__index++;
  $alltypes->MoveNext();
}
?>
      </table></td>
  </tr>
  <tr>
  <tr> 
    <td width="50%" class="toplinks">View By Source</td>
    <td class="toplinks">View by Region</td>
  </tr>
  <tr> 
    <td width="50%" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="0">
       <?php while (($Repeat4__numRows-- != 0) && (!$source->EOF)) 
   { 
?>
	    <tr class="results"> 
          <td><a href="allcontacts.php?&source=<?php echo $source->Fields("id")?>"><?php echo $source->Fields("title")?></a></td>
        </tr>
		<?php
  $Repeat4__index++;
  $source->MoveNext();
}
?>
      </table></td>
    <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="0">
        <?php while (($Repeat5__numRows-- != 0) && (!$region->EOF)) 
   { 
?>
		<tr class="results"> 
          <td><a href="allcontacts.php?&region=<?php echo $region->Fields("id")?>"><?php echo $region->Fields("title")?></a></td>
        </tr>
			<?php
  $Repeat5__index++;
  $region->MoveNext();
}
?>
      </table></td>
  </tr>
    <td width="50%">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php include ("footer.php");?>

