<?php
$modid=4;
  require("Connections/freedomrising.php");
 
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?><?php
  // *** Update Record: set variables
  
    if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
        //Delete cached versions of output file
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "faq";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "faq_list.php";
    $MM_fieldsStr = "question|value|first|value|last|value|email|value|textarea|value|textarea2|value|publish|value|select|value|answered|value";
    $MM_columnsStr = "question|',none,''|firstname|',none,''|lastname|',none,''|email|',none,''|shortanswer|',none,''|longanswer|',none,''|publish|none,1,0|typeid|none,none,NULL|answered|none,1,0";
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
 
 $faq__MMColParam = "90000000";
if (isset($HTTP_GET_VARS["id"]))
  {$faq__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $faq=$dbcon->Execute("SELECT * FROM faq WHERE id = " . ($faq__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $faq_numRows=0;
   $faq__totalRows=$faq->RecordCount();
?><?php
   $faqtypes=$dbcon->Execute("SELECT * FROM faqtype ORDER BY type ASC") or DIE($dbcon->ErrorMsg());
   $faqtypes_numRows=0;
   $faqtypes__totalRows=$faqtypes->RecordCount();
?><?php  include("header.php"); ?>
<h2>Edit FAQ </h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
        <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
          <tr> 
            <td><b>Question</b></td>
            <td> <textarea name="question" wrap="VIRTUAL" rows="3" cols="50"><?php echo $faq->Fields("question")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td><b>First Name</b></td>
            <td> <input type="text" name="first" size="40" value="<?php echo $faq->Fields("firstname")?>"> 
            </td>
          </tr>
          <tr> 
            <td><b>Last Name</b></td>
            <td> <input type="text" name="last" size="40" value="<?php echo $faq->Fields("lastname")?>"> 
            </td>
          </tr>
          <tr> 
            <td><b>E-mail</b></td>
            <td> <input type="text" name="email" size="40" value="<?php echo $faq->Fields("email")?>"> 
            </td>
          </tr>
          <tr> 
            <td><b>Short Answer</b></td>
            <td> <textarea name="textarea" wrap="VIRTUAL" rows="3" cols="50"><?php echo $faq->Fields("shortanswer")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td><b>Long Answer</b></td>
            <td> <textarea name="textarea2" wrap="VIRTUAL" rows="10" cols="50"><?php echo $faq->Fields("longanswer")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td><b>Publish</b></td>
            <td> <input <?php If (($faq->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="publish"> 
            </td>
          </tr>
          <tr> 
            <td><b>Type </b></td>
            <td> <select name="select">
                <?php
  if ($faqtypes__totalRows > 0){
    $faqtypes__index=0;
    $faqtypes->MoveFirst();
    WHILE ($faqtypes__index < $faqtypes__totalRows){
?>
                <OPTION VALUE="<?php echo  $faqtypes->Fields("id")?>"<?php if ($faqtypes->Fields("id")==$faq->Fields("typeid")) echo "SELECTED";?>> 
                <?php echo  $faqtypes->Fields("type");?> </OPTION>
                <?php
      $faqtypes->MoveNext();
      $faqtypes__index++;
    }
    $faqtypes__index=0;  
    $faqtypes->MoveFirst();
  }
?>
              </select> <a href="faqtype_list.php?show=1">Add Faq Type</a> </td>
          </tr>
          <tr> 
            <td><b>Answered</b></td>
            <td> <input <?php If (($faq->Fields("answered")) == "1") { echo "CHECKED";} ?> type="checkbox" name="answered" value="1"> 
            </td>
          </tr>
          <tr> 
            <td colspan="2"> <input name="submit" type="submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
                <?php 
		}
		else { ?>
                <input type="hidden" name="MM_update" value="true">
                <?php } ?>
                <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">

            </td>
          </tr>
        </table>
  </form>
<?php include ("footer.php")?>
<?php
  $faq->Close();
?><?php
  $faqtypes->Close();
?>
