<?php

   $petiton=$dbcon->CacheExecute("SELECT * FROM petition where id = $id") or DIE($dbcon->ErrorMsg());

   $petiton_numRows=0;

   $petiton__totalRows=$petiton->RecordCount();

?>
 
      <p class="title"> 
        <?php echo $petiton->Fields("title")?>
      </p>
 
<p><B><span class="bodystrong">To:</span> <span class="text"> 
  <?php echo $petiton->Fields("addressedto")?>
  </span></B></p>
<p class="text"> 
  <?php echo converttext( $petiton->Fields("text")) ?>
</p>
<p><B><span class="bodystrong">Initiated By:</span>  
  <?php echo $petiton->Fields("intsigner")?>
  , 
  <?php echo $petiton->Fields("org")?>
  <a href="http://<?php echo $petiton->Fields("url")?>"> 
  <?php echo $petiton->Fields("url")?>
  </a><br>
  <?php echo $petiton->Fields("intsignerad")?>
  <a href="mailto:<?php echo $petiton->Fields("intsignerem")?>"> 
  <?php echo $petiton->Fields("intsignerem")?>
  </a></span></B></p><br>

      <p class="title">Sign The Petition</p>
  <hr>
<?php

  $petiton->Close();

?>
