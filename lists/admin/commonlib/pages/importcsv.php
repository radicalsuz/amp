<?

print '<script language="Javascript" src="js/progressbar.js" type="text/javascript"></script>';

ignore_user_abort();
set_time_limit(500);
?>
<p>

<?php
function my_shutdown () {
#	print "Shutting down";
#	print connection_status(); # with PHP 4.2.1 buggy. http://bugs.php.net/bug.php?id=17774
}

function parsePlaceHolders($templ,$data) {
	$retval = $templ;
	foreach ($data as $key => $val) {
  	if (!is_array($val)) {
      $retval = preg_replace('/\['.preg_quote($key).'\]/i',$val,$retval);
   	}
 	}
  return $retval;
}

register_shutdown_function("my_shutdown");
require_once $GLOBALS["coderoot"] . "structure.php";

# identify system values from the database structure
$system_attributes = array();
reset($DBstruct["user"]);
while (list ($key,$val) = each ($DBstruct["user"])) {
  if (!ereg("sys",$val[1])) {
    $system_attributes[strtolower($val[1])] = $key;
  } elseif (ereg("sysexp:(.*)",$val[1],$regs)) {
    $system_attributes[strtolower($regs[1])] = $key;
  }
}

ob_end_flush();
if(isset($_POST["import"])) {

  $test_import = (isset($_POST["import_test"]) && $_POST["import_test"] == "yes");
 /*
  if (!is_array($_POST["lists"]) && !$test_import) {
    Fatal_Error("Please select at least one list");
    return;
  }
 */
  if(!$_FILES["import_file"]) {
    Fatal_Error("File is either too large or does not exist.");
    return;
  }
  if(empty($_FILES["import_file"])) {
    Fatal_Error("No file was specified. Maybe the file is too big? ");
    return;
  }
  if (filesize($_FILES["import_file"]['tmp_name']) > 1000000) {
    # if we allow more, we will certainly run out of memory
  	Fatal_Error("File too big, please split it up into smaller ones");
    return;
  }
  if( !preg_match("/^[0-9A-Za-z_\.\-\/\s \(\)]+$/", $_FILES["import_file"]["name"]) ) {
    Fatal_Error("Use of wrong characters: ".$_FILES["import_file"]["name"]);
    return;
  }
  if (!$_POST["notify"] && !$test_import) {
    Fatal_Error("Please choose whether to sign up immediately or to send a notification");
    return;
  }

  if ($_FILES["import_file"] && filesize($_FILES["import_file"]['tmp_name']) > 10) {
    $fp = fopen ($_FILES["import_file"]['tmp_name'], "r");
    $email_list = fread($fp, filesize ($_FILES["import_file"]['tmp_name']));
    fclose($fp);
    unlink($_FILES["import_file"]['tmp_name']);
  } elseif ($_FILES["import_file"]) {
    Fatal_Error("Something went wrong while uploading the file. Empty file received. Maybe the file is too big?");
    return;
  }

  // Clean up email file
  $email_list = trim($email_list);
  $email_list = str_replace("\r","\n",$email_list);
  $email_list = str_replace("\n\r","\n",$email_list);
  $email_list = str_replace("\n\n","\n",$email_list);

  // Change delimiter for new line.
  if(isset($_POST["import_record_delimiter"]) && $_POST["import_record_delimiter"] != "") {
    $email_list = str_replace($_POST["import_record_delimiter"],"\n",$email_list);
  };

  if (!isset($_POST["import_field_delimiter"]) || $_POST["import_field_delimiter"] == "" || $_POST["import_field_delimiter"] == "TAB") {
    $import_field_delimiter = "\t";
  } else {
    $import_field_delimiter = $_POST["import_field_delimiter"];
  }

  // Check file for illegal characters
#  $illegal_cha = array("\t",",", ";", ":", "#");
#  for($i=0; $i<count($illegal_cha); $i++) {
#    if( ($illegal_cha[$i] != $import_field_delimiter) && ($illegal_cha[$i] != $import_record_delimiter) && (!strpos($email_list, $illegal_cha[$i])) )
#      Fatal_Error("Some characters that are not valid have been found. These might be delimiters. Please check the file and select the right delimiter. Character found: $illegal_cha[$i]");
#  };

  // Split file/emails into array
  $email_list = explode("\n",$email_list);
  if (sizeof($email_list) > 300 && !$test_import) {
    # this is a possibly a time consuming process, so let's show a progress bar
    print '<script language="Javascript" type="text/javascript"> document.write(progressmeter); start();</script>';
    flush();
    # increase the memory to make sure we're not running out
    ini_set("memory_limit","16M");
  }

  # take the header and parse it to attributes
  $header = array_shift($email_list);
  $attributes = explode($import_field_delimiter,$header);
  for ($i=0;$i<sizeof($attributes);$i++) {
    $attribute = clean($attributes[$i]);
    $attribute = preg_replace('#/#','',$attribute);
    # check whether they exist
    if (in_array(strtolower($attribute),array_keys($system_attributes))) {
      $systemindex[strtolower($attribute)] = $i;
#      print "$attribute => $i<br/>";
    } elseif (strtolower($attribute) == "email") {
      $emailindex = $i;
    } elseif (strtolower($attribute) == "send this user html emails") {
      $htmlindex = $i;
    } elseif (preg_match("/^sys/",strtolower($attribute))) {
      # skip this one
    } elseif (strtolower($attribute) == "list membership") {
      # skip this one
    } else {
      $req = Sql_Query("select id from ".$tables["attribute"]." where name = \"$attribute\"");
      if (!Sql_Affected_Rows()) {
        # it is a new one # oops, bad coding cut-n-paste
        $lc_name = substr(str_replace(" ","", strtolower($attribute)),0,10);
        if ($lc_name == "") {
        #  Warn("Attribute Name cannot be empty: $lc_name, skipped");
        } else {
          Sql_Query("select * from ".$tables["attribute"]." where tablename = \"$lc_name\"");
          if (Sql_Affected_Rows()) Fatal_Error("Name is not unique enough: $attribute");

          if (!$test_import) {
            Sql_Query(sprintf('insert into %s (name,type,listorder,default_value,required,tablename) values("%s","%s",0,"",0,"%s")', $tables["attribute"],addslashes($attribute),"textline",$lc_name));
            $attid = Sql_Insert_id();
          } else $attid = 0;
        }
      } else {
        $d = Sql_Fetch_Row($req);
        $attid = $d[0];
      }
#			print $attribute.$attid."<br>";
      $import_attribute[$attribute] = array("index"=>$i,"record"=>$attid);
    }
  }
#	print "A: ".sizeof($import_attribute);
  reset($system_attributes);
  foreach ($system_attributes as $key => $val) {
#    print "$key => $val $systemindex[$key]<br/>";
    if (isset($systemindex[$key]))
      $system_attribute_mapping[$key] = $systemindex[$key];
  }
  if (!isset($system_attributes["email"])) {
    Fatal_error("Cannot find the email in the header");
    return;
  }

  // Parse the lines into records
  $c = 1;$invalid_email_count = 0;

  print "Loading emails .. ";
  flush();
  foreach ($email_list as $line) {
    $values = explode($import_field_delimiter,$line);

    reset($system_attribute_mapping);
    $system_values = array();
    foreach ($system_attribute_mapping as $column => $index) {
      $system_values[$column] = $values[$index];
    }
    $index = clean($system_values["email"]);
    $invalid = 0;
    if (!$index) {
      if ($_POST["show_warnings"])
        Warn("Record has no email: $c -> $line");
      $index = "Invalid Email $c";
			$system_values["email"] = $_POST["assign_invalid"];
      $invalid = 1;
      $invalid_email_count++;
    }
    if (sizeof($values) != sizeof($attributes) && $test_import && $_POST["show_warnings"])
      Warn("Record has more values than header indicated, this may cause trouble: $email");
    if (!$invalid || ($invalid && $omit_invalid != "yes")) {
      $user_list[$index] = array ();
      $user_list[$index]["systemvalues"] = $system_values;
      reset($import_attribute);
      $replace = array();
			while (list($key,$val) = each ($import_attribute)) {
        $user_list[$index][$val["index"]] = addslashes($values[$val["index"]]);
        $replace[$key] = addslashes($values[$val["index"]]);
   		}
		} else {
     # Warn("Omitting invalid one: $email");
    }
 #   $user_list[$index][$htmlindex] = $sendhtml;
    $user_list[$index]["systemvalues"]["email"] = parsePlaceHolders($system_values["email"],array_merge($replace,$system_values,array("number" => $c)));
    $user_list[$index]["systemvalues"]["email"] = clean($user_list[$index]["systemvalues"]["email"]);
    $c++;
    if ($test_import && $c > 50) break;
  }

  print "Loaded emails<br/>\n";
  flush();
  // View test output of emails
  if($test_import) {
    print 'Test output:<br>There should only be ONE email per line.<br>If the output looks ok, go <a href="javascript:history.go(-1)">Back</a> to resubmit for real<br><br>';
    $i = 1;
    while (list($index,$data) = each ($user_list)) {
      $index = trim($index);
      if(strlen($index) > 4) {
        $html = "";
        print "<b>$index</b><br/>";
        foreach ($data["systemvalues"] as $column => $value) {
          $html .= "$column -> $value<br/>\n";
        }
        reset($import_attribute);
        foreach ($import_attribute as $item) {
          if ($data[$item["index"]])
            $html .= $attributes[$item["index"]]." -> ".$data[$item["index"]]."<br>";
				}
        if ($html) print "<blockquote>$html</blockquote>";
      };
      if($i == 50) {break;};
      $i++;
    };

  // Do import
  } else {
    $count_email_add = 0;
    $count_exist = 0;
    $count_list_add = 0;
    $num_lists = sizeof($_POST["lists"]);
    print "<br/>Adding emails\n";
    flush();
    
    $total = sizeof($user_list);
    $cnt = 0;

    if (is_array($user_list))
    while (list($index,$userdata) = each ($user_list)) {
			$new = 0;
			$cnt++;
      if ($cnt % 25 ==0) {
      	print "<br/>\n$cnt/$total";
        flush();
      }

      if(strlen($index) > 4) {
        if ($userdata["systemvalues"]["foreign key"]) {
          $result = Sql_query(sprintf('select id,uniqid from %s where foreignkey = "%s"',
            $tables["user"],$userdata["systemvalues"]["foreign key"]));
        #	print "<br/>Using foreign key for matching: ".$userdata["systemvalues"]["foreign key"];
        	$fkeymatch++;
          $exists = Sql_Affected_Rows();
  	      $user = Sql_fetch_array($result);
          # check whether the email will clash
          $clashcheck = Sql_Fetch_Row_Query(sprintf('select id from %s
          	 where email = "%s"',$tables["user"],$userdata["systemvalues"]["email"]));
          if ($clashcheck[0] != $user["id"]) {
          	$notduplicate = 0;$c=0;
            while (!$notduplicate) {
              $c++;
              Sql_Query(sprintf('select email from %s where email = "%s"',
                $tables["user"],"duplicate$c ".$userdata["systemvalues"]["email"]));
              $notduplicate = !Sql_Affected_Rows();
            }
            $userdata["systemvalues"]["email"] = "duplicate$c ".$userdata["systemvalues"]["email"];
          }
        } else {
          $result = Sql_query(sprintf('select id,uniqid from %s where email = "%s"',$tables["user"],$userdata["systemvalues"]["email"]));
        #	print "<br/>Using email for matching: ".$userdata["systemvalues"]["email"];
        	$emailmatch++;
          $exists = Sql_Affected_Rows();
  	      $user = Sql_fetch_array($result);
        }
        if ($exists) {
          // User exist, remember some values to add them to the lists
          $count_exist++;
          $userid = $user["id"];
          $uniqid = $user["uniqid"];
        } else {
          // user does not exist
					$new = 1;
          // Create unique number
          mt_srand((double)microtime()*1000000);
          $randval = mt_rand();
          $uniqid = getUniqid();
          $confirmed = $_POST["notify"] != "yes" && !preg_match("/Invalid Email/i",$index);

          $query = sprintf('INSERT INTO %s (email,entered,confirmed,uniqid)
             values("%s",now(),%d,"%s")',
             $tables["user"],$userdata["systemvalues"]["email"],$confirmed,$uniqid);
          $result = Sql_query($query,1);
          $userid = Sql_insert_id();
          if (!$userid) {
          	# no id returned, so it must have been a duplicate entry
            if ($_POST["show_warnings"]) print Warn("Duplicate Email $index");
            $c = 0;
            while (!$userid) {
              $c++;
              $query = sprintf('INSERT INTO %s (email,entered,confirmed,uniqid)
                values("%s",now(),%d,"%s")',
                $tables["user"],$userdata["systemvalues"]["email"]." ($c)",0,$uniqid);
              $result = Sql_query($query,1);
              $userid = Sql_insert_id();
            }
            $userdata["systemvalues"]["email"] = $userdata["systemvalues"]["email"]." ($c)";
          }

	        $count_email_add++;
          $some = 1;
        }

        reset($import_attribute);
	      if ($new || (!$new && $_POST["overwrite"] == "yes")) {
          $query = "";
          $dataupdate++;
          foreach ($userdata["systemvalues"] as $column => $value) {
            $query .= sprintf('%s = "%s",',$system_attributes[$column],$value);
          }
          if ($query) {
            $query = substr($query,0,-1);
            # this may cause a duplicate error on email, so add ignore
            Sql_Query("update ignore {$tables["user"]} set $query where id = $userid");
          }
          foreach ($import_attribute as $item) {
						if ($userdata[$item["index"]]) {
							$attribute_index = $item["record"];
							$uservalue = $userdata[$item["index"]];
							# check whether this is a textline or a selectable item
							$att = Sql_Fetch_Row_Query("select type,tablename,name from ".$tables["attribute"]." where id = $attribute_index");
							switch ($att[0]) {
								case "select":
								case "radio":
									$val = Sql_Query("select id from $table_prefix"."listattr_$att[1] where name = \"$uservalue\"");
									# if we do not have this value add it
									if (!Sql_Affected_Rows()) {
										Sql_Query("insert into $table_prefix"."listattr_$att[1] (name) values(\"$uservalue\")");
										Warn("Value $uservalue added to attribute $att[2]");
										$user_att_value = Sql_Insert_Id();
									} else {
										$d = Sql_Fetch_Row($val);
										$user_att_value = $d[0];
									}
									break;
								case "checkbox":
                  if ($uservalue && $uservalue != "off")
										$user_att_value = "on";
          				else
										$user_att_value = "off";
									break;
								default:
									$user_att_value = $uservalue;
									break;
							}

							Sql_query(sprintf('replace into %s (attributeid,userid,value) values("%s","%s","%s")',
								$tables["user_attribute"],$attribute_index,$userid,$user_att_value));
						}
					}
				}

        #add this user to the lists identified
        if (is_array($_POST["lists"])) {
          reset($_POST["lists"]);
          $addition = 0;
          $listoflists = "";
          while (list($key,$listid) = each($_POST["lists"])) {
            $query = "replace INTO ".$tables["listuser"]." (userid,listid,entered) values($userid,$listid,now())";
            $result = Sql_query($query,1);
            # if the affected rows is 2, the user was already subscribed
            $addition = $addition || Sql_Affected_Rows() == 1;
            $listoflists .= "  * ".$listname[$key]."\n";
          }
          if ($addition)
            $additional_emails++;
          if (!TEST && $_POST["notify"] == "yes" && $addition) {
            $subscribemessage = ereg_replace('\[LISTS\]', $listoflists, getUserConfig("subscribemessage",$userid));
            sendMail($email, getConfig("subscribesubject"), $subscribemessage,system_messageheaders(),$envelope);
          }
        }
        if (!is_array($_POST["groups"])) {
        	$groups = array();
        } else {
        	$groups = $_POST["groups"];
        }
        if (isset($everyone_groupid) && !in_array($everyone_groupid,$groups)) {
        	array_push($groups,$everyone_groupid);
        }
        if (is_array($groups)) {
          #add this user to the groups identified
          reset($groups);
          $groupaddition = 0;
          while (list($key,$groupid) = each($groups)) {
          	if ($groupid) {
              $query = "replace INTO user_group (userid,groupid) values($userid,$groupid)";
              $result = Sql_query($query);
              # if the affected rows is 2, the user was already subscribed
              $groupaddition = $groupaddition || Sql_Affected_Rows() == 1;
           	}
          }
          if ($groupaddition)
            $group_additional_emails++;
        }
      }; // end if
    }; // end while

    print '<script language="Javascript" type="text/javascript"> finish(); </script>';
    # be gramatically correct :-)
    $displists = ($num_lists == 1) ? "list": "lists";
    $dispemail = ($count_email_add == 1) ? "new email was ": "new emails were ";
    $dispemail2 = ($additional_emails == 1) ? "email was ":"emails were ";

    if(!$some && !$additional_emails) {
      print "<br>All the emails already exist in the database and are member of the $displists.";
    } else {
      print "$count_email_add $dispemail succesfully imported to the database and added to $num_lists $displists.<br>$additional_emails $dispemail2 subscribed to the $displists";
      if ($count_exist)
      	print "<br/>$count_exist emails already existed in the database";
    }
    if ($invalid_email_count) {
      print "<br>$invalid_email_count Invalid Emails found.";
      if (!$_POST["omit_invalid"])
        print " These records were added, but the email has been made up from ".$_POST["assign_invalid"];
      else
        print " These records were deleted. Check your source and reimport the data. Duplicates will be identified.";
    }
    if ($_POST["overwrite"] == "yes") {
    	print "<br/>User data was updated for $dataupdate users";
    }
    printf('<br/>%d users were matched by foreign key, %d by email',$fkeymatch,$emailmatch);
  }; // end else
  print '<p>'.PageLink2("import","Import some more emails");


} else {
?>


<ul>
<?=formStart('enctype="multipart/form-data" name="import"');?>
<?php
if ($GLOBALS["require_login"] && !isSuperUser()) {
  $access = accessLevel("import2");
  if ($access == "owner")
    $subselect = " where owner = ".$_SESSION["logindetails"]["id"];
  elseif ($access == "all")
    $subselect = "";
  elseif ($access == "none")
    $subselect = " where id = 0";
}

if (Sql_Table_Exists($tables["list"])) {
  $result = Sql_query("SELECT id,name FROM ".$tables["list"]." $subselect ORDER BY listorder");
  $c=0;
  if (Sql_Affected_Rows() == 1) {
    $row = Sql_fetch_array($result);
    printf('<input type=hidden name="listname[%d]" value="%s"><input type=hidden name="lists[%d]" value="%d">Adding users to list <b>%s</b>',$c,$row["name"],$c,$row["id"],$row["name"]);;
  } else {
    print '<p>Select the lists to add the emails to</p>';
    while ($row = Sql_fetch_array($result)) {
      printf('<li><input type=hidden name="listname[%d]" value="%s"><input type=checkbox name="lists[%d]" value="%d">%s',$c,$row["name"],$c,$row["id"],$row["name"]);;
      $some = 1;$c++;
    }

    if (!$some)
      echo 'No lists available, '.PageLink2("editlist","Add a list");
  }
}

if (Sql_Table_Exists("groups")) {
  $result = Sql_query("SELECT id,name FROM groups ORDER BY listorder");
  $c=0;
  if (Sql_Affected_Rows() == 1) {
    $row = Sql_fetch_array($result);
    printf('<p><input type=hidden name="groupname[%d]" value="%s"><input type=hidden name="groups[%d]" value="%d">Adding users to group <b>%s</b></p>',$c,$row["name"],$c,$row["id"],$row["name"]);;
  } else {
    print '<p>Select the groups to add the users to</p>';
    while ($row = Sql_fetch_array($result)) {
      if ($row["id"] == $everyone_groupid) {
        printf('<li><input type=hidden name="groupname[%d]" value="%s"><input type=hidden name="groups[%d]" value="%d"><b>%s</b> - automatically added',$c,$row["name"],$c,$row["id"],$row["name"]);;
      } else {
        printf('<li><input type=hidden name="groupname[%d]" value="%s"><input type=checkbox name="groups[%d]" value="%d">%s',$c,$row["name"],$c,$row["id"],$row["name"]);;
      }
      $some = 1;$c++;
    }
  }
}

?>

</ul>

<table border="1">
<tr><td colspan=2><p>
The file you upload will need to have the attributes of the records on the first line.
Make sure that the email column is called "email" and not something like "e-mail" or
"Email Address".
Case is not important.
If attributes do not exist, they will be added to the system as textline attributes.
You can then convert the attributes to other types in the attributes pages.</p>
If you have a column called "Foreign Key", this will be used for synchronisation between an
external database and the PHPlist database. The foreignkey will take precedence when matching
an existing user. This will slow down the import process. If you use this, it is allowed to have
records without email, but an "Invalid Email" will be created instead. You can then do
a search on "invalid email" to find those records. Maximum size of a foreign key is 100.
<br/><br/>
<b>Warning</b>: the file needs to be plain text. Do not upload binary files like a Word Document.
<br/>
</td></tr>
<tr><td>File containing emails:<br/>
</td><td><input type="file" name="import_file">
<br/>The following limits are set by your server:<br/>
Maximum size of a total data sent to server: <b><?=ini_get("post_max_size")?></b><br/>
Maximum size of each individual file: <b><?=ini_get("upload_max_filesize")?></b>
</td></tr>
<tr><td>Field Delimiter:</td><td><input type="text" name="import_field_delimiter" size=5> (default is TAB)</td></tr>
<tr><td>Record Delimiter:</td><td><input type="text" name="import_record_delimiter" size=5> (default is line break)</td></tr>
<tr><td colspan=2>If you check "Test Output", you will get the list of parsed emails on screen, and the database will not be filled with the information. This is useful to find out whether the format of your file is correct. It will only show the first 50 records.</td></tr>
<tr><td>Test output:</td><td><input type="checkbox" name="import_test" value="yes"></td></tr>
<tr><td colspan=2>If you check "Show Warnings", you will get warnings for invalid records. Warnings will only be shown if you check "Test Output". They will be ignored when actually importing. </td></tr>
<tr><td>Show Warnings:</td><td><input type="checkbox" name="show_warnings" value="yes"></td></tr>
<tr><td colspan=2>If you check "Omit Invalid", invalid records will not be added. Invalid records are records without an email. Any other attributes will be added automatically, ie if the country of a record is not found, it will be added to the list of countries.</td></tr>
<tr><td>Omit Invalid:</td><td><input type="checkbox" name="omit_invalid" value="yes"></td></tr>
<tr><td colspan=2>Assign Invalid will be used to create an email for users with an invalid email address.
You can use values between [ and ] to make up a value for the email. For example if your import file contains a column "First Name" and one called "Last Name", you can use
"[first name] [last name]" to construct a new value for the email for this user containing their first name and last name.
The value [number] can be used to insert the sequence number for importing.
</td></tr>
<tr><td>Assign Invalid:</td><td><input type="text" name="assign_invalid" value="Invalid Email [number]"></td></tr>
<tr><td colspan=2>If you check "Overwrite Existing", information about a user in the database will be replaced by the imported information. Users are matched by email.</td></tr>
<tr><td>Overwrite Existing:</td><td><input type="checkbox" name="overwrite" value="yes"></td></tr>
<tr><td colspan=2>If you choose "send notification email" the users you are adding will be sent the request for confirmation of subscription to which they will have to reply. This is recommended, because it will identify invalid emails.</td></tr>
<tr><td>Send&nbsp;Notification&nbsp;email&nbsp;<input type="radio" name="notify" value="yes"></td><td>Make confirmed immediately&nbsp;<input type="radio" name="notify" value="no"></td></tr>

<tr><td><input type="submit" name="import" value="Import"></td><td>&nbsp;</td></tr>
</table>
<? } ?>

</p>
