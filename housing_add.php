<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  input page for housing into housing 
CSS:  form
GET VARS
MOD VARS: $confirm -(poster/admin)
					   $MM_email_housing
To Do:  	declare POST vars
				multip	le instances of modules
				add to contacts database
				required fields
				
*********************/ 
$modid = 3;
$intro_id = 6;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  


if (isset($_POST["MM_insert"])){
   $MM_editTable  = "housing";
   $MM_fieldsStr = "firstname|value|lastname|value|email|value|phone|value|avalible|value|beds|value|floor|value|tents|value|access|value|location|value|transport|value|parking|value|cooking|value|children|value|smoking|value|info|value|publish|value|board|value";
   $MM_columnsStr = "firstname|',none,''|lastname|',none,''|email|',none,''|phone|',none,''|avalible|',none,''|beds|',none,''|floor|',none,''|tents|',none,''|access|',none,''|location|',none,''|transport|',none,''|parking|',none,''|cooking|',none,''|children|',none,''|smoking|',none,''|info|',none,''|publish|',none,''|board|none,none,none";
   
//Mail to admin for confirmation
 	if ($confirm == "admin") {
  		$MM_editRedirectUrl = "housing_confirm.php?step=admin"; 
  		$messagetext = $_POST["firstname"]. ' ' .$_POST["lastname"]. " has added a posting to the ride board\n Information:$firstname, $Lastname,  $email, $phone,  $avalible, $beds, $floor,  $tents,  $access,  $location, $transport, $parking, $cooking, $children, $smoking, $info \n\nPlease visit ".$Web_url."housing_confirm.php?email=".$_POST["email"]." to publish";
   		if ($MM_email_housing) {
			mail ( $MM_email_housing, "new housing board posting", "$messagetext", "From: $MM_email_from\nX-Mailer: My PHP Script\n");  
		}
	}

//Mail to poster for confirmation
	if ($confirm == "poster") { 
		$MM_editRedirectUrl = "housing_confirm.php?step=email";
		$messagetext2 = "\nPlease visit ".$Web_url."housing_confirm.php?email=".$_POST["email"]." to confirm your housing board posting./n/n Information:$firstname, $Lastname/n  $email/n $phone/n  $avalible/n $beds/n $floor/n  $tents/n  $access/n  $location/n $transport/n $parking/n $cooking/n $children/n $smoking/n $info ";
	   if ($_POST["email"]) {
		   mail ( $_POST["email"], "confirm your housing board posting", "$messagetext2", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
		}
	}
	require ("DBConnections/insetstuff.php");
    require ("DBConnections/dataactions.php");
}

?>
      <table width="100%" border="0" cellspacing="0" cellpadding="15">
        <tr> 
          <td> 

            <form method="POST" action="<?php echo $MM_editAction?>" name="form1">
              
        <table border=0 cellpadding=2 cellspacing=0 align="center" width="500">
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">First Name:</td>
            <td> <input type="text" name="firstname" value="" size="30">
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Last Name</td>
            <td><input type="text" name="lastname" value="" size="30"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">E-mail:</td>
            <td><input type="text" name="email" value="" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Phone:</td>
            <td> <input type="text" name="phone" value="" size="32"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">When Available:</td>
            <td> <input type="text" name="avalible" value="" size="50"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Number of Beds/Sofas:</td>
            <td> <input type="text" name="beds" value="" size="50"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Floor Space:</td>
            <td> <input type="text" name="floor" value="" size="50"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Tents in Yard:</td>
            <td> <input type="text" name="tents" value="" size="50"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Handicapped accessibility: </td>
            <td> <input type="text" name="access" value="" size="50"> </td>
          </tr>
        </table>
              <input type="hidden" name="MM_insert" value="true">
              <table border=0 cellpadding=2 cellspacing=0 align="center" width="500" class="form">
                <tr> 
                  <td nowrap align="right" valign="top"> 
                    <div align="left">Location (do not post address):<br>
                      <textarea name="location" cols="30" rows="3" wrap="VIRTUAL"></textarea>
                      <br>
                    </div>
                  </td>
                  <td valign="baseline">Access to Public Transportation <br>
                    <textarea name="transport" cols="30" rows="3" wrap="VIRTUAL"></textarea>
                  </td>
                </tr>
                <tr> 
                  <td nowrap align="right" valign="top"> 
                    <div align="left">Parking: <br>
                      <textarea name="parking" cols="30" rows="3" wrap="VIRTUAL"></textarea>
                    </div>
                  </td>
                  <td valign="baseline">Cooking: <br>
                    <textarea name="cooking" cols="30" rows="3" wrap="VIRTUAL"></textarea>
                  </td>
                </tr>
                <tr> 
                  <td nowrap align="right" valign="top"> 
                    <div align="left">Can you accommodate children?<br>
                      <textarea name="children" cols="30" rows="3" wrap="VIRTUAL"></textarea>
                    </div>
                  </td>
                  <td valign="baseline">Smoking: <br>
                    <textarea name="smoking" cols="30" rows="3" wrap="VIRTUAL"></textarea>
                  </td>
                </tr>
                <tr> 
                  <td nowrap align="right" valign="top" colspan="2"> 
                    <div align="left">Other Information: <br>
                      <textarea name="info" cols="50" rows="5" wrap="VIRTUAL"></textarea>
                    </div>
                  </td>
                </tr>
                <tr valign="baseline"> 
                  <td nowrap align="right" colspan="2"> 
                    <div align="center"> 
					<input type="hidden" name="publish" value="0">
					 	 <input type="hidden" name="board" value="2">
                      <input type="submit" value="Submit" name="submit">
                    </div>
                  </td>
                </tr>
              </table>
              <p>&nbsp;</p>
            </form>
            <p>&nbsp;</p>
                      </td>
        </tr>
      </table>
<?php include("AMP/BaseFooter.php");  ?>
