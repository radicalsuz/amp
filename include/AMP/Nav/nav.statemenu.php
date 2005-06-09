<?php $state=$dbcon->CacheExecute("SELECT * FROM region order by title asc") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();?>

<select name="lstate"  onChange="MM_jumpMenu('parent',this,0)" class="ddmenu">
    <option SELECTED>Your Region</option>
    <?php    
 while  (!$state->EOF) {
?>
    <option value="<?php echo (isset($regionlink)?$regionlink:"");  ?>?area=<?php echo  $state->Fields("id")?>" ><?php echo  $state->Fields("title");?> 
    </option>
    <?php
      $state->MoveNext();
     
    }
  ?>
