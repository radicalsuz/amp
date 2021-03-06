<?

# default configuration. These values can be changed
# via the admin page, so you do not need to edit them here
# they are used to initialise things
# if you *do* edit them, make sure they stay in the correct format
# otherwise you will end up with parse errors and things will stop working

$defaultheader = '
<link href="styles/phplist.css" type="text/css" rel="stylesheet">

</head>
<body bgcolor="#ffffff">
<a name="top"></a>
<div align=center>
<table cellspacing=0 cellpadding=0 width=710 border=0>

<tr>
<td bgcolor="#000000" rowspan=3><img height=1 alt="" src="images/transparent.png" width=1 border=0></td>
<td bgcolor="#000000"><img height=1 alt="" src="images/transparent.png" width=708 border=0></td>
<td bgcolor="#000000" rowspan=3><img height=1 alt="" src="images/transparent.png" width=1 border=0></td>
</tr>

<tr valign="top">
<td>
<!--TOP TABLE starts-->
<table cellspacing=0 cellpadding=0 width=708 border="0" bgcolor="#ffffff">

<tr valign="top">
<td height="41" background="images/top01.png" colspan="2">
<span class="phphead"><a href="http://www.phplist.com" class="phphead"><b>PHP</b>list</a></span></td>
<td background="images/top02.png" align="left">
<font size="-2">&nbsp;<i>powered by:
</i><br />&nbsp;<b>[<a class="powered" href="http://www.php.net/" target="_new"><i>PHP</i></a>]</b> + <b>[<a class="powered" href="http://www.mysql.com/" target="_new">mySQL</a>]</b></font></td>
</tr>

<tr valign="bottom">
<td><img src="images/top03a.png" width=20 height=34 alt="" border="0"></td>
<td background="images/top03b.png" height="34"><!--hello <b>ben</b>:&nbsp;<a class="urhere" href="">you are here &gt; main admin</a>-->
<td width="132" valign="bottom" background="images/top04.png"><span class="webblermenu">PHPlist</span></td>
</tr>

<tr>
<td bgcolor="#000000"><img height=1 alt="" src="images/transparent.png" width=20 border=0></td>
<td bgcolor="#000000"><img height=1 alt="" src="images/transparent.png" width=576 border=0></td>
<td bgcolor="#000000"><img height=1 alt="" src="images/transparent.png" width=132 border=0></td>
</tr>

<tr valign="top">
<td>&nbsp;</td>
<td>
<br />
';

$defaultfooter = '
</td>
<td>
&nbsp;</td>
</tr>
<tr><td colspan="4">&nbsp;</td></tr>
<tr><td colspan="4">&nbsp;</td></tr>
</table>
<!--TOP TABLE ends-->

</td></tr>

<!-- main page content-->

<!-- end of main page content--><!-- bottom black line-->
<tr>
<td bgcolor="#000000" colspan=3><img height=1 alt="" src="images/transparent.png" width=1 border=0></td>
</tr>

<tr>
<td bgcolor="#000000"><img height=1 alt="" src="images/transparent.png" width=1 border=0></td>
<td bgcolor="#ff9900" class="bottom">&copy; <a href="http://tincan.co.uk" target="_tincan" class="urhere">tincan limited</a> | <a class="urhere" href="http://www.phplist.com" target="_blank">phplist</a> - version <?=VERSION?></td>
<td bgcolor="#000000"><img height=1 alt="" src="images/transparent.png" width=1 border=0></td>
</tr>

<tr>
<td bgcolor="#000000" colspan=3><img height=1 alt="" src="images/transparent.png" width=1 border=0></td>
</tr>

<tr>
<td colspan=3><img height=3 alt="" src="images/transparent.png" width=1 border=0></td>
</tr>

<tr>
<td colspan=3>
&nbsp;
</td>
</tr>
</tbody>
</table>

</div>
</body></html>
';

if (is_file("../config/header.inc")) {
	$fd = fopen ("../config/header.inc", "r");
	$defaultheader = fread ($fd, filesize ("../config/header.inc"));
	fclose ($fd);
}
if (is_file("../config/footer.inc")) {
	$fd = fopen ("../config/footer.inc", "r");
	$defaultfooter = fread ($fd, filesize ("../config/footer.inc"));
	fclose ($fd);
}

$D_website = strtolower(getenv("SERVER_NAME"));
$D_domain = $D_website;
if (preg_match("#^www\.(.*)#i",$D_domain,$regs))
	$D_domain = $regs[1];

# for starters, you want to leave this line as it is.
$default_config = array (

# any next line has the format
# "name" => array('value','description','type'),

# what is your website location (url)
"website" => array($D_website,
		"Website address","text"),

# what is your domain (for sending emails)
"domain" =>  array($D_domain,
		"Domain Name of your server (for email)","text"),

# admin address is the person who is in charge of this system
"admin_address" => array('webmaster@[DOMAIN]',
		"Person in charge of this system (one email address)","text"),

# admin addresses are other people who receive copies of subscriptions
"admin_addresses" => array('',
		"List of people to CC in system emails (separate by commas)","text",1),

# report address is the person who gets the reports
"report_address" => array('listreports@[DOMAIN]',
		"Who gets the reports (email address, separate multiple emails with a comma)","text"),

# where will messages appear to come from
"message_from_address" => array("noreply@[DOMAIN]",
	"From email address for system messages","text"),

"message_from_name" => array("Webmaster",
	"What name do system messages appear to come from","text"),

# what is the reply-to on messages?
"message_replyto_address" => array("noreply@[DOMAIN]",
	"Reply-to email address for system messages","text"),

# if there is only one visible list, do we hide it and automatically
# subscribe users who sign up
"hide_single_list" => array ("1",
	"if there is only one visible list, should it be hidden in the page and automatically
	 subscribe users who sign up (0/1)","boolean"),

# width of a textline field
"textline_width" => array ("40",
	"width of a textline field (numerical)","text"),

# dimensions of a textarea field
"textarea_dimensions" => array ("10,40",
	"dimensions of a textarea field (rows,columns)","text"),

# send copies of subscribe, update unsubscribe messages to the administrator
"send_admin_copies" => array ("0",
	"Does the admin get copies of subscribe, update and unsubscribe messages (0/1)","boolean"),

# the main subscribe page, when there are multiple
"defaultsubscribepage" => array(1,
	"The default subscribe page when there are multiple","text"),

# the location of your subscribe script
"subscribeurl" => array("http://[WEBSITE]$pageroot/?p=subscribe",
	"URL where users can subscribe","text"),

# the location of your unsubscribe script:
"unsubscribeurl" => array("http://[WEBSITE]$pageroot/?p=unsubscribe",
	"URL where users can unsubscribe","text"),

# the location of your confirm script:
"confirmationurl" => array("http://[WEBSITE]$pageroot/?p=confirm",
  "URL where users have to confirm their subscription","text"),

# url to change their preferences
"preferencesurl" => array("http://[WEBSITE]$pageroot/?p=preferences",
	"URL where users can update their details","text"),

# the subject of the message
"subscribesubject" => array("Request for confirmation",
	"Subject of the message users receive when they subscribe","text"),

# message that is sent when people sign up to a list
# [LISTS] will be replaced with the list of lists they have signed up to
# [CONFIRMATIONURL] will be replaced with the URL where a user has to confirm
# their subscription
"subscribemessage" => array('

  Almost welcome to our mailinglist(s) ...

  Someone, hopefully you, has subscribed your email address to the following mailinglists:

[LISTS]

  If this is correct, please click this URL to confirm your subscription:

[CONFIRMATIONURL]

  If this is not correct, you do not need to do anything, simply delete this message.

  Thank you

  ',
  "Message users receive when they subscribe","textarea"),

# subject of the message when they unsubscribe
"unsubscribesubject" => array( "Goodbye from our Mailinglist",
	"Subject of the message users receive when they unsubscribe","text"),

# message that is sent when they unsubscribe
"unsubscribemessage" => array('

  Goodbye from our Mailinglist, sorry to see you go.

  You have been unsubscribed from the following mailinglists:
[LISTS]

  If there is an error in this information, you can re-subscribe:
  please go to [SUBSCRIBEURL] and follow the steps.

  Thank you

',
	"Message users receive when they unsubscribe","textarea"),

# confirmation of subscription
"confirmationsubject" => array('Welcome to our Mailinglist',
	"Subject of the message users receive after confirming their email address","text"),

# message that is sent to confirm subscription
"confirmationmessage" => array('

  Welcome to our Mailinglists

  Please keep this email for later reference.

  Your email address has been added to the following mailinglists:
[LISTS]

  To unsubscribe please go to [UNSUBSCRIBEURL] and follow the steps.
  To update your details and preferences please go to [PREFERENCESURL].

  Thank you

',
	"Message users receive after confirming their email address","textarea"),

# the subject of the message sent when changing the user details
"updatesubject" => array("[notify] Change of List-Membership details",
	"Subject of the message users receive when they have changed their details","text"),

# the message that is sent when a user updates their information.
# just to make sure they approve of it.
# confirmationinfo is replaced by one of the options below
# userdata is replaced by the information in the database
"updatemessage" => array('

  This message is to inform you of a change of your details on our mailinglists

  You are currently member of the following mailinglists:

[LISTS]

[CONFIRMATIONINFO]

  The information on our system for you is as follows:

[USERDATA]

  If this is not correct, please update your information at the following location:

[PREFERENCESURL]

  Thank you

  ',
  "Message that is sent when users change their information","textarea"),

# this is the text that is placed in the [!-- confirmation --] location of the above
# message, in case the email is sent to their new email address and they have changed
# their email address
"emailchanged_text" => array('
  When updating your details, your email address has changed.
  Please confirm your new email address by visiting this webpage:

[CONFIRMATIONURL]

',
	"Part of the message that is sent to their new email address when users change their information,
  	and the email address has changed","textarea"),

# this is the text that is placed in the [!-- confirmation --] location of the above
# message, in case the email is sent to their old email address and they have changed
# their email address
"emailchanged_text_oldaddress" => array('
  Please Note: when updating your details, your email address has changed.

  A message has been sent to your new email address with a URL
  to confirm this change. Please visit this website to activate
  your membership.
',
	"Part of the message that is sent to their old email address when users change their information,
  	and the email address has changed","textarea"),

"personallocation_subject" => array('Your personal location',
"Subject of message to send when users request their personal location","text"),

"personallocation_message" => array('

You have requested your personal location to update your details from our website.
The location is below. Please make sure that you use the full line as mentioned below.
Sometimes email programme can wrap the line into multiple lines.

Your personal location is:
[PREFERENCESURL]

Thank you.
','Message to send when they request their personal location',"textarea"),

"messagefooter" => array("--\n$strToUnsubscribe [UNSUBSCRIBE]\n\n$strToUpdate [PREFERENCES]\n",
	"Default footer for sending a message","textarea"),

"pageheader" => array($defaultheader,"Header of public pages. The header should start with &lt;/head&gt; . You can add header elements, but don't add the title or other basic header elements.","textarea"),

"pagefooter" => array($defaultfooter,"Footer of public pages","textarea"),

"html_charset" => array("iso-8859-1","Charset for HTML messages","text"),
"text_charset" => array("iso-8859-1","Charset for Text messages","text"),

"html_email_style" => array('
<style type="text/css">
body { font-size : 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
a { font-size: 11px; color: #ff6600; font-style: normal; font-family: verdana, sans-serif; text-decoration: none; }
a:visited { color: #666666; }
a:hover {	text-decoration: underline; }
p { font-weight: normal; font-size: 11px; color: #666666; font-style: normal; font-family: verdana, sans-serif; text-decoration: none; }
h1 {font-weight: bold; font-size: 14px; color: #666666; font-style: normal; font-family: verdana, sans-serif; text-decoration: none;}
h2 {font-weight: bold; font-size: 13px; color: #666666; font-style: normal; font-family: verdana, sans-serif; text-decoration: none;}
h3 {font-weight: bold; font-size: 12px; color: #666666; font-style: normal; font-family: verdana, sans-serif; text-decoration: none; margin:0px; padding:0px;}
h4 {font-weight: bold; font-size: 11px; color: #666666; font-style: normal; font-family: verdana, sans-serif; text-decoration: none; margin:0px; padding:0px;}
hr {width : 100%; height : 1px; color: #ff9900; size:1px;}
</style>
',"CSS for HTML messages without a template","textarea"),

"alwayssendtextto" => array("mail.com\nemail.com","Domains that only accept text emails, one per line","textarea"),
# do not do anything with this line
"last one" => "blah");

if (defined("ENABLE_RSS") && ENABLE_RSS) {
	$default_config["rssthreshold"] = array(4,"Minimum amount of items to send in an RSS feed","text");
  $default_config["rsstexttemplate"] = array(
  '
  [title]
  [description]
  URL: [link]
  ',"Template for text item in RSS feeds","textarea");
  $default_config["rsshtmltemplate"] = array(
  '<br/>
  <a href="[link]"><b>[title]</b></a><br/>
  <p>[description]</p>
  <hr/>
  ',"Template for HTML item in RSS feeds","textarea");
	$default_config["rssthreshold"] = array(4,"Minimum amount of items to send in an RSS feed","text");
	$default_config["rssmax"] = array(30,"Maximum amount of items to send in an RSS feed","text");
  $default_config["rsstextseparatortemplate"] = array(
  '**** [listname] ******

  ',"Template for separator between feeds in RSS feeds (text)","textarea");
  $default_config["rsshtmlseparatortemplate"] = array(
  '<br/>
	<h3>[listname]</h3>
  ',"Template for separator between feeds in RSS feeds (HTML)","textarea");

}

########## certainly do not edit after this #########

$redfont = "<font color=\"red\">";
$efont = "</font>";

if (!TEST && REGISTER && defined("VERSION")) {
  if (ereg("dev",VERSION)) $v = "dev"; else $v = VERSION;
  $PoweredBy = '<p align=left><a href="http://www.phplist.com"><img src="http://phplist.tincan.co.uk/images/'.$v.'/power-phplist.png" width=88 height=31 title="Powered by PHPlist" alt="Powered by PHPlist" border="0"></a></p>';
} else
  $PoweredBy = '<center><a href="http://www.phplist.com"><img src="images/power-phplist.png" width=88 height=31 title="Powered by PHPlist" alt="Powered by PHPlist" border="0"></a></center>';

if (!function_exists("getconfig")) {
  function getConfig($item) {
    global $default_config,$domain,$website,$tables;
    $hasconf = Sql_Table_Exists($tables["config"]);
    if ($hasconf)
      $req = Sql_Query("select value from {$tables["config"]} where item = \"$item\"");
    if (!Sql_Affected_Rows() || !$hasconf) {
      $value = $default_config[$item][0];
      # save the dafault value to the database, so we can obtain
      # the information when running off commandline
      if (Sql_Table_Exists($tables["config"]))
	      saveConfig($item,addslashes($value));
  #  	print "$item => $value<br/>";
    } else {
      $row = Sql_fetch_Row($req);
      $value = $row[0];
    }
    $value = preg_replace('/\[DOMAIN\]/i', $domain, $value);
    $value = preg_replace('/\[WEBSITE\]/i', $website, $value);
    $value = preg_replace('/<\?=VERSION\?>/i', VERSION, $value);
    $type = $default_config[$item][2];

    if ($type == "boolean") {
      if ($value == "0") {
        $value = "false";
      } elseif ($value == "1") {
        $value = "true";
      }
    }
  #  print "$item => $value<br/>";
  	# if this is a subpage item, and no value was found get the global one
  	if (!$value && ereg(":",$item)) {
    	list($a,$b) = split(":",$item);
      return getConfig($a);
   	} else {
	    return $value;
    }
  }
} else {
	reset($default_config);
	while (list($item,$values) = each ($default_config)) {
		$val = getConfig($item);
    if (!isset($val)) {
			saveConfig($item,$values[0],0);
		}
	}
}

function getUserConfig($item,$userid = 0) {
	global $default_config,$tables;
  $hasconf = Sql_Table_Exists($tables["config"]);
  if ($hasconf)
    $req = Sql_Query("select value from {$tables["config"]} where item = \"$item\"");
  if (!Sql_Affected_Rows() || !$hasconf)
  	$value = $default_config[$item][0];
	else {
	  $row = Sql_fetch_Row($req);
		$value = $row[0];
	}
  # if this is a subpage item, and no value was found get the global one
  if (!$value && ereg(":",$item)) {
    list($a,$b) = split(":",$item);
    $value = getUserConfig($a,$userid);
  }
  if ($userid) {
    $user_req = Sql_Fetch_Row_Query("select uniqid from {$tables["user"]} where id = $userid");
    $uniqid = $user_req[0];
    # parse for placeholders
    # do some backwards compatibility:
    $url = getConfig("unsubscribeurl");$sep = ereg('\?',$url)?'&':'?';
    $value = eregi_replace('\[UNSUBSCRIBEURL\]', $url.$sep.'uid='.$uniqid, $value);
    $url = getConfig("confirmationurl");$sep = ereg('\?',$url)?'&':'?';
    $value = eregi_replace('\[CONFIRMATIONURL\]', $url.$sep.'uid='.$uniqid, $value);
    $url = getConfig("preferencesurl");$sep = ereg('\?',$url)?'&':'?';
  	$value = eregi_replace('\[PREFERENCESURL\]', $url.$sep.'uid='.$uniqid, $value);
  }
  $value = eregi_replace('\[SUBSCRIBEURL\]', getConfig("subscribeurl"), $value);
  if ($value == "0") {
    $value = "false";
  } elseif ($value == "1") {
    $value = "true";
  }
  return $value;
}

# list of pages and categorisation in the system
$system_pages = array (
  "system" => array (
    "adminattributes" => "none",
    "attributes" => "none",
    "upgrade" => "none",
    "configure" => "none",
    "spage" => "owner",
    "spageedit" => "owner",
    "defaultconfig" => "none",
    "defaults" => "none",
    "initialise" => "none",
		"bounces" => "none",
		"bounce" => "none",
		"processbounces" => "none",
    "eventlog" => "none",
    "reconcileusers" => "none",
    "getrss" => "owner",
    "viewrss" => "owner",
    "setup" => "none"
  ),
  "list" => array(
    "list" => "owner",
    "editlist" => "owner",
    "members" => "owner"
  ),
  "user" => array(
    "user" => "none",
    "users" => "none",
    "dlusers" => "none",
    "editattributes" => "none",
    "import1" => "none",
    "import2" => "none",
    "import3" => "none",
    "import4" => "none",
    "import" => "none",
    "export" => "none"
  ),
  "message" => array(
    "message" => "owner",
    "messages" => "owner",
    "processqueue" => "none",
    "send" => "owner",
    "preparesend" => "none",
    "sendprepared" => "all",
    "template" => "none",
    "templates" => "none"
  ),
  "admin" => array(
    "admins" => "none",
    "admin" => "owner"
  )
);

$access_levels =
  array(
    0 => "none",
    1 => "all",
    2 => "view",
 #   3 => "edit",
    4 => "owner"
  );

?>
