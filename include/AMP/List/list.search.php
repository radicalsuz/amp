<?php
  
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }
 // require_once("Connections/menu.class.php");
//$obj2 = new Menu; 
if  (isset($searchx)){

if  ($_POST[rel1y] != NULL) {
 $relsel ="article.php?list=rel1&type=".$MM_type."&rel1=".$_POST[rel1y]."";}
if  ($_POST[rel2y] != NULL) {
 $relsel ="article.php?list=rel2&type=".$MM_type."&rel2=".$_POST[rel2y]."";}
if  ($_POST[rel2y] != NULL && $_POST[rel1y] != NULL) {
 $relsel ="article.php?list=rel&type=".$MM_type."&rel2=".$_POST[rel2y]."&rel1=".$_POST[rel1y]."";}
if  ($_POST[authy] != NULL) {
 $relsel ="article.php?list=authort&type=".$MM_type."&author=".$_POST[authy]."";}
 header ("Location: $relsel");
}
  $authorl=$dbcon->CacheExecute("SELECT distinct  author FROM articles where $MX_type=$MM_type ORDER BY author ASC") or DIE($dbcon->ErrorMsg());
	if ( (isset($relsection1id)) or (isset($relsection1id)) or  ($authorl->Fields("author") != NULL) ) {
?>

<form ACTION="<?php echo $MM_editAction ?>" METHOD="POST">
<span class="text">Search content in this section by:</span><br>
<?php

  if (isset($relsection1id)) {?>

			<select name="rel1y">
	  <OPTION SELECTED value=""><?php echo $relsection1label ;?></option>
	   <?php echo $obj->select_type_tree($relsection1id); ?>
	  </Select>

	<?php } 
	 if (isset($relsection2id)) {
	
	?>

       <select name="rel2y">
                 <OPTION SELECTED  value=""><?php echo $relsection2label ;?></option>
                <?php echo $obj->select_type_tree($relsection2id); ?> </select>
  <?php }   
  
 
	 if   ($authorl->Fields("author") != NULL) {
  	?>
  <select name="authy">
                 <OPTION SELECTED  value="">Author</option>
      <?php         while  (!$authorl->EOF) {?>
			   <OPTION   value="<?php echo $authorl->Fields("author"); ?>"><?php echo $authorl->Fields("author"); ?></option>
		<?php	    $authorl->MoveNext();
}?>
			    </select><?php } ?>
  <input name="searchx" type="hidden" value="1">
  <input type="submit" name="search" value="Search">
</form>
<?php }?>
