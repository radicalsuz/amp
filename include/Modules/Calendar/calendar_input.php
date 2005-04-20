<?php 
/*********************
05-06-2003  v3.01
Module:  Calendar
Description:  inserts calendar data into calendar, and contacts sends confirmation email
CSS: text, form
To Do:  declare post vars, verify the required fields

*********************/ 

$mod_id = 15;
$modid=1;
require_once("AMP/BaseDB.php"); 
require_once("AMP/BaseTemplate.php"); 
require_once("AMP/BaseModuleIntro.php"); 
require_once("dropdown.php"); ?>

<?php
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

   // $MM_editConnection = MM__STRING;
   $startdate = DateConvertIn($_REQUEST['startdate']);
   $MM_editTable  = "calendar";
    if ($nonstateregion !=1) {$region = $_POST[lstate];} 
   $MM_fieldsStr = "type|value|startdate|value|time|value|event|value|description|value|longdescription|value|organization|value|contact|value|email|value|phone1|value|url|value|location|value|city|value|lstate|value|lcountry|value|laddress|value|lzip|value|fname2|value|lname2|value|organization2|value|address2|value|city2|value|state2|value|zip2|value|country2|value|email2|value|phone2|value|endorse|value|publish|value|repeat|value|student|value|region|value";
   $MM_columnsStr = "typeid|none,none,NULL|date|',none,NULL|time|',none,''|event|',none,''|shortdesc|',none,''|fulldesc|',none,''|org|',none,''|contact1|',none,''|email1|',none,''|phone1|',none,''|url|',none,''|location|',none,''|lcity|',none,''|lstate|',none,''|lcountry|',none,''|laddress|',none,''|lzip|',none,''|fname2|',none,''|lname2|',none,''|orgaznization2|',none,''|address2|',none,''|city2|',none,''|state2|',none,''|zip2|',none,''|country2|',none,''|email2|',none,''|phone2|',none,''|endorse|',none,''|publish|none,none,NULL|repeat|none,1,0|student|none,1,0|region|',none,''";
 
    
 require ("Connections/insetstuff.php"); 
require ("Connections/dataactions.php");
$recent=$dbcon->Execute("SELECT id FROM calendar order by id desc LIMIT 1") or DIE($dbcon->ErrorMsg());
 $idval= $recent->Fields("id");
 $recent->Close();

 $mailtext = "Event Name: $event \n Date: $startdate \n Time: $time \n Description: $description \n Long Description: $longdescription \n Organization: $organization \n Contact: $contact \n Email: $email \n Phone: $phone1 \n Website: $url \n Location: $location \n City: $city \n State Code: $lstate \n Country: $lcountry \n Address: $laddress \n Zip: $lzip \n Student Event: $studnet \n Endorse: $endorse \n Repeating Event: $repeat \n     \nPlease visit ";
 $mailtext .= $Web_url."system/calendar_gxedit.php?id=$idval to edit";
   mail ( "$MM_email_calendar", "new calendar posting", "$mailtext", "From: $MM_email_from\nX-Mailer: My PHP Script\n"); 

	$recent->Close();
    if ( ($fname2==($null)) && ($lname2==($null)) && ($organization2==($null)))
	{ header ("Location: calendar_input.php?thank=1");	}
	else {
	 $MM_editTable  = "contacts2";
   $MM_editRedirectUrl = "calendar_input.php?thank=1";
  if ($student == 1) {$classid = 8; }
   $MM_fieldsStr = "fname2|value|lname2|value|organization2|value|address2|value|city2|value|state2|value|zip2|value|country2|value|email2|value|phone2|value|source|value|enteredby|value|classid|value";
 
   $MM_columnsStr = "FirstName|',none,''|LastName|',none,''|Company|',none,''|BusinessStreet|',none,''|BusinessCity|',none,''|BusinessState|',none,''|BusinessPostalCode|',none,''|BusinessCountry|',none,''|EmailAddress|',none,''|BusinessPhone|',none,''|source|none,none,NULL|enteredby|none,none,NULL|classid|none,none,NULL";
   require ("Connections/insetstuff.php"); 
require ("Connections/dataactions.php"); }
  
  }


   $state=$dbcon->CacheExecute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
   $eventtype=$dbcon->CacheExecute("SELECT * FROM eventtype order by name asc") or DIE($dbcon->ErrorMsg());
   $eventtype_numRows=0;
   $eventtype__totalRows=$eventtype->RecordCount();
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>


 <?php if ($HTTP_GET_VARS["thank"] == ($null)) { ?>


      <form action="<?php echo $MM_editAction?>" method="POST" name="form" onSubmit="MM_validateForm('event','','R','startdate','','R','contact','','R','email','','R','city','','R','description','','R');return document.MM_returnValue" >
        
  <table width="100%" align="center" class="form">
    <tr> 
      <td class="form">Event Name*</td>
      <td> <input type="text" name="event" size="40"> </td>
    </tr>
    <tr> 
      <td class="form">Type</font></td>
      <td class="test"> <select NAME="type" id="type">
          <option value="0">Select Event Type</option>
          <?php    if ($eventtype__totalRows > 0){
    $eventtype__index=0;
    $eventtype->MoveFirst();
    WHILE ($eventtype__index < $eventtype__totalRows){
?>
          <OPTION VALUE="<?php echo  $eventtype->Fields("id")?>"> 
          <?php echo  $eventtype->Fields("name");?> </OPTION>
          <?php
      $eventtype->MoveNext();
      $eventtype__index++;
    }
    $eventtype__index=0;  
    $eventtype->MoveFirst();
  } ?>
      </select> &nbsp;&nbsp;<?php if ($studenton == 1){?><br> <input name="student" type="checkbox" id="student" value="1">&nbsp;&nbsp;Student Event<?php } ?>
		</td>
    </tr>
    <tr> 
      <td class="form">Event Date*</font></td>
      <td class="text"> <input name="startdate" type="text" value="00-00-0000" size="13">
        Format must be month-day-year (12-30-2002) </td>
    </tr>
    <tr> 
      <td class="form">Event Time</td>
      <td><input type="text" name="time" size="20"></td>
    </tr>
    <tr> 
      <td class="form">Weekly or Repeating Event</td>
      <td><input name="repeat" type="checkbox" id="repeat" value="1"></td>
    </tr>
    <tr> 
      <td class="form">Event Cost</td>
      <td><input name="cost" type="text" id="cost" size="20"></td>
    </tr>
    <tr> 
      <td class="form">Web Site</td>
      <td><input name="url" type="text" value="http://" size="35"></td>
    </tr>
    <tr> 
      <td colspan="2" class="form"> <br>
        Brief Description of the event *<br> <textarea name="description" rows="4" cols="48" wrap="VIRTUAL"></textarea> 
      </td>
    </tr>
    <tr> 
      <td colspan="2" class="form"> <br>
        Full Description of the event (optional)<br> <textarea name="longdescription" rows="10" cols="48" wrap="VIRTUAL"></textarea> 
      </td>
    </tr>
    <tr> 
      <td colspan="2" class="form">Endorsing Organizations (If any):<br> <textarea name="organization" cols="48" rows="3" wrap="VIRTUAL"></textarea> 
        <br> </td>
    </tr>
    <tr> 
      <td colspan="2"><table width="100%" border="0">
          <tr> 
            <td valign="top"><table width="100%" border="0">
                <tr> 
                  <td colspan="2"><strong> Public Contact Information</strong></td>
                </tr>
                <tr> 
                  <td class="form">Contact</font> Name:*</td>
                  <td><input name="contact" type="text" id="contact" size="40"></td>
                </tr>
                <tr> 
                  <td class="form">Contact Email: *</td>
                  <td><input type="text" name="email" id="email" size="40"></td>
                </tr>
                <tr> 
                  <td class="form">Contact Phone:</td>
                  <td><input type="text" name="phone1" size="40"></td>
                </tr>
              </table>
              <table width="100%" border="0">
                <tr> 
                  <td colspan="2" ><strong><br>
                    Event Location</strong></td>
                </tr>
                <tr> 
                  <td class="form">Event Location</td>
                  <td><input name="location" type="text" id="location" size="40"></td>
                </tr>
                <tr> 
                  <td class="form">Event City:*</td>
                  <td><input name="city" type="text" id="city" size="40"></td>
                </tr>
                <tr> 
                  <td class="form">Event State*</td>
                  <td><select NAME="lstate" id="lstate">
                      <option>Select State</option>
                      <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
                      <OPTION VALUE="<?php echo  $state->Fields("id")?>"> 
                      <?php echo  $state->Fields("statename");?> </OPTION>
                      <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
                    </select></td>
                </tr>
				<tr> 
       <?php if ($nonstateregion ==1) {
	   ?>           <td class="form">Region</td>
                  <td><select NAME="region" id="region">
                      <option>Select Region</option>
                      <?php  
					  $regionsel=$dbcon->CacheExecute("SELECT * FROM region order by title asc") or DIE($dbcon->ErrorMsg());
					    while (!$regionsel->EOF)   {
?>
                      <OPTION VALUE="<?php echo  $regionsel->Fields("id")?>"> 
				
                      <?php echo  $regionsel->Fields("title");?> </OPTION>
                      <?php
  $regionsel->MoveNext();
} ?>
                    </select></td>
                </tr><?php } ?>
                <tr> 
                  <td class="form">Event Country:*</td>
                  <td><select NAME="lcountry" id="lcountry">
                      <?php echo $countryDropDown; ?> </select></td>
                </tr>
                <tr> 
                  <td class="form">Event Street Address:</td>
                  <td><input name="laddress" type="text" id="laddress" size="40"></td>
                </tr>
                <tr> 
                  <td class="form">Event Zip</td>
                  <td><input name="lzip" type="text" id="lzip" size="40"></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2"><br>
        The following information is for our staff and will not be listed on the 
        website, unless it is the same as the information above. </td>
    </tr>
    <tr> 
      <td class="form">First Name:</td>
      <td><input name="fname2" type="text" id="fname2" size="35"></td>
    </tr>
    <tr> 
      <td class="form">Last Name:</td>
      <td><input name="lname2" type="text" id="lname2" size="35"></td>
    </tr>
    <tr> 
      <td class="form">Organization (if any):</td>
      <td><input name="organization2" type="text" id="organization2" size="35"></td>
    </tr>
    <tr> 
      <td class="form">Address</td>
      <td><input name="address2" type="text" id="address2" size="35"></td>
    </tr>
    <tr> 
      <td class="form"> <div align="left">City</div></td>
      <td><div align="left"> 
          <input name="city2" type="text" id="city2" size="35">
        </div></td>
    </tr>
    <tr> 
      <td class="form"><div align="left">State</div></td>
      <td><div align="left"> 
          <select NAME="state2" id="state2">
            <option>Select State</option>
            <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
            <OPTION VALUE="<?php echo  $state->Fields("state")?>"> 
            <?php echo  $state->Fields("statename");?> </OPTION>
            <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
          </select>
        </div></td>
    </tr>
    <tr> 
      <td class="form"><div align="left">Zip</div></td>
      <td><div align="left"> 
          <input name="zip2" type="text" id="zip2" size="15">
        </div></td>
    </tr>
    <tr> 
      <td class="form"><div align="left">Country</div></td>
      <td><div align="left"> 
          <select name="country2" id="select">
            <?php echo $countryDropDown; ?> 
          </select>
        </div></td>
    </tr>
    <tr> 
      <td class="form"><div align="left">Phone</div></td>
      <td><div align="left"> 
          <input name="phone2" type="text" id="phone2" size="35">
        </div></td>
    </tr>
    <tr> 
      <td class="form"><div align="left">E-mail</div></td>
      <td><div align="left"> 
          <input name="email2" type="text" id="email2" size="35">
        </div></td>
    </tr>
   
    <tr> 
      <td align="center" colspan="2"><br> <input name="enteredby" type="hidden" id="enteredby" value="2"> 
        <input name="publish" type="hidden" id="publish" value="0"> <input name="source" type="hidden" id="source" value="7"> 
        <input name="classid" type="hidden" id="classid" value="7"> <input name="submit" type="submit" value="Submit your Event"></td>
    </tr>
  </table>
        <input type="hidden" name="MM_insert" value="true">
		 <input type="hidden" name="publish" value="0">
		 	 <input type="hidden" name="board" value="2">
      </form>
      <?php
	  
	   }     //end if not thank you ?>
	  
	  <?php if ($HTTP_GET_VARS["thank"] == ("1")) { ?>
      <?php 
	  $mod_id = 51 ;
	  require_once("AMP/BaseModuleIntro.php"); ?>
      <?php } //end thank you
  $state->Close();
  $eventtype->Close();
?>
<?php require_once("AMP/BaseFooter.php"); ?>
