<?php  include_once  "Connections/jpcache-sql.php"; 


  require_once("adodb/adodb.inc.php");
  require_once("Connections/freedomrising.php");
  
//get instance of the custom input module and define $mod_id	 
	 $modin = $HTTP_GET_VARS[modin];
	$customfields=$dbcon->Execute("SELECT * FROM modfields WHERE id = $modin") or DIE($dbcon->ErrorMsg());
	$mod_id=10;
	//define vars used in the email
		$label1 = $customfields->Fields("field1text");
		$label2 = $customfields->Fields("field2text");
		$label3 = $customfields->Fields("field3text");
		$label4 = $customfields->Fields("field4text");
		$label5 = $customfields->Fields("field5text");
		$label6 = $customfields->Fields("field6text");
		$label7 = $customfields->Fields("field7text");
		$label8 = $customfields->Fields("field8text");
		$label9 = $customfields->Fields("field9text");
		$label10 = $customfields->Fields("field10text");
		$mailto = $customfields->Fields("mailto");
		$subject = $customfields->Fields("subject");
		if ($thank == 1){
		$mod_id = $customfields->Fields("modidresponse");}
		else {
  		$mod_id = $customfields->Fields("modidinput");}		

 function customfields ($fieldtext,$fieldname,$fielddata,$pub) {  
 global $customfields;
 	    if ($customfields->Fields("$pub") == (1)) {  //start field 
		  if ($customfields->Fields("$fielddata") != (0)){ 
		  	echo  "<tr> <td align=\"right\" class=\"form\">";
          	echo $customfields->Fields("$fieldtext"); 
          	echo "</td> <td colspan=2>";	  	
					  }
		  else{
		 	echo  "<tr> <td  class=\"form\" colspan=2>";
          	echo $customfields->Fields("$fieldtext"); 
          	echo "&nbsp;&nbsp;&nbsp;";}
		if ($customfields->Fields("$fielddata") == ("1")){ 
            echo " <input type=\"text\" name=\"";
			echo $fieldname;
			echo "\" size=\"40\">"; }
		if ($customfields->Fields("$fielddata") == ("2")){ 
            echo " <input type=\"checkbox\" name=\"";
			echo $fieldname;
			echo "\" value = 1>"; }
		if ($customfields->Fields("$fielddata") == ("3")){ 
            echo " <textarea name=\"";
			echo $fieldname;
			echo "\" wrap=\"VIRTUAL\" cols=\"38\" rows=\"5\"></textarea>"; }
			echo "</td> </tr>"; } 
	} 

//build template		
  require_once("Connections/modhierarchy.php");  
  require_once("Connections/templateassign.php");
  include("header.php"); 
  
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
  
   $MM_editTable  = "moduserdata";
   $MM_fieldsStr = "Organization|value|FirstName|value|LastName|value|EmailAddress|value|Phone|value|Fax|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|notes|value|field1|value|field2|value|field3|value|field4|value|field5|value|field6|value|field7|value|field8|value|field9|value|field10|value|modin|value";
   $MM_columnsStr = "Organization|',none,''|FirstName|',none,''|LastName|',none,''|EmailAddress|',none,''|Phone|',none,''|Fax|',none,''|WebPage|',none,''|Address|',none,''|Address2|',none,''|City|',none,''|State|',none,''|PostalCode|',none,''|Country|',none,''|notes|',none,''|field1|',none,''|field2|',none,''|field3|',none,''|field4|',none,''|field5|',none,''|field6|',none,''|field7|',none,''|field8|',none,''|field9|',none,''|field10|',none,''|modinid|none,none,NULL";

//set up notifaction e-mail

if (($customfields->Fields("useemail")) == (1)){
$recent=$dbcon->Execute("SELECT id FROM moduserdata where modinid=$modin order by id desc LIMIT 1") or DIE($dbcon->ErrorMsg());
 $idval= $recent->Fields("id");
mail ( "$mailto", "$subject", 
		"Name: $firstname $lastname \n
		Organization: $Organization  \n
		FirstName: $FirstName \n
		LastName: $LastName \n
		EmailAddress: $EmailAddress \n
		Phone: $Phone \n
		Fax: $Fax \n
		WebSite: $WebSite \n
		Address: $Address \n
		Address2: $Address2\n
		City: $City \n
		State: $State \n
		PostalCode: $PostalCode \n
		Country: $Country \n
		notes: $notes \n
		$label1 : $field1 \n
		$label2 : $field2 \n
		$label3 : $field3 \n
		$label4 : $field4 \n
		$label5 : $field5 \n
		$label6 : $field6 \n
		$label7 : $field7 \n
		$label8 : $field8 \n
		$label9 : $field9 \n
		$label10 : $field10 \n
		\nPlease visit ".$Web_url."system/moddata.php?modin=$modin&id=$idval to publish", 
		"From: $MM_email_from\nX-Mailer: My PHP Script\n"); 
		$recent->close();}
  require ("Connections/insetstuff.php");
   require ("Connections/dataactions.php");
    
	//insert into contacts database
	 $MM_editTable  = "contacts2";
	 $MM_editRedirectUrl = $customfields->Fields("redirect");
	 $source= $customfields->Fields("sourceid");
	 $enteredby= $customfields->Fields("enteredby");
	 $MM_fieldsStr = "Organization|value|FirstName|value|LastName|value|EmailAddress|value|Phone|value|Fax|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|notes|value|source|value|enteredby|value";
	 $MM_columnsStr = "Company|',none,''|FirstName|',none,''|LastName|',none,''|EmailAddress|',none,''|BusinessPhone|',none,''|BusinessFax|',none,''|WebPage|',none,''|BusinessStreet|',none,''|BusinessStreet2|',none,''|BusinessCity|',none,''|BusinessState|',none,''|BusinessPostalCode|',none,''|BusinessCountry|',none,''|notes|',none,''|source|none,none,NULL|enteredby|none,none,NULL";
	  require ("Connections/insetstuff.php");
 require ("Connections/dataactions.php");


   }
      $state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();

      $region=$dbcon->Execute("SELECT id, title FROM region ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $region_numRows=0;
   $region__totalRows=$region->RecordCount();
?>  
				  <?php if ($HTTP_GET_VARS["thank"] == ($null)) { ?>

       
      <form name="Form1" action="<?php echo $MM_editAction?>" method="POST">
        
  <table border="0" width="100%" class="form">
    
    <tr> 
      <td align="right" class="form"><div align="left">First Name </div></td>
      <td> <input type="text" name="FirstName" size="35">
      </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Last Name</div></td>
      <td><input type="text" name="LastName" size="35"></td>
    </tr>
	<tr> 
      <td align="right" class="form"><div align="left">Organization</div></td>
      <td> <input type="text" name="Organization" size="50"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">E-mail</div></td>
      <td> <input type="text" name="EmailAddress" size="45"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Phone Number</div></td>
      <td> <input type="text" name="Phone" size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Fax Number</div></td>
      <td> <input type="text" name="Fax" size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Web Page</div></td>
      <td> <input name="WebPage" type="text" value="http://"  size="50"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Mailing Address</div></td>
      <td> <input type="text" name="Address"  size="50"> </td>
    </tr>
    <tr> 
      <td align="right" class="form">&nbsp;</td>
      <td> <input type="text" name="Address2"  size="50"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">City</div></td>
      <td> <input type="text" name="City"  size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">State</div></td>
      <td class="form"> <?php echo statelist('State') ?>
        Zip 
        <input type="text" name="PostalCode" size="15"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Country</div></td>
      <td>          <select name="Country" id="select">
            <?php echo $countryDropDown2; ?> 
          </select></td>
    </tr>
   <!-- <tr> 
      <td align="right" class="form"><div align="left">Region</div></td>
      <td><select name="region">
          <?php
  if ($region__totalRows > 0){
    $region__index=0;
    $region->MoveFirst();
    WHILE ($region__index < $region__totalRows){
?>
          <OPTION VALUE="<?php echo  $region->Fields("id")?>"> 
          <?php echo  $region->Fields("title");?> </OPTION>
          <?php
      $region->MoveNext();
      $region__index++;
    }
    $region__index=0;  
    $region->MoveFirst();
  }
?>
        </select> </td>
    </tr> -->
    <?php  //load custom fields
  customfields ('field1text','field1','1ftype','1pub');
  customfields ('field2text','field2','2ftype','2pub');
  customfields ('field3text','field3','3ftype','3pub'); 
  customfields ('field4text','field4','4ftype','4pub');
  customfields ('field5text','field5','5ftype','5pub');
  customfields ('field6text','field6','6ftype','6pub');
  customfields ('field7text','field7','7ftype','7pub');
  customfields ('field8text','field8','8ftype','8pub');
  customfields ('field9text','field9','9ftype','9pub');
  customfields ('field10text','field10','10ftype','10pub');?>
    <tr> 
      <td align="right" class="form"><div align="left">Other Comments</div></td>
      <td><textarea name="notes" cols="38" rows="5" wrap="VIRTUAL"></textarea></td>
    </tr>
  </table>
        <p> 
          <input type="submit" name="Submit" value="Submit">
		  <input type="reset" name="Submit2" value="Reset">
          <input type="hidden" name="publish" value="0">
        </p>
        <input type="hidden" name="MM_insert" value="true">
      </form>
      <?php } //end if not thank you 
	  
	   if ($HTTP_GET_VARS["thank"] == ("1")) {
      	 // $mod_id = $customfields->Fields("modidresponse") ;
	 // include("module.inc.php"); 
      } //end thank you

 include("footer.php"); 
?>