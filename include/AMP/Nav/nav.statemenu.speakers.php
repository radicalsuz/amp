<?php $state=$dbcon->CacheExecute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();?>

<select name="lstate" id="select4" onChange="MM_jumpMenu('parent',this,0)">
    <option SELECTED>Your State</option>
    <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
    <option value="speakers.php?area=<?php echo  $state->Fields("id")?>" ><?php echo  $state->Fields("statename");?> 
    </option>
    <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
  </select>
<?php $state->Close();?>