<?
require_once "accesscheck.php";

# library used for plugging into the webbler, instead of "connect"
# depricated and should be removed

if (!defined(TEST))
	define("TEST",0);

#error_reporting(63);

# make sure magic quotes are on. Try to switch it on
ini_set("magic_quotes_gpc","on");
if (!defined("USE_PDF")) define("USE_PDF",0);
if (!defined("ENABLE_RSS")) define("ENABLE_RSS",0);
if (!defined("ALLOW_ATTACHMENTS")) define("ALLOW_ATTACHMENTS",0);
if (!defined("EMAILTEXTCREDITS")) define("EMAILTEXTCREDITS",0);
if (!defined("PAGETEXTCREDITS")) define("PAGETEXTCREDITS",0);
if (!defined("NUMCRITERIAS")) define("NUMCRITERIAS",2);

$domain = getConfig("domain");
$website = getConfig("website");
if (is_object($config["plugins"]["phplist"])) {
	$tables = $config["plugins"]["phplist"]->tables;
  $table_prefix = $config["plugins"]["phplist"]->table_prefix;
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

function sendMail ($to,$subject,$message,$header,$parameters) {
	# global function to capture sending emails, to avoid trouble with
	# older php versions
	$v = phpversion();
	$v = preg_replace("/\-.*$/","",$v);
	if ($v > "4.0.5")
    return mail($to,$subject,$message,$header,$parameters);
	else
    return mail($to,$subject,$message,$header);
}

function safeImageName($name) {
  $name = "image".ereg_replace("\.","DOT",$name);
  $name = ereg_replace("-","DASH",$name);
  $name = ereg_replace("_","US",$name);
  $name = ereg_replace("/","SLASH",$name);
  return $name;
}

function clean2 ($value) {
  $value = trim($value);
  $value = ereg_replace("\r","",$value);
  $value = ereg_replace("\n","",$value);
  $value = ereg_replace('"',"&quot;",$value);
  $value = ereg_replace("'","&rsquo;",$value);
  $value = ereg_replace("`","&lsquo;",$value);
  $value = stripslashes($value);
  return $value;
}

if (TEST && REGISTER)
  $pixel = '<img src="http://phplist.tincan.co.uk/images/pixel.gif" width=1 height=1>';

function Menu() {
  global $pixel,$tables;
  $html = "";
  if ($GLOBALS["require_login"])
    $html .= PageLink2("logout","Logout").'&nbsp;<br /><br />';
  $html .= PageLink2("home","Main Page")."&nbsp;<br />";
  $html .= PageLink2("configure","Configure")."&nbsp;<br />";
  $html .= '&nbsp;=====<br />';
  $html .= PageLink2("list","Lists").'&nbsp;<br />';
  $html .= PageLink2("users","Users").'&nbsp;<br />';
  $html .= PageLink2("messages","Messages").'&nbsp;<br />';
  $html .= PageLink2("import","Import Emails").'&nbsp;<br />';
  $html .= '&nbsp;=====<br />';
	$url = getConfig("subscribeurl");
	if ($url)
		$html .= '<a href="'.$url.'">Subscribe</a>&nbsp;<br/>';
	else
	  $html .= '<a href="../?p=subscribe">Signup</a>&nbsp;<br/>';
	$url = getConfig("unsubscribeurl");
	if ($url)
		$html .= '<a href="'.$url.'">Unsubscribe</a>&nbsp;<br/>';
	else
	  $html .= '<a href="../?p=unsubscribe">Sign Off</a>&nbsp;<br/>';

  $html .= '&nbsp;=====<br />';
  $html .= PageLink2("attributes","Attributes").'&nbsp;<br />';
  if ($tables["attribute"] && Sql_Table_Exists($tables["attribute"])) {
    $res = Sql_Query("select * from {$tables['attribute']}",1);
    while ($row = Sql_Fetch_array($res)) {
      if ($row["type"] != "checkbox" && $row["type"] != "textline" && $row["type"] != "hidden")
        $html .= PageLink2("editattributes",$row["name"],"id=".$row["id"]) ."&nbsp;<br />";
    }
  }
  $html .= '&nbsp;=====<br />';
  $html .= PageLink2("messages","All Messages").'&nbsp;<br />';
  $html .= PageLink2("templates","Templates").'&nbsp;<br />';
  $html .= PageLink2("send","Send a message").'&nbsp;<br />';
  $html .= PageLink2("preparesend","Prepare a message").'&nbsp;<br />';
  $html .= PageLink2("sendprepared","Send a message").'&nbsp;<br />';
  $html .= '&nbsp;=====<br />';
  $html .= PageLink2("processqueue","Process Queue").'&nbsp;<br />';
  return $html . $pixel;
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

function previewTemplate($id,$adminid = 0,$text = "") {
  global $tables;
  if ($_GET["pi"])
  	$rest = '&pi='.$_GET["pi"];
  $tmpl = Sql_Fetch_Row_Query(sprintf('select template from %s where id = %d',$tables["template"],$id));
  $template = $tmpl[0];
  $img_req = Sql_Query(sprintf('select id,filename from %s where template = %d order by filename desc',$tables["templateimage"],$id));
  while ($img = Sql_Fetch_Array($img_req)) {
    $template = preg_replace("#".preg_quote($img["filename"])."#","?page=image&id=".$img["id"].$rest,$template);
  }
  if ($adminid) {
    $att_req = Sql_Query("select name,value from {$tables["adminattribute"]},{$tables["admin_attribute"]} where {$tables["adminattribute"]}.id = {$tables["admin_attribute"]}.adminattributeid and {$tables["admin_attribute"]}.adminid = $adminid");
    while ($att = Sql_Fetch_Array($att_req)) {
      $template = preg_replace("#\[LISTOWNER.".strtoupper(preg_quote($att["name"]))."\]#",$att["value"],$template);
    }
  }
  $template = preg_replace("#\[CONTENT\]#",$text,$template);
  $template = ereg_replace("\[[A-Z\. ]+\]","",$template);
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

function logEvent($msg) {
	global $tables;
  if (Sql_Table_Exists($tables["eventlog"]))
	Sql_Query(sprintf('insert into %s (entered,page,entry) values(now(),"%s","%s")',$tables["eventlog"],
  	$GLOBALS["page"],addslashes($msg)));
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

if (!function_exists("dbg")) {
  function dbg($msg) {
  }
}
?>
