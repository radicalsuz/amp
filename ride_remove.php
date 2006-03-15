<?php
$modid = 2;
$mod_id = 19;
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 
include_once("AMP/System/Email.inc.php");
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
$messagetext = "To remove your listing simply visit this page ".$Web_url."ride_remove2.php?email=$email and press the remove button";
   mail ( "$email", "remove your ride posting", "$messagetext", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\nX-Mailer: My PHP Script\n"); 
   header ("Location: ride_remove.php?show=yes");
}
   

?><?php
   $emails=$dbcon->Execute("SELECT *  FROM ride  where publish='1' and board='2'  ORDER BY email ASC") or DIE($dbcon->ErrorMsg());
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
  from the ride board. </p>
<p>Thank You! </p>
<p><a href="ride.php">Return to the Ride Board</a><br>
</p>
<?php }
/* if ($HTTP_GET_VARS["show"] == ("yes")) */
?>

<?php
  $emails->Close();
?>
<?php include("AMP/BaseFooter.php"); ?>
