
<div align="center">
<table><tr><td>

<?
require_once "accesscheck.php";

$dbversion = getConfig("version");
if (!$dbversion)
  $dbversion = "Older than 1.4.1";
print '<p>Your database version: '.$dbversion.'</p>';
if ($dbversion == VERSION)
  print "Your database is already the correct version, there is no need to upgrade";
else

if ($doit == "yes") {
  $success = 1;
  # once we are off, this should not be interrupted
  ignore_user_abort(1);
  # rename tables if we are using the prefix
  include $GLOBALS["coderoot"] . "structure.php";
  while (list($table,$value) = each ($DBstruct)) {
    set_time_limit(500);
    if (isset($table_prefix)) {
      if (Sql_Table_exists($table) && !Sql_Table_Exists($tables[$table])) {
        Sql_Verbose_Query("alter table $table rename $tables[$table]",1);
      }
    }
  }
  ob_end_flush();
  print '<script language="Javascript" src="js/progressbar.js" type="text/javascript"></script>';
  print '<script language="Javascript" type="text/javascript"> document.write(progressmeter); start();</script>';
  # upgrade depending on old version
  $dbversion = ereg_replace("-dev","",$dbversion);
  switch ($dbversion) {
    case "1.4.1":
      # nothing changed,
    case "1.4.2":
      # nothing changed,
  	case "dev":
    case "1.4.3":
    	foreach (array("admin","adminattribute","admin_attribute","task","admin_task") as $table) {
      	if (!Sql_Table_Exists($table)) {
					Sql_Create_Table($tables[$table],$DBstruct[$table]);
          if ($table == "admin") {
            # create a default admin
            Sql_Query(sprintf('insert into %s values(0,"%s","%s","%s",now(),now(),"%s","%s",now(),%d,0)',
              $tables["admin"],"admin","admin","",$adminname,"phplist",1));
          } elseif ($table == "task") {
            while (list($type,$pages) = each ($system_pages)) {
              foreach ($pages as $page)
                Sql_Query(sprintf('insert into %s (page,type) values("%s","%s")',
                  $tables["task"],$page,$type));
            }
          }
     		}
      }
      Sql_Query("alter table {$tables["list"]} add column owner integer");
      Sql_Query("alter table {$tables["message"]} change column status status enum('submitted','inprocess','sent','cancelled','prepared')");
      Sql_Query("alter table {$tables["template"]} change column template template longblob");
      # previous versions did not cleanup properly, fix that here
      $req = Sql_Query("select userid from {$tables["user_attribute"]} left join {$tables["user"]} on {$tables["user_attribute"]}.userid = {$tables["user"]}.id where {$tables["user"]}.id IS NULL");
      while ($row = Sql_Fetch_Row($req))
        Sql_query("delete from ".$tables["user_attribute"]." where userid = ".$row[0]);
      $req = Sql_Query("select user from {$tables["user_message_bounce"]} left join {$tables["user"]} on {$tables["user_message_bounce"]}.user = {$tables["user"]}.id where {$tables["user"]}.id IS NULL");
      while ($row = Sql_Fetch_Row($req))
        Sql_query("delete from ".$tables["user_message_bounce"]." where user = ".$row[0]);
      $req = Sql_Query("select userid from {$tables["usermessage"]} left join {$tables["user"]} on {$tables["usermessage"]}.userid = {$tables["user"]}.id where {$tables["user"]}.id IS NULL");
      while ($row = Sql_Fetch_Row($req))
        Sql_query("delete from ".$tables["usermessage"]." where userid = ".$row[0]);

      $success = 1;
    case "1.5.0":
			# nothing changed
    case "1.5.1":
			# nothing changed
		case "1.6.0":
		case "1.6.1": # not released
			# nothing changed
    case "1.6.2":
    	# something we should have done ages ago. make checkboxes save "on" value in user_attribute
      $req = Sql_Query("select * from {$tables["attribute"]} where type = \"checkbox\"");
      while ($row = Sql_Fetch_Array($req)) {
      	$req2 = Sql_Query("select * from $table_prefix"."listattr_$row[tablename]");
        while ($row2 = Sql_Fetch_array($req2)) {
        	if ($row2["name"] == "Checked")
          	Sql_Query(sprintf('update %s set value = "on" where attributeid = %d and value = %d',
            	$tables["user_attribute"],$row["id"],$row2["id"]));
        }
       	Sql_Query(sprintf('update %s set value = "" where attributeid = %d and value != "on"',
         	$tables["user_attribute"],$row["id"]));
        Sql_Query("drop table $table_prefix"."listattr_".$row["tablename"]);
      }
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"export\",\"user\")");
    case "1.6.3":
		case "1.6.4":
			Sql_Query("alter table {$tables["user"]} add column bouncecount integer default 0");
			Sql_Query("alter table {$tables["message"]} add column bouncecount integer default 0");
			# we actually never used these tables, so we can just as well drop and recreate them
			Sql_Query("drop table if exists {$tables["bounce"]}");
			Sql_Query("drop table if exists {$tables["user_message_bounce"]}");
			Sql_Query(sprintf('create table %s (
        id integer not null primary key auto_increment,
        date datetime,
        header text,
        data blob,
        status varchar(255),
        comment text)',$tables["bounce"]));
	   Sql_Query(sprintf('create table %s (
        id integer not null primary key auto_increment,
        user integer not null,
        message integer not null,
        bounce integer not null,
        time timestamp,
        index (user,message,bounce))',
				$tables["user_message_bounce"]));
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"bounce\",\"system\")");
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"bounces\",\"system\")");
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"processbounces\",\"system\")");
    case "1.7.0":
    case "1.7.1":
    case "1.8.0":
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"spage\",\"system\")");
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"spageedit\",\"system\")");
			Sql_Query("alter table {$tables["user"]} add column subscribepage integer default 0");
    	Sql_Create_Table($tables["subscribepage"],$DBstruct["subscribepage"]);
    	Sql_Create_Table($tables["subscribepage_data"],$DBstruct["subscribepage_data"]);
    case "1.9.0":
    case "1.9.1":
    case "1.9.2":
    	# no changes
    case "1.9.3":
    	# add some indexes to speed things up
      Sql_Query("alter table {$tables["bounce"]} add index dateindex (date)");
    	Sql_Create_Table($tables["eventlog"],$DBstruct["eventlog"]);
      Sql_Query("alter table {$tables["sendprocess"]} add column page varchar(100)");
      Sql_Query("alter table {$tables["message"]} add column sendstart datetime");
      # some cleaning up of data:
			$req = Sql_Query("select {$tables["usermessage"]}.userid
      	from {$tables["usermessage"]} left join {$tables["user"]} on {$tables["usermessage"]}.userid = {$tables["user"]}.id
        where {$tables["user"]}.id IS NULL group by {$tables["usermessage"]}.userid");
			while ($row = Sql_Fetch_Row($req)) {
      	Sql_Query("delete from {$tables["usermessage"]} where userid = $row[0]");
   		}
 			$req = Sql_Query("select {$tables["user_attribute"]}.userid
      	from {$tables["user_attribute"]} left join {$tables["user"]} on {$tables["user_attribute"]}.userid = {$tables["user"]}.id
        where {$tables["user"]}.id IS NULL group by {$tables["user_attribute"]}.userid");
			while ($row = Sql_Fetch_Row($req)) {
      	Sql_Query("delete from {$tables["user_attribute"]} where userid = $row[0]");
   		}
 			$req = Sql_Query("select {$tables["listuser"]}.userid
      	from {$tables["listuser"]} left join {$tables["user"]} on {$tables["listuser"]}.userid = {$tables["user"]}.id
        where {$tables["user"]}.id IS NULL group by {$tables["listuser"]}.userid");
			while ($row = Sql_Fetch_Row($req)) {
      	Sql_Query("delete from {$tables["listuser"]} where userid = $row[0]");
   		}
 			$req = Sql_Query("select {$tables["usermessage"]}.userid
      	from {$tables["usermessage"]} left join {$tables["user"]} on {$tables["usermessage"]}.userid = {$tables["user"]}.id
        where {$tables["user"]}.id IS NULL group by {$tables["usermessage"]}.userid");
			while ($row = Sql_Fetch_Row($req)) {
      	Sql_Query("delete from {$tables["usermessage"]} where userid = $row[0]");
   		}
 			$req = Sql_Query("select {$tables["user_message_bounce"]}.user
      	from {$tables["user_message_bounce"]} left join {$tables["user"]} on {$tables["user_message_bounce"]}.user = {$tables["user"]}.id
        where {$tables["user"]}.id IS NULL group by {$tables["user_message_bounce"]}.user");
			while ($row = Sql_Fetch_Row($req)) {
      	Sql_Query("delete from {$tables["user_message_bounce"]} where user = $row[0]");
   		}
    case "2.1.0":
    case "2.1.1":
    	# oops deleted tables columns that should not have been deleted:
			if (!Sql_Table_Column_Exists($tables["message"],"tofield")) {
      	Sql_Query("alter table {$tables["message"]} add column tofield varchar(255)");
   		}
			if (!Sql_Table_Column_Exists($tables["message"],"replyto")) {
      	Sql_Query("alter table {$tables["message"]} add column replyto varchar(255)");
   		}
		case "2.1.2":
    case "2.1.3":
    case "2.1.4":
    	Sql_Query("alter table {$tables["message"]} change column asboth astextandhtml integer default 0");
    	Sql_Query("alter table {$tables["message"]} add column aspdf integer default 0");
    	Sql_Query("alter table {$tables["message"]} add column astextandpdf integer default 0");
     	Sql_Query("alter table {$tables["message"]} add column rsstemplate varchar(100)");
   		Sql_Query("alter table {$tables["list"]} add column rssfeed varchar(255)");
    	Sql_Query("alter table {$tables["user"]} add column rssfrequency varchar(100)");
    	Sql_Create_Table($tables["message_attachment"],$DBstruct["message_attachment"]);
    	Sql_Create_Table($tables["attachment"],$DBstruct["attachment"]);
    	Sql_Create_Table($tables["rssitem"],$DBstruct["rssitem"]);
    	Sql_Create_Table($tables["rssitem_data"],$DBstruct["rssitem_data"]);
    	Sql_Create_Table($tables["user_rss"],$DBstruct["user_rss"]);
    	Sql_Create_Table($tables["rssitem_user"],$DBstruct["rssitem_user"]);
    case "2.2.0":
    case "2.2.1":
    	Sql_Query("alter table {$tables["user"]} add column password varchar(255)");
    	Sql_Query("alter table {$tables["user"]} add column passwordchanged datetime");
    	Sql_Query("alter table {$tables["user"]} add column disabled tinyint default 0");
    	Sql_Query("alter table {$tables["user"]} add column extradata text");
    	Sql_Query("alter table {$tables["message"]} add column owner integer");
    case "2.3.0":
    case "2.3.1":
    case "2.3.2":case "2.3.3":
    	Sql_Create_Table($tables["listrss"],$DBstruct["listrss"]);
    case "2.3.4":case "2.4.0":
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"import3\",\"user\")");
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"import4\",\"user\")");
    case "2.5.0":case "2.5.1":
    case "2.5.2":
      Sql_Query("alter table {$tables["subscribepage"]} add column owner integer");
      Sql_Query("alter ignore table {$tables["task"]} add unique (page)");
    case "2.5.3":case "2.5.4":
      Sql_Query("alter table {$tables["user"]} add column foreignkey varchar(100)");
      Sql_Query("alter table {$tables["user"]} add index fkey (foreignkey)");
    case "2.5.5": case "2.5.6": case "2.5.7": case "2.5.8":
      # some very odd value managed to sneak in
      $cbgroups = Sql_Query("select id from {$tables["attribute"]} where type = \"checkboxgroup\"");
      while ($row = Sql_Fetch_Row($cbgroups)) {
        Sql_Query("update {$tables["user_attribute"]} set value = \"\" where attributeid = $row[0] and value=\"Empty\"");
      }
    case "2.6.0":case "2.6.1":case "2.6.2":case "2.6.3":case "2.6.4":case "2.6.5":
			Sql_Verbose_Query("alter table {$tables["message"]} add column embargo datetime");
			Sql_Verbose_Query("alter table {$tables["message"]} add column repeat integer default 0");
			Sql_Verbose_Query("alter table {$tables["message"]} add column repeatuntil datetime");
      # make sure that current queued messages are sent
      Sql_Verbose_Query("update {$tables["message"]} set embargo = now() where status = \"submitted\"");
      Sql_Query("alter table {$tables["message"]} change column status status enum('submitted','inprocess','sent','cancelled','prepared','draft')");
    case "2.6.6":
    case "whatever versions we will get later":
      #Sql_Query("alter table table that altered");
      break;
    default:
      # an unknown version, so we do a generic upgrade, if the version is older than 1.4.1
      if ($dbversion > "1.4.1")
      	break;
      ignore_user_abort();
      set_time_limit(500);
      reset($DBstruct);
      while (list($table,$value) = each ($DBstruct)) {
        set_time_limit(500);
        if ($table_prefix)
          Sql_Query("alter table $table rename $tables[$table]",1);
        print "<br>Upgrading $table<br />";
				if ($table == "user" && Sql_Table_Column_Exists($tables["user"],"name")) {
					$c = 1;
					foreach (array("name","address1","address2","postcode","town") as $item) {
						Sql_Verbose_Query(sprintf('insert into %s (name,type,listorder,required) values("%s","textline",%d,0)',$tables["attribute"],$item,$c));
						$c++;
						$attribute_id = Sql_Insert_id();
						print "<P>Now copying user data to new table<br>";
						$req = Sql_Query("select id,$item from ".$tables["user"]);
						while ($user = Sql_Fetch_Row($req))
							Sql_Query(sprintf('insert into %s values(%d,%d,"%s")',$tables["user_attribute"],$attribute_id,$user[0],addslashes($user[1])));
						Sql_Verbose_Query("alter table {$tables["user"]} drop column $item");
						flush();
					}
				}
        $success = $success && upgradeTable($tables[$table],$DBstruct[$table]);
				flush();
      }
			Sql_Verbose_Query("update {$tables["user"]} set confirmed = 1");
      $req = Sql_Query("select tablename from ".$tables["attribute"]);
      while ($row = Sql_Fetch_Row($req)) {
        set_time_limit(500);
        if (Sql_Table_Exists("listattr_".$row[0]) && $table_prefix)
          Sql_Verbose_Query("alter table listattr_$row[0] rename $table_prefix"."listattr_".$row[0]);
        if (Sql_Table_Exists($table_prefix."listattr_".$row[0]))
          Sql_Query("alter table $table_prefix"."listattr_".$row[0]." add column listorder integer default 0",0);
      }
			flush();

    	# something we should have done ages ago. make checkboxes save "on" value in user_attribute
      $req = Sql_Query("select * from {$tables["attribute"]} where type = \"checkbox\"");
      while ($row = Sql_Fetch_Array($req)) {
      	$req2 = Sql_Query("select * from $table_prefix"."listattr_$row[tablename]");
        while ($row2 = Sql_Fetch_array($req2)) {
        	if ($row2["name"] == "Checked")
          	Sql_Query(sprintf('update %s set value = "on" where attributeid = %d and value = %d',
            	$tables["user_attribute"],$row["id"],$row2["id"]));
        }
       	Sql_Query(sprintf('update %s set value = "" where attributeid = %d and value != "on"',
         	$tables["user_attribute"],$row["id"]));
        Sql_Query("drop table $table_prefix"."listattr_".$row["tablename"]);
      }
      Sql_Query("insert into {$tables["task"]} (page,type) values(\"export\",\"user\")");
			Sql_Query(sprintf('insert into %s values(0,"%s","%s","%s",now(),now(),"%s","%s",now(),%d,0)',
				$tables["admin"],"admin","admin","",$adminname,"phplist",1),0);
    	if (is_array($system_pages))
        while (list($type,$pages) = each ($system_pages)) {
          foreach ($pages as $page)
            Sql_Query(sprintf('insert into %s (page,type) values("%s","%s")',
              $tables["task"],$page,$type));
        }
      # make sure all users have a uniqid
      $req = Sql_Query("select id from {$tables["user"]} where uniqid = \"\"");
      print "<br>Giving every user a unique ID<br />";
      flush();
      $num = Sql_Affected_Rows();
      print "$num to process<br/>";
      $c =0;
      while ($user = Sql_Fetch_Row($req)) {
        $c++;
        Sql_Query(sprintf('update %s set uniqid = "%s" where id = %d',$tables["user"],getUniqId(),$user[0]));
        if ($c % 15 == 0) {
          print $c . "<br/>";
          flush();
        }
      }
      print "<p>All done";
      break;
  }
  print '<script language="Javascript" type="text/javascript"> finish(); </script>';
  # update the system pages
  while (list($type,$pages) = each ($system_pages)) {
    foreach ($pages as $page)
      Sql_Query(sprintf('replace into %s (page,type) values("%s","%s")',
        $tables["task"],$page,$type));
  }


  # mark the database to be our current version
  if ($success) {
		SaveConfig("version",VERSION,0);
    Info("Success");
   }
   else
    Error("An error occurred while upgrading your database");
} else {

?>
<p>Your database requires upgrading, please make sure to create a backup of your database first.</p>

<p>When you're ready click <?=PageLink2("upgrade","Here","doit=yes")?>. Depending on the size of your database, this may take quite a while. Please make sure not to interrupt the process, once you've started it.</p>
<? } ?>
</td></tr></table>
</div>
