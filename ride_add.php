<?php 
$modid = 2;
$mod_id = 5;
include("sysfiles.php");
include("header.php"); 
include("dropdown.php"); 

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
?><?php
// *** Insert Record: set Variables

if (isset($MM_insert)){

   $MM_editTable  = "ride";
   $MM_fieldsStr = "firstname|value|lastname|value|phone|value|email|value|depatingfrom|value|depaturedate|value|returningto|value|returndate|value|numpeople|value|ride|value|commets|value|publish|value|board|value";
   $MM_columnsStr = "firstname|',none,''|lastname|',none,''|phone|',none,''|email|',none,''|depatingfrom|',none,''|depaturedate|',none,''|returningto|',none,''|returndate|',none,''|numpeople|',none,''|need|',none,''|commets|',none,''|publish|',none,''|board|none,none,none";
  //Mail to admin for confirmation
 if ($confirm == "admin")   
	{ $MM_editRedirectUrl = "ride_confirm.php?step=admin";
	$messagetext = "$firstname $lastname has added a posting to the ride board\n Information:$phone, $email, $departingform, $depaturedate, $returningto, $retundate, $numpeople, $ride, $comments \n\nPlease visit ".$Web_url."ride_confirm.php?email=$email to publish";
  mail ( "$MM_email_ride", "new ride board posting", "$messagetext", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); }
  
//Mail to poster for confirmation
 if ($confirm == "poster")
{$MM_editRedirectUrl = "ride_confirm.php?step=email";
$messagetext2 = "\nPlease visit ".$Web_url."ride_confirm.php?email=$email to confirm your ride board posting./n/n Information: $phone, $email, $departingform, $depaturedate, $returningto, $retundate, $numpeople, $ride, $comments ";
 mail ( "$email", "confirm your ride board posting", "$messagetext2", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); }

     require ("Connections/insetstuff.php"); 
require ("Connections/dataactions.php"); } 

?>
     <form name="rieinput" action="<?php echo $MM_editAction?>" method="POST"> 
  <table width="100%" border="0" cellspacing="0">
    <tr> 
      <td align="right" class="form"><div align="left">First Name</div></td>
      <td> <input name="firstname" type="text" size="35" > </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Last Name</div></td>
      <td><input name="lastname" type="text" size="35"></td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Phone Number</div></td>
      <td> <input type="text" name="phone" size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">E-mail</div></td>
      <td> <input type="text" name="email" size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Location Depating From</div></td>
      <td> <input type="text" name="depatingfrom" size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Depature Date</div></td>
      <td> <input type="text" name="depaturedate"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Location Returning To</div></td>
      <td> <input type="text" name="returningto" size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Return Date</div></td>
      <td> <input type="text" name="returndate"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Number of People</div></td>
      <td> <input type="text" name="numpeople" size="15"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Need a Ride</div></td>
      <td> <input type="radio" name="ride" value="need"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Have a Ride to Offer</div></td>
      <td> <input type="radio" name="ride" value="have"> </td>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left">Commets<br>
        </div></td>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left">
          <textarea name="commets" cols="40" wrap="VIRTUAL" rows="4"></textarea>
        </div></td>
    </tr>
  </table>
              <input type="hidden" name="publish" value="0">
              <input type="submit" name="Submit" value="Save">
			   	 <input type="hidden" name="board" value="2">
              <input type="hidden" name="MM_insert" value="true">
            </form>
            
        
 <?php include("footer.php"); ?>     