 <?php  //include_once "Connections/jpcache-sql.php"; 

if  (isset($HTTP_GET_VARS["mod"])) {$mod_id = $mod ; }
else {$mod_id = 41;}
 if ($HTTP_GET_VARS["thank"] == ("1")) { 
	  $mod_id = 49 ;}
include("sysfiles.php");
include("header.php"); ?>
<?php
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
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "articles";
    $MM_editRedirectUrl = "useraddarticle.php?thank=1";
	$date =  DateConvertIn($date);
    $MM_fieldsStr = "type|value|class|value|titlex|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|state|value|source|value|contact|value|subtype|value|catagory|value";
    $MM_columnsStr = "type|none,none,NULL|class|none,none,NULL|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|state|none,none,NULL|source|',none,''|contact|',none,''|subtype|none,none,NULL|catagory|none,none,NULL";

$emailtext = " Title = $title \nSubitle = $subtitle \nTitle = $article \nAuthor = $author \n Source = $source \nContact = $contact \n ";
	mail ( "$MM_email_usersubmit", "user submited article", "$emailtext", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
  
  require ("Connections/insetstuff.php");
require ("Connections/dataactions.php");

   }
  

?>

<?php if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
              
  <table width="90%" border="0" align="center" class="form">
    <tr class="intitle"> 
      <td colspan="2" valign="top"><font color="#000000" size="4">&nbsp;</font></td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Title </span
			></td>
      <td> <textarea name="titlex" cols="40" rows="3" wrap="VIRTUAL"></textarea> 
      </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Subtitle</span></td>
      <td> <textarea name="subtitle" cols="40" rows="3" wrap="VIRTUAL"></textarea> 
      </td>
    </tr>
    <tr> 
      <td valign="top"> <span align="left" class="name">Author</span></td>
      <td> <input name="author" size="50" value="" > </td>
    </tr>
    <tr> 
      <td valign="top"><span align="left" class="name">Date</span><br> </td>
      <td valign="top" class="text"> <input type="text" name="date" size="25" value=""> 
        <font size="2">(format ex 12-30-2002) </font></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="text"> <p align="left"><span class="name"><strong><br>
          Short Description</strong></span><br>
          <textarea name="textfield" cols="45" rows="4" wrap="VIRTUAL"></textarea>
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="text"><span class="name"><strong><br>
        Article Text</strong></span><br>
        <br> <textarea name=article rows=20 wrap=VIRTUAL cols=45></textarea> 
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
<input type="hidden" name="type" value="1">

<input type="hidden" name="class" value="9">

</form>
<p></p>
 <?php } //end if not thank you ?>
 
	 

<?php include("footer.php"); ?>


