<?php  
if (!defined( 'AMP_FORM_ID_EMAIL' )) define( 'AMP_FORM_ID_EMAIL', 3 );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Page/Urls.inc.php' );
ampredirect( AMP_URL_AddVars( AMP_CONTENT_URL_FORM, "modin=".AMP_FORM_ID_EMAIL ));
/*

$modid=9;
$source = 11;
$enteredby =2;
ob_start();
if (isset($MM_insert)){$mod_id = 24;}
elseif ($p=="subscribe"){ $mod_id = 20; }
else { $mod_id = 20; }
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");


 // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";


if ($p =="unsubscribe"){ $showform =1; };
if ($p =="preferences"){ $showform =1; };
if ($p =="confirm"){ $showform =1; };

if (isset($MM_insert)){
$showform =1;

if ($State != NULL){
$state2=$dbcon->CacheExecute("SELECT state FROM states where id= $State") or DIE($dbcon->ErrorMsg());
$statename = $state2->Fields("state");}
 

	if ($_POST[WebPage] == "http://") { $WebPage = NULL ; }
	else {$WebPage = $_POST[WebPage];}

$organization = $_POST[Organization];
$organizati = $_POST[Organization];
$name = $_POST[name]; 
$lastname = $_POST[LastName];
$EmailAddress = $_POST[email]; 
$phone = $_POST[Phone]; 
$fax =  $_POST[Fax]; 
$webpage = $WebPage;
$address = $_POST[Address]; 
$address2 = $_POST[Address2]; 
$city = $_POST[City]; 
$zip =  $_POST[PostalCode]; 
$country = $_POST[Country];
$comment =$_POST[notes]; 
$state = $statename; 


/* $emailck=$dbcon->CacheExecute("SELECT id FROM email where email= '$email'") or DIE($dbcon->ErrorMsg());
if ($emailck->Fields("id") == NULL){

   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "email";
   $MM_fieldsStr = "lastname|value|firstname|value|organization|value|select|value|email|value|phone|value|fax|value|web|value|address1|value|address2|value|city|value|State|value|zip|value|country|value|notes|value|html|value|student|value";
   $MM_columnsStr = "lastname|',none,''|firstname|',none,''|organization|',none,''|type|',none,''|email|',none,''|phone|',none,''|fax|',none,''|url|',none,''|address1|',none,''|address2|',none,''|city|',none,''|state|',none,''|zip|',none,''|country|',none,''|description|',none,''|html|none,1,0|student|none,1,0";

  require ("DBConnections/insetstuff.php");
  require ("DBConnections/dataactions.php");
 } */
/*
$MM_editTable  = "contacts2";
  ##add sourceid and enteredby and date enetered
   $MM_fieldsStr = "firstname|value|email|value|lastname|value|organization|value|address|value|address2|value|city|value|state|value|zip|value|country|value|phone|value|fax|value|website|value|enteredby|value|sourceid|value|notes|value";
   $MM_columnsStr = "FirstName|',none,''|EmailAddress|',none,''|LastName|',none,''|Company|',none,''|BusinessStreet|',none,''|BusinessStreet2|',none,''|BusinessCity|',none,''|BusinessState|',none,''|BusinessPostalCode|',none,''|BusinessCountry|',none,''|BusinessPhone|',none,''|BusinessFax|',none,''|WebPage|',none,''|enteredby|',none,''|source|',none,''|notes|',none,''";
   require ("Connections/insetstuff.php");
   require ("Connections/dataactions.php");

 
   }// end insert
   

  

################POPULATE FORM  ######################
  
$state=$dbcon->CacheExecute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
$bodydata2 = ob_get_clean();
chdir('lists');
include("index.php");
chdir('../');



	 
 ADOLoadCode($MM_DBTYPE);
   $dbcon=&ADONewConnection($MM_DBTYPE);
   $dbcon->Connect($MM_HOSTNAME,$MM_USERNAME,$MM_PASSWORD,$MM_DATABASE);
if ($showform != 1) {
?>
								  
				  
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

      <form  method=post name="subscribeform" class=form >
  <p><em><font color="#FF0000">Required fields are in red<br>
    </font></em></p>
  <table class="form" width="400" border=0 align="center" cellpadding=2 cellspacing=0>
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left"><strong> 
          <font size="3">Select the Mailings You Wish to Receive<br><br></font></strong></div></td>
      <?php 
	 $getlists=$dbcon->Execute("Select *, description as descriptionl  from phplist_list where active = 1 order by listorder") or DIE($dbcon->ErrorMsg());
	 while  (!$getlists->EOF)
   { ?>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="form"><div align="left"> 
          <input name="list[<?php echo $getlists->Fields("id"); ?>]" type="checkbox" value=signup <?php // if ($getlists->Fields("prefix") ==1) {echo "checked";} ?> checked >
          <?php echo $getlists->Fields("name"); ?>:<span class="text"> <?php echo $getlists->Fields("descriptionl"); ?> </span>
          <input type=hidden name="listname[<?php echo $getlists->Fields("id"); ?>]" value="<?php echo $getlists->Fields("name"); ?>"/>
        </div></td>
    </tr>
    <?php $getlists->MoveNext();

} ?>
    <tr> 
      <td align="right" class="form">&nbsp;</td>
      <td width="300">&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left"><font color="#FF0000">E-mail</font></div></td>
      <td> <input type="text" name="email" size="35" value="<?php echo $emailsub; ?>"> 
        <script language="Javascript" type="text/javascript">addFieldToCheck("email","E-Mail");</script> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">First Name </div></td>
      <td width="300"><input type="text" name="name" size="35">
      </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Last Name</div></td>
      <td><input name="lastname" type="text" id="lastname" size="35">
      </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Organization</div></td>
      <td> <input type="text" name="Organization" size="35"> </td>
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
      <td> <input name="WebPage" type="text" value="http://"  size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left"><font color="#000000">Mailing 
          Address</font></div></td>
      <td> <input type="text" name="Address"  size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form">&nbsp;</td>
      <td> <input type="text" name="Address2"  size="35"> </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">City</div></td>
      <td> <input type="text" name="City"  size="35"> 
      </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">State</div></td>
      <td class="form"> <?php echo statelist('State') ?> Zip 
        <input type="text" name="PostalCode" size="15">
      </td>
    </tr>
    <tr> 
      <td align="right" class="form"><div align="left">Country</div></td>
      <td> <input type="text" name="Country" value=US size="35"> </td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" align="right" valign="top" nowrap class="form"><div align="left"><br>
          Comments: <br>
          <textarea name="notes" cols="40" rows="4" wrap="VIRTUAL" id="notes"></textarea>
        </div></td>
    </tr>
    <?php if ($studenton == 1){ ?>
    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><div align="left"> 
          <input type="checkbox" name="student" value="1"  >
          Student</div></td>
    </tr><?php } ?>

    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><div align="left"> 
          <input type="checkbox" name="htmlemail" value="1" >
          Receive E-Mails in HTML</div></td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><div align="center">
          <p><strong> 
            <input type="hidden" name="showform" value="0">
            <input type="hidden" name="MM_insert" value="1">
            <br>
            </strong><br>
            <input type=submit name="subscribe" value="Subscribe" onClick="return checkform();">
          </p>
          <div align="left">
            <p><a href="email4.php?p=unsubscribe">Click Here to Unsubscribe</a> 
              <br>
            </p>
            </div>
        </div></td>
    </tr>
  </table>
		

  </form>
<?php }

 
 include("AMP/BaseFooter.php"); 
 */
?>
