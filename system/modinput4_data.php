<?php
  require("Connections/freedomrising.php");

if ($_GET[offset]) {$offset=$_GET[offset];}
else { $offset=0;}
if ($_GET[limit]) {$limit=$_GET[limit];}
else { $limit=50;}
  /**
 * a function to take an array of IDs and set their publish status to 1
 */
function event_publish($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update userdata set publish=1 where id=$id";
			#die($q);
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items posted live.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}
function event_unpublish($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update userdata set publish=0 where id=$id";
			#die($q);
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items unpublished.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}


/**
 * a function to take an array of IDs and delete them
 */
function event_delete($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "delete from userdata where id=$id";
			#die($q);
			$dbcon->execute($q) or die($dbcon->errorMsg());
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
	$modin = "&modin=".$_POST[modin];
	header("location:$loc$q$modin ");
}

/**
 * a switch to see what the page should be doing
 */
switch($_POST['act']) {
	case 'Publish':
		event_publish($_POST['id']);
		break;
	case 'Unpublish':
		event_unpublish($_POST['id']);
		break;	
	case 'Delete':
		event_delete($_POST['id']);
		break;
}
  
  
  
$modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=$_GET[modin]") or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");
  

if (($_GET["sort"]) == ("Company")) {$sqlorder="ORDER BY Company ASC";}
elseif (($_GET["sort"]) == ("Last_Name")) {$sqlorder="ORDER BY Last_Name ASC";}
elseif (($_GET["sort"]) == ("publish")) {$sqlorder="ORDER BY publish ASC";}
elseif (($_GET["sort"]) == ("id")) {$sqlorder="ORDER BY id ASC";}
elseif (empty($_GET["sort"]) == TRUE ){$sqlorder="ORDER BY publish, id ASC";}

if ($student) {$sqlstudent = " and custom4 = 1";}
if  (($search) && ($search != "Search")) {
$sqlorg = " and $_GET[searchby] like '%$_GET[search]%' ";}


 $endorse=$dbcon->Execute("SELECT id, First_Name, Last_Name, Company, publish  FROM userdata where modin = $_GET[modin] $sqlorg    $sqlstudent  $sqlorder  Limit  $offset, $limit;" ) or DIE($dbcon->ErrorMsg()); 
 $sqlct= "SELECT  COUNT(DISTINCT id) FROM userdata where modin = $_GET[modin] $sqlorg    $sqlstudent  $sqlorder ";

?>


<?php include("header.php"); ?>

            <table width="100%" border="0" align="center">
        <tr class="banner"> 
          <td colspan="6">List</td>
        </tr>
			<?php
if ($_GET['msg'] != '') {
	echo '<tr><td colspan="6"><b class="red">'. $_GET['msg'] .'</b></td></tr>';
}
?>
        <tr>
          <td colspan="6" class="name"> 
            <form name="form1" method="get" action="moddata_list.php?<? echo  $keeper1?>">
              <input name="search" type="text" class="name" value="Search" size="30">
              by 
              <select class="name" name="searchby">
			  		<option value = "Company">Company</option>
			   		<option value = "Last_Name">Last Name</option>
				  	<option value = "First_Name">First Name</option>
				    <option value = "EmailAddress">Email</option>
					  <option value = "City ">City</option>
					  <option value = "Country">Country</option>
					  <option value = "region">Region</option>
					  <option value = "publish">Publish</option>
              </select>
              <input type="submit" name="Submit" value="search" class="name">
              <br><?php $keeper2="&modin=$_GET[modin]&sort=$_GET[sort]&offset=$_GET[offset]&search=$_GET[search]&searchby=$_GET[searchby]"; ?>
              <select name="repeat" onChange="MM_jumpMenu('parent',this,0)" class="name">
                <option selected># to Display</option>
                <option value="moddata_list.php?limit=10<?php echo $keeper2; ?>">10</option>
                <option value="moddata_list.php?limit=50<?php echo $keeper2; ?>">50</option>
                <option value="moddata_list.php?limit=100<?php echo $keeper2; ?>">100</option>
                <option value="moddata_list.php?limit=250<?php echo $keeper2; ?>">250</option>
                <option value="moddata_list.php?limit=-1<?php echo $keeper2; ?>">All</option>
			
              </select>&nbsp;&nbsp;<a href="export.php?id=<?php echo $modin ?>">export  to csv file</a>&nbsp;&nbsp;&nbsp;&nbsp;
              <?php if ($modin == 2) { ?>
              <a href="moddata_list.php?modin=<?php echo $modin ?>&student=1" class="name">view 
              all student groups</a> 
              <?php } ?>
              <input type="hidden" name="modin" value="<?php echo $modin?>">
			  </form>
              <?php
	  $listct=$dbcon->CacheExecute("$sqlct");
$count = $listct->fields[0];
$total = ($offset +$limit);
if ($total > $count) {$total = $count ;}
echo "Displaying ".($offset +1)."-".$total." of ".$count."  <b>".$q."</b> <br>";
 $pages = ceil(($count/$limit));
if ($pages > 1) {
$i = 0;
$io =0;
echo "<b>Pages:</b>&nbsp;";
while ($i != $pages) {
echo "<a  href=\"moddata_list.php?modin=$_GET[modin]&search=$_GET[search]&searchby=$_GET[searchby]&sort=$_GET[sort]&limit=$_GET[limit]&offset=";
echo $io;
echo "\">";
echo ($i +1);
echo "</a> ";
$io = ($io+$limit);
$i++;
}
echo "<br><br>"; }
$i = ($offset+1);
?>
              <?php $keeper1="modin=$_GET[modin]&sort=$_GET[sort]&offset=$_GET[offset]&limit=$_GET[limit]"; ?>
             </td>
        </tr>
		<td colspan=6 bgcolor="#ACACBF">
		<form name="multi" action="<?= $PHP_SELF ?>" method="POST">
		<script language="javascript" type="text/javascript"> 
function selectall(){ 
t=document.forms[1].length; 
for(i=1; i<t; i++) document.forms[1][i].checked=document.forms[1][4].checked; 
} 
</script> 
				<input type="submit" name="act" value="Publish" class="name">
				<input type="submit" name="act" value="Unpublish" class="name">
				<input type="submit" name="act" value="Delete" class="name" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
				<input name="modin" type="hidden" value="<?php echo $_GET[modin] ;?>">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><input type="checkbox"  value="select_all" onClick="selectall();" />
        Select All</strong> </td>

      </tr>
        <tr class="intitle"> 
		<td></td>
		<td><b><a href="moddata_list.php?modin=<?php echo $modin ?>&sort=id" class="intitle">id</a></b></td>
         <td><b><a href="moddata_list.php?modin=<?php echo $modin ?>&sort=Last_Name" class="intitle">Name</a></b></td>
		  <td><b><a href="moddata_list.php?modin=<?php echo $modin ?>&sort=Company" class="intitle">Org</a></b></td>
          
          
          <td><b><a href="moddata_list.php?modin=<?php echo $modin ?>&sort=publish" class="intitle">status</a></b></td>
          <td><b></b></td>
		  <td><b></b></td>
        </tr>
        <?php while(!$endorse->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
		 
		 <td bgcolor="#ACACBF"><input type="checkbox" name="id[]" value="<?php echo $endorse->Fields("id")?>"></td>
          <td><?php echo $endorse->Fields("id")?> </td>
          <td> <?php echo $endorse->Fields("First_Name")?>&nbsp;<?php echo $endorse->Fields("Last_Name")?> 
          </td>
          <td><?php echo $endorse->Fields("Company")?> </td>
          <td><?php If (($endorse->Fields("publish")) == "1") { echo "live";} ?> 
          </td>
          <td><div align="right"><A HREF="<?php if ($_GET[editlink]) {echo $_GET[editlink]."?";} else {?>modinput4_view.php?modin=<?php echo $modin ?>&<?php  } echo "uid=".$endorse->Fields("id") ;?>">edit</A></div></td>
		    <td><div align="right"><A HREF="<?php if ($_GET[deletelink]) {echo $_GET[deletelink]."?";} else {?>modinput4_remove.php?modin=<?php echo $modin ?>&<?php  } echo "uid=".$endorse->Fields("id") ;?>">remove</A></div></td>
        </tr>
        
        <?php

  $endorse->MoveNext();
}
?>
      </table>
	  </form>
	
            <p>&nbsp;</p>
            <?php include("footer.php"); ?>