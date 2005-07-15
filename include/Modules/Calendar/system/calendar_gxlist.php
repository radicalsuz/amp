<?php
$studenton=1;
$modid=1;
$mod_name = "calendar";
  require("Connections/freedomrising.php");
/**
 * a function to take an array of IDs and set their publish status to 1
 */
function event_publish($ids) {
	global $dbcon;
	if (is_array($ids)) {
		foreach ($ids as $id) {
			$q = "update calendar set publish=1 where id=$id";
			#die($q);
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected events posted live.')
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
			$q = "delete from calendar where id=$id";
			#die($q);
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected events deleted.')
		);
	}
	send_to($_SERVER['PHP_SELF'], $qs);
}

/**
 * a function to to do a header redirect, you can feed it an option associative array to build a query string
 */
function send_to($loc, $query=null) {
	if (is_array($query)) {
		$q = '?';
		foreach ($query as $k=>$v) {
			$q .= "$k=$v&";
		}
	}
	header("location:$loc$q");
}

/**
 * a switch to see what the page should be doing
 */
switch($_POST['act']) {
	case 'Publish':
		event_publish($_POST['id']);
		break;
	case 'Delete':
		event_delete($_POST['id']);
		break;
}
  
  
  
?><?php
if  ($area != NULL){ $sqlarea = " and lstate = $area " ;
 } else {
 $sqlarea = "";
}
  if  ((isset($byid))  and ($byid != "By ID") ){ $sqltype = "and calendar.id = $byid" ;}
 if  (isset($caltype)  and ($caltype != "By Event Type") and ($caltype != "student")){ $sqltype = "and typeid = $caltype" ;}
   if  ($caltype == "student"){ $sqltype = "and student = 1" ;}
 $sqldate = " and calendar.date >=  CURDATE()" ;
 $repeatvar = "repeat = 0 and";
 if  (isset($calid)){ $sqlid = "and calendar.id = $calid" ;
  $sqldate = " " ;
  $repeatvar ="";
 }
if  (isset($bydate) and ($bydate != "By Date (ex 01-28-03)")){ 
 if ((ereg ("([0-9]{1,2})-([0-9]{1,2})-([0-9]{2})", $bydate, $regs)) ) {
    $bydate = "$regs[3]$regs[1]$regs[2]";}
 $sqldate = "and calendar.date = $bydate" ;
 $sqldate2 =$sqldate;}



if (($HTTP_GET_VARS["sort"]) == ("event")) {$sortvar="calendar.event"; }
elseif (($HTTP_GET_VARS["sort"]) == ("date")) {$sortvar="calendar.date"; }
elseif (($HTTP_GET_VARS["sort"]) == ("state")) {$sortvar="calendar.lstate"; }
elseif (($HTTP_GET_VARS["sort"]) == ("id")) {$sortvar="calendar.id"; }
elseif (($HTTP_GET_VARS["sort"]) == ("publish")) {$sortvar="calendar.publish"; }
else {$sortvar="calendar.publish, calendar.date"; }
if ($_REQUEST['old'] == '1')
 {$neworold = "<=";
 } 
else {$neworold = ">";}
if ($_REQUEST['endorse']== 1){$endosresql = " and endorse=1 ";}
else {$endosresql = "";}
if ($_REQUEST['fpevent']== 1){$fpeventsql = " and fpevent=1 ";}
else {$fpeventsql = "";}
if ($_REQUEST['student']== 1){$studentsql = " and student=1 ";}
else {$studentsql = "";}
	$state_lookup=$dbcon->GetAssoc("SELECT id, state from states");
	//print "SELECT date, id, publish, event, lstate FROM calendar WHERE date $neworold Now()-1 $endosresql $fpeventsql $studentsql and repeat != 1 $sqldate2 $sqlarea $sqltype $sqlid ORDER BY ".$sortvar." asc";
   $currentevents=$dbcon->Execute("SELECT date, id, publish, event, lstate FROM calendar WHERE date $neworold Now()-1 $endosresql $fpeventsql $studentsql and repeat != 1 $sqldate2 $sqlarea $sqltype $sqlid ORDER BY ".$sortvar." asc") or DIE($dbcon->ErrorMsg());
   $currentevents_numRows=0;
   $currentevents__totalRows=$currentevents->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $currentevents_numRows = $currentevents_numRows + $Repeat1__numRows;
?><?php require_once("header.php"); ?>
      <p></p>

            
      <table width="100%" border="0" align="center">
        
		   <tr class="banner" > 
          <td colspan="7">Current Events</td>
        </tr>
		<?php
if ($_GET['msg'] != '') {
	echo '<tr><td colspan="7"><b class="red">'. $_GET['msg'] .'</b></td></tr>';
}
?>
		   <tr > 
          <td colspan="7">
		  <?php $typelist=$dbcon->Execute("SELECT * from eventtype order by name asc");?>
		  <form name="form1" method="post" action="calendar_gxlist.php<?php if  (isset($HTTP_GET_VARS["area"])){?>?area=<?php echo $HTTP_GET_VARS[area]; }?>" class="name">Search the Calendar<br>&nbsp; 
              <input name="byid" type="text" id="byid" value="By ID" size="7" class="name">
              &nbsp;<span class="name"><?php statelist(area); ?></span>
              <select name="caltype" id="bytype" class="name">
<option selected>By Event Type </option>
<?php while (!$typelist->EOF) { ?>
<option value="<?php echo $typelist->Fields("id"); ?>"><?php echo $typelist->Fields("name"); ?></option>
  <?php $typelist->MoveNext();}?>
  </option>
<?php if ($studenton == "1") { ?>
<option value="student">Student</option>
<?php } ?>
  </select>
              or 
              <input name="bydate" type="text" id="bydate" value="By Date (ex 01-28-03)" size="23" class="name">
  <input name="Search" type="submit" id="Search" value="Search" class="go">
     
</form>
		<a href="calendar_gxlist.php?old=1" class="name"><strong>Old Events</strong></a>&nbsp;&nbsp;&nbsp;<a href="calendar_gxlist.php?endorse=1" class="name">Endorsed</a>&nbsp;&nbsp;&nbsp;<a href="calendar_gxlist.php?fpevent=1" class="name">Front 
      Page</a>&nbsp;&nbsp;&nbsp;<a href="calendar_gxlist.php?student=1" class="name">Student</a>&nbsp;&nbsp;&nbsp;<a href="calendar_gxlist.php?repeating=1" class="name">Repeating</a>&nbsp;&nbsp;&nbsp;<a href="calendar_gxlist.php" class="name">Current</a></td>
	  <?php if ($_REQUEST['repeating'] != 1){?>
	  <form name="multi" action="<?= $PHP_SELF ?>" method="POST">
        </tr>
		
		<tr> 
          <td colspan=7 bgcolor="#ACACBF">
				<input type="submit" name="act" value="Publish" class="name">
				<input type="submit" name="act" value="Delete" class="name" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"></td>

      </tr>
		
		<tr class="intitle">
		<td>&nbsp;</td> 
          <td><b><a href="calendar_gxlist.php?sort=id" class="intitle">ID</a></b></td>
          <td><b><a href="calendar_gxlist.php?sort=date" class="intitle">Start Date</a></b></td>
          <td><strong><a href="calendar_gxlist.php?sort=state" class="intitle">State</a></strong></td>
          <td><b><a href="calendar_gxlist.php?sort=event" class="intitle">Event</a></b></td>
          <td><strong><a href="calendar_gxlist.php?sort=publish" class="intitle">status</a></strong></td>
          <td>&nbsp;</td>
        </tr>
        <?php while (($Repeat1__numRows-- != 0) && (!$currentevents->EOF)) 
   { 
?>
        <tr bgcolor="#CCCCCC"> 
		 <td bgcolor="#ACACBF"><input type="checkbox" name="id[]" value="<?php echo $currentevents->Fields("id")?>"></td>
          <td> <?php echo $currentevents->Fields("id")?></td>
          <td> <?php echo DateConvertOut($currentevents->Fields("date")) ?> </td>
          <td><?php if (isset($state_lookup[$currentevents->Fields("lstate")])) {
						echo $state_lookup[$currentevents->Fields("lstate")];
					} else {
						echo $currentevents->Fields("lstate");
					} ?></td>
          <td><?php echo $currentevents->Fields("event")?> </td>
          <td> <?php if (($currentevents->Fields("publish")) == "1") { echo "live";} ?> </td>
          <td><div align="right"><A HREF="calendar_gxedit.php?id=<?php echo $currentevents->Fields("id") ?>">edit</A></div></td>
        </tr>
     
        <?php
  $Repeat1__index++;
  $currentevents->MoveNext();
}
?><tr class="intitle" > 
          <td colspan="7">&nbsp; </td>
        </tr>
      </table>
<?php
  $currentevents->Close();
?>
<?php
}//end not repeating
if ($_REQUEST['repeating'] == 1){
if (($HTTP_GET_VARS["sort"]) == ("event")) {$sortvar="calendar.event"; }
elseif (($HTTP_GET_VARS["sort"]) == ("date")) {$sortvar="calendar.date"; }
elseif (($HTTP_GET_VARS["sort"]) == ("state")) {$sortvar="calendar.lstate"; }
elseif (($HTTP_GET_VARS["sort"]) == ("id")) {$sortvar="calendar.id"; }
elseif (($HTTP_GET_VARS["sort"]) == ("publish")) {$sortvar="calendar.publish"; }
else {$sortvar="calendar.date"; }

   $currentevents=$dbcon->Execute("SELECT calendar.date, calendar.id, calendar.publish, calendar.event, calendar.lstate FROM calendar WHERE repeat = 1 $sqldate2 $sqlarea $fpeventsql  $sqltype $sqlid $endosresql $studentsql ORDER BY ".$sortvar." asc") or DIE($dbcon->ErrorMsg());
   $currentevents_numRows=0;
   $currentevents__totalRows=$currentevents->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $currentevents_numRows = $currentevents_numRows + $Repeat1__numRows;

?>
          
            <p>&nbsp;</p><table width="100%" border="0" align="center">
               <tr class="banner" > 
          <td colspan="7">Repeating Events</td>
        </tr>  <tr class="intitle"> 
		<td>&nbsp;</td> 
          <td><b><a href="calendar_gxlist.php?sort=id" class="intitle">ID</a></b></td>
          <td><b><a href="calendar_gxlist.php?sort=date" class="intitle">Start Date</a></b></td>
          <td><strong><a href="calendar_gxlist.php?sort=state" class="intitle">State</a></strong></td>
          <td><b><a href="calendar_gxlist.php?sort=event" class="intitle">Event</a></b></td>
          <td><strong><a href="calendar_gxlist.php?sort=publish" class="intitle">status</a></strong></td>
          <td>&nbsp;</td>
        </tr>
			  <?php while (($Repeat1__numRows-- != 0) && (!$currentevents->EOF)) 
   { 
?>
              <tr bgcolor="#CCCCCC"> 
                
          <td> <?php echo $currentevents->Fields("id")?></td>
                <td> <?php echo DateConvertOut($currentevents->Fields("date")) ?> </td>
				<td> <?php 
				if (isset($state_lookup[$currentevents->Fields('lstate')])) {
					echo $state_lookup[$currentevents->Fields('lstate')];
				} else {
					echo $currentevents->Fields("lstate");               
				}
				?> </td>
          <td><?php echo $currentevents->Fields("event")?> </td>
                <td><?php if (($currentevents->Fields("publish")) == "1") { echo "live";} ?></td>
                <td><div align="right"><A HREF="calendar_gxedit.php?id=<?php echo $currentevents->Fields("id") ?>">edit</A></div></td>
              </tr>
			  <?php
  $Repeat1__index++;
  $currentevents->MoveNext();
}
?>  <tr class="intitle" > 
          <td colspan="7">&nbsp;</td>
        </tr>
            </table>
</form>
<?php
  $currentevents->Close();
}
?>

<?php require_once("footer.php"); ?>
