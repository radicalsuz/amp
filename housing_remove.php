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
$intro_id = 19;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

if (isset($_POST["MM_insert"])){
	$messagetext = "To remove your listing simply visit this page ".$Web_url."housing_remove2.php?email=".$_POST["email"]." and press the remove button";
	if ($_POST["email"]){
		mail ( $_POST["email"], "remove your housing posting", "$messagetext", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
		header ("Location: housing_remove.php?show=yes");
	}
}
   
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
<?php if ($_GET["show"] == ("yes")) { ?>
<p> An E-mail has been sent to you with instructions on how to remove yourself from the housing board. </p>
<p>Thank You! </p>
<p><a href="housing.php">Return to the Housing Board</a><br>
</p>
<?php }
include("AMP/BaseFooter.php"); 
?>