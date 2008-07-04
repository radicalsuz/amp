<?php
/* disabled for security and lack of use ap 2008-07
 */
/*

if  (isset($HTTP_GET_VARS["mod"])) {
	$mod_id = $mod;
} elseif ($HTTP_GET_BARS["thank"] == "1") {
	$mod_id = 49;
} else {
	$mod_id = 41;
}


include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php"); 
include('AMP/Content/Map/Select.inc.php');

// *** Edit Operations: declare Tables
$MM_editAction = $PHP_SELF;
if ($QUERY_STRING) {
	$MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
}

$MM_abortEdit = 0;
$MM_editQuery = "";
 
// *** Insert Record: set variables
  
if (isset($MM_insert)) {
  
//    $MM_editConnection = $MM__STRING;
	$MM_editTable  = "articles";
	$MM_editRedirectUrl = "useraddarticle.php?thank=1";
	$MM_fieldsStr = "type|value|class|value|titlex|value|shortdesc|value|linkover|value|link|value";
	$MM_columnsStr = "type|',none,''|class|',none,''|title|',none,''|shortdesc|',none,''|linkover|',none,''|link|',none,''";

//	$emailtext = " Title = $title \nSubitle = $subtitle \nTitle = $article \nAuthor = $author \n Source = $source \nContact = $contact \n ";
//	mail ( "$MM_email_usersubmit", "user submited article", "$emailtext", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
  
	require ("DBConnections/insetstuff.php");
	require ("DBConnections/dataactions.php");

}
  
if ( !isset( $HTTP_GET_VARS["thank"] )) { ?>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">	
              
  <table width="90%" border="0" align="center" class="form">
    <tr class="intitle"> 
      <td colspan="2" valign="top"><font color="#000000" size="4">&nbsp;</font></td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Link Title:</span
			></td>
      <td> <textarea name="titlex" cols="45" rows="3" wrap="VIRTUAL"></textarea> 
      </td>
    </tr>
	    <tr> 
      <td valign="top"> Description: </td>
      <td><textarea name="shortdesc" cols="45" rows="5" wrap="VIRTUAL"></textarea></td>
    </tr>
	    <tr> 
      <td valign="top">Link URL</td>
      <td><input name="link" type="text" id="link" value="http://" size="50\"></td>
    </tr>
	 <tr> 
      <td valign="top">Link in Section</td>
      <td><select name="type"><option value="1" selected>Select Section</option> <?php 
      echo ContentMap_Select::getIndentedOptions();
      ?></select></td>
	 </tr>
    <tr> 
      <td colspan="2" valign="top" class="text"> <p align="left"><span class="name"><strong><br>
          </strong></span><br>
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
<input type="hidden" name="class" value="11">
<input type="hidden" name="linkover" value="1">

</form>


<?php

}

//end if not thank you 

include("AMP/BaseFooter.php");

*/
?>
