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
include_once("AMP/System/Email.inc.php");

$captcha_valid = false;
$captcha_message = '';
if (isset($_POST['MM_insert'])&&$_POST['MM_insert']) {
    require_once( 'AMP/Form/Element/Captcha.inc.php');
    $captcha_demo = &new PhpCaptcha( array( ) );
    $captcha_valid = $captcha_demo->Validate( $_POST['captcha']);
    if ( !$captcha_valid ) {
        $_REQUEST['kill_insert'] = 'captcha';
		$_GET['showask'] = '1';
        $captcha_message = AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED;
    }
}
if (isset($_REQUEST["MM_insert"]) && $_REQUEST["MM_insert"] && $captcha_valid ){

	$MM_editTable  = "faq";
   	$MM_editRedirectUrl = "faq.php";
   	$MM_fieldsStr = "question|value|first|value|last|value|email|value";
   	$MM_columnsStr = "question|',none,''|firstname|',none,''|lastname|',none,''|email|',none,''";
	if ($MM_email_faq) {
		mail ( $MM_email_faq, "faq needs response", "There is a new FAQ that needs answering: ".$_POST["question"]." From:".$_POST["email"]."  \nPlease visit $Web_url/system/faq.php?action=list to answer", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\nX-Mailer: My PHP Script\n"); 
	}
	require ("Connections/insetstuff.php");
    require ("Connections/dataactions.php");
	header("Location: faq.php");
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
            <form name="form1" method="POST" action="faq.php">
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
                    </div>
                  </div>
                  </td>
                </tr>
                <tr> 
                  <td class="form"></td><td> 
                   <br />
<div align="left"> 
                  First, enter the code from the image below 
                        <span class='red'><font color = 'red'><?php print $GLOBALS['captcha_message']; ?></font></span><BR />
                        <img src='<?php print AMP_url_add_vars( AMP_CONTENT_URL_CAPTCHA, array( 'key=' . AMP_SYSTEM_UNIQUE_VISITOR_ID ));?>'/>
					 <div align="left"> 
                  <p>
                      <input name="captcha" type="text" id="captcha" size="8"/>
                      <input name='AMP_SYSTEM_UNIQUE_VISITOR_ID' type='hidden' value='<?php print AMP_SYSTEM_UNIQUE_VISITOR_ID; ?>'/>
                    </p>
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
	$questions=$dbcon->CacheExecute("SELECT *  FROM faq  where publish = 1");
	while ($questions && !$questions->EOF) { 
		echo '<p><b class="question">' . $questions->Fields("question") . '</b></p>';
		echo '<p class="text">' . $questions->Fields("longanswer") . '</p><p class="text">&nbsp;</p>';
		$questions->MoveNext();
	}
}

echo '</td></tr></table>';

include("AMP/BaseFooter.php"); 
?>
