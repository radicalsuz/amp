<?php 


  require_once("adodb/adodb.inc.php");
  require_once("Connections/freedomrising.php");
  include_once("dropdown.php");
  
//get instance of the custom input module and define $mod_id	 
	 //$modin = $HTTP_GET_VARS[modin];
	$customfields=$dbcon->CacheExecute("SELECT * FROM modfields WHERE id = $modin") or DIE($dbcon->ErrorMsg());
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
  if ($_GET[modin])
  {	$mod_id = $customfields->Fields("modidinput");
  }}
		
		
 //population the list information 
 if ($customfields->Fields("uselists") ) {
 $uselist=1;
 
  if ($customfields->Fields("list1") ) {
 $list1r=$dbcon->CacheExecute("SELECT name, id from lists where id =".$customfields->Fields("list1")." and  publish =1 ") ; 
 $list1name = $list1r->Fields("name");
  } 
   if ($customfields->Fields("list2") ) {
 $list2r=$dbcon->CacheExecute("SELECT name, id from lists where id =".$customfields->Fields("list2")." and  publish =1 ") ; 
  $list2name = $list2r->Fields("name");
 } 
   if ($customfields->Fields("list3") ) {
 $list3r=$dbcon->CacheExecute("SELECT name, id from lists where id =".$customfields->Fields("list3")." and  publish =1 ") ; 
  $list3name = $list3r->Fields("name");
 } 
 }//end list population 		

function customfields ($fieldtext,$fieldname,$fielddata,$pub) {  
 global $customfields;
 	    if ($customfields->Fields("$pub") == (1)) {  //start field 
		  if ($customfields->Fields("$fielddata") == (2)){ 
		   echo "<tr> <td colspan=2><table><tr><td valign=top>";
		   echo " <input type=\"checkbox\" name=\"";
			echo $fieldname;
			echo "\" value = 1>";
			echo "</td><td class=text>".$customfields->Fields("$fieldtext")."</td></tr></table></td></tr>";
			
		  }
		  elseif ($customfields->Fields("$fielddata") != (0)){ 
		  	echo  "<tr> <td class=text valign=top>";
          	echo $customfields->Fields("$fieldtext"); 
          	echo "</td> <td colspan=2>";	  	
					  }
		  else{
		 	echo  "<tr> <td  class=\"form\" colspan=2>";
          	echo $customfields->Fields("$fieldtext"); 
          	echo "&nbsp;&nbsp;&nbsp;";}
		if ($customfields->Fields("$fielddata") == ("1")){ 
            echo " <input type=\"text\" style=\"width: 262px;\" name=\"";
			echo $fieldname;
			echo "\" size=\"40\">"; }
		
		if ($customfields->Fields("$fielddata") == ("3")){ 
            echo " <textarea name=\"";
			echo $fieldname;
			echo "\" wrap=\"VIRTUAL\" cols=\"38\" rows=\"5\"  style=\"width: 262px;\"></textarea>"; }
			echo "</td> </tr>"; } 
	} 
//build template		
  require_once("Connections/modhierarchy.php");  
  require_once("Connections/templateassign.php");
  include_once("header.php"); 
  
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
		
		if($WebPage){
			$WebPage = eregi_replace( "http://", "", $WebPage ); 
			$WebPage = "http://" . $WebPage; 
		}

######### EMAIL INSERT RECORD  ################################## 
if (($_POST[list1] ) or ($_POST[list2] ) or ($_POST[list3])){
 

if (($MM_insert) && ($_POST[EmailAddress]) ){
 $emailck=$dbcon->Execute("SELECT id FROM email where email ='$_POST[EmailAddress]'") or DIE($dbcon->ErrorMsg());
if ( !$emailck->Fields("id") ) {
   $MM_editTable  = "email";
   $MM_fieldsStr = "EmailAddress|value|LastName|value|FirstName|value|Organization|value |html|value|Phone|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|Fax|value";
   $MM_columnsStr = "email|',none,''|lastname|',none,''|firstname|',none,''|organization|',none,''|html|none,1,0|phone|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|fax|',none,''";
  require ("Connections/insetstuff.php");
  require ("Connections/dataactions.php");
  
  
 $newrec=$dbcon->Execute("SELECT id FROM email ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$recid=$newrec->Fields("id");}
else {$recid=$emailck->Fields("id");}

	if ($_POST[list1]) {
$listid = $_POST[listid1]; 
$subck=$dbcon->Execute("SELECT id FROM  subscription where userid =$recid and listid=$listid");
if ( !$emailck->Fields("id")) {
 $MM_editTable  = "subscription";
  $MM_fieldsStr = "recid|value|listid|value";
   $MM_columnsStr = "userid|none,none,NULL|listid|none,none,NULL"; 
	require ("Connections/insetstuff.php");
    require ("Connections/dataactions.php");
	}}
	if ($_POST[list2]) {
$listid = $_POST[listid2]; 
$subck=$dbcon->Execute("SELECT id FROM  subscription where userid =$recid and listid=$listid");
if ( !$emailck->Fields("id")) {
 $MM_editTable  = "subscription";
  $MM_fieldsStr = "recid|value|listid|value";
   $MM_columnsStr = "userid|none,none,NULL|listid|none,none,NULL"; 
	require ("Connections/insetstuff.php");
    require ("Connections/dataactions.php");
	}	}
	if ($_POST[list3]) {
$listid = $_POST[listid3]; 
$subck=$dbcon->Execute("SELECT id FROM  subscription where userid =$recid and listid=$listid");
if ( !$emailck->Fields("id")) {
 $MM_editTable  = "subscription";
  $MM_fieldsStr = "recid|value|listid|value";
   $MM_columnsStr = "userid|none,none,NULL|listid|none,none,NULL"; 
	require ("Connections/insetstuff.php");
    require ("Connections/dataactions.php");
	}}	

	} 
   }// end insert
   ########################################################################
		

   // $MM_editConnection = MM__STRING;
    if ($nonstateregion !=1) {$region = $_POST[State];} 
   $MM_editTable  = "moduserdata";
   $MM_fieldsStr = "Organization|value|FirstName|value|LastName|value|EmailAddress|value|Phone|value|Fax|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|notes|value|field1|value|field2|value|field3|value|field4|value|field5|value|field6|value|field7|value|field8|value|field9|value|field10|value|modin|value|field11|value|field12|value|field13|value|field14|value|field15|value|field16|value|field17|value|field18|value|field19|value|field20|value|region|value";
   $MM_columnsStr = "Organization|',none,''|FirstName|',none,''|LastName|',none,''|EmailAddress|',none,''|Phone|',none,''|Fax|',none,''|WebPage|',none,''|Address|',none,''|Address2|',none,''|City|',none,''|State|',none,''|PostalCode|',none,''|Country|',none,''|notes|',none,''|field1|',none,''|field2|',none,''|field3|',none,''|field4|',none,''|field5|',none,''|field6|',none,''|field7|',none,''|field8|',none,''|field9|',none,''|field10|',none,''|modinid|none,none,NULL|field11|',none,''|field12|',none,''|field13|',none,''|field14|',none,''|field15|',none,''|field16|',none,''|field17|',none,''|field18|',none,''|field19|',none,''|field20|',none,''|region|',none,''";

//set up notifaction e-mail

if (($customfields->Fields("useemail")) == (1)){
$recent=$dbcon->CacheExecute("SELECT id FROM moduserdata where modinid=$modin order by id desc LIMIT 1") or DIE($dbcon->ErrorMsg());
 $idval= $recent->Fields("id");
if (emailisvalid($mailto)) {mail ( "$mailto", "$subject", 
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
		$label11 : $field11 \n
		$label12 : $field12 \n
		$label13 : $field13 \n
		$label14 : $field14 \n
		$label15 : $field15 \n
		$label16 : $field16 \n
		$label17 : $field17 \n
		$label18 : $field18 \n
		$label19 : $field19 \n
		$label20 : $field20 \n
		\nPlease visit ".$Web_url."system/moddata.php?modin=$modin&id=$idval to publish", 
		"From: $MM_email_from\nX-Mailer: My PHP Script\n"); }
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
      $state=$dbcon->CacheExecute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();

      $region=$dbcon->CacheExecute("SELECT id, title FROM region ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $region_numRows=0;
   $region__totalRows=$region->RecordCount();
?>  
				  <?php if ($HTTP_GET_VARS["thank"] == ($null)) { ?>

       
      <form name="Form1" action="<?php echo $MM_editAction?>" method="POST">
        
  <table border="0" width="100%" class="form">
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left">Personal Information:</div></td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">First Name&nbsp;&nbsp; 
        </div></td>
      <td> <input type="text" name="FirstName" size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Last Name&nbsp;&nbsp;</div></td>
      <td><input type="text" name="LastName"  size="20" style="width: 262px;"></td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Organization&nbsp;&nbsp;</div></td>
      <td> <input type="text" name="Organization"  size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Title/Position&nbsp;&nbsp;</div></td>
      <td> <input type="text" name="position"  size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left"><br>
          Contact Information:</div></td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">E-mail&nbsp;&nbsp;</div></td>
      <td> <input type="text" name="EmailAddress" size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Mailing Address </div></td>
      <td> <input type="text" name="Address"  size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="text">&nbsp;</td>
      <td> <input type="text" name="Address2"  size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="text"> <div align="right">City&nbsp;&nbsp;</div></td>
      <td> <input type="text" name="City"  size="20" style="width: 262px;"> </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">State&nbsp;&nbsp;</div></td>
      <td class="form"> <?php echo statelist('State') ?> Zip 
        <input type="text" name="PostalCode" size="15"> </td>
    </tr>
    <?php if ($nonstateregion ==1) {
	   ?>
    <tr> 
      <td class="text"><div align="right">Region&nbsp;&nbsp;</div></td>
      <td><select NAME="region" id="region">
          <option value=''>Select Region</option>
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
    </tr>
    <?php } ?>
    <tr> 
      <td align="right" class="text"><div align="right">Country&nbsp;&nbsp;</div></td>
      <td> <select name="Country" id="select">
          <?php echo $countryDropDown2; ?> </select> </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Phone Number&nbsp;&nbsp;</div></td>
      <td> <input type="text" name="Phone" size="20" style="width: 262px;"> </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Fax Number&nbsp;&nbsp;</div></td>
      <td> <input type="text" name="Fax" size="20" style="width: 262px;"> </td>
    </tr>
    <tr> 
      <td align="right" class="text"><div align="right">Web Page&nbsp;&nbsp;</div></td>
      <td> <input name="WebPage" type="text" value=""  size="20" style="width: 262px;"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="text"></td>
      <td>&nbsp; </td>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left"><br>
          Other Information:</div></td>
    </tr>
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
  customfields ('field10text','field10','10ftype','10pub');
  customfields ('field11text','field11','11ftype','11pub');
  customfields ('field12text','field12','12ftype','12pub');
  customfields ('field13text','field13','13ftype','13pub');
  customfields ('field14text','field14','14ftype','14pub');
  customfields ('field15text','field15','15ftype','15pub');
  customfields ('field16text','field16','16ftype','16pub');
  customfields ('field17text','field17','17ftype','17pub');
  customfields ('field18text','field18','18ftype','18pub');
  customfields ('field19text','field19','19ftype','19pub');
  customfields ('field20text','field20','20ftype','20pub');?>
    <tr> 
      <td align="right" class="text"><div align="left"> Comments</div></td>
      <td><textarea name="notes" cols="38" rows="5" wrap="VIRTUAL" style="width: 262px;"></textarea></td>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="text"> <?php if ($uselist ) {?>     
  <br>
        <br>
        <table cellpadding=0 cellspacing=2 border=0 align=center class="form"l>
    <tr> 
      <td colspan="2" valign=top class="form"><center>
          Can we send you occasional updates from the following list(s)? <br>
        </center></td>
    </tr>
    <?php if  ($customfields->Fields("list1")) {?>
    <tr> 
      <td valign=top><div align="right"><small><?php echo $list1r->Fields("name") ?></small></div></td>
      <td valign=top><small>&nbsp; 
        <input type=checkbox name="list1" value="1" checked>
        <input name="listid1" type="hidden" value="<?php echo $list1r->Fields("id") ?>">
        </small></td>
    </tr>
    <?php }?>
    <?php if  ($customfields->Fields("list2") ) {?>
    <tr> 
      <td valign=top><div align="right"><small><?php echo $list2r->Fields("name") ?></small></div></td>
      <td valign=top><small>&nbsp; 
        <input name="list2" type=checkbox value="1" checked>
        <input name="listid2" type="hidden" value="<?php echo $list2r->Fields("id") ?>">
        </small></td>
    </tr>
    <?php }?>
    <?php if  ($customfields->Fields("list3") ) {?>
    <tr> 
      <td valign=top><div align="right"><small><?php echo $list3r->Fields("name") ?></small></div></td>
      <td valign=top><small>&nbsp; 
        <input name="list3" type=checkbox value="1" checked>
        <input name="listid3" type="hidden" value="<?php echo $list3r->Fields("id") ?>">
        </small></td>
    </tr>
    
    <?php }?><tr> 
      <td colspan="2" valign=top><center>
          Read our <a href="pp.php" target="_blank">Privacy Policy</a></center></td>
    </tr>
  </table> 

 <?php }?></td>
    </tr>
  </table>
     
  <p> 
    <input type="submit" name="Submit" value="Submit">
    <input type="hidden" name="publish" value="0">
  </p>
        <input type="hidden" name="MM_insert" value="true">
      </form>
      <?php } //end if not thank you 
	  
	   if ($HTTP_GET_VARS["thank"] == ("1")) {
      	 // $mod_id = $customfields->Fields("modidresponse") ;
	 // include("module.inc.php"); 
      } //end thank you

 include_once("footer.php"); 
?>