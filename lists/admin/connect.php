<?php

if (is_file(getenv("DOCUMENT_ROOT") ."/../VERSION")) {
  $fd = fopen (getenv("DOCUMENT_ROOT") ."/../VERSION", "r");
  while ($line = fscanf ($fd, "%[a-zA-Z0-9,. ]=%[a-zA-Z0-9,. ]")) {
    list ($key, $val) = $line;
    if ($key == "VERSION")
      $version = $val . "-";
  }
  fclose($fd);
} else {
  $version = "dev";
}

define("VERSION","2.7.2");

if (file_exists("commonlib/lib/userlib.php")) {
	include_once ("commonlib/lib/userlib.php");
} elseif (file_exists("admin/commonlib/lib/userlib.php")) {
	include_once ("admin/commonlib/lib/userlib.php");
}

# make sure magic quotes are on. Try to switch it on, this may fail
ini_set("magic_quotes_gpc","on");

# set some defaults if they are not specified
if (!defined("REGISTER")) define("REGISTER",1);
if (!defined("USE_PDF")) define("USE_PDF",0);
if (!defined("ENABLE_RSS")) define("ENABLE_RSS",0);
if (!defined("ALLOW_ATTACHMENTS")) define("ALLOW_ATTACHMENTS",0);
if (!defined("EMAILTEXTCREDITS")) define("EMAILTEXTCREDITS",0);
if (!defined("PAGETEXTCREDITS")) define("PAGETEXTCREDITS",0);
if (!defined("USEFCK")) define("USEFCK",0);
if (!defined("ASKFORPASSWORD")) define("ASKFORPASSWORD",0);
if (!defined("ENCRYPTPASSWORD")) define("ENCRYPTPASSWORD",0);
if (!defined("PHPMAILER")) define("PHPMAILER",0);
if (!defined("MANUALLY_PROCESS_QUEUE")) define("MANUALLY_PROCESS_QUEUE",1);
if (!defined("CHECK_SESSIONIP")) define("CHECK_SESSIONIP",1);
if (!defined("FILESYSTEM_ATTACHMENTS")) define("FILESYSTEM_ATTACHMENTS",0);
if (!defined("MIMETYPES_FILE")) define("MIMETYPES_FILE","/etc/mime.types");
if (!defined("DEFAULT_MIMETYPE")) define("DEFAULT_MIMETYPE","application/octet-stream");
if (!defined("USE_REPETITION")) define("USE_REPETITION",0);
if (!defined("USE_EDITMESSAGE")) define("USE_EDITMESSAGE",0);
# let's keep it default to old behaviour for now
if (!defined("USE_PREPARE")) define("USE_PREPARE",1);

if (!isset($GLOBALS["export_mimetype"])) $GLOBALS["export_mimetype"] = 'application/csv';

if (!defined("WORKAROUND_OUTLOOK_BUG") && defined("USE_CARRIAGE_RETURNS")) {
	define("WORKAROUND_OUTLOOK_BUG",USE_CARRIAGE_RETURNS);
}

$GLOBALS["img_tick"] = '<img src="images/tick.gif" alt="Yes">';
$GLOBALS["img_cross"] = '<img src="images/cross.gif" alt="No">';

# if keys need expanding with 0-s
$checkboxgroup_storesize = 1; # this will allow 10000 options for checkboxes

if (!isset($adminlanguage) || !is_array($adminlanguage)) {
	$adminlanguage = array(
		"info" => "en",
    "iso" => "en",
  	"charset" => "iso-8859-1",
	);
}

# identify pages that can be run on commandline
$commandline_pages = array("send","processqueue","processbounces");

if (isset($message_envelope))
  $envelope = "-f$message_envelope";
$database_connection = Sql_Connect($database_host,$database_user,$database_password,$database_name);

if (!isset($table_prefix))
	$table_prefix = "";
if (!isset($usertable_prefix))
  $usertable_prefix = $table_prefix;

$tables = array(
  "user" => $usertable_prefix . "user",
  "list" => $table_prefix . "list",
  "listuser" => $table_prefix . "listuser",
  "message" => $table_prefix . "message",
  "listmessage" => $table_prefix . "listmessage",
  "usermessage" => $table_prefix . "usermessage",
  "attribute" => $usertable_prefix . "attribute",
  "user_attribute" => $usertable_prefix . "user_attribute",
  "sendprocess" => $table_prefix . "sendprocess",
  "template" => $table_prefix . "template",
  "templateimage" => $table_prefix . "templateimage",
  "bounce" => $table_prefix ."bounce",
  "user_message_bounce" => $table_prefix . "user_message_bounce",
  "config" => $table_prefix . "config",
  "admin" => $table_prefix . "admin",
  "adminattribute" => $table_prefix . "adminattribute",
  "admin_attribute" => $table_prefix . "admin_attribute",
  "admin_task" => $table_prefix . "admin_task",
  "task" => $table_prefix . "task",
  "subscribepage" => $table_prefix."subscribepage",
  "subscribepage_data" => $table_prefix . "subscribepage_data",
  "eventlog" => $table_prefix . "eventlog",
  "attachment" => $table_prefix."attachment",
  "message_attachment" => $table_prefix . "message_attachment",
  "rssitem" => $table_prefix . "rssitem",
  "rssitem_data" => $table_prefix . "rssitem_data",
  "user_rss" => $table_prefix . "user_rss",
  "rssitem_user" => $table_prefix . "rssitem_user",
  "listrss" => $table_prefix . "listrss"
);
$domain = getConfig("domain");
$website = getConfig("website");

$rssfrequencies = array(
#	"hourly" => $strHourly, # to be added at some other point
	"daily" => $strDaily,
  "weekly" => $strWeekly,
  "monthly" => $strMonthly
);

$redfont = "<font color=\"red\">";
$efont = "</font>";
$GLOBALS["coderoot"] = "";

/*
	We request you retain the $PoweredBy variable including the links.
	This not only gives respect to the large amount of time given freely
  by the developers	but also helps build interest, traffic and use of
  PHPlist, which is beneficial to it's future development.

  You can configure your PoweredBy options in your config file

	Michiel Dethmers, Tincan Ltd 2003
*/
if (ereg("dev",VERSION)) $v = "dev"; else $v = VERSION;
if (REGISTER) {
	$PoweredByImage = '<p align=left><a href="http://www.phplist.com"><img src="http://phplist.tincan.co.uk/images/'.$v.'/power-phplist.png" width=70 height=30 title="Powered by PHPlist version '.$v.', &copy; tincan ltd" alt="Powered by PHPlist'.$v.', &copy tincan ltd" border="0"></a></p>';
} else {
	$PoweredByImage = '<p align=left><a href="http://www.phplist.com"><img src="images/power-phplist.png" width=70 height=30 title="Powered by PHPlist version '.$v.', &copy; tincan ltd" alt="Powered by PHPlist'.$v.', &copy tincan ltd" border="0"></a></p>';
}
$PoweredByText = '';
if (!TEST && REGISTER) {
  if (!PAGETEXTCREDITS) {;
  	$PoweredBy = $PoweredByImage;
  } else {
  	$PoweredBy = $PoweredByText;
	}
} else {
  if (!PAGETEXTCREDITS) {;
  	$PoweredBy = $PoweredByImage;
  } else {
  	$PoweredBy = $PoweredByText;
	}
}
# some other configuration variables, which need less tweaking
# number of users to show per page if there are more
define ("MAX_USER_PP",50);
define("MAX_MSG_PP",10);

function formStart($additional="") {
	global $form_action,$page,$p;
	# depending on server software we can post to the directory, or need to pass on the page
  if ($form_action) {
		$html = sprintf('<form method=post action="%s" %s>',$form_action,$additional);
		# retain all get variables as hidden ones
    foreach ( array("p","page") as $key) {
    	$val = $_REQUEST[$key];
      if ($val)
	    	$html .= sprintf('<input type=hidden name="%s" value="%s">',
	      	$key,$val);
    }
 	}
  else
  	$html = sprintf('<form method=post %s>',$additional);
/*		$html = sprintf('<form method=post action="./" %s>
		%s',$additional,isset($page) ?
		'<input type=hidden name="page" value="'.$page.'">':(
		isset($p)?'<input type=hidden name="p" value="'.$p.'">':"")
		);
*/
  return $html;
}

function checkAccess($page) {
  global $tables;
  if (!$GLOBALS["require_login"] || isSuperUser())
    return 1;
  # check whether it Is a page to protect
  Sql_Query("select id from {$tables["task"]} where page = \"$page\"");
  if (!Sql_Affected_Rows())
    return 1;
  $req = Sql_Query(sprintf('select level from %s,%s where adminid = %d and page = "%s" and %s.taskid = %s.id',
    $tables["task"],$tables["admin_task"],$_SESSION["logindetails"]["id"],$page,$tables["admin_task"],$tables["task"]));
  $row = Sql_Fetch_Row($req);
  if (!$row[0])
    return 0;
  return 1;
}

function adminName($id) {
  global $tables;
  $req = Sql_Fetch_Row_Query(sprintf('select loginname from %s where id = %d',$tables["admin"],$id));
  return $req[0] ? $req[0] : "<font color=red>Nobody</font>";
}

function listName($id) {
  global $tables;
  $req = Sql_Fetch_Row_Query(sprintf('select name from %s where id = %d',$tables["list"],$id));
  return $req[0] ? $req[0] : "Unnamed List";
}

function HTMLselect ($name, $table, $column, $value) {
  $res = "<!--$value--><select name=$name>\n";
  $result = Sql_Query("SELECT id,$column FROM $table");
  while($row = Sql_Fetch_Array($result)) {
    $res .= "<option value=".$row["id"] ;
    if ($row["$column"] == $value)
      $res .= " selected";
    if ($row["id"] == $value)
      $res .= " selected";
    $res .= ">" . $row[$column] . "\n";
  }
  $res .= "</select>\n";
  return $res;
}

function sendMail ($to,$subject,$message,$header = "",$parameters = "") {
	# global function to capture sending emails, to avoid trouble with
	# older (and newer!) php versions
	if (TEST)
		return 1;
  if (!$to)  {
		logEvent("Error: empty To: in message with subject $subject to send");
  	return 0;
  } elseif (!$subject) {
		logEvent("Error: empty Subject: in message to send to $to");
  	return 0;
  }    
	$v = phpversion();
	$v = preg_replace("/\-.*$/","",$v);
	if ($GLOBALS["message_envelope"]) {
  	$header = rtrim($header);
    if ($header)
      $header .= "\n";
		$header .= "Errors-To: ".$GLOBALS["message_envelope"];
		if (!$parameters || !ereg("-f".$GLOBALS["message_envelope"],$parameters)) {
			$parameters = '-f'.$GLOBALS["message_envelope"];
		}
	}

  if (WORKAROUND_OUTLOOK_BUG) {
  	$header = rtrim($header);
    if ($header)
      $header .= "\n";
 		$header .= "X-Outlookbug-fixed: Yes";
		$message = preg_replace("/\r?\n/", "\r\n", $message);
  }

  # version 4.2.3 (and presumably up) does not allow the fifth parameter in safe mode
  # make sure not to send out loads of test emails to ppl when developing
  if (!ereg("dev",VERSION)) {
    if ($v > "4.0.5" && !ini_get("safe_mode")) {
    	if (mail($to,$subject,$message,$header,$parameters))
      	return 1;
      else
      	return mail($to,$subject,$message,$header);
    }
    else
      return mail($to,$subject,$message,$header);
  } else {
  	# send mails to one place when running a test version
    $message = "To: $to\n".$message;
    if ($GLOBALS["developer_email"]) {
     	return mail($GLOBALS["developer_email"],$subject,$message,$header,$parameters);
    } else {
      print Warn("Running CVS version, but developer_email not set");
    }
  }
}

function sendAdminCopy($subject,$message) {
	$sendcopy = getConfig("send_admin_copies");
  if ($sendcopy == "true") {
  	$admin_mail = getConfig("admin_address");
    $mails = explode(",",getConfig("admin_addresses"));
    array_push($mails,$admin_mail);
    $sent = array();
    foreach ($mails as $admin_mail) {
    	if (!$sent[$admin_mail]) {
	  	  sendMail($admin_mail,$subject,$message,system_messageheaders($admin_mail));
        $sent[$admin_mail] = 1;
     	}
   	}
  }
}

function sendReport($subject,$message) {
	$report_addresses = explode(",",getConfig("report_address"));
	foreach ($report_addresses as $address) {
  	sendMail($address,$GLOBALS["installation_name"]." ".$subject,$message);
 	}
}

function timeDiff($time1,$time2) {
	if (!$time1 || !$time2) {
  	return "Unknown";
 	}
	$t1 = strtotime($time1);
  $t2 = strtotime($time2);

  if ($t1 < $t2) {
  	$diff = $t2 - $t1;
  } else {
  	$diff = $t1 - $t2;
  }
  if ($diff == 0)
  	return "very little time";
  $hours = (int)($diff / 3600);
  $mins = (int)(($diff - ($hours * 3600)) / 60);
  $secs = (int)($diff - $hours * 3600 - $mins * 60);

  if ($hours)
  	$res = $hours . " hours";
  if ($mins)
  	$res .= " ".$mins . " mins";
  if ($secs)
  	$res .= " ".$secs . " secs";
  return $res;
}

function sendMessageStats($msgid) {
	global $stats_collection_address,$tables;
	if (defined("NOSTATSCOLLECTION") && NOSTATSCOLLECTION) {
  	return;
 	}
  if (!isset($stats_collection_address)) {
  	$stats_collection_address = 'phplist-stats@tincan.co.uk';
  }
  $data = Sql_Fetch_Array_Query(sprintf('select * from %s where id = %d',
  	$tables["message"],$msgid));
  $msg .= "PHPlist version ".VERSION . "\n";
  $diff = timeDiff($data["sendstart"],$data["sent"]);

  if ($data["id"] && $data["processed"] > 10 && $diff != "very little time") {
  	$msg .= "\n".'Time taken: '.$diff;
  	foreach (array('entered','processed',
    	'sendstart','sent','htmlformatted','sendformat','template','astext',
		  'ashtml','astextandhtml','aspdf','astextandpdf') as $item) {
      	$msg .= "\n".$item.' => '.$data[$item];
    }
    if ($stats_collection_address == 'phplist-stats@tincan.co.uk' && $data["processed"] > 500) {
			mail($stats_collection_address,"PHPlist stats",$msg);
    } else {
			mail($stats_collection_address,"PHPlist stats",$msg);
    }
  }
}

function safeImageName($name) {
  $name = "image".ereg_replace("\.","DOT",$name);
  $name = ereg_replace("-","DASH",$name);
  $name = ereg_replace("_","US",$name);
  $name = ereg_replace("/","SLASH",$name);
  return $name;
}

function normalize($var) {
  $var = str_replace(" ","_",$var);
  $var = str_replace(";","",$var);
  return $var;
}

function ClineSignature() {
  return "PHPlist version ".VERSION." (c) 2000-".date("Y")." Tincan Ltd, http://www.phplist.com\n";
}  

function ClineError($msg) {
  ob_end_clean();
  print ClineSignature();
  print "\nError: $msg\n";
  exit;
}

function clineUsage($line = "") {
#	if (!ereg("dev",VERSION))
	  ob_end_clean();
  print clineSignature();
  print "Usage: ".$_SERVER["SCRIPT_FILENAME"]." -p page $line\n\n";
  exit;
}

function parseCline() {
	$res = array();
  $cur = "";
  foreach ($GLOBALS["argv"] as $clinearg) {
  	if (substr($clinearg,0,1) == "-") {
    	$par = substr($clinearg,1,1);
      $clinearg = substr($clinearg,2,strlen($clinearg));
     # $res[$par] = "";
      $cur = $par;
	    $res[$cur] .= $clinearg;
   	} elseif ($cur) {
    	if ($res[$cur])
		    $res[$cur] .= ' '.$clinearg;
      else
      	$res[$cur] .= $clinearg;
    }
  }
  return $res;
}

function Error($msg) {
  if ($GLOBALS["commandline"]) {
    clineError($msg);
    return;
  }
  print "<div class=\"error\" align=center>Error: $msg </div>";
  $message = '

  An error has occurred in the Mailinglist System
  URL: '.$_SERVER["REQUEST_URI"].'
  Error message: ' . $msg;

  $message .= "\n==== debugging information\n\nSERVER Vars\n";
  if (is_array($_SERVER))
  while (list($key,$val) = each ($_SERVER))
  	if ($key != "password")
	    $message .= $key . "=" . $val . "\n";
  $message .= "\nPOST Vars\n";
  if (is_array($_POST))
  while (list($key,$val) = each ($_POST))
  	if ($key != "password")
	    $message .= $key . "=" . $val . "\n";
  sendMail(getConfig("report_address"),"Mail list error",$message,"");
}

function clean ($value) {
  $value = trim($value);
  $value = ereg_replace("\r","",$value);
  $value = ereg_replace("\n","",$value);
  $value = ereg_replace('"',"&quot;",$value);
  $value = ereg_replace("'","&rsquo;",$value);
  $value = ereg_replace("`","&lsquo;",$value);
  $value = stripslashes($value);
  return $value;
}

function Fatal_Error($msg) {
  print "<div align=\"center\" class=\"error\">Fatal Error: $msg </div>";
  $message = '

  An error has occurred in the Mailinglist System
  URL: '.$_SERVER["REQUEST_URI"].'
  Error: ' . $msg;
  sendMail(getConfig("report_address"),"Mail list error",$message,"");
 # include "footer.inc";
 # exit;
  return 0;
}

function Warn($msg) {
  print "<div align=center class=\"error\">Warning: $msg </div>";
  $message = '

  An warning has occurred in the Mailinglist System

  ' . $msg;
  sendMail(getConfig("report_address"),"Mail list warning",$message,"");
}

function Info($msg) {
  print "<center><table border=1><tr><td class=\"error\">Information: $msg </td></tr></table></center>";
}

function logEvent($msg) {
	global $tables;
  if (Sql_Table_Exists($tables["eventlog"]))
	Sql_Query(sprintf('insert into %s (entered,page,entry) values(now(),"%s","%s")',$tables["eventlog"],
  	$GLOBALS["page"],addslashes($msg)));
}

$main_menu = array(
  "setup" => "Configure",
  "community" => "Help",
  "div1" => "<hr />",
  "list" => "Lists",
  "send"=>"Send a message",
  "users" => "Users",
  "usermgt" => "Manage Users",
  "messages" => "Messages",
  "div2" => "<hr />",
  "templates" => "Templates",
  "preparesend"=>"Prepare a message",
  "sendprepared"=>"Send a prepared message",
  "processqueue"=>"Process Queue",
  "processbounces"=>"Process Bounces",
  "bounces"=>"View Bounces",
  "eventlog"=>"Eventlog"
);

function newMenu() {
  if ($GLOBALS["firsttime"]) { return; }
  $access = accessLevel("spage");
  switch ($access) {
    case "owner":
      $subselect = sprintf(' where owner = %d',$_SESSION["logindetails"]["id"]);break;
    case "all":
    case "view":
      $subselect = "";break;
    case "none":
    default:
      $subselect = " where id = 0";break;
  }
  if (TEST && REGISTER)
    $pixel = '<img src="http://phplist.tincan.co.uk/images/pixel.gif" width=1 height=1>';
  global $tables;
  $html = "";
	$spb ='<span class="menulinkleft">';
	$spe = '</span>';
  if ($GLOBALS["require_login"])
    $html .= $spb.PageLink2("logout","Logout").'<br />'.$spe;
    
  $_GET["pi"] = "";
  $html .= $spb.PageLink2("home","Main Page").$spe;
  $req = Sql_Query(sprintf('select * from %s %s',$tables["subscribepage"],$subselect));
  $spages = array();
  if (Sql_Affected_Rows()) {
    $spages["div1"] = $GLOBALS["strSubscribeTitle"];
    while ($row = Sql_Fetch_Array($req)) {
      $spages[sprintf('%s&id=%d',getConfig("subscribeurl"),$row["id"])] = $row["title"];
    }
    $url = getConfig("unsubscribeurl");
    if ($url)
      $spages[$url] = 'Unsubscribe';
  } else {
#	  $html .= $spb.sprintf('<a href="%s">%s</a>',getConfig("subscribeurl"),$GLOBALS["strSubscribeTitle"]).$spe;
    $spages["spage"] = "Create Subscribe Pages";
  }
  if ($tables["attribute"] && Sql_Table_Exists($tables["attribute"])) {
    $attrmenu = array();
    $res = Sql_Query("select * from {$tables['attribute']}",1);
    while ($row = Sql_Fetch_array($res)) {
      if ($row["type"] != "checkbox" && $row["type"] != "textarea" && $row["type"] != "textline" && $row["type"] != "hidden")
        $attrmenu["editattributes&id=".$row["id"]] = strip_tags($row["name"]);
    }
  }
  foreach ($GLOBALS["main_menu"] as $page => $desc) {
    $link = PageLink2($page,$desc);
    if ($link) {
      if ($page == "preparesend" || $page == "sendprepared") {
        if (USE_PREPARE) {
          $html .= $spb.$link.$spe;
        }
      } else {
        $html .= $spb.$link.$spe;
      }
    }
  }
  if (sizeof($GLOBALS["plugins"])) {
    $html .= $spb."<hr/>".$spe;
    foreach ($GLOBALS["plugins"] as $pluginName => $plugin) {
      $html .= $spb.PageLink2("main&pi=$pluginName",$pluginName).$spe;
    }
  }  

  return $html . $pixel;
}

function SaveConfig($item,$value,$editable=1) {
	global $tables;
  if ($value == "false" || $value == "no") {
  	$value = 0;
  } else if ($value == "true" || $value == "yes") {
  	$value = 1;
  }
 	Sql_Query(sprintf('replace into %s (item,value,editable) values("%s","%s",%d)',
 	  $tables["config"],$item,$value,$editable));
}

function debug($text) {
  global $debug;
  if ($debug)
    echo "\n<b>$text</b><br>\n";
}

function PageLink2($name,$desc="",$url="") {
  if ($url)
    $url = "&".$url;
  $access = accessLevel($name);
  if ($access == "owner" || $access == "all" || $access == "view") {
    if ($name == "processqueue" && !MANUALLY_PROCESS_QUEUE)
      return "";#'<!-- '.$desc.'-->';
    elseif ($name == "processbounces" && !MANUALLY_PROCESS_BOUNCES)
      return "";#'<!-- '.$desc.'-->';
    else {
    	if (!preg_match("/&pi=/i",$name) && $_GET["pi"] && is_object($GLOBALS["plugins"][$_GET["pi"]])) {
      	$pi = '&pi='.$_GET["pi"];
      } else {
      	$pi = "";
      }
    	return sprintf('<a href="./?page=%s%s%s">%s</a>',$name,$url,$pi,strtolower($desc));
    }
  } else return "";
#    return "\n<!--$name disabled $access -->\n";
#    return "\n$name disabled $access\n";
}

function SidebarLink($name,$desc,$url="") {
  if ($url)
    $url = "&".$url;
  $access = accessLevel($name);
  if ($access == "owner" || $access == "all") {
    if ($name == "processqueue" && !MANUALLY_PROCESS_QUEUE)
      return '<!-- '.$desc.'-->';
    elseif ($name == "processbounces" && !MANUALLY_PROCESS_BOUNCES)
      return '<!-- '.$desc.'-->';
    else
    	return sprintf('<a href="./?page=%s%s" target="phplistwindow">%s</a>',$name,$url,strtolower($desc));
  } else
    return "\n<!--$name disabled $access -->\n";
#    return "\n$name disabled $access\n";
}

function PageURL2($name,$desc = "",$url="") {
  if ($url)
    $url = "&".$url;
  return sprintf('./?page=%s%s',$name,$url);
}

function Redirect($page) {
	global $website,$adminpages;
	Header("Location: http://".$website."$adminpages/?page=$page");
  exit;
}

function formatBytes ($value) {
	$gb = 1024 * 1024 * 1024;
  $mb = 1024 * 1024;
  $kb = 1024;
	$gbs = $value / $gb;
	if ($gbs > 1)
		return sprintf('%2.2fGb',$gbs);
  $mbs = $value / $mb;
  if ($mbs > 1)
		return sprintf('%2.2fMb',$mbs);
  $kbs = $value / $kb;
  if ($kbs > 1)
		return sprintf('%dKb',$kbs);
	else
  	return sprintf('%dBytes',$value);
}

### process locking stuff
function getPageLock() {
	global $tables;
	$thispage = $GLOBALS["page"];
  $running_req = Sql_query("select now() - modified,id from ".$tables["sendprocess"]." where page = \"$thispage\" and alive order by started desc");
  $running_res = Sql_Fetch_row($running_req);
	$waited = 0;
  while ($running_res[1]) { # a process is already running
    output ("A process for this page is already running and it was still alive $running_res[0] seconds ago");
    output ("Sleeping for 20 seconds, aborting will now quit");
    $abort = ignore_user_abort(0);
    sleep(20);
		$waited++;
		if ($waited > 10) {
			# we have waited 10 cycles, abort and quit script
			output("We've been waiting too long, I guess the other script is still going ok");
			exit;
		}
    $running_req = Sql_query("select now() - modified,id from ".$tables["sendprocess"]." where page = \"$thispage\" and alive order by started desc");
    $running_res = Sql_Fetch_row($running_req);
    if ($running_res[0] > 1200) # some sql queries can take quite a while
      # process has been inactive for too long, kill it
      Sql_query("update {$tables["sendprocess"]} set alive = 0 where id = $running_res[1]");
  }
  $res = Sql_query('insert into '.$tables["sendprocess"].' (started,page,alive,ipaddress) values(now(),"'.$thispage.'",1,"'.getenv("REMOTE_ADDR").'")');
  $send_process_id = Sql_Insert_Id();
  $abort = ignore_user_abort(1);
  return $send_process_id;
}

function keepLock($processid) {
	global $tables;
	$thispage = $GLOBALS["page"];
  Sql_query("Update ".$tables["sendprocess"]." set alive = alive + 1 where id = $processid");
}

function checkLock($processid) {
	global $tables;
	$thispage = $GLOBALS["page"];
  $res = Sql_query("select alive from {$tables['sendprocess']} where id = $processid");
  $row = Sql_Fetch_Row($res);
  return $row[0];
}

function releaseLock($processid) {
	global $tables;
  if (!$processid) return;
  Sql_query("delete from {$tables["sendprocess"]} where id = $processid");
}

# I would prefer not to use this version, as it is very heavy on memory, loading the content
# of an entire table in memory, which tends to fail, but the other one (below) is not
# always available, hmm
function upgradeTableOld($table,$tablestructure) {
  $columns = array();
  $records = array();

  $cols = Sql_Query("show columns from $table");
  while ($row = Sql_Fetch_Row($cols))
    array_push($columns,$row[0]);

  $recs = Sql_Query("select * from $table");
  while ($data = Sql_Fetch_Array($recs)) {
    $rec = array();
    reset($columns);
    foreach ($columns as $column)
      $rec[$column] = $data[$column];

    # this is likely to require some memory, do we need to intercept that?
    # hmm
    array_push($records,$rec);
  }

  Sql_Query("drop table $table");
  Sql_Create_table($table,$tablestructure);

  foreach ($records as $record) {
#    while (list($key,$val) = each ($record))
#      print "$key => $val<br>\n";
    $collist = "";
    $vallist = "";

    reset($tablestructure);
    while (list($column, $value) = each ($tablestructure)) {
      if ($column != "primary key" && $column != "unique") {
        $collist .= "$column,";
        $vallist .= sprintf('"%s",',addslashes($record[$column]));
      }
    }
    $collist = substr($collist,0,-1);
    $vallist = substr($vallist,0,-1);
    $query = "replace into $table ($collist) values($vallist)";
    Sql_Query($query);
  }
}

# This one is a bit better, as it writes data to file instead of memory, but
# its not brilliant
function upgradeTable($table,$tablestructure) {
  global $tmpdir;
  $columns = array();
  $records = array();

  $fname = tempnam($tmpdir,"");
  $fp = fopen($fname,"w");
  if (Sql_Table_Exists($table)) {
    $cols = Sql_Query("show columns from $table");
    while ($row = Sql_Fetch_Row($cols))
      array_push($columns,$row[0]);

    #$fp = tmpfile();
#    print "Writing tempfile $fname<br>";
    $recs = Sql_Query("select * from $table");
    while ($data = Sql_Fetch_Array($recs)) {
      reset($columns);
      foreach ($columns as $column) {
        fwrite($fp,"$column:".base64_encode($data[$column])."\n");
      }
      fwrite($fp,"--\n");
    }
  }
  fclose($fp);

  Sql_Query("drop table if exists $table");
  Sql_Create_table($table,$tablestructure);

  $fp=fopen($fname,"r");
  if (!$fp) {
    unlink($fname);
    return 0;
  }
#  print "Reading tempfile<br>";
  while (!feof($fp)) {
    # read one record
    $buffer = "";
    $record = array();
    $buffer = fgets($fp, 4096);
    while (!feof ($fp) && !ereg("^--",$buffer)) {
      list($column,$value) = explode(":",$buffer);
      if ($column && $value)
        $record[$column] = base64_decode($value);
      $buffer = fgets($fp, 4096);
    }

    $collist = "";
    $vallist = "";
    if (sizeof($record)) {
      reset($tablestructure);
      while (list($column, $value) = each ($tablestructure)) {
        if ($column != "primary key" && $column != "unique") {
          $collist .= "$column,";
          $vallist .= sprintf('"%s",',addslashes($record[$column]));
        }
      }
      $collist = substr($collist,0,-1);
      $vallist = substr($vallist,0,-1);
      $query = "replace into $table ($collist) values($vallist)";
#      print $query . "<br>";
      if (!Sql_Query($query)) {
        unlink($fname);
        return 0;
      }
    }
  }
  fclose($fp);
  unlink($fname);
  return 1;
}

# this version can only be used with the right permissions, which are generally not
# available by default, so for now they are disabled
function upgradeTable2($table,$tablestructure) {
  global $tmpdir;
  $filename = "phpupgrade.tmp";
  if (is_file("$tmpdir/$filename"))
    unlink("$tmpdir/$filename");
  if (Sql_Verbose_Query("select * from $table into outfile '$tmpdir/$filename'")) {
    Sql_Query("drop table $table");
    Sql_Create_table($table,$tablestructure);
    Sql_Query("load data infile '$tmpdir/$filename' into $table IGNORE");
  } else {
    print '<p>Error: Cannot dump old database tables. Please make sure you have the correct Database and File permissions.</p>
      <p>Required permissions are:
        <ul>
        <li>Mysql "alter" and "file" permission on your database</li>
        <li>Write permission in '.$tmpdir.'</li>
        </ul>';
  }
  if (is_file("$tmpdir/$filename"))
    unlink("$tmpdir/$filename");
}

function previewTemplate($id,$adminid = 0,$text = "", $footer = "") {
  global $tables;
  $tmpl = Sql_Fetch_Row_Query(sprintf('select template from %s where id = %d',$tables["template"],$id));
  $template = stripslashes($tmpl[0]);
  $img_req = Sql_Query(sprintf('select id,filename from %s where template = %d order by filename desc',$tables["templateimage"],$id));
  while ($img = Sql_Fetch_Array($img_req)) {
    $template = preg_replace("#".preg_quote($img["filename"])."#","?page=image&id=".$img["id"],$template);
  }
  if ($adminid) {
    $att_req = Sql_Query("select name,value from {$tables["adminattribute"]},{$tables["admin_attribute"]} where {$tables["adminattribute"]}.id = {$tables["admin_attribute"]}.adminattributeid and {$tables["admin_attribute"]}.adminid = $adminid");
    while ($att = Sql_Fetch_Array($att_req)) {
      $template = preg_replace("#\[LISTOWNER.".strtoupper(preg_quote($att["name"]))."\]#",$att["value"],$template);
    }
  }
  if ($footer)
	  $template = eregi_replace("\[FOOTER\]",$footer,$template);
  $template = preg_replace("#\[CONTENT\]#",$text,$template);
  $template = eregi_replace("\[UNSUBSCRIBE\]",sprintf('<a href="%s">%s</a>',getConfig("unsubscribeurl"),$GLOBALS["strThisLink"]),$template);
  $template = eregi_replace("\[PREFERENCES\]",sprintf('<a href="%s">%s</a>',getConfig("preferencesurl"),$GLOBALS["strThisLink"]),$template);
  if (!EMAILTEXTCREDITS) {
	  $template = eregi_replace("\[SIGNATURE\]",$GLOBALS["PoweredByImage"],$template);
  } else {
	  $template = eregi_replace("\[SIGNATURE\]",$GLOBALS["PoweredByText"],$template);
  }
  $template = ereg_replace("\[[A-Z\. ]+\]","",$template);
  $template = ereg_replace('<form','< form',$template);
  $template = ereg_replace('</form','< /form',$template);

  return $template;
}

function parseMessage($content,$template,$adminid = 0) {
  global $tables;
  $tmpl = Sql_Fetch_Row_Query("select template from {$tables["template"]} where id = $template");
  $template = $tmpl[0];
  $template = preg_replace("#\[CONTENT\]#",$content,$template);
  $att_req = Sql_Query("select name,value from {$tables["adminattribute"]},{$tables["admin_attribute"]} where {$tables["adminattribute"]}.id = {$tables["admin_attribute"]}.adminattributeid and {$tables["admin_attribute"]}.adminid = $adminid");
  while ($att = Sql_Fetch_Array($att_req)) {
    $template = preg_replace("#\[LISTOWNER.".strtoupper(preg_quote($att["name"]))."\]#",$att["value"],$template);
  }
  return $template;
}

function listOwner($listid = 0) {
  global $tables;
  $req = Sql_Fetch_Row_Query("select owner from {$tables["list"]} where id = $listid");
  return $req[0];
}

function Help($topic,$text = '?') {
	return sprintf('<a href="javascript:help(\'help/?topic=%s\')">%s</a>',$topic,$text);
}

function system_messageHeaders($useremail = "") {
  $from_address = getConfig("message_from_address");
  $from_name = getConfig("message_from_name");
  if ($from_name)
  	$additional_headers = "From: \"$from_name\" <$from_address>\n";
  else
	  $additional_headers = "From: $from_address\n";
  $message_replyto_address = getConfig("message_replyto_address");
  if ($message_replyto_address)
    $additional_headers .= "Reply-To: $message_replyto_address\n";
  else
    $additional_headers .= "Reply-To: $from_address\n";
  $v = VERSION;
  $v = ereg_replace("-dev","",$v);
  $additional_headers .= "X-Mailer: PHPlist version $v (www.phplist.com)\n";
	$additional_headers .= "X-MessageID: systemmessage\n";
	if ($useremail)
		$additional_headers .= "X-User: ".$useremail."\n";
  return $additional_headers;
}

if (!function_exists("dbg")) {
  function dbg($msg) {
  }
}

function PageData($id) {
	global $tables;
  $req = Sql_Query(sprintf('select * from %s where id = %d',$tables["subscribepage_data"],$id));
  if (!Sql_Affected_Rows()) {
  	FileNotFound();
    exit;
  }
  while ($row = Sql_Fetch_Array($req))
    $data[$row["name"]] = preg_replace('/<\?=VERSION\?>/i', VERSION, $row["data"]);
	return $data;
}

function PageAttributes($data) {
  $attributes = explode('+',$data["attributes"]);
  if (is_array($attributes)) {
    foreach ($attributes as $attribute) {
      if ($data[sprintf('attribute%03d',$attribute)]) {
          list($attributedata[$attribute]["id"],
          $attributedata[$attribute]["default_value"],
          $attributedata[$attribute]["listorder"],
          $attributedata[$attribute]["required"]) = explode('###',$data[sprintf('attribute%03d',$attribute)]);
        $sorted[$attributedata[$attribute]["id"]] = $attributedata[$attribute]["listorder"];
      }
    }
    if (is_array($sorted)) {
	    $attributes = $sorted;
	    asort($attributes);
    }
  }
	return array($attributes,$attributedata);
}

function formatDate ($date,$short = 0) {
  $months = array("","January","February","March","April","May","June","July","August","September","October","November","December");
  $shortmonths = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
  $year = substr($date,0,4);
  $month = substr($date,5,2);
  $day = substr($date,8,2);
  $day = ereg_replace("^0","",$day);

  if ($date) {
  	if ($short)
	    return $day . "&nbsp;" . $shortmonths[intval($month)] . "&nbsp;" . $year;
    else
	    return $day . "&nbsp;" . $months[intval($month)] . "&nbsp;" . $year;
   }
}

$oldpoweredimage = 'iVBORw0KGgoAAAANSUhEUgAAAFgAAAAfCAMAAABUFvrSAAAABGdBTUEAALGPC/xhBQAAAMBQTFRFmQAAZgAAmgICmwUFnAgInQsLnxAQbw4OohYWcBERpBwcpiIiqCcnqiwsfCAgrDAwrjU1rzg4sTs7iTAws0FBtEVFtklJuU9Pu1VVn0pKkEREvltbtFxcwWRkw2trm1ZWrGNjx3V1y3x8zoWFqW5u0I6O15ycuoqK3aysxZqa3rm55s3N8t3d9+zs+fHx5t/f/Pf3/fr6////7+/vz8/PtbW1j4+Pb29vVVVVRkZGKioqExMTDg4OBwcHAwMDAAAAB4LGQwAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAHdElNRQfSBAITGhB/UY5ZAAAD2ElEQVR4nI2VC3uiOhCGoVqq9YbcZHGxIoI0SLGhIJdt8///1c4kHnVPhTpPK4TPvEzmpkTvsiK/73vckmAuSdJ93/26G5wEhsQN7uuaVTSrWP1BGT1WtCpgUWUf7FhVX1WWVZ/Hz/Qu6ltoSf8ZLFnxwfKypPBXZ02dsrQss7oovnJ+PZa0au6gHqJFT5KuwDmjGctZzp09lux4pF911RRFTT/x+geU8ifqe2T3pX8MEsM+ioY2BThHyyavm5TWRQbhKMS1KVJQOo24ivR/o/RY101Oi4Yd4SUVBoTmNaCqnOYV0POqKLtyR7zBNyoHVz+402nxZqI83uIi+KdSWjtOfFPYh+boeaB8D4N0Xx3LsnzjaRK5hqZOkNwK7u4rIsv6Nyrxl0t7YRmc3ApmneCdLK//efAWhxvPW63cpc3JreCU1QyrNj/31+tul5K1s+brtSzv0p3j7IS0ffHW+lT3kO3aljYbP7eBcyhk6BAKnXGJ6gv8y0NMmg4eD3G1pe97iIvs4OIpCjbearkw1PGoDQzFm7OU5U124sbI3G6HIriIcXY6pnAf+VzCF+kHCIhrm/NJK7iqM+gKdmmvV+Er8hPMHcY44bURrbn0HqGU+OAyxKIV3JQweWh9dphu8dgiCARzNwXujrsfvfCIkGiKUrBBsMvnpAl4xTThBm10qeO8uTQgBDE+XQkF1I4eyBr9fiM6SntC+DsjDqY+d9CTzAQcmHGCdwFX58xdOmKIlClHRQ7yee4gRoQ84VMOnp/BJFaUfcRvpZudF5/AcB2eYns6+z4QKxKgREOevDPYo6E7kjrAkDtw57B38PTgowOIULi65RIhXDpAVUC5ncGSBwF0O8C4W08xqk+pSOQ+XInc/bqWYlEUZ7BtSkpEO8DgzlTm9koPOn7G/i90MQn1a8kX/UFDKAMe48S2430b+BDjqVNsvCmBcPIERp6OuYuDaykCLrYH34a0WQTBmt0EH8hm6f7mhRu8QsCSEGYNFJHvuitYktW15AJX6x6bwt7JSlWNxRJO/ULf/E0QBjDAwGy05dJdeSfJ55INXJhAg9ZfEGHEfVaexzPNssWpcSyCTwvLsngvWQt76QqJzzUcmXPO7QLHq4H00FcGo8ncsHjFRq4Y5NocTFXVuWYAWkh8EoO76onbbwHHHh+oCAaX54aubxPqA9U0tNlsMpmMwSYzVTNMIeErTXCXx/fxsd+7Cd6MTzcPvcfBYIRkKwxD2KnB1vFo9CxsNJ6A2yZItmWdNOT2+73b4LMBGFzG/RrYXBU7uSkKfKA0UyEwVyJwe72Hh1u4v1tVRVPPqSx/AAAAAElFTkSuQmCC';
$oldnewpoweredimage = 'iVBORw0KGgoAAAANSUhEUgAAAFgAAAAfCAYAAABjyArgAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsSAAALEgHS3X78AAAAB3RJTUUH0wcfFB4OyvJGjAAACCJJREFUeJztmj1sG8kVx3+zS+qDsi+hIV9yCXAClIo13foqOXYbAVJjVynkLvBVEpAi1RlUZVcBJKRMkxNgd0EcscoBqW6LJAWDQ0xARqoTIZ7Ppj5I7k6Kt48zu/yQZFmxcfADCO7ODmd3f/OfN2/e0ACWD3ZpVgCwW+/6Md4XM2AMBAUIpqEwA1M/huJVmPpIzoMimIJUt32IT6D3Grrfw3ELui8h6YGNMWuW4N2+EGw8MRPPL988qGERCnMw9SOYnYfZn0Dpp1D6GKbLArowB+G01FfQ/WMBHXdds1YcQwZwtAfNlnyr+efNFrQPXV2/rH0I9cZwW82WnNcb7reZe73IeqjohR1c8++TP76wGQMmgCCEcAbC2RzYT2DuZzAzL2DDaTCh/DbpQb8jaj1qQbcN3e8gPpJrSTy4TcG/58ZTWJwXECtVUdJOZFmcN+xEsHhdYFQ/hdVteP4FbP4F1u/A/T/CUgU2n8HuA9fW2k245V3bugfRnmEnspRLw+8d7cFOZIheWLbuud/UG4Zmy1JduDBZp1gTQjglcBXi1EfyCYopVA/RwCV0oHcI/ddwcgDdV5CcgE3AxqJeMwIwQG1Zvle3BeaXa1AuWW48hN0UZPsQ1j5zKtyJBKaaKlnAuPLFealbb1h2H0g7q9vZ+y9eh9qypd6QuksV+Y72LOt33pSpScEGqWqnBWBhRoZ88SoU5+QTzoqqM2BjSPqiWvW33ZcCWlWrYGEAdyRgkBdXdfnH5ZJ82h3D2meWjScCAKC6IGpdqZJRZrkkYNdvO1eiHTNKwWrqItZuSqc2W9lOPDdUE0IwJYotXknBlqBwJZ28pp2q82Djo1S1HXEHvdfid5MTuW4t44KxIcD6Mlt33bm+KAjIaM+yOC+gassCanVblAbiItSqC1CODDceuhGxflvqjwLW7sCtxwzqqmlHTjRjwBpRkAlEpQo1nJFP8aoALV5JJ6sp1wFqo8D2XsmnfyTlNkl97eQo1wBWw7Rbj7Nw3gdrtqSTxVXlr6Zj0YxRqkINp0WpxZKUK/igmG0u6UHSFT/bPxoGm3SH3cEEM/dzCl6pXoDEJVlzH9ZuGsoliwx5cGBPgVosQVhy8es4tdrEgVUfG58Mg00SRLFnX5tlFPzeWMrSzRbesFdAppDO9KkS/eEfTE2GChIRJLH40f6xiwp0Iou7nmKTiX52nA0p+N2acV865GEyUPWhxbnsNVNIV2OB+GS1vFrVDSRdiQriYzd5DUIuuEg24fIBD9QIAyVqmc0pExxMEwgwEw4DzatU6w8Nf7JQk54os/9aFKtuIO6mE1c6uWHP5GPPYucDrPccWs2abLkPMMDBgyxISIdwmMaeKUxdhhavQpgehzOpKtM8wSigg+f0oNo4Xcqm6uy9kmM9t/0LuYHTTAAbfxiRBWg91eUzF6PAQfalNWAPigJRj03oQKoyFab6zcyQD+XBxgFV1flKHQVVfat2groBb/X1Nk3ePpw5vaa/shkFUxWo1wflHkTIgvQVq0H+oEMmAAUPUOIC/v6RHOvw96FqPf1N3g1cUo5J3mZmwhIp8MHmPIpCAwEHAs8GMsGYFLoJUoC5KMCfyGAyTBgGmvS9iUknro4Xz3pKfYMQ622YECv9XFJ1k0xBqQU5RechaWecFaKawkwsEIuP1MlnFND42LmEpOfq56HmXd//yYTC7DU3ceTNJsPlOnkBmac+Dd4oS2Fs/OFfbP7pm6HLa7/8mNrdTyiXYtqvjrj26+eUS3Dw+2sp+C7tTsK135xI+eNAzj8fvtXiPNSWDSvVrIo3n0lSatwq1tyXa6ct1yXHkm1fAAfT2eH+NszYVEGp2cR9+yqzCSR9on9/C0D1FyXKc9Jp0X86bP/1W8pmn9rdeaJ/7kudBaD3vbSXJETNxJXbZJA3LpcYpDc1ybS6bTl4NDnRlLfdB5wpTbrxFJYq2c47fxysQ9gVQIIABQdQ63oQXejUh7gvxwDxCfV/fAfA17+bGySsd/5+yOq2laR89yXRC6m+VCGTaMmUe+frdySxpHbjoUCO9s6YPBph7UPY/kqOF+edWqM9udbcl2PtEAGcnIxvUWGBAwbuG9LgHJcISRIwyRBEiTnVp/bS38RE3xwA6UN1Xw46pdmy6YsAcS+zo1FvWO/YvTDYQT09VzDN/bT8+vjXHWWaBFuqyHG7I23UG5b124basmXjqeu8Zsu5GwF83HJhk69QBQUCy7fE239SeOAAaowJorZBmafu1FRx7Q5s/lnalZ0NKZcklAO38WQ0iOqCUxMIgGZL8tc7kaV9KJDOlVf2rN6Qtp9/IW3IpoDcc/eBwF+qZEeNAH793xHJEC808s/VBvtOGqhrvWGAQ6rXoD5d5zf35aTZysIrlyTfvFRJFZgm6f2djXbHsPnMDhL7Wg/cUFYVL1WyOebzWnVB7n/rkbS1VDFs3Zsc9gngk/boq0P+VsuTCedeSGS9Mv0y2SJwG5+1Zdnvg+wEBU6V1YWsQuoNy+YzV9evV/uVq6dwLmLlEnz9W9mH3Ilg+yvZeNj9fPyoEMD94/GtZpbN+Ys2ezixLmPjUPWhPri85SeyceV6vlJ984lsnDVb8qxb93Tz1u3kjHt2/QfF+FbPuvB5wyDeV9xZ6vkT16jycfVOs/zfDkAmMl+ZzX236Vv91P1lQUedPs9wFPEOTRXnP+TIeoOOsBPLx9U79Tn23F6gWm05q8ylipRt/81twq7fcSNlpSpzSL0BB4+k7P3c0fiBmLk/nID8YG/ZzueoPti57X8R0X5CmAXRQQAAAABJRU5ErkJggg==';
$newpoweredimage = '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';

function FileNotFound() {
	ob_end_clean();
  header("404, File Not Found");
  printf('<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1>The requested document was not found on this server<br/>Please contact the <a href="mailto:%s?subject=File not Found: %s">Administrator</a><p><hr><address><a href="http://tincan.co.uk/phplist" target="_tincan">phplist</a> version %s</address></body></html>',getConfig("admin_address"),$_SERVER["REQUEST_URI"],VERSION);
  exit;
}

function findMime($filename) {
  list($name,$ext) = explode(".",$filename);
  if (!$ext || !is_file(MIMETYPES_FILE)) {
    return DEFAULT_MIMETYPE;
  }
  $fp = @fopen(MIMETYPES_FILE,"r");
  $contents = fread($fp,filesize(MIMETYPES_FILE));
  fclose($fp);
  $lines = explode("\n",$contents);
  foreach ($lines as $line) {
    if (!ereg("#",$line) && !preg_match("/^\s*$/",$line)) {
    	$line = preg_replace("/\t/"," ",$line);
      $items = explode(" ",$line);
      $mime = array_shift($items);
      foreach ($items as $extension) {
        $extension = trim($extension);
        if ($ext == $extension) {
          return $mime;
        }
      }
    }
  }
  return DEFAULT_MIMETYPE;
}

function repeatMessage($msgid) {
	if (!USE_REPETITION) return;
  $msgdata = Sql_Fetch_Array_Query(
  	sprintf('select *,date_add(embargo,interval repeat minute) as newembargo,
    	date_add(now(),interval repeat minute) as newembargo2, date_add(embargo,interval repeat minute) > now() as isfuture
    	from %s where id = %d and repeatuntil > now()',$GLOBALS["tables"]["message"],$msgid));
  if (!$msgdata["id"] || !$msgdata["repeat"]) return;
  
  # copy the new message
  Sql_Query(sprintf('
  	insert into %s (entered) values(now())',$GLOBALS["tables"]["message"]));
  $id = Sql_Insert_id();
  require $GLOBALS["coderoot"]."structure.php";
  if (!is_array($DBstruct["message"])) {
		logEvent("Error including structure when trying to duplicate message $msgid");
    return;
 	}
  foreach ($DBstruct["message"] as $column => $rec) {
  	if ($column != "id" && $column != "entered") {
      Sql_Query(sprintf('update %s set %s = "%s" where id = %d',
        $GLOBALS["tables"]["message"],$column,addslashes($msgdata[$column]),$id));
   	}
  }
  # correct some values
  if (!$msgdata["isfuture"]) {
  	$msgdata["newembargo"] = $msgdata["newembargo2"];
  }

  Sql_Query(sprintf('update %s set embargo = "%s",status = "submitted",sent = "" where id = %d',
			$GLOBALS["tables"]["message"],$msgdata["newembargo"],$id));
  foreach (array("processed","astext","ashtml","astextandhtml","aspdf","astextandpdf","viewed", "bouncecount") as $item) {
    Sql_Query(sprintf('update %s set %s = 0 where id = %d',
        $GLOBALS["tables"]["message"],$item,$id));
	}

  # lists
  $req = Sql_Query(sprintf('select listid from %s where messageid = %d',$GLOBALS["tables"]["listmessage"],$msgid));
  while ($row = Sql_Fetch_Row($req)) {
  	Sql_Query(sprintf('insert into %s (messageid,listid,entered) values(%d,%d,now())',
    	$GLOBALS["tables"]["listmessage"],$id,$row[0]));
  }

  # attachments
  $req = Sql_Query(sprintf('select * from %s,%s where %s.messageid = %d and %s.attachmentid = %s.id',
		$GLOBALS["tables"]["message_attachment"],$GLOBALS["tables"]["attachment"],
    $GLOBALS["tables"]["message_attachment"],$msgid,$GLOBALS["tables"]["message_attachment"],
    $GLOBALS["tables"]["attachment"]));
  while ($row = Sql_Fetch_Array($req)) {
  	if (is_file($row["remotefile"])) {
    	# if the "remote file" is actually local, we want to refresh the attachment, so we set
      # filename to nothing
      $row["filename"] = "";
    }

  	Sql_Query(sprintf('insert into %s (filename,remotefile,mimetype,description,size)
    	values("%s","%s","%s","%s",%d)',
      $GLOBALS["tables"]["attachment"],addslashes($row["filename"]),addslashes($row["remotefile"]),
      addslashes($row["mimetype"]),addslashes($row["description"]),$row["size"]));
    $attid = Sql_Insert_id();
    Sql_Query(sprintf('insert into %s (messageid,attachmentid) values(%d,%d)',
			$GLOBALS["tables"]["message_attachment"],$id,$attid));
  }
	logEvent("Message $msgid was successfully rescheduled");
}


function formatTime($time,$short = 0) {
	return $time;
}

function formatDateTime ($datetime,$short = 0) {
  $date = substr($datetime,0,10);
  $time = substr($datetime,11,8);
  return formatDate($date,$short). " ".formatTime($time,$short);
}
?>
