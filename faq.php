<?php 
/*********************
05-06-2003  v3.01
Module: FAQ
Description:  display and input for faq
CSS: subtitile, title, question, text, form
Get  Vars:  typeid,  id, showask - shows the question from
To Do:  declare post vars

*********************/ 
 include_once  "Connections/jpcache-sql.php"; 
$modid = 4;
$mod_id = 35;
include("sysfiles.php");
include("header.php"); 

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  
if (isset($MM_insert)){

   $MM_editTable  = "faq";
   $MM_editRedirectUrl = "faq.php";
   $MM_fieldsStr = "question|value|first|value|last|value|email|value";
   $MM_columnsStr = "question|',none,''|firstname|',none,''|lastname|',none,''|email|',none,''";
mail ( "$MM_email_faq", "faq needs response", "There is a new FAQ that needs answering: $question From:$email  \nPlease visit $Web_url/system/faq_list.php to answer", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 

   require ("Connections/insetstuff.php");
  require ("Connections/dataactions.php");
   }
  

$type__MMColParam = "0";
if (isset($HTTP_GET_VARS["typeid"]))
  {$type__MMColParam = $HTTP_GET_VARS["typeid"];}

$called__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$called__MMColParam = $HTTP_GET_VARS["id"];}

   $type=$dbcon->CacheExecute("SELECT *  FROM faqtype  WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();

   $called=$dbcon->CacheExecute("SELECT * FROM faq WHERE id = " . ($called__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $called_numRows=0;
   $called__totalRows=$called->RecordCount();

 $MM_paramName = ""; 
// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained
$MM_removeList = "&index=";
if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";
$MM_keepURL="";
$MM_keepForm="";
$MM_keepBoth="";
$MM_keepNone="";

// add the URL parameters to the MM_keepURL string
reset ($HTTP_GET_VARS);
while (list ($key, $val) = each ($HTTP_GET_VARS)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}

// add the URL parameters to the MM_keepURL string
if(isset($HTTP_POST_VARS)){
	reset ($HTTP_POST_VARS);
	while (list ($key, $val) = each ($HTTP_POST_VARS)) {
		$nextItem = "&".strtolower($key)."=";
		if (!stristr($MM_removeList, $nextItem)) {
			$MM_keepForm .= "&".$key."=".urlencode($val);
		}
	}
}

// create the Form + URL string and remove the intial '&' from each of the strings
$MM_keepBoth = $MM_keepURL."&".$MM_keepForm;
if (strlen($MM_keepBoth) > 0) $MM_keepBoth = substr($MM_keepBoth, 1);
if (strlen($MM_keepURL) > 0)  $MM_keepURL = substr($MM_keepURL, 1);
if (strlen($MM_keepForm) > 0) $MM_keepForm = substr($MM_keepForm, 1);
?>
      <table width="100%" border="0" cellspacing="0" cellpadding="10" valign="top">
        <tr> 
          <td> 
          <?php if ($HTTP_GET_VARS["id"] != ($NULL)) { ?>
            <p class="question"> 
              <?php echo $called->Fields("question")?>
            </p><p class="text"> 
              <?php echo $called->Fields("longanswer")?>
            </p><?php }
###############SHOW QUESTION FORM ###################################
?><?php if ($HTTP_GET_VARS["showask"] == (1)) { ?>
            <form name="form1" method="POST" action=<?php echo $MM_editAction?>>
              <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
                <tr> 
                  <td class="form"><b>Question</b></td><td> 
                    <textarea name="question" wrap="VIRTUAL" cols="35" rows="5"></textarea>
                  </td></tr>
                <tr> 
                  <td class="form"><b>First Name</b></td><td> 
                    <input type="text" name="first" size="40">
                  </td>
                </tr>
                <tr> 
                  <td class="form"><b>Last Name</b></td><td> 
                    <input type="text" name="last" size="40">
                  </td>
                </tr>
                <tr> 
                  <td class="form"><b>E-mail</b></td><td> 
                    <input type="text" name="email" size="40">
                  </td>
                </tr>
                <tr> 
                  <td class="form"> 
                    <input type="submit" name="Submit" value="Submit">
                  </td><td>&nbsp;</td>
                </tr>
              </table>
              <input type="hidden" name="MM_insert" value="true">
            </form>
            <?php }
## ##############show all questions################

 if ($type->Fields("id") != NULL) { ?>
            <p class="subtitile">   <?php echo $type->Fields("type")?></p>
             
              <?php 
		$questions__MMColParam = "1";
if (isset($HTTP_GET_VARS["typeid"]))
  {$questions__MMColParam = $HTTP_GET_VARS["typeid"];}
   $questions=$dbcon->CacheExecute("SELECT *  FROM faq  WHERE typeid = " . ($questions__MMColParam) . " and publish = 1  ORDER BY date DESC") or DIE($dbcon->ErrorMsg());	  
while  (!$questions->EOF)   { 
?>
<p><b class="question"> 
  <?php echo $questions->Fields("question")?>
  </b></p>
<p class="text"> 
  <?php echo $questions->Fields("longanswer")?>
</p>
<p class="text">&nbsp;</p>
<?php

  $questions->MoveNext();
}
 }
 #############################################################
/* if ($type->Fields("id") != $HTTP_GET_VARS["$NULL"]) */
?><?php if ($type->Fields("id") == NULL) { ?>
            <p class="subtitile">Recently Asked Questions</p>
            <p> 
              <?php 
			  
			  			  $questions=$dbcon->CacheExecute("SELECT *  FROM faq  where publish = 1") or DIE($dbcon->ErrorMsg());
			  
			   while (!$questions->EOF)
   { 
?>
<p><b class="question"> 
  <?php echo $questions->Fields("question")?>
  </b></p>
<p class="text"> 
  <?php echo $questions->Fields("longanswer")?>
</p>
<p class="text">&nbsp;</p>
<?php

  $questions->MoveNext();
}
			 }
	#################################################################		 

?></td></tr></table>


<?php include("footer.php"); ?>          
   