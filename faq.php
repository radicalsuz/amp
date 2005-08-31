<?php 
/*********************
05-06-2003  v3.01
Module: FAQ
Description:  display and input for faq
CSS: subtitile, title, question, text, form
Get  Vars:  typeid,  id, showask - shows the question from
To Do:  

*********************/ 

# check to see if this var is set any where:  $MM_email_faq


$modid = 4;
$intro_id = 35;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

 
if (isset($MM_insert)){

	$MM_editTable  = "faq";
   	$MM_editRedirectUrl = "faq.php";
   	$MM_fieldsStr = "question|value|first|value|last|value|email|value";
   	$MM_columnsStr = "question|',none,''|firstname|',none,''|lastname|',none,''|email|',none,''";
	if ($MM_email_faq) {
		mail ( $MM_email_faq, "faq needs response", "There is a new FAQ that needs answering: ".$_POST["question"]." From:".$_POST["email"]."  \nPlease visit $Web_url/system/faq_list.php to answer", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
	}
	require ("DBConnections/insetstuff.php");
    require ("DBConnections/dataactions.php");
}
  

$type__MMColParam = "0";
if (isset($_GET["typeid"]))
  {$type__MMColParam = $_GET["typeid"];
}

$called__MMColParam = "1";
if (isset($_GET["id"]))  {
	$called__MMColParam = $_GET["id"];
}

   $type=$dbcon->CacheExecute("SELECT *  FROM faqtype  WHERE id = " . ($type__MMColParam) . "") or DIE($dbcon->ErrorMsg());
  $called=$dbcon->CacheExecute("SELECT * FROM faq WHERE id = " . ($called__MMColParam) . "") or DIE($dbcon->ErrorMsg());


echo '<table width="100%" border="0" cellspacing="0" cellpadding="10" valign="top"><tr><td>';

#show question
if ($_GET["id"] != (NULL)) { 
	echo '<p class="question">' . $called->Fields("question") . '</p><p class="text">' . $called->Fields("longanswer") .'</p>';
}

#show form
if ($_GET["showask"] == (1)) { ?>
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
<?php 
}

#show of type
if ($type->Fields("id") != NULL) { 
	echo ' <p class="subtitile">' .  $type->Fields("type") . '</p>';
	
	$questions__MMColParam = "1";
	if (isset($_GET["typeid"])){$questions__MMColParam = $_GET["typeid"];}
	$questions=$dbcon->CacheExecute("SELECT * FROM faq  WHERE typeid = $questions__MMColParam and publish = 1  ORDER BY date DESC") or DIE($dbcon->ErrorMsg());	  
	while  (!$questions->EOF)   { 
		echo '<p><b class="question">' . $questions->Fields("question") . '</b></p>';
		echo '<p class="text"> ' . $questions->Fields("longanswer") . '</p><p class="text">&nbsp;</p>';
		$questions->MoveNext();
	}
}

#defualt list
if ($type->Fields("id") == NULL) { 
	echo '<p class="subtitile">Recently Asked Questions</p><p></p>';
	$questions=$dbcon->CacheExecute("SELECT *  FROM faq  where publish = 1") or DIE($dbcon->ErrorMsg());
	while (!$questions->EOF) { 
		echo '<p><b class="question">' . $questions->Fields("question") . '</b></p>';
		echo '<p class="text">' . $questions->Fields("longanswer") . '</p><p class="text">&nbsp;</p>';
		$questions->MoveNext();
	}
}

echo '</td></tr></table>';

include("AMP/BaseFooter.php"); 
?>    
