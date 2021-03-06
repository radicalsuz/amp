
<script language="Javascript" src="js/jslib.js" type="text/javascript"></script>

<?php
require_once "accesscheck.php";

#include "interfacelib.php";
print PageLink2("admins","List of Administrators","start=$start");

require $GLOBALS["coderoot"] . "structure.php";
$struct = $DBstruct["admin"];

if ($list)
  echo "<br />".PageLink2("members","Back to Members of this list","id=$list")."\n";
if ($start)
  echo "<br />".PageLink2("users","Back to the list of users","start=$start")."\n";
if ($find)
  echo "<br />".PageLink2("users","Back to the search results","start=$start&find=".urlencode($find))."\n";

echo "<hr /><br />";
$noaccess = 0;
$accesslevel = accessLevel("admin");
switch ($accesslevel) {
  case "owner":
    $id = $_SESSION["logindetails"]["id"];break;
  case "all":
    $subselect = "";break;
  case "none":
  default:
    $noaccess = 1;
}
if ($noaccess) {
	print Error("No Access");
  return;
}

if ($change) {
  if (!$_POST["id"]) {
    # new one
    Sql_Query(sprintf('insert into %s (namelc,created) values("%s",now())',
      $tables["admin"],strtolower(normalize($_POST["loginname"]))));
    $id = Sql_Insert_Id();
  } else {
  	$id = $_POST["id"];
  }

  if ($id) {
    reset($struct);
    while (list ($key,$val) = each ($struct)) {
      list($a,$b) = explode(":",$val[1]);
      if ($a != "sys" && $val[1])
        Sql_Query("update {$tables["admin"]} set $key = \"".$$key."\" where id = $id");
    }
    if (is_array($attribute))
      while (list($key,$val) = each ($attribute)) {
        Sql_Query(sprintf('replace into %s (adminid,adminattributeid,value)
          values(%d,%d,"%s")',$tables["admin_attribute"],$id,$key,$val));
      }
    Sql_Query(sprintf('update %s set modifiedby = "%s" where id = %d',$tables["admin"],adminName($_SESSION["logindetails"]["id"]),$id));

    if ($accesslevel == "all" && is_array($_POST["access"])) {
      Sql_Query("delete from {$tables["admin_task"]} where adminid = $id");
      if (is_array($_POST["access"]))
        while (list($key,$val) = each ($_POST["access"]))
          Sql_Query("replace into {$tables["admin_task"]} (adminid,taskid,level) values($id,$key,$val)");
    }
    Info("Changes saved");
  } else {
    Info("Error adding new admin");
  }
}

if ($_POST["setdefault"]) {
  Sql_Query("delete from {$tables["admin_task"]} where adminid = 0");
  if (is_array($access))
    while (list($key,$val) = each ($access))
      Sql_Query("insert into {$tables["admin_task"]} (adminid,taskid,level) values(0,$key,$val)");
  Info("Current set of permissions made default");
}

if ($_POST["resetaccess"]) {
  $reverse_accesscodes = array_flip($access_levels);
  $req = Sql_Query("select * from {$tables["task"]} order by type");
  while ($row = Sql_Fetch_Array($req)) {
    $level = $system_pages[$row["type"]][$row["page"]];
    Sql_Query(sprintf('replace into %s (adminid,taskid,level) values(%d,%d,%d)',
      $tables["admin_task"],$id,$row["id"],$reverse_accesscodes[$level]));
  }
}

if (isset($delete) && $delete) {
  # delete the index in delete
  print "Deleting $delete ..\n";
  Sql_query("delete from {$tables["admin"]} where id = $delete");
  Sql_query("delete from {$tables["admin_attribute"]} where adminid = $delete");
  Sql_query("delete from {$tables["admin_task"]} where adminid = $delete");
  print "..Done<br /><hr><br />\n";
}

if ($id) {
  print "Edit Administrator: ";
  $result = Sql_query("SELECT * FROM {$tables["admin"]} where id = $id");
  $data = sql_fetch_array($result);
  print $data["loginname"];
  if ($data["loginname"] != "admin" && $accesslevel == "all")
    printf( "<br /><li><a href=\"javascript:deleteRec('%s');\">Delete</a> %s\n",PageURL2("admin","","delete=$id"),$admin["loginname"]);
} else {
  print "Add a new Administrator";
}
print "<br/>";
print '<p>Admin Details:'.formStart().'<table border=1>';
printf('<input type=hidden name="id" value="%d">',$id);

reset($struct);
while (list ($key,$val) = each ($struct)) {
  list($a,$b) = explode(":",$val[1]);
  if ($a == "sys")
    printf('<tr><td>%s</td><td>%s</td></tr>',$b,$data[$key]);
  elseif ($key == "loginname" && $data[$key] == "admin") {
    printf('<tr><td>Login Name</td><td>admin</td></tr>');
    print('<input type=hidden name="loginname" value="admin">');
  } elseif ($key == "superuser" || $key == "disabled") {
    if ($accesslevel == "all") {
      printf('<tr><td>%s</td><td><input type="text" name="%s" value="%s" size=30></td></tr>'."\n",$val[1],$key,$data[$key]);
    }
  } elseif ($val[1]) {
    printf('<tr><td>%s</td><td><input type="text" name="%s" value="%s" size=30></td></tr>'."\n",$val[1],$key,$data[$key]);
  }
}
$res = Sql_Query("select
  {$tables["adminattribute"]}.id,
  {$tables["adminattribute"]}.name,
  {$tables["adminattribute"]}.type,
  {$tables["adminattribute"]}.tablename from
  {$tables["adminattribute"]}
  order by {$tables["adminattribute"]}.listorder");
while ($row = Sql_fetch_array($res)) {
  if ($id) {
    $val_req = Sql_Fetch_Row_Query("select value from {$tables["admin_attribute"]}
      where adminid = $id and adminattributeid = $row[id]");
    $row["value"] = $val_req[0];
  }

  if ($row["type"] == "checkbox") {
    $checked_index_req = Sql_Fetch_Row_Query("select id from $table_prefix"."adminattr_".$row["tablename"]." where name = \"Checked\"");
    $checked_index = $checked_index_req[0];
    $checked = $checked_index == $row["value"]?"checked":"";
    printf('<tr><td>%s</td><td><input style="attributeinput" type=hidden name="cbattribute[]" value="%d"><input style="attributeinput" type=checkbox name="attribute[%d]" value="Checked" %s></td></tr>'."\n",$row["name"],$row["id"],$row["id"],$checked);
  }
  else
  if ($row["type"] != "textline" && $row["type"] != "hidden")
    printf ("<tr><td>%s</td><td>%s</td></tr>\n",$row["name"],AttributeValueSelect($row["id"],$row["tablename"],$row["value"],"adminattr"));
  else
    printf('<tr><td>%s</td><td><input style="attributeinput" type=text name="attribute[%d]" value="%s" size=30></td></tr>'."\n",$row["name"],$row["id"],htmlspecialchars($row["value"]));
}
print '<tr><td colspan=2><input type=submit name=change value="Save Changes"></table>';

# what pages can this administrator see:
if (!$data["superuser"] && $accesslevel == "all") {
  print $strAccessExplain;
  print '<p>Access Details:</p><table border=1>';
  reset($access_levels);
  printf ('<tr><td colspan="%d" align=center>Access Privileges</td></tr>',sizeof($access_levels)+2);
  print "<tr><td>Type</td><td>Page</td>\n";
  foreach ($access_levels as $level)
    printf('<td>%s</td>',$level);
  print "</tr>\n";
  $req = Sql_Query("select * from {$tables["task"]} order by type");
  while ($row = Sql_Fetch_Array($req)) {
    printf('<tr><td>%s</td><td>%s</td>',$row["type"],$row["page"]);
    reset($access_levels);
    while (list($key,$level) = each ($access_levels)) {
      $current_level_req = Sql_Query(sprintf('
        select level from %s where adminid = %d and taskid = %d',$tables["admin_task"],$id,$row["id"]));
      if (!Sql_Affected_Rows()) {
        # take a default
				$default = $system_pages[$row["type"]][$row["page"]];
     #   if ($row["type"] == "system") {
     #     $curval = 0;
     #   } else {
     #     $curval = 4;
     #   }
          # by default disable everything
          $curval = 0;
				if ($level == $default) $curval = $key;
      } else {
        $current_level = Sql_Fetch_Row($current_level_req);
        $curval = $current_level[0];
      }
      printf('<td><input type=radio name="access[%d]" value="%s" %s></td>',$row["id"],$key,$key == $curval ? "checked":"");
    }
    print "</tr>\n";
  }

  printf('<tr><td colspan="%d"><input type=submit name=setdefault value="Set these permissions as default"><input type=submit name=change value="Save Changes"></table>',sizeof($access_levels)+2);
	print '<input type=submit name="resetaccess" value="Reset to Default">';
}
print "</form>";
?>


