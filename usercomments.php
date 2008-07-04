<?php  // 
/* Disabled for security reasons ap 2008-07
 */
/*

if  (isset($HTTP_GET_VARS["mod"])) {$mod_id = $mod ; }
else {$mod_id = 56;}
 if ($HTTP_GET_VARS["thank"] == ("1")) { 
	  $mod_id = 49 ;}
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");
include_once("AMP/System/Email.inc.php");

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";

?><?php
  // *** Insert Record: set variables
  
  if (isset($MM_insert)) {
  $source = $source1."&nbsp;&nbsp;".$source2;
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "articles";
    $MM_editRedirectUrl = "useraddarticle.php?thank=1";
	$date =  DateConvertIn($date);
    $MM_fieldsStr = "type|value|class|value|title|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|state|value|source|value|contact|value|subtype|value|catagory|value";
    $MM_columnsStr = "type|none,none,NULL|class|none,none,NULL|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|state|none,none,NULL|source|',none,''|contact|',none,''|subtype|none,none,NULL|catagory|none,none,NULL";

$emailtext = " Title = $title \nSubitle = $subtitle \nTitle = $article \nAuthor = $author \n Source = $source \nContact = $contact \n ";
	mail ( "$MM_email_usersubmit", "user submited article", "$emailtext", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\nX-Mailer: My PHP Script\n"); 
  
  require ("DBConnections/insetstuff.php");
require ("DBConnections/dataactions.php");
 
   }
  

?>

<?php if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
              
  <table width="90%" border="0" align="center" class="form">
    <tr class="intitle"> 
      <td colspan="2" valign="top"><font color="#000000" size="4">&nbsp;</font></td>
    </tr>
	<tr> 
      <td valign="top"> <span align="left" class="name">Name </span></td>
      <td> <input name="source" size="50" value="" > </td>
    </tr>
	<tr> 
      <td valign="top"> <span align="left" class="name">E-Mail</span></td>
      <td> <input name="contact" size="50" value="" > </td>
    </tr>
	<tr> 
      <td valign="top"><span align="left" class="name">Date</span><br> </td>
      <td valign="top" class="text"> <input type="text" name="date" size="25" value="">
        <font size="2">(format ex 12-30-2002) </font></td>
    </tr>
	<tr> 
      <td valign="top"> <span align="left" class="name">Location</span></td>
      <td> <?php statelist(state);?> </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Subject</span
			></td>
      <td> <textarea name="title" cols="38" rows="3" wrap="VIRTUAL"></textarea> </td>
    </tr>
   
    <tr>
      <td colspan="2" valign="top" class="text"><span class="name"><strong><br>
        Comments</strong></span><br>
<textarea name=article rows=20 wrap=VIRTUAL cols=48></textarea>
      </td>
    </tr>
    <tr> 
      <td valign="top" ><input type="submit" name="Submit" value="Submit"></td>
      <td>&nbsp; </td>
    </tr>
    
    <tr> 
      <td colspan="2" valign="top"> </td>
    </tr>
  </table>
  <p>&nbsp; </p>

<input type="hidden" name="MM_insert" value="true">
<input type="hidden" name="type" value="<?php echo $typep ?>">
<input type="hidden" name="subtype" value="<?php echo $subp ?>">
<input type="hidden" name="catagory" value="<?php echo $catp ?>">
<input type="hidden" name="class" value="9">

</form>
<p></p>
 <?php } //end if not thank you ?>
 
	 

<?php include("AMP/BaseFooter.php"); 
*/
?>
