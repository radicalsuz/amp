<?php 
$ddtype=$dbcon->CacheExecute("SELECT id, type FROM articletype WHERE usenav = 1 ORDER BY textorder, type asc") or DIE($dbcon->ErrorMsg());
	$ddtype_numRows=0;
   	$ddtype__totalRows=$ddtype->RecordCount();

   	$Repeat1__numRows = -1;
   	$Repeat1__index= 0;
   	$ddtype_numRows = $ddtype_numRows + $Repeat1__numRows;
	$Repeat2__numRows = -1;
  	$Repeat2__index= 0;
   	$nested_numRows = $nested_numRows + $Repeat1__numRows; ?>

<form ><select name="nav" onChange="MM_jumpMenu('parent',this,0)">
    <option SELECTED>Quick Navigation</option>
	<option>------------</option>
	<?php while (($Repeat1__numRows-- != 0) && (!$ddtype->EOF)) 
   { ?>
   <option value="article.php?list=type&type=<?php echo $ddtype->Fields("id") ?>"><?php echo $ddtype->Fields("type") ?></option>
   <?php $typedd=$ddtype->Fields("id");
   $nested=$dbcon->CacheExecute("SELECT subname, id  FROM articlesubtype  Where typeid=$typedd   and usenav = 1 order by textorder, subname asc") or DIE($dbcon->ErrorMsg());
   $nested_numRows=0;
   $nested__totalRows=$nested->RecordCount();
while (($Repeat2__numRows-- != 0) && (!$nested->EOF)) 
   { ?>
   <option value="article.php?list=sub&sub=<?php echo $nested->Fields("id")?>">&nbsp;&nbsp;&nbsp;<?php echo $nested->Fields("subname") ?></option>
      
 <?php $Repeat2__index++;
  $nested->MoveNext();
}
   
     $Repeat1__index++;
  $ddtype->MoveNext();
}
?>
<option value="contactus.php">Contact Us</option>
<option value="article.php?id=160">Donate</option>
<option value="trainers.php">Trainers Network</option>
<option value="calendar.php">Events</option>


</select></form>