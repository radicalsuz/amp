<?php 
ob_start();
$modid = 100;
$mod_id = 62;
#$modinid=52;

include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");
include_once("AMP/System/Email.inc.php");
include("dropdown.php"); 
$board="ride";

if ($MM_insert||$MM_update){
	if ($MM_insert) {
		$uniqueid = randomid() ;
	} else {
		$MM_editColumn="uniqueid";
		$MM_recordId=$dbcon->qstr($uniqueid);
		$MM_editRedirectUrl=$Web_url."rides.php?uid=".$uniqueid;
	}
   $MM_editTable  = $board;
   $MM_fieldsStr = "firstname|value|lastname|value|phone|value|email|value|state|value|depatingfrom|value|depaturedate|value|returningto|value|returndate|value|numpeople|value|ride|value|commets|value|publish|value|uniqueid|value|pemail|value";
   $MM_columnsStr = "firstname|',none,''|lastname|',none,''|phone|',none,''|email|',none,''|state|',none,''|depatingfrom|',none,''|depaturedate|',none,''|returningto|',none,''|returndate|',none,''|numpeople|',none,''|need|',none,''|commets|',none,''|publish|',none,''|uniqueid|',none,''|pemail|',none,''";
   if ($MM_insert) {
	   //Check to see if e-mail is in system
	   $checkfirst=$dbcon->Execute("Select * from $board where pemail=".$dbcon->qstr( $_POST['pemail'] )." or email=".$dbcon->qstr( $_POST['email'] ));
		if ($checkfirst->RecordCount()>0) { 
			$MM_abortEdit=1; 
			$problem="<strong><p class=docbox>The email address you entered is already registered on the ride board.  Please use <a href=\"rides.php?uid=".$checkfirst->Fields("uniqueid")."\">this link</a> to login or enter new information below.</strong><P>";
		}
		if (!$MM_abortEdit) {
			//Mail to admin for confirmation
			if ($confirm == "admin") {
				$MM_editRedirectUrl = $board."_signin.php?step=admin"; 
				$messagetext = "$firstname $lastname has added a posting on the $board board\n\n Information:\n Phone: $phone\n Email: $email\n State: $state\n Departing From: $depatingfrom\n Departure Date: $depaturedate\n Returning to: $returningto\n Return Date:$returndate\n Number of People: $numpeople\n  Comments:  $commets  \n\nPlease visit ".$Web_url.$board."_signin.php?uid=$uniqiueid to publish";
				mail("$MM_email_ride", "new $board board posting", "$messagetext", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\n");}

			//Mail to poster for confirmation
			if ($confirm == "poster") { 
				$MM_editRedirectUrl = $board."_signin.php?step=email";
				$messagetext2 = "\nPlease visit <a href=\"".$Web_url.$board."_signin.php?uid=$uniqueid&action=confirm\">".$Web_url.$board."_signin.php?uid=$uniqueid&action=confirm</A> to confirm your $board board posting.\n\n Information:\n Phone: $phone\n Email: $email\n State: $state\n Departing From: $depatingfrom\n Departure Date: $depaturedate\n Returning to: $returningto\n Return Date:$returndate\n Number of People: $numpeople\n Comments:  $commets  \n ";
				mail("$pemail", "Confirm your $board board posting", "$messagetext2", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\n"); }
		}
   }

#} else { 
	# echo $dbcon->ErrorMsg();
#}

require ("DBConnections/insetstuff.php"); 
require ("DBConnections/dataactions.php"); 

ob_end_flush();
}
if ($MM_update) echo "MMUPDATE<BR>";
if ($MM_insert) echo "MMINSERT<BR>";
#echo "hey$uid";
if ($uid) { //load form edit vars from DB
	
	$sql="SELECT * FROM $board where uniqueid=".$dbcon->qstr( $_GET[uid] );
	$user=$dbcon->Execute($sql);
	if ($user->RecordCount()>0) {
		$firstname=$user->Fields("firstname");
	
		$lastname=$user->Fields("lastname");
		$phone=$user->Fields("phone");
		$email=$user->Fields("email");
		$pemail=$user->Fields("pemail");
		$depatingfrom=$user->Fields("depatingfrom");
		$depaturedate=$user->Fields("depaturedate");
		$returningto=$user->Fields("returningto");
		$returndate=$user->Fields("returndate");
		$state=$user->Fields("state");
		$numpeople=$user->Fields("numpeople");
		$ride=$user->Fields("need");
		$commets=$user->Fields("commets");
		$button_action="MM_update";
		$publish=$user->Fields("publish");
		$uniqueid=$uid;
		$recordid=$user->Fields("id");
	} else {
		$problem="No user information was found.  Please enter a new record below.";
	}
} else {
	$button_action="MM_insert";
}
if($_POST['pemail']) { //load form POST vars
		$firstname=$_POST['firstname'];
		$lastname=$_POST['lastname'];
		$phone=$_POST['phone'];
		$email=$_POST['email'];
		$pemail=$_POST['pemail'];
		$depatingfrom=$_POST['depatingfrom'];
		$depaturedate=$_POST['depaturedate'];
		$returningto=$_POST['returningto'];
		$returndate=$_POST['returndate'];
		$state=$_POST['state'];
		$numpeople=$_POST['numpeople'];
		$publish=$_POST['publish'];
		$ride=$_POST['ride'];
		$commets=$_POST['commets'];
		#$button_action="MM_insert";
		
}

	echo $problem;

?>
     <form name="rideinput" action="<?php echo $PHP_SELF; ?>" method="POST"> 
 <center>

  <table width= "400" border="0" cellspacing="0">
    <tr> <td colspan=2>

<center>
<table width="300" bgcolor="silver"><tr>
      <td align="right" class="form"><div align="left" class="bodygreystrong">I have a Ride to Offer</div></td>
      <td> <input type="radio" name="ride" value="have"<?php if ($ride=="have") echo " CHECKED";?>> </td>
    </tr>
	<tr> 
      <td align="right" class="form"><div align="left"  class="bodygreystrong">I Need a Ride</div></td>
      <td> <input type="radio" name="ride" value="need"<?php if ($ride=="need") echo " CHECKED";?>> </td>
    </tr>
<tr> 
      <td align="right" class="form"><div align="left"  class="bodygreystrong">I am willing to Organize a Bus</div></td><td> <input type="radio" name="ride" value="org"<?php if ($ride=="org") echo " CHECKED";?>> </td></tr>
      </table>
    </center>
<P><P>
	</td>
	</tr>
    <tr> 
      <td align="right" class="form"><div align="left">First Name</div></td>
      <td> <input name="firstname" type="text" size="35"<?php echo " value=\"".$firstname."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Last Name</div></td>
      <td><input name="lastname" type="text" size="35"<?php echo " value=\"".$lastname."\"";?>></td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Phone Number</div></td>
      <td> <input type="text" name="phone" size="35"<?php echo " value=\"".$phone."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Public E-mail</div></td>
      <td> <input type="text" name="email" size="35"<?php echo " value=\"".$email."\"";?>> </td>
    </tr>
	 <tr> 
      <td align="right" class="form"><div align="left">*Private E-mail<BR> *use this to logon later</div></td>
      <td> <input type="text" name="pemail" size="35"<?php echo " value=\"".$pemail."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form" width =100><div align="left">State</div></td>
      <td> <SELECT name="state" size="1"><?php 
		 if ($state) {
			$statespot1=strpos($StateDropDown, "VALUE=\"".$state."\"");
			$statespot1=strpos($StateDropDown, ">", $statespot1);
			$statespot2=strpos($StateDropDown, "<", ($statespot1+2));
			$statename=substr($StateDropDown, $statespot1+1, $statespot2-$statespot1-1);
			echo "<OPTION VALUE=\"".$state."\" SELECTED>".$statename."</OPTION>";
		}?>
	  <?php echo $StateDropDown;?></SELECT> </td>
    </tr>
    
	<tr> 
      <td align="right" class="form" width =100><div align="left">Location Departing From</div></td>
      <td> <input type="text" name="depatingfrom" size="35"<?php echo " value=\"".$depatingfrom."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Depature Date</div></td>
      <td> <input type="text" name="depaturedate"<?php echo " value=\"".$depaturedate."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Location Returning To</div></td>
      <td> <input type="text" name="returningto" size="35"<?php echo " value=\"".$returningto."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Return Date</div></td>
      <td> <input type="text" name="returndate"<?php echo " value=\"".$returndate."\"";?>> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Available Spaces (if offering ride)</div></td>
      <td> <input type="text" name="numpeople" size="15"<?php echo " value=\"".$numpeople."\"";?>> </td>
    </tr>
     <tr> 
      <tr> 
      <td colspan="2" align="right" class="form"><div align="left">&nbsp;<P>Comments<br>
        </div></td>
    </tr>
	
	  <td colspan="2" align="right" class="form"><div align="left">
          <textarea name="commets" cols="40" wrap="VIRTUAL" rows="4"><?php echo $commets;?></textarea>
        </div></td>
    </tr>
  </table>
  
              <input type="hidden" name="publish" value="<?php if ($publish) {echo $publish;} else {echo "0";}?>">
              <input type="submit" name="Submit" value="Save">
			   	 <input type="hidden" name="boards" value="2">
              <input type="hidden" name="<?php echo $button_action;?>" value="true">
			<input type="hidden" name="uniqueid" value="<?php echo $uniqueid;?>">
			</form>
            
  </center>      
 <?php include("AMP/BaseFooter.php"); ?>     
