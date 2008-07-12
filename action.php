<?php

$intro_id = 62;  
$actionid = isset( $_REQUEST['action']) ? intval( $_REQUEST['action'] ): false; 
if ( !$actionid ) {
    $actionid = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : false;
}
if ( !$actionid ) {
    $actionid = isset( $_REQUEST['actionid'] ) ? intval( $_REQUEST['actionid'] ) : false;
}
//$modid = 21;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
//include("AMP/BaseModuleIntro.php");


include("emaillist_functions.php");


function buildactionform($id,$error=NULL) {
	global $dbcon;
	$act = $dbcon->Execute("SELECT *  FROM action_text  WHERE id = ".$dbcon->qstr( $id ) . " and actiontype != 'Congress Merge'") ;
    if ( $act ) {

  
?>
 <p class="title"><?php echo $act->Fields("title"); ?></p>
 <p class="text"><?php echo converttext($act->Fields("introtext")); ?></p>
 <?php echo $error;?>
<script language="Javascript" type="text/javascript">

var fieldstocheck = new Array();
    fieldnames = new Array();

function checkform() {
  for (i=0;i<fieldstocheck.length;i++) {
    if (eval("document.subscribeform.elements['"+fieldstocheck[i]+"'].value") == "") {
      alert("Please enter your "+fieldnames[i]);
      eval("document.subscribeform.elements['"+fieldstocheck[i]+"'].focus()");
      return false;
    }
  }
  return true;
}
function addFieldToCheck(value,name) {
  fieldstocheck[fieldstocheck.length] = value;
  fieldnames[fieldnames.length] = name;
}

</script>	

 
<form method="POST"  name="subscribeform" class="form" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <script language="Javascript" type="text/javascript">
  addFieldToCheck("First_Name","First Name");

  
  
  </script> 

        
    <input type="hidden" name="actionid" value="<?php echo $act->Fields("id"); ?>">
    <input type="hidden" name="MM_insert" value="true">

  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr> 
      <td width="50%" valign="top"><h3>Read and Edit the Letter</h3></td>
      <td width="20">&nbsp;</td>
      <td width="50%"><h3>Send the Message</h3></td>
    </tr>
    <tr> 
      <td width="31%" valign="top"><TABLE width=250  cellpadding=0 cellspacing=0>
          <TR> 
            <!-- PREVIEW LETTER -->
            <TD valign="top"> </TD>
          </TR>
        </TABLE>
        <center>
          <TABLE CELLPADDING="2" CELLSPACING="2" bgcolor="#E1E1E1">
            <tr> 
              <td valign="top"> 
                Subject:<BR> <input type="text" name="subjectText" size="42" style="width:225px" value="<?php if ($_POST['subjectText']) {echo $_POST['subjectText'];} else { echo $act->Fields("subject"); }?>" > 
                <br></i>
                <br>
                Dear <?php echo $act->Fields("prefix")?>&nbsp;<?php echo $act->Fields("firstname")?>&nbsp;<?php echo $act->Fields("lastname")?>,<br> <br> <textarea name="Letter_Content" rows="22" cols="53" wrap="soft" style="width:250px"><?php if ($_POST['Letter_Content']) {echo $_POST['Letter_Content'];} else { echo $act->Fields("text"); }?></textarea> 
                <br>
                Sincerely,<br> <br> <i>Your signature will be added from the information 
                you provide below.</i></td>
            </tr>
          </TABLE>
        </center></td>
      <td width="19%" valign="top">&nbsp;</td>
      <td width="50%" valign="top"> <center>
          <table border="0" cellpadding="0" cellspacing="2">
            <tr> 
              <td colspan="2" valign="top" class="form"><TABLE cellpadding=0 cellspacing=0>
                <tr> 
                  <td>
                  <div align="left"> 
                  First, enter the code from the image below 
                        <span class='red'><font color = 'red'><?php print $GLOBALS['captcha_message']; ?></font></span><BR />
                        <img src='<?php print AMP_url_add_vars( AMP_CONTENT_URL_CAPTCHA, array( 'key=' . AMP_SYSTEM_UNIQUE_VISITOR_ID ));?>'/>
                  </div></td>
                </tr>
                <tr> 
                  <td>
                  <div align="left"> 
                  <p>
                      <input name="captcha" type="text" id="captcha" size="8"/>
                      <input name='AMP_SYSTEM_UNIQUE_VISITOR_ID' type='hidden' value='<?php print AMP_SYSTEM_UNIQUE_VISITOR_ID; ?>'/>
                    </p>
                    </div></td>
                </tr>
                  <tr> 
                    <td>  <B>Already Taken Action? <br>
                      Just Enter Your Email and Send:</B> </td>
                  </tr>
                  <tr> 
                    <td> <table>
                        <tr> 
                          <td>Email:&nbsp; <input type="text" maxlength="99" name="old_EmailAddress" size="20"  style="width:140px"></td>
                          <td><input type="submit" class="submit" name="Submit2" value="Send"></td>
                        </tr>
                        <tr> 
                          <td> 
                            <?php if ($act->Fields("email")) { ?>
                            <INPUT  type="checkbox"	NAME="'Send_Email'" checked> 
                            <span class="form">Send an Email</span> 
                            <?php } ?>
                            <?php if ($act->Fields("fax")) { ?>
                            <INPUT  type="checkbox"	NAME="Send_Fax" checked > 
                            <span class="form">Send a Fax</span> 
                            <?php } ?>                            &nbsp;&nbsp;&nbsp; </td>
                        </tr>
                      </table> </td>
                  </tr>
                </TABLE></td>
            </tr>
            <tr> 
              <td colspan="2" valign="top" class="form"><div align="left"><br>
                <!--  First Time Taking Action? <br>
                  Please fill out the fields below<br> -->
                 <b>All fields below are required</b><br>
                </div></td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">E-Mail:&nbsp;</div></td>
              <td> <input name="Email" type="text"  size="35" value="<?php echo $_POST["Email"] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">Title:&nbsp;</div></td>
              <td> <select name="Title" size="1" >
  			  	<?php if ($_POST['Title']) {echo "<option>".$_POST['Title']."</option>";}?>
                  <option value=""SELECTED>Select Title 
                  <option value="Mr." >Mr. 
                  <option value="Ms." >Ms. 
                  <option value="Mrs." >Mrs. 
                  <option value="Miss" >Miss 
                  <option value="Dr." >Dr. 
                  <option value="Rabbi" >Rabbi 
                  <option value="Fr." >Fr. 
                  <option value="Rev." >Rev. 
                  <option value="Hon." >Hon. 
                  <option value="Br." >Br. 
                  <option value="Sr." >Sr. 
                  <option value="Msr." >Msr. </select> </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right"><nobr>First Name:&nbsp;</nobr></div></td>
              <td> <input name="First_Name" type="text"  size="35" value="<?php echo $_POST['First_Name'] ;?>" style="width:160px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right"><nobr>Last Name:&nbsp;</nobr></div></td>
              <td> <input name="Last_Name" type="text"  size="35" value="<?php echo $_POST['Last_Name'] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">Address:&nbsp;</div></td>
              <td> <input name="Street" type="text"  size="35" value="<?php echo $_POST['Street'] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right"></div></td>
              <td> <input name="Street_2" type="text"  size="35" value="<?php echo $_POST['Street_2'] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">City:&nbsp;</div></td>
              <td> <input name="City" type="text"  size="35" value="<?php echo $_POST['City'] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">State:&nbsp;</div></td>
              <td><nobr> <select TABINDEX=7 name="State" size="1">
			  	<?php if ($_POST['State']) {echo "<option>".$_POST['State']."</option>";}?>
                  <OPTION VALUE="">Select One...</option>
                  <OPTION VALUE="AL" >Alabama</OPTION>
                  <OPTION VALUE="AK" >Alaska</OPTION>
                  <OPTION VALUE="AZ" >Arizona</OPTION>
                  <OPTION VALUE="AR" >Arkansas</OPTION>
                  <OPTION VALUE="CA" >California</OPTION>
                  <OPTION VALUE="CO" >Colorado</OPTION>
                  <OPTION VALUE="CT" >Connecticut</OPTION>
                  <OPTION VALUE="DE" >Delaware</OPTION>
                  <OPTION VALUE="DC" >D.C.</OPTION>
                  <OPTION VALUE="FL" >Florida</OPTION>
                  <OPTION VALUE="GA" >Georgia</OPTION>
                  <OPTION VALUE="HI" >Hawaii</OPTION>
                  <OPTION VALUE="ID" >Idaho</OPTION>
                  <OPTION VALUE="IL" >Illinois</OPTION>
                  <OPTION VALUE="IN" >Indiana</OPTION>
                  <OPTION VALUE="IA" >Iowa</OPTION>
                  <OPTION VALUE="KS" >Kansas</OPTION>
                  <OPTION VALUE="KY" >Kentucky</OPTION>
                  <OPTION VALUE="LA" >Louisiana</OPTION>
                  <OPTION VALUE="ME" >Maine</OPTION>
                  <OPTION VALUE="MD" >Maryland</OPTION>
                  <OPTION VALUE="MA" >Massachusetts</OPTION>
                  <OPTION VALUE="MI" >Michigan</OPTION>
                  <OPTION VALUE="MN" >Minnesota</OPTION>
                  <OPTION VALUE="MS" >Mississippi</OPTION>
                  <OPTION VALUE="MO" >Missouri</OPTION>
                  <OPTION VALUE="MT" >Montana</OPTION>
                  <OPTION VALUE="NE" >Nebraska</OPTION>
                  <OPTION VALUE="NV" >Nevada</OPTION>
                  <OPTION VALUE="NH" >New Hampshire</OPTION>
                  <OPTION VALUE="NJ" >New Jersey</OPTION>
                  <OPTION VALUE="NM" >New Mexico</OPTION>
                  <OPTION VALUE="NY" >New York</OPTION>
                  <OPTION VALUE="NC" >North Carolina</OPTION>
                  <OPTION VALUE="ND" >North Dakota</OPTION>
                  <OPTION VALUE="OH" >Ohio</OPTION>
                  <OPTION VALUE="OK" >Oklahoma</OPTION>
                  <OPTION VALUE="OR" >Oregon</OPTION>
                  <OPTION VALUE="PA" >Pennsylvania</OPTION>
                  <OPTION VALUE="RI" >Rhode Island</OPTION>
                  <OPTION VALUE="SC" >South Carolina</OPTION>
                  <OPTION VALUE="SD" >South Dakota</OPTION>
                  <OPTION VALUE="TN" >Tennessee</OPTION>
                  <OPTION VALUE="TX" >Texas</OPTION>
                  <OPTION VALUE="UT" >Utah</OPTION>
                  <OPTION VALUE="VT" >Vermont</OPTION>
                  <OPTION VALUE="VA" >Virginia</OPTION>
                  <OPTION VALUE="WA" >Washington</OPTION>
                  <OPTION VALUE="WV" >West Virginia</OPTION>
                  <OPTION VALUE="WI" >Wisconsin</OPTION>
                  <OPTION VALUE="WY" >Wyoming</OPTION>
                  <OPTION VALUE="">Other</OPTION>
                </SELECT> &nbsp; <input name="State2" type="text" size="10" value="<?php echo $_POST['State2'] ;?>" style="width:30px"></nobr>
                </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">Zip:&nbsp;</div></td>
              <td> <input name="Zip" type="text" size="35" value="<?php echo $_POST['Zip'] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">Country:&nbsp;</div></td>
              <td> <input name="Country" type="text" id="Country" value="<?php echo $_POST['Country'] ;?>"  size="35" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top"><div align="right">Phone:&nbsp;</div></td>
              <td> <input name="Phone" type="text" id="Phone" size="35" value="<?php echo $_POST['Phone'] ;?>" style="width:165px"> 
              </td>
            </tr>
            <tr> 
              <td valign="top">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr> 
              <?php if   ($act->Fields("uselist")) { ?>
              <td colspan="2" valign="top" class="form"><input type="checkbox" name="list1" checked value="<?php echo $act->Fields("list1"); ?>"> 
                <input type="hidden" name="list2" value="<?php echo $act->Fields("list2"); ?>"> 
                <input type="hidden" name="list3" value="<?php echo $act->Fields("list3"); ?>"> 
                <input type="hidden" name="list4" value="<?php echo $act->Fields("list4"); ?>">
                Keep me informed about this and other related campaigns</td>
            </tr>
            <?php } ?>
          </table>
        </center></td>
    </tr>
    <tr> 
      <td colspan="3" valign="top"><center>
          <input type="submit" class="submit" name="Submit" value="Send My Message!" onClick="return checkform();">
        </center></td>
    </tr>
  </table>
  <p>&nbsp; </p>

</form>
<?php
    }

}

if(!function_exists('mail_sanitize')) {
	function mail_sanitize($content) {
		if (eregi("\r",$content) || eregi("\n",$content)){
			trigger_error("Contact Us Spam at ".time()." :(".$content.")");
			die("Contact Us Spam at ".time()." :(".$content.")");
		}
		return $content;
	}
}
function  getactionemail($email) {
	global $dbcon;
	$getuser = $dbcon->Execute("select * from userdata where Email =".$dbcon->qstr( $email )." and modin=12 ") ;
	return $getuser;
}

function memebershipemail($email) {
	global $dbcon;
	mail($email,$subject,$message,$headers);
}

function getmember($email){
	global $dbcon;
	$getid = $dbcon->Execute("select id from userdata where Email =".$dbcon->qstr( $email )." and modin=12 ") ;
	$id = $getid->Fields("id");
	return $id;
}
function setckvalue($v) {
	if ($_POST[$v] != "") {
		$$v = $_POST[$v];
		return $$v;
	}
	else {
		$_REQUEST['kill_insert'] = $_REQUEST['kill_insert']." ".$v;
		//buildactionform($_POST[actionid],$error_msg);
		//$_REQUEST['kill_insert'] ="1";
	}
}

$captcha_valid = false;
$captcha_message = '';
if (isset($_POST['MM_insert'])&&$_POST['MM_insert']) {
    require_once( 'AMP/Form/Element/Captcha.inc.php');
    $captcha_demo = &new PhpCaptcha( array( ) );
    $captcha_valid = $captcha_demo->Validate( $_POST['captcha']);
    if ( !$captcha_valid ) {
        $_REQUEST['kill_insert'] = 'captcha';
        $captcha_message = AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED;
    }
}

if (isset($_REQUEST['MM_insert']) && $_REQUEST['MM_insert'] && $captcha_valid ) {
	$getuser = getactionemail($_POST[old_EmailAddress]);

    if ($getuser->Fields("id")) {
	    $First_Name = $getuser->Fields("First_Name");
        $Last_Name = $getuser->Fields("Last_Name");
        $Email = $getuser->Fields("Email");
        $City = $getuser->Fields("City");
        $Zip = $getuser->Fields("Zip");
        $Street = $getuser->Fields("Street");
        $Street_2 = $getuser->Fields("Street_2");
        $Country = $getuser->Fields("Country");
        $State = $getuser->Fields("State");
        $Phone = $getuser->Fields("Phone");
        $Title = $getuser->Fields("Title");
    }

#insert new  user into table
    else {
		$First_Name = setckvalue("First_Name");
		$Last_Name = setckvalue("Last_Name");
		$Email = setckvalue("Email");
		$City = setckvalue("City");
		$Zip = setckvalue("Zip");
		$Street = setckvalue("Street");
		$Street_2 = $_POST['Street_2'];
		$Country = $_POST['Country'];
		$Phone = setckvalue("Phone");
		$Title = $_POST['Title'];
		if (!$_POST['State'] && $_POST['State2']) { 
            $State = $_POST['State2'];
        } else {
            $State =  $_POST['State'];
        }
	}		
}

if ((isset($_REQUEST['MM_insert'])) && (!$_REQUEST['kill_insert'] ) ) {
	    $MM_editTable  = "userdata";
	    $modin =12;
		$MM_fieldsStr =  "First_Name|value|Last_Name|value|Email|value|City|value|State|value|Zip|value|Street|value|Street_2|value|Country|value|Phone|value|Title|value|modin|value";
	    $MM_columnsStr = "First_Name|',none,''|Last_Name|',none,''|Email|',none,''|City|',none,''|State|',none,''|Zip|',none,''|Street|',none,''|Street_2|',none,''|Country|',none,''|Phone|',none,''|Title|',none,''|modin|',none,''";
	    require ("Connections/insetstuff.php");
	    require ("Connections/dataactions.php");
			
		#send welcome message	
		#membershipemail($_POST[Email]);
#
######### EMAIL INSERT RECORD  ################################## 
# add conditional adds
#
# Adds up to four email addresses to the user account.

	if ($_POST['list1'] ) { e_addemail($Email, $_POST['list1']); }
	if ($_POST['list1'] && $_POST['list2'] ) { e_addemail($Email, $_POST['list2']); }
	if ($_POST['list1'] && $_POST['list3'] ) { e_addemail($Email, $_POST['list3']); }
	if ($_POST['list1'] && $_POST['list4'] ) { e_addemail($Email, $_POST['list4']); }

#################################################################
#
#insert into action history
	$memberid = getmember($Email);
	$actionid = $_POST['actionid'];
	$subject = $_POST['subject'];
	$text = $_POST['text'];
	$MM_editTable  = "action_history";
	$MM_fieldsStr =  "actionid|value|memberid|value|subjectText|value|Letter_Content|value|date|value";
	$MM_columnsStr = "actionid|',none,''|memberid|',none,''|subject|',none,''|text|',none,''|date|',none,now()";
	require ("Connections/insetstuff.php");
	require ("Connections/dataactions.php");

##### mail to target
# get send info

	$actiondetails = $dbcon->Execute("select * from action_text where id = ".$dbcon->qstr( $_POST['actionid']));
    if( $actiondetails ) {

# prepare email

	$finalemail = 
	"Dear "
    .$actiondetails->Fields("prefix")." "
    .$actiondetails->Fields("firstname")." "
    .$actiondetails->Fields("lastname")
    .",\n"
    . strip_tags( $_REQUEST['Letter_Content'] 
        . "\n"
        . $_REQUEST['Title']  . " "
        . $_REQUEST['First_Name'] . " "
        . $_REQUEST['Last_Name'] 
        . " \n"
        . $_REQUEST['City']  . " "
        . $_REQUEST['State'] . ' '
        . $_REQUEST['Country'] 
        . " \n"
        . $_REQUEST['Email'] 
        ." \n" );
 
#send emails	
    $emailemail =  $actiondetails->Fields("email");
	$emailheaders = mail_sanitize("From: " . $_REQUEST['First_Name'] . " " . $_REQUEST['Last_Name'] . " <". $_REQUEST['Email'] . ">")
                    . "\n"
                    .mail_sanitize("Reply-To: ".$_REQUEST['Email']);
	$temailheaders = mail_sanitize("From: " . AMP_SITE_EMAIL_SENDER );
	$faxemail = $actiondetails->Fields("fax");
	$faxheaders = mail_sanitize("From: ".$actiondetails->Fields("faxaccount"));
	if ($actiondetails->Fields("faxsubject") != 'subject') {	
	 	$faxsubject = $actiondetails->Fields("faxsubject");
	} else {
		$faxsubject = $_POST["subjectText"];
	}
     
	 	 
	if ($_POST['Send_Email'] && ($emailemail != "vsform")) {mail($emailemail,$_POST['subjectText'],$finalemail,$emailheaders);  }
    /*
	if ($_POST['Send_Email'] && ($emailemail == "vsform")) {
		require_once( 'Modules/vsLetter.inc.php' );
		$vsr = sendVSletter( $_POST['First_Name'], $_POST['Last_Name'], $_POST[Email], $finalemail );
		if ( $vsr ) {
        	print "Success!";
		} else {
        	print "Failure!";
		}
	}
    */
	
	if ($_POST["Send_Fax"]) {mail($faxemail,$faxsubject,$finalemail,$faxheaders); }
	if ($actiondetails->Fields("bcc")) {mail($actiondetails->Fields("bcc"),$_POST['subjectText'],$finalemail,$temailheaders); }
	mail($Email,"Thank you for Taking Action",$finalemail,$temailheaders); 

#go to tell a friend
    require_once( 'AMP/System/IntroText.inc.php');
    $page = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ), AMP_CONTENT_PUBLICPAGE_ID_SHARE_RESPONSE );
    $page_display = $page->getDisplay( );
    print( $page_display->execute( ));

	echo "<p class=title>".$actiondetails->Fields("thankyou_title")."</p>";
	echo "<p class=text>".converttext($actiondetails->Fields("thankyou_text"))."</p>";
	echo "<br><br><p class=title>Tell A Friend</p><br>";
	if  ($actiondetails->Fields("tellfriend")) {
		#tellfriend($_REQUEST['First_Name'],$_REQUEST['Last_Name'],$_REQUEST['Email'],$actiondetails->Fields("tf_subject"),$actiondetails->Fields("tf_text"));
        require_once( 'Modules/Share/Public/ComponentMap.inc.php' );
        $map = & new ComponentMap_Share_Public( );
        $controller = $map->get_controller( );
        $controller->_init_form( );
        $controller->_form->applyDefaults( );
        $controller->_form->setValues( array( 
                'sender_name' => $_REQUEST['First_Name'] . ' ' . $_REQUEST['Last_Name'],
                'sender_email' => $_REQUEST['Email'],
                'message' => $actiondetails->Fields( 'tf_text' ), 
                'subject' => $actiondetails->Fields(  'tf_subject') 
                ) );
        print( $controller->_form->execute( ) );
	}
 

    }
}
if ( !$actionid ) {
    require_once( 'Modules/WebAction/Public/ComponentMap.inc.php');
    $map = new ComponentMap_WebAction_Public( );
    $list = $map->getComponent( 'list');
    print $list->execute( );
}
if (isset($_POST['MM_insert']) && ($_REQUEST['kill_insert'] && $actionid ) ) {
	$error = "<p><b> <font color=red>Please fill the the following required fields: ".$_REQUEST['kill_insert']."</font></b></p>";
	buildactionform($actionid,$error);
}

if ((!isset( $_POST['MM_insert'])) && !isset($_REQUEST['kill_insert']) && $actionid ) {
	buildactionform($actionid);
}
	
include("AMP/BaseFooter.php");

?>
