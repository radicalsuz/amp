<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  housing remove page  
GET VARS show
SYS VARS  $MM_email_from
To Do:  
				
*********************/ 
$modid = 3;
$mod_id = 19;
include("sysfiles.php");
include("header.php"); 
include("dropdown.php"); 
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
?><?php
// *** Insert Record: set Variables

if (isset($MM_insert)){
$messagetext = "To remove your listing simply visit this page ".$Web_url."housing_remove2.php?email=".$HTTP_POST_VARS["email"]." and press the remove button";
   mail ( $HTTP_POST_VARS["email"], "remove your housing posting", "$messagetext", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
   header ("Location: housing_remove.php?show=yes");
}
   

?><?php
   $emails=$dbcon->Execute("SELECT *  FROM housing  where publish='1' and board='2'  ORDER BY email ASC") or DIE($dbcon->ErrorMsg());
   $emails_numRows=0;
   $emails__totalRows=$emails->RecordCount();
?>


<p>Select your e-mail</p>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
  <select name="email">
    <?php
  if ($emails__totalRows > 0){
    $emails__index=0;
    $emails->MoveFirst();
    WHILE ($emails__index < $emails__totalRows){
?>
    <OPTION VALUE="<?php echo $emails->Fields("email")?>"> 
    <?php echo $emails->Fields("email");?>
    </OPTION>
    <?php
      $emails->MoveNext();
      $emails__index++;
    }
    $emails__index=0;  
    $emails->MoveFirst();
  }
?>
  </select>
  <input type="submit" name="submit" value="go!">
  <input type="hidden" name="hiddenField" value="1">
  <input type="hidden" name="MM_insert" value="true">
</form>
<p>&nbsp;</p>
<?php if ($HTTP_GET_VARS["show"] == ("yes")) { ?>
<p> An E-mail has been sent to you with instructions on how to remove yourself 
  from the housing board. </p>
<p>Thank You! </p>
<p><a href="housing.php">Return to the Housing Board</a><br>
</p>
<?php }
/* if ($HTTP_GET_VARS["show"] == ("yes")) */
?>

<?php
  $emails->Close();
?>
<?php include("footer.php"); ?>