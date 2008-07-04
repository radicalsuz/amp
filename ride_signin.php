<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  housing confirmation page  
GET VARS step
To Do:  				
*********************/ 
$board= "ride";
$modid = 100;
$mod_id = 64;
#$modinid=52;
ob_start();
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");
include_once("AMP/System/Email.inc.php");

function getid($uid) {
	global $dbcon,$board, $modinid;
	$sql="Select id from $board where uniqueid = ".$dbcon->qstr($uid);
	#echo $sql."\r\n<P>";
	$getuid=$dbcon->Execute($sql);
    if( $getuid ) {
        return $getuid->Fields("id");
    }
    return false;
}

function postposting($uid) {
	global $dbcon, $board;
   $id = getid($uid);
   if (!$id) {$reply = "Your posting is not in the system";}
   else{
	$sql="Update $board set publish = 1 where id = ".$dbcon->qstr( $id );
   #echo $sql;
   $dbcon->Execute($sql);
   	$reply = "Your posting has been added to the board. <a href=\"rides.php?uid=$uid\"><BR>Return to ride board</a><P>";
	}
	echo $reply;
}


function confirmride($uid) {
	global $dbcon, $board, $modinid, $Web_url, $user;
	$sql="SELECT * FROM $board where DriverID=".$dbcon->qstr( $uid );
	if ($getriders=$dbcon->execute($sql)) {
		
		$my_id="";
		while (!$getriders->EOF) {
			#echo "howdy2/";
			$my_id = $getriders->Fields("uniqueid"); #rider id field
			$valuename="confirmed".$my_id;
			$newvalue= isset( $_GET[$valuename] ) && $_GET[$valuename] == 'on' ? 1 : 0;
			if ($newvalue <> $getriders->Fields("Confirmed")) {
				$sql= "UPDATE $board SET Confirmed=".$dbcon->qstr( $newvalue )." WHERE uniqueid=".$dbcon->qstr( $my_id );
				#echo $sql;
				if($didwork = $dbcon->Execute($sql)) {
					$messagetext2 = "\nYour status on the $board board at $Web_url has changed.\n";
					if ($newvalue==1) {
						$messagetext2.=$user->Fields('firstname')." ".$user->Fields('lastname')." has confirmed you as a passenger. \n\n Trip Information:\n Departing From: ".$user->Fields('depatingfrom')."\nDeparture Date: ".$user->Fields('depaturedate')."\nReturning To: ".$user->Fields('returningto')."\nReturn Date: ".$user->Fields('returndate')."\nPhone: ".$user->Fields('phone')."\nEmail: ".$user->Fields('email');
					} else {
						$messagetext2.=$user->Fields('firstname')." ".$user->Fields('lastname')." has removed you as a passenger in their vehicle. \n";
					}
					mail($getriders->Fields("pemail"), "Ride request status change", "$messagetext2", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\n");
					
				}
			}
			$getriders->MoveNext();
		}  
		echo "\n<P>Your changes have been logged.  <P>To review your riders, <a href=\"rides.php?uid=$uid\">go back to the ride board.</a>";
	}
}


function deleteposting($uid) {
	global $dbcon, $board;
	$id = getid($uid);
	if (!$id) {$reply = "Your posting is not in the system";}
   else{
	$getuid=$dbcon->Execute("Delete from $board where id = ".$dbcon->qstr( $id ));
	$reply = "Your posting has been removed from the board. <a href=\"$PHP_SELF\">Return to $board board</a><P>";}
	echo $reply;
}

function deleteform() {
global $board;
?>
<p>Enter your private contact e-mail</p>
<form ACTION="<?php echo $_SERVER['PHP_SELF']?>" METHOD="POST" name="form1">
  <input type="text" name="pemail">
  <input type="submit" name="submit" value="go!">
  <input type="hidden" name="action" value="delemailsend">
</form>
 <?php
}

function senddelemail($pemail) {
global $board, $dbcon, $Web_url, $modinid;
$getuid = $dbcon->Execute("Select id, uniqueid from $board where pemail = ".$dbcon->qstr( $pemail ) );
$uid = $getuid->Fields("uniqueid");
$messagetext = "To remove your listing simply visit this page ".$Web_url.$board."_signin.php?deluid=$uid";
  if (email_is_valid($pemail)){
   mail($pemail, "remove your $board posting", "$messagetext", "From:".AMPSystem_Email::sanitize($MM_email_from));
   echo "An e-mail has been sent to you with instructions on how to remove yourself from the board.";}
else {echo "Your email is invalid or not in our system<br><br>"; 
			deleteform();}
}

$showlogin=1;

// delete 
if ($_GET['del']) {deleteform(); $showlogin=0;}
if ($_GET['deluid']){deleteposting($_GET[deluid]); $showlogin=0;}
if ($_POST['action'] == 'delemailsend'){ senddelemail($_POST['pemail']); $showlogin=0; }

//confirm posting
if (($_GET['uid'])&&($_GET['action']=="confirm")) {postposting($_GET['uid']); $showlogin=0;}
if ($_GET['step'] == 'email') {echo "An e-mail has been sent to your e-mail account with instructions on how to confirm your posting."; $showlogin=0;}
if ($_GET['step'] == 'admin') { echo "Your posting has been added to the board.  The moderator will approve your posting soon."; $showlogin=0;}

//request ride
if ($uid && $_GET['setdriver'] && ($action=="requestride")) {
	$showlogin=0;
	$user=$dbcon->Execute("SELECT * from $board where uniqueid=".$dbcon->qstr( $uid ));
	$sql="SELECT * from $board where uniqueid='".$_GET['setdriver']."'";
	#echo $sql;
	$getriders=$dbcon->Execute($sql);
	
	$sql="UPDATE $board SET DriverID=".$dbcon->qstr($_GET['setdriver'])." WHERE uniqueid='".$uid."'";
	if($didwork=$dbcon->Execute($sql)){ 
		//send e-mail ride request notification to driver:
		$messagetext2 = "\nRide Requested on $board board at $Web_url.\n";
		$messagetext2.=$user->Fields('firstname')." ".$user->Fields('lastname')." requested a ride in your vehicle. \nTo confirm this and other ride requests, please visit ".$Web_url."rides.php?uid=".$getriders->Fields("uniqueid")."\n\n Ride Request Information:\n";
		$messagetext2.="Departing From: ".$user->Fields('depatingfrom')."\nDeparture Date: ".$user->Fields('depaturedate')."\nReturning To: ".$user->Fields('returningto')."\nReturn Date: ".$user->Fields('returndate')."\nPhone: ".$user->Fields('phone')."\nEmail: ".$user->Fields('email');
		
		mail($getriders->Fields("pemail"), "Ride request", "$messagetext2", "From: ".AMPSystem_Email::sanitize($MM_email_from)."\n");
		echo "<P>Your ride request was sent successfully. <BR><A href=\"rides.php?uid=$uid\">Click here to return to the ride board.</A><P>";	
	}
}

//confirmrides
if ($uid&&($action=="confirmrides")) {
	$user=$dbcon->Execute("Select * from $board where uniqueid=".$dbcon->qstr( $uid ));
	confirmride($uid);
	$showlogin=0;
}

//showpage redirect

if ($_POST['action']=="showpage") {
	$showlogin=0;
	$sql="SELECT * FROM $board WHERE pemail=".$dbcon->qstr($_POST['pemail']);
	#echo $sql;
	$user = $dbcon->Execute($sql);
	if($user && $user->RecordCount()>0) {

		header ("Location: ".$Web_url."rides.php?uid=".$user->Fields("uniqueid"));
	} else {
		echo "No record was found in the $board board\n";
		echo "Please <a href=\"ride_add_offer.php\">click here to enter your information.</A><P>";
	}
}




//enables user signin if coming from external page
if ($showlogin){
?>

<strong>Please login using your private e-mail address:</strong><BR>
<form ACTION="<?php echo $PHP_SELF;?>" METHOD="POST" name="form1">
  <input type="text" name="pemail">
  <input type="submit" name="submit" value="go!">
  <input type="hidden" name="action" value="showpage">
</form>
<P>
If you have not used the ride board before, <a href="ride_add_offer.php">click here to enter a new ride record.</A>
<?php
} else {
	echo "Thank you for using the ride board.<P> <A href=\"$Web_url\">Click here to return to the home page.</A>";
	#header ("Location: ".$Web_url."rides.php?uid=".$user->Fields("uniqueid"));
}
ob_end_flush();		
include("AMP/BaseFooter.php"); 
?>
