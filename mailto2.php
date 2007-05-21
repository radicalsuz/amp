<?PHP 
/*
$modid = 22;
if ($_POST["FromEmail"]) {$mod_id = 32 ; }
else {$mod_id = 33;}

include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

include("includes/emaillist_functions.php");


if ($_POST["FromEmail"]) {

	if ($_POST["friend1"]) { friendsend($_POST["friend1"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]) ;}
 	if ($_POST["friend2"]) { friendsend($_POST["friend2"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	if ($_POST["friend3"]) { friendsend($_POST["friend3"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	if ($_POST["friend4"]) { friendsend($_POST["friend4"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	if ($_POST["friend5"]) { friendsend($_POST["friend5"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	if ($_POST["friend6"]) { friendsend($_POST["friend6"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	if ($_POST["friend7"]) { friendsend($_POST["friend7"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	if ($_POST["friend8"]) { friendsend($_POST["friend8"],$_POST["FromEmail"],$_POST["name"],$_POST["subject"],$_POST["text"]);}
	
	$MM_insert = 1 ;
	$MM_editTable  = "userdata";
	$modin =14;
	$names = explode(" ", $_POST["name"]);
	$First_Name = $names[0];
	$Last_Name = $names[1];
	$MM_fieldsStr =  "First_Name|value|Last_Name|value|FromEmail|value|subject|value|text|value|friend1|value|friend2|value|friend3|value|friend4|value|friend5|value|friend6|value|friend7|value|friend8|value|modin|value";
	$MM_columnsStr = "First_Name|',none,''|Last_Name|',none,''|Email|',none,''|custom1|',none,''|custom2|',none,''|custom3|',none,''|custom4|',none,''|custom5|',none,''|custom6|',none,''|custom7|',none,''|custom8|',none,''|custom9|',none,''|custom10|',none,''|modin|',none,''";
	require ("Connections/insetstuff.php");
    require ("Connections/dataactions.php");
}
else {
	if (isset($_POST['url_link'])) { 
		$messsageor=str_replace("[-url_link-]", $_POST['url_link'], $messsageor);
	}
	tellfriend($firstname,$lastname,$email,$subjector,$messsageor);
}

include("AMP/BaseFooter.php"); 
*/
?>
