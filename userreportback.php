 <?php  include_once "Connections/jpcache-sql.php"; 

$mod_id = 41;
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
	$MM_editRedirectUrl = "index.php";
	$date =  DateConvertIn($date);
    $MM_fieldsStr = "type|value|class|value|select3|value|uselink2|value|uselink|value|publish|value|titlex|value|subtitle|value|html|value|article|value|textfield|value|author|value|linktext|value|date|value|usedate|value|doc|value|radiobutton|value|link|value|linkuse|value|new|value|actionitem|value|actionlink|value|piccap|value|picture|value|usepict|value|morelink|value|usemore|value|ID|value|pageorder|value";
    $MM_columnsStr = "type|none,none,NULL|class|none,none,NULL|catagory|none,none,NULL|fplink|none,1,0|uselink|none,1,0|publish|none,1,0|title|',none,''|subtitile|',none,''|html|none,1,0|test|',none,''|shortdesc|',none,''|author|',none,''|linktext|',none,''|date|',none,NULL|usedate|none,1,0|doc|',none,''|doctype|',none,''|link|',none,''|linkover|none,1,0|new|none,1,0|actionitem|none,1,0|actionlink|none,none,NULL|piccap|',none,''|picture|',none,''|picuse|none,none,NULL|morelink|',none,''|usemore|none,1,0|enteredby|none,none,NULL|pageorder|none,none,NULL";
	mail ( "$MM_email_usersubmit", "user submited article", "$article", "From: $author\nX-Mailer: My PHP Script\n"); 
  
  require ("Connections/insetstuff.php");
  
   }
  
require ("Connections/dataactions.php");
  

?><?php
   $typelab=$dbcon->Execute("SELECT id, type FROM articletype ORDER BY type ASC") or DIE($dbcon->ErrorMsg());
   $typelab_numRows=0;
   $typelab__totalRows=$typelab->RecordCount();
?><?php
   $sublab=$dbcon->Execute("SELECT id, subname FROM articlesubtype") or DIE($dbcon->ErrorMsg());
   $sublab_numRows=0;
   $sublab__totalRows=$sublab->RecordCount();
?><?php
   $catagory=$dbcon->Execute("SELECT * FROM catagory ORDER BY catname ASC") or DIE($dbcon->ErrorMsg());
   $catagory_numRows=0;
   $catagory__totalRows=$catagory->RecordCount();
?>
<?php
   $action=$dbcon->Execute("SELECT * FROM sendfax ORDER BY subject ASC") or DIE($dbcon->ErrorMsg());
   $action_numRows=0;
   $action__totalRows=$action->RecordCount();
?>

<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
              <table width="90%" border="0" align="center">
                <tr class="intitle"> 
                  <td colspan="2" valign="top">Document Content</td>
                </tr>
                <tr> 
                  <td valign="top"> <span align="left" class="name">Title </span
			></td>
                  <td> <input name="titlex" value="" size="50" > </td>
                </tr>
                <tr> 
                  <td valign="top"> <span align="left" class="name">Subtitle</span></td>
                  <td> <input name="subtitle" size="50" value=""            > 
                  </td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="text"> <p align="left"><span class="name">Article 
                      Text</span><BR>
                      <textarea name=article rows=20 wrap=VIRTUAL cols=48></textarea>
                      <br>
          HTML tags are allowed </td>
                </tr>
                <tr> 
                  <td valign="top" ><span class="name">Short Description</span></td>
                  <td> <textarea name="textfield" cols="38" wrap="VIRTUAL"></textarea> 
                  </td>
                </tr>
                <tr> 
                  <td valign="top"> <span align="left" class="name">Author</span></td>
                  <td> <input name="author" size="50" value="" > </td>
                </tr>
                <tr> 
                  <td valign="top"><span align="left" class="name">Date</span><br> 
                  </td>
                  <td valign="top" class="text"> <input type="text" name="date" size="25" value="">
        <font size="2">(format ex 12-30-2002) </font></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top"> </td>
                </tr>
              </table>
  <p> 
    <input type="reset" name="Submit2" value="Clear Form">
    <input type="submit" name="Submit" value="Submit">
  </p>

<input type="hidden" name="MM_insert" value="true">
<input type="hidden" name="type" value="10">
<input type="hidden" name="class" value="9">
<input type="hidden" name="publish" value="0">
</form>

<?php echo $footer; ?>

<?php
  $typelab->Close();
  $sublab->Close();
  $catagory->Close();
  $action->Close();
?>
