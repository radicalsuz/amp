<?PHP
/*********************
05-07-2003  v3.01
Module:  Mailto
Description:  popup that sends email link to page
SYS VARS:  $SiteName
To Do:  make multiple var pages send
			  add design and css to page

*********************/ 
$modid = 22;
include("AMP/BaseDB.php");
include_once("AMP/System/Email.inc.php");
if (isset($modid)){
$modinstance = $dbcon->CacheExecute("SELECT * from module_control where modid = $modid") or DIE($dbcon->ErrorMsg());
while (!$modinstance->EOF) {
$a = $modinstance->Fields("var");
$$a = $modinstance->Fields("setting");
$modinstance->MoveNext();} }

$setvar=$dbcon->CacheExecute("SELECT * FROM sysvar WHERE id = 1") or DIE($dbcon->ErrorMsg());

echo $GLOBALS[tophtml]; 

#
# Title of the "poped" page:
	
# Path to mailto.php script:
	$GLOBALS["path"]="mailto.php";
# Site name:
	$GLOBALS["site_name"]=  $SiteName;
# webmaster's email:
	$GLOBALS["your_email"]= $setvar->Fields("emfrom");
	$setvar->Close();

# Last words: You can distribute this script freely as long as my name and email are in header!

############################ DO NOT EDIT BELOW ################################
function show_form() {
?>

Recommend <b><?PHP echo substr ($GLOBALS["QUERY_STRING"],4); ?></b> to a friend ...
<form method="post" action="<?PHP echo $GLOBALS["path"]; ?>">
<input type=hidden name="url" value="<?PHP echo substr ($GLOBALS["QUERY_STRING"],4); ?>">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="left" valign="top"> 
      <td width="50%"> Your name: </td>
      <td> 
        <input type="text" name="form[from]" size="30">
      </td>
    </tr>
    <tr align="left" valign="top"> 
      <td> <small>*</small> Your e-mail: </td>
      <td> 
        <input type="text" name="form[from_email]" maxlength="40" size="30">
      </td>
    </tr>
    <tr align="left" valign="top"> 
      <td> <small>*</small> Friend's e-mail:</td>
      <td> 
        <input type="text" name="form[to_email]" size="30">
      </td>
    </tr>
    <tr align="left" valign="top"> 
      <td> Short note about this page: </td>
      <td> 
        <textarea name="form[comment]" rows="5" cols="30"></textarea>
      </td>
    </tr>
    <tr align="left" valign="top">
      <td>
        <input type="submit" name="submit" value="Send">
        <input type="reset" name="Reset" value="Reset">
      </td>
      <td>&nbsp;</td>
    </tr>
  </table>
<p><small>* - required!</small></p>
</form>


<?PHP
}

function error($string) {
	print ("<div align=center valign=center><b>Warning:</b> $string<br><br>
		[ <a href=\"javascript:history.go(-1)\">Back</a> ] |
		[ <a href=\"javascript:window.close()\">Close this window</a> ]
		</div>");
	exit;
}

function check_email ($address) {
# this function was copied from PHP mailing list
	return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$address));
}

if ($submit) {

	if (! check_email ($form["from_email"]) || ! check_email ($form["to_email"]) ) error ("Invalid e-mail address!");
        $date=date( "D, j M Y H:i:s -0600");
	$to_email=$form["to_email"];
	$from=$form["from"];
	$from_email=AMPSystem_Email::sanitize($form["from_email"]);
	$comment=$form["comment"];
	$site_name=AMPSystem_Email::sanitize($GLOBALS["site_name"]);
	$your_email=AMPSystem_Email::sanitize($GLOBALS["your_email"]);
	$message="Hi\n$from ($from_email) invited you to visit $site_name\n".$GLOBALS['prmailtomessage']."\nCheck out this URL: $url";
		if ($form["comment"] != "") {
			$message.="\n\n$from left you a note:\n$comment";
		}
	
	$subject="You were invited by $from to visit ".$GLOBALS["site_name"]."!";
	$add="From: $site_name <$your_email>\nReply-To: $from_email\nDate: $date\n";
	if (@mail ("$to_email","$subject","$message","$add")) {
		echo "<center>Message successfully sent!<br>Thank you!<br><br>[ <a href=\"javascript:window.close()\">Close this window</a> ]</center>";
	} else error ("Internal server error. Cannot send email, please try later!");

} else show_form();

 echo $GLOBALS['bthtml']; ?>
