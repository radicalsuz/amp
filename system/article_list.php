<?php
$mod_name='content';
require("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");

$obj = new SysMenu;
if (!AMP_Authorized(AMP_PERMISSION_CONTENT_EDIT )) ampredirect("index.php"); 
  
   
    /**
 * a function to take an array of IDs and set their publish status to 1
 */
function art_publish($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update articles set publish=1 where id=$id";
			#die($q);
			$dbcon->execute($q) or die($q.$dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items posted live.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

 function art_order($ids) {
	global $dbcon;
	if (is_array($ids)) {

	while(list($key,$value)= each($ids)){ 
			$q = "update articles set  pageorder='$value' where id=$key";
			$dbcon->execute($q) or die($q.$dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Order Updated.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

function art_change_section($ids,$type) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update articles set type=$type where id=$id";
			
			$dbcon->execute($q) or die($q.$dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items section changes.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

function art_change_class($ids,$class) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update articles set class=$class where id=$id";
			
			$dbcon->execute($q) or die($q.$dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items class changes.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

function art_unpublish($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update articles set publish=0 where id=$id";
			#die($q);
			$dbcon->execute($q) or die("error unpublishing ".$q.$dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items posted as draft.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

/**
 * a function to take an array of IDs and delete them
 */
function art_delete($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "delete from articles where id=$id";
			#die($q);
			$dbcon->execute($q) or die($q.$dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items deleted.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

/**
 * a function to to do a header redirect, you can feed it an option associative array to build a query string
 */
function send_to($loc, $query=null) {
global $_POST;
	if (is_array($query)) {
		$q = '?';
		foreach ($query as $k=>$v) {
			$q .= "$k=$v&";
		}
	}
	$type = "&type=".$_POST[type];
	header("location:$loc$q$type");
}

/**
 * a switch to see what the page should be doing
 */
switch($_POST['act']) {
	case 'Publish':
		art_publish($_POST['id']);
		break;
	case 'Unpublish':
		art_unpublish($_POST['id']);
		break;
	case 'Delete':
		art_delete($_POST['id']);
		break;
	case 'Move Articles':
		art_change_section($_POST['id'],$_POST['change_type']);
		break;
	case 'Change Class':
		art_change_class($_POST['id'],$_POST['change_class']);
		break;
	case 'Change Order':
		art_order($_POST['order']);
		break;
}

  
  
if ($_GET['offset']) {$offset=$_GET['offset'];}
else { $offset=0;}
if ($_GET['limit']) {$limit=$_GET['limit'];}
else { $limit=50;}

$limit_sql = " LIMIT $offset, $limit ";

if ($limit=='-1') {
    $offset=0;
    $limit=false;
    $limit_sql='';
}
## create Menus


$allclass=$dbcon->Execute("SELECT distinct class.id, class.class FROM class, articles a where a.class =class.id and a.id  is not null ORDER BY class ASC") or DIE($dbcon-->ErrorMsg());


######define search sql ###########################
 if ($_POST["Search"]){
 if ( ($_GET[type]) or ($_GET["class"])  ) {$sqlspacer = "and";}
 else {$sqlspacer = "and";}
 if  ($_POST[sid] != "ID") {$sqlid = "$sqlspacer a.id = '$_POST[sid]'";}
 if  ($_POST[stitle] != "Title") {$sqltitle = "$sqlspacer a.title LIKE '%$_POST[stitle]%'";}
 if  ($_POST[sauthor] != "Author") {$sqlauthor = "$sqlspacer a.author LIKE '%$_POST[sauthor]%'";}
 if  ($_POST[sdate] != "Date (ex 01-12-02)") {
  if ((ereg ("([0-9]{1,2})-([0-9]{1,2})-([0-9]{2})", $_POST[sdate], $regs)) ) {
    $sdate = "$regs[3]$regs[1]$regs[2]";}
 $sqldate = "$sqlspacer a.date = '$sdate'" ; }
 }
 
###########################define sql for list  #########################
$track ="all";
if ($_GET['subsite']) {
$subsql= "and  a.subsite=".$_GET["subsite"];
$track ="site";
}
$class = (isset($_GET['class']) && $_GET['class'])?$_GET['class']:false;
if ($_GET["class"]) {
$subsql= "and  a.class=".$_GET["class"];
$track ="class";
}
if ($_GET['type']) {
$subsql= "and  a.type=".$_GET['type'];
$track ="type";
}
if (($_GET['typer']) or ($AMP_view_rel && $_GET['type']) ) {
if ($_GET['typer']) {$type =  $_GET['typer'];} else  {$type =  $_GET['type'];}
$subsql= "and  (a.$MX_type=$type or a.relsection1 = $type or a.relsection2 = $type)  ";
$track ="type";
}
if ($MM_reltype && $_GET['type'] ) {
$subsql= "and  (a.type=$_GET[type] or   articlereltype.typeid =$_GET[type])  ";
//$subsql= "and  a.type=".$_GET[type];
$track ="type";
}


if ($_GET['fpnews']) {
$subsql= "and a.fplink= 1";
$track ="fpnews";
}

###########################define sql for ordering  ###########################
if ($_GET["sorder"]){ $sql = " ORDER BY a.".$_GET["sorder"]; }
//elseif ($_GET["typeo"]){$sql = " ORDER BY articletype.type,  class.class";  }
//elseif ($_GET["classo"]){ $sql = " ORDER BY  class.class";}
//elseif ($_GET["ido"]){$sql = " ORDER BY  a.id"; }
//elseif ($_GET["uselink"]){ $sql = " ORDER BY  a.publish";}
else { $sql = "  ORDER BY a.pageorder asc, a.date desc"; }
 
###########################make sql statement ###########################
$fullsql = "SELECT DISTINCTROW a.date, a.id, a.pageorder, a.publish, a.title,  articletype.type, a.publish, a.uselink,  class.class FROM articles a, class left join articletype on articletype.id = a.type where  class.id=a.class   $subsql $sqlid $sqlauthor $sqldate $sqltitle $sqlfpnews $sql $limit_sql ";
$sqlct= "SELECT DISTINCTROW  a.id FROM articles a, articletype, class where articletype.id = a.type and class.id=a.class  $subsql $sqlid $sqlauthor $sqldate $sqltitle $sqlfpnews $sql ";
if ($MM_reltype ) {
$fullsql = "SELECT DISTINCTROW a.date, a.id,  a.pageorder,  a.publish, a.title, articletype.type, a.publish, a.uselink, class.class  FROM articles a, class  left JOIN articlereltype   on a.id = articlereltype.articleid  left join articletype on articletype.id = a.type where class.id=a.class $subsql $sqlid $sqlauthor $sqldate $sqltitle $sqlfpnews $sql $limit_sql";
$sqlct= "SELECT DISTINCTROW a.id FROM articles a, class  left JOIN articlereltype   on a.id = articlereltype.articleid Left Join articletype on articletype.id = a.type where  class.id=a.class  $subsql $sqlid $sqlauthor $sqldate $sqltitle $sqlfpnews $sql ";
}
//$subsql $sqlid $sqlauthor $sqldate $sqltitle $sqlfpnews $sq
//echo $fullsql;
//die;

   $Recordset1=$dbcon->Execute($fullsql) or DIE($fullsql.$dbcon->ErrorMsg());
   
###########################KEEP PARAMATERS###########################
// create the list of parameters which should not be maintained
$MM_removeList = "&order= &offset= &limit=";
if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";
$MM_keepURL="";
$MM_keepForm="";
$MM_keepBoth="";
$MM_keepNone="";

// add the URL parameters to the MM_keepURL string
reset ($_GET);
while (list ($key, $val) = each ($_GET)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}

// create the Form + URL string and remove the intial '&' from each of the strings
$MM_keepBoth = $MM_keepURL."&".$MM_keepForm;
if (strlen($MM_keepBoth) > 0) $MM_keepBoth = substr($MM_keepBoth, 1);
if (strlen($MM_keepURL) > 0)  $MM_keepURL = substr($MM_keepURL, 1);
if (strlen($MM_keepForm) > 0) $MM_keepForm = substr($MM_keepForm, 1);
#######################################################################
?>

<?php include ("header.php");?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td class="banner"><?php if ($_GET["class"]) {echo $Recordset1->Fields("class");}
if ($_GET['type']) {echo $Recordset1->Fields("type");}
?>&nbsp;Content </td>
        </tr>
        <tr> 
          <td>
<select onChange="MM_jumpMenu('parent',this,0)" class="name">
	  <option SELECTED>View By Section</option>
	  <?php 
	//$selcode="article_list.php?type=";
	  echo $obj->select_type_tree($MX_top,0,"article_list.php?type="); ?></Select>
           
      &nbsp;&nbsp;
<select onChange="MM_jumpMenu('parent',this,0)" class="name">
              <option SELECTED>View By Class</option>
              <option>-----</option>
              <?php  while (!$allclass->EOF){?>
              <option value="article_list.php?class=<?php echo  $allclass->Fields("id")?>" ><?php echo  $allclass->Fields("class");?> 
              </option>
              <?php      $allclass->MoveNext();  } ?>
            </select>
        <?php 
		$keep = "";
		if ($_GET['type']) {$keep = "type=".$_GET['type'];}
		if ($_GET["class"]) {$keep = "class=".$_GET['class'];}
		?>  
		<br>
<select name="repeat" onChange="MM_jumpMenu('parent',this,0)" class="name">
                <option selected># to Display</option>
                <option value="article_list.php?limit=10&<?php echo  $MM_keepURL; ?>">10</option>
                <option value="article_list.php?limit=50&<?php echo  $MM_keepURL; ?>">50</option>
                <option value="article_list.php?limit=100&<?php echo  $MM_keepURL; ?>">100</option>
                <option value="article_list.php?limit=250&<?php echo  $MM_keepURL; ?>">250</option>
                <option value="article_list.php?limit=-1&<?php echo  $MM_keepURL; ?>">All</option>
			
              </select>&nbsp;&nbsp;&nbsp;<a href="article_list.php" class="name"><strong><font face="Verdana, Arial, Helvetica, sans-serif">Display All Content</font></strong></a>&nbsp;&nbsp;&nbsp;<a a href="#" onClick="change_any('search')" class="name"><strong><font face="Verdana, Arial, Helvetica, sans-serif">Search For Content</font></strong></a>
		
		<div id='search' style="display:none">
		
		   <form action="article_list.php?<?php echo $keep ;?>" method="post" name="form2" class="name">
              <strong>Search By </strong><br>
              <input name="sid" type="text" id="id" value="ID" size="5" class="name">
              <input name="stitle" type="text" id="title" value="Title" size="25" class="name">
              <input name="sauthor" type="text" id="author" value="Author" size="20" class="name">
              <input name="sdate" type="text" value="Date (ex 01-12-02)" size="25" class="name">
              <input name="Search" type="submit" id="Search" value="Search" class="name">
              <br>
              Note: Search is based on the section that you are currently in. 
              To search all content please click &quot;All Content&quot; below 
              before you start your search. You may only search one field at a 
              time. 
            </form>
		</div> 
		<br>
			 <script language="javascript" type="text/javascript"> 
function selectall(){ 
t=document.forms[1].length; 
for(i=1; i<t; i++) document.forms[1][i].checked=document.forms[1][0].checked; 
} 
</script> 

            			<?php
if ($_GET['msg'] != '') {
	echo '<b class="red">'. $_GET['msg'] .'</b><br><br>';
}
?>
<?php
//echo $sqlct;
$listct=$dbcon->CacheExecute("$sqlct");
//$count = $listct->fields[0];
$count = $listct->RecordCount();
$total = $limit?($offset +$limit):$count;
if ($total > $count) {$total = $count ;}
echo "<p class=name>Displaying ".($offset +1)."-".$total." of ".$count."  <b>".$q."</b> <br>";
 $pages = ceil(($count/$limit));
if ($pages > 1) {
$i = 0;
$io =0;
echo "<b>Pages:</b>&nbsp;";
while ($i != $pages) {
echo "<a  href=\"article_list.php?offset=";
echo $io. "&";
echo $MM_keepURL ;
echo "\">";
echo ($i +1);

echo "</a> ";
$io = ($io+$limit);
$i++;
}
echo "<br>"; }
$i = ($offset+1);
?></td>
        </tr>
      </table>	 <script language="javascript" type="text/javascript"> 
function selectall(){ 
t=document.forms[1].length; 
for(i=1; i<t; i++) document.forms[1][i].checked=document.forms[1][7].checked; 
} 
</script> 

            <form  action="<?= $PHP_SELF ?>" method="POST">
				<?php  if (AMP_Authorized( AMP_PERMISSION_CONTENT_PUBLISH )){ ?><input type="submit" name="act" value="Publish" class="name">
				<input type="submit" name="act" value="Unpublish" class="name">
        <input type="submit" name="act" value="Delete" class="name" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
        <input type="submit" name="act" value="Change Order" class="name">
		<input name="type" type="hidden" value="<?php echo $_GET['type']; ?>">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong> 
        <input type="checkbox"  value="select_all" onClick="selectall();" />
        Select All</strong> <strong><?php } ?>

		<br>&nbsp;&nbsp;&nbsp;<a href="#" onClick="change_any('section_change')" class=name>Move Articles</a>
		<div id='section_change' style="display:none">
		<select name="change_type" class="name">
	  <option>Select Section To Move To</option>
	  <?php echo $obj->select_type_tree($MX_top); ?></Select>
		<input type="submit" name="act" value="Move Articles" class="name">
		<br>
		<select name="change_class" class="name">
	  <option>Select Class To Change</option>
	        <?php  $allclass->MoveFirst();
	        while (!$allclass->EOF){?>
              <option value="<?php echo  $allclass->Fields("id")?>" ><?php echo  $allclass->Fields("class");?> 
              </option>
              <?php      $allclass->MoveNext();  } ?>
	  </Select>
		<input type="submit" name="act" value="Change Class" class="name">
		</div>

	<div class='list_table'> 
		<table class='list_table'>
			<tr class="intitle"> 
            	<td>&nbsp;</td>
            	<td><b><a href="article_list.php?sorder=id&<?php echo $MM_keepURL ?>" class="intitle">ID</a></b></td>
            	<td><b><a href="article_list.php?sorder=title&<?php echo $MM_keepURL ?>" class="intitle">title</a></b></td>
      	      <td><b><a href="article_list.php?sorder=type&<?php echo $MM_keepURL ?>" class="intitle">section</a></b></td>
        	    <td><b><a href="article_list.php?sorder=date&<?php echo $MM_keepURL ?>" class="intitle">date</a></b></td>
            	<td><b><a href="article_list.php?sorder=pageorder&<?php echo $MM_keepURL ?>" class="intitle">order</a></b></td>
            	<td><b><a href="article_list.php?sorder=class&<?php echo $MM_keepURL ?>" class="intitle">class</a></b></td>
            	<td><b><a href="article_list.php?sorder=publish&<?php echo $MM_keepURL ?>" class="intitle">status</a></b></td>
            	<td>&nbsp;</td>
			</tr><?php 
$i =0;
while (!$Recordset1->EOF){ 
	$i++;
	$bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
?>			<tr bordercolor="#333333" bgcolor="<?php echo $bgcolor; ?>" onMouseover="this.bgColor='#CCFFCC'" onMouseout="this.bgColor='<?php echo $bgcolor; ?>'"> 
            	<td><?php if ( AMP_Authorized( AMP_PERMISSION_CONTENT_EDIT)){ ?><input type="checkbox" name="id[]" value="<?php echo $Recordset1->Fields("id")?>"><?php } ?></td>
            	<td><a href="<?php if ($class==2) {echo "article_fpedit.php";} else { echo "article_edit.php"; } ?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>"><?php echo $Recordset1->Fields("id")?></a>&nbsp;</td>
            	<td><a href="<?php if ($class==2) {echo "article_fpedit.php";} else { echo "article_edit.php"; } ?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id")."&t=".$track ?>"><?php echo $Recordset1->Fields("title")?></a><br></td>
            	<td><?php if ($Recordset1->Fields("type")!= ("none")) {echo $Recordset1->Fields("type");}?></td>
            	<td><font size="-2"><?php echo  DateConvertOut($Recordset1->Fields("date"));?></font></td>
            	<td><input name="order[<?php echo $Recordset1->Fields("id")?>]" type="text"  maxlength="3"  style="width: 29px;"value = "<?php echo $Recordset1->Fields("pageorder");	?>" ></td>
            	<td><?php if ($Recordset1->Fields("class")!= ("none")) {echo $Recordset1->Fields("class");}?></td>
            	<td><?php if (($Recordset1->Fields("publish")) == "1") { echo "live";} else { echo "draft";}  ?></td>
            	<td><a href="<?php if ($class==2) {echo "article_fpedit.php";} else { echo "article_edit.php"; } ?>?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id")."&t=".$track ?>"><img src="images/edit.png" width="16" height="16" border=0></a>&nbsp;<a href="../article.php?id=<?php echo $Recordset1->Fields("id")?>&preview=1" target="_blank"><img src="images/view.jpg" width="16" height="16" border=0></a>&nbsp;<img src="images/delete.png" width="16" height="16"></td>
			</tr>
<?php
	$Recordset1->MoveNext();
}
?>
		</table>
	</div>
</form>
<p> 
<?php include ("footer.php");?>
