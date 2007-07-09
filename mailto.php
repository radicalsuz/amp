<?PHP
/*********************
05-07-2003  v3.01
Module:  Mailto
Description:  popup that sends email link to page
SYS VARS:  $SiteName
To Do:  make multiple var pages send
			  add design and css to page

*********************/ 
require_once( 'AMP/Base/Config.php' );
require_once( 'Modules/Share/Public/ComponentMap.inc.php' );
$intro_id = 22;
$map = & new ComponentMap_Share_Public( );

$controller = $map->get_controller( );
$header = AMP_get_header( );
print $header->output( );
print $controller->execute( ) ;

/*

include("AMP/BaseDB.php");
include_once("AMP/System/Email.inc.php");
if (isset($modid) && $modid ){
  #this code initializes the $tophtml, $prmailtomessage, and $bthtml variables
    require_once( 'AMP/System/Tool/Control/Set.inc.php' );
    $controls = &new ToolControlSet( AMP_Registry::getDbcon( ), $modid );
    $controls->globalizeSettings();
    /* old way
$modinstance = $dbcon->CacheExecute("SELECT * from module_control where modid = $modid") or DIE($dbcon->ErrorMsg());
while (!$modinstance->EOF) {
$a = $modinstance->Fields("var");
$$a = $modinstance->Fields("setting");
$modinstance->MoveNext();} 
*/
/*
}

echo $tophtml; 

function show_form() {
  $recommend = substr($_SERVER["QUERY_STRING"],4);
?>

Recommend <b><?PHP echo $recommend; ?></b> to a friend ...
<form method="post" action="mailto.php">
<input type=hidden name="url" value="<?PHP echo $recommend; ?>">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="left" valign="top"> 
      <td width="50%"> Your name: </td>
      <td> 
        <input type="text" name="from" size="30">
      </td>
    </tr>
    <tr align="left" valign="top"> 
      <td> <small>*</small> Your e-mail: </td>
      <td> 
        <input type="text" name="from_email" maxlength="40" size="30">
      </td>
    </tr>
    <tr align="left" valign="top"> 
      <td> <small>*</small> Friend's e-mail:</td>
      <td> 
        <input type="text" name="to_email" size="30">
      </td>
    </tr>
    <tr align="left" valign="top"> 
      <td> Short note about this page: </td>
      <td> 
        <textarea name="comment" rows="5" cols="30"></textarea>
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

if ($_REQUEST['submit']) {

	if (! check_email ($_REQUEST["from_email"]) || ! check_email ($_REQUEST["to_email"]) ) error ("Invalid e-mail address!");
        $date=date( "D, j M Y H:i:s -0800");
	$to_email=$_REQUEST["to_email"];
	$from=$_REQUEST["from"];
	$from_email=AMPSystem_Email::sanitize($_REQUEST["from_email"]);
	$comment=$_REQUEST["comment"];
	$site_name=AMPSystem_Email::sanitize(AMP_SITE_NAME);
  $url = $_REQUEST["url"];
	$message="Hi\n$from ($from_email) invited you to visit ". AMP_SITE_NAME."\n".$prmailtomessage."\nCheck out this URL: $url";
		if ($_REQUEST["comment"] != "") {
			$message.="\n\n$from left you a note:\n$comment";
		}
	
	$your_email=AMPSystem_Email::sanitize(AMP_SITE_EMAIL_SENDER);

	$subject="You were invited by $from to visit ".$site_name."!";
	$add="From: $site_name <$your_email>\nReply-To: $from_email\nDate: $date\n";
	if (@mail ("$to_email","$subject","$message","$add")) {
		echo "<center>Message successfully sent!<br>Thank you!<br><br>[ <a href=\"javascript:window.close()\">Close this window</a> ]</center>";
	} else error ("Internal server error. Cannot send email, please try later!");

} else show_form();

 echo $bthtml; 
 */
?>
