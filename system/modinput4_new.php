<?php
# UDM Wizard
#set differnt list 
#defulat file

$mod_name='udm';
require("Connections/freedomrising.php");

$list = $dbcon->Execute("SELECT id, name from lists ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$list_numRows = 0;
$list__totalRows = $list->RecordCount();
$Recordset1__MMColParam = 9999999999999;
$Recordset1 = $dbcon->Execute("SELECT * FROM userdata_fields WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
$enteredby = $dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
$enteredby_numRows = 0;
$enteredby__totalRows = $enteredby->RecordCount();

$MM_editAction = $PHP_SELF;
if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
}

$MM_abortEdit = 0;
$MM_editQuery = "";
  
if ($Submit) {

	$MM_insert = 1;

	## insert UDM
    $MM_editTable  = "userdata_fields";
    $MM_fieldsStr = "name|value";
    $MM_columnsStr = "name|',none,''";
 	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	## get UDM id
	$getmodid=$dbcon->Execute( "SELECT id FROM userdata_fields ORDER BY id DESC LIMIT 1") or die($dbcon->ErrorMsg());
	$modid = $getmodid->Fields("id");

	## insert new permission
	$pname="$name Module";
	$publish  =1;
    $MM_editTable  = "per_description ";
    $MM_fieldsStr = "pname|value|description|value|publish|value";
    $MM_columnsStr = "name|',none,''|description|',none,''|publish|,none,''";
		 	require ("../Connections/insetstuff.php");
	  		require ("../Connections/dataactions.php");
##get per id
	$getperid=$dbcon->Execute( "select id from  per_description   order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$udmper = $getperid->Fields("id");

##make new module
	$addmodule=$dbcon->Execute( "insert into modules (name) values ('$name')") or DIE($dbcon->ErrorMsg());
## get module id
	$getmoduleid=$dbcon->Execute( "select id from modules  order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$udmmodid = $getmoduleid->Fields("id");




	## insert header page
	$hname = "$name Input";
    $MM_editTable  = "moduletext";
    $MM_fieldsStr = "htitle|value|harticle|value|hname|value|udmmodid|value";
    $MM_columnsStr = "title|',none,''|test|',none,''|name|',none,''|modid|',none,''";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	##get heder id
	$getheaderid=$dbcon->Execute( "select id from moduletext  order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$modidinput  = $getheaderid->Fields("id");

	##insert header response page
	$rname = "$name Thank You";
    $MM_editTable  = "moduletext";
    $MM_fieldsStr = "rtitle|value|rarticle|value|rname|value|udmmodid|value";
    $MM_columnsStr = "title|',none,''|test|',none,''|name|',none,''|modid|',none,''";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	# get reposne id
	$getheaderid=$dbcon->Execute( "select id from moduletext  order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$modidresponse = $getheaderid->Fields("id");

	#add source
	$source= "Web $name";
	$MM_editTable  = "source";
	$MM_fieldsStr = "source|value";
	$MM_columnsStr = "title|',none,''";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	#get source id
	$getsourceid=$dbcon->Execute( "select id from source order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	$sourceid = $getsourceid->Fields("id");

	#update udm
	unset($MM_insert);

	$MM_update=1;
    $MM_editTable  = "userdata_fields";
    $MM_editColumn = "id";
    $MM_recordId =$modid;
	$MM_fieldsStr ="modidinput|value|modidresponse|value|sourceid|value|enteredbyid|value|useemail|value|mailto|value|subject|value|redirect|value|list1|value|list2|value|list3|value|uselists|value";
	$MM_columnsStr = "modidinput|none,none,NULL|modidresponse|none,none,NULL|sourceid|none,none,NULL|enteredby|none,none,NULL|useemail|none,none,NULL|mailto|',none,''|subject|',none,''|redirect|',none,''|list1|',none,''|list2|',none,''|list3|',none,''|uselists|none,1,0";
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");

	$file = "modinput4_data.php?modin=$modid";
	$userdatamod =1 ;
	$navhtml= "<A class=side href=\"modinput4_data.php?modin=$modid\">View/Edit $name</A><br>
<A class=side href=\"modinput4_view.php?modin=$modid\">Add $name</A><br>
<A class=side href=\"modinput4_edit.php?modin=$modid\">Data Module Settings</A><br>
<A class=side href=\"module_control_list.php?modid=$udmmodid\">Settings</A>";
    $MM_editColumn = "id";
    $MM_recordId =$udmmodid;
    $MM_editTable  = "modules";
    $MM_fieldsStr ="name|value|userdatamod|value|modid|value|file|value|udmper|value|navhtml|value|publish|value";
    $MM_columnsStr = "name|',none,''|userdatamod|',none,''|userdatamodid|',none,''|file|',none,''|perid|',none,''|navhtml|',none,''|publish|',none,''";
 	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");
  
	while (list($k, $v) = each($pergroup)) { 
		$perupdate=$dbcon->Execute("INSERT INTO permission  VALUES ( '',$v,$udmper)") or DIE($dbcon->ErrorMsg());
	} 
	header("Location: modinput4_edit.php?modin=$modid");
}
$usergp=$dbcon->Execute("select * from per_group ") or die($dbcon->ErrorMsg());

?>

<?php include ("header.php"); ?>

<h2>Add User Data Module</h2>

<form name="form1" method="post" action="<?php echo $MM_editAction?>">
  <table width="95%" border="0" cellspacing="0" cellpadding="5" class="table">
    
  </table>
        
        <table width="95%" border="0" cellspacing="0" cellpadding="2">
		<tr> 
            <td class="name">User Data Module Name </td>
            <td><input type="text" name="name" size="25" > </td>
          </tr>
          <tr> 
            <td class="name">Header Title</td>
            <td><input name="htitle" type="text" id="htitle"> </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Header Text</td>
            <td><textarea name="harticle" cols="40" rows="4" wrap="VIRTUAL" id="harticle"></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name">Response Page Title</td>
            <td><input name="rtitle" type="text" id="rtitle"> </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Response Page Text</td>
            <td><textarea name="rarticle" cols="40" rows="4" wrap="VIRTUAL" id="rarticle"></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name">Entered By</td>
            <td><select name="enteredbyid" id="enteredbyid">
                <?php
  if ($enteredby__totalRows > 0){
    $enteredby__index=0;
    $enteredby->MoveFirst();
    WHILE ($enteredby__index < $enteredby__totalRows){
?>
                <OPTION VALUE="<?php echo  $enteredby->Fields("id")?>"<?php if ($enteredby->Fields("id")==$Recordset1->Fields("enteredby")) echo "SELECTED";?>> 
                <?php echo  $enteredby->Fields("name");?> </OPTION>
                <?php
      $enteredby->MoveNext();
      $enteredby__index++;
    }
    $enteredby__index=0;  
    $enteredby->MoveFirst();
  }
?>
              </select> </td>
          </tr>
          <tr> 
            <td class="name">use lists</td>
            <td><input name="uselists" type="checkbox" id="uselists" value="1" <?php if ($Recordset1->Fields("uselists") == 1) { echo "CHECKED";} ?>></td>
          </tr>
          <tr> 
            <td  class="name">List # 1</td>
            <td><select name="list1">
                <option value="">none</option>
                <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$Recordset1->Fields("list1")) echo "SELECTED";?>> 
                <?php echo  $list->Fields("name");?> </OPTION>
                <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr> 
            <td  class="name">List #2</td>
            <td><select name="list2">
                <option value="">none</option>
                <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$Recordset1->Fields("list2")) echo "SELECTED";?>> 
                <?php echo  $list->Fields("name");?> </OPTION>
                <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr> 
            <td class="name">List #3</td>
            <td><select name="list3">
                <option value="">none</option>
                <?php
  if ($list__totalRows > 0){
    $list__index=0;
    $list->MoveFirst();
    WHILE ($list__index < $list__totalRows){
?>
                <OPTION VALUE="<?php echo  $list->Fields("id")?>"<?php if ($list->Fields("id")==$Recordset1->Fields("list3")) echo "SELECTED";?>> 
                <?php echo  $list->Fields("name");?> </OPTION>
                <?php
      $list->MoveNext();
      $list__index++;
    }
    $list__index=0;  
    $list->MoveFirst();
  }
?>
              </select></td>
          </tr>
          <tr> 
            <td class="name">Use E-mail</td>
            <td><input name="useemail" type="checkbox" id="useemail" value="1"  ></td>
          </tr>
          <tr> 
            <td class="name">Mail to:</td>
            <td><input name="mailto" type="text" id="mailto" size="45" value=""></td>
          </tr>
          <tr> 
            <td class="name">E-mail Subject</td>
            <td><input name="subject" type="text" id="subject" size="45" value=""></td>
          </tr>
          <tr>
            <td class="name">Permission Groups</td>
            <td><select multiple name='pergroup[]' size='8'>
			<?php while ((!$usergp->EOF)){ ?>
                <option value="<?php echo  $usergp->Fields("id")?>"  ><?php echo  $usergp->Fields("name")?></option>
			<?php 	$usergp->MoveNext(); }?>
             
              </select></td>
          </tr>
        </table>
            
              <p>
                   <input type="submit" name="Submit" value="Submit">
        </p>
      </form>
			
			
<?php include ("footer.php");?>
