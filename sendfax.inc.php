<?php

$sendfaxck=$dbcon->Execute("SELECT emailfax FROM sendfax  WHERE id = $item ") or DIE($dbcon->ErrorMsg());

if ($sendfaxck->Fields("emailfax") == 1) {

 $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";

	if (isset($MM_insert)){

 $MM_editTable  = "tookaction";
   $MM_editRedirectUrl = "article.php?list=type&type=$MM_type";
   $MM_fieldsStr =  "firstname|value|lastname|value|address|value|city|value|zip|value|country|value|email|value|phone|value|fax|value|msgText|value|actionid|value|statename|value";
   $MM_columnsStr = "firstname|',none,''|lastname|',none,''|address|',none,''|city|',none,''|zip|',none,''|country|',none,''|email|',none,''|phone|',none,''|fax|',none,''|text|',none,''|actionid|',none,''|state|',none,''";

//Format of e-mail that is sent to target
set_magic_quotes_runtime (1);
mail ( "$toemail", "$subjectText", "$msgText\n \n $firstname $lastname\n $address \n $city, $statename $zip $country \n $email \n $phone", "From: $email\nX-Mailer: My PHP Script\n"); 
//mail ( "$toemail", "$subjectText", "$toText\n $msgText\n \n $firstname $lastname\n $address \n $city, $statename $zip $country \n $email \n $phone", "From: $email\nX-Mailer: My PHP Script\n"); 
set_magic_quotes_runtime (0);
 require ("Connections/insetstuff.php");
  require ("Connections/dataactions.php");
  
   }

$item = $Recordset1->Fields("actionlink");
   $sendfax=$dbcon->Execute("SELECT *  FROM sendfax  WHERE id = $item and emailfax = 1") or DIE($dbcon->ErrorMsg());
   $sendfax_numRows=0;
   $sendfax__totalRows=$sendfax->RecordCount();
  
?>
 
<form method="POST"  class="form" action="<?php echo $MM_editAction?>">
   <table border="0" cellspacing="1" cellpadding="0" width="100%" class="form">
       <tr> 
      <td valign="top"><b>First Name</b>: </td>
      <td> 
        <input type="text" name="firstname" size="35">
      </td>
    </tr> 
	<tr> 
      <td valign="top"><b>Last Name</b>: </td>
      <td> 
       <input type="text" name="lastname" size="35">
      </td>
    </tr>
       <tr> 
      <td valign="top"><b>Address</b>: </td>
      <td> 
        <input type="text" name="address" size="35">
      </td>
    </tr>
   <tr> 
      <td valign="top"><b>City</b>: </td>
      <td> 
        <input type="text" name="city" size="35">
      </td>
    </tr>
	 <tr> 
      <td valign="top"><b>State</b>: </td>
      <td> 
        <input type="text" name="statename" size="35">
      </td>
    </tr>
	 <tr> 
      <td valign="top"><b>Zip</b>: </td>
      <td> 
        <input type="text" name="zip" size="35">
      </td>
    </tr>
	 <tr> 
      <td valign="top"><b>Country</b>: </td>
      <td> 
        <input type="text" name="country" size="35">
      </td>
    </tr>
     <tr> 
      <td valign="top"><b>E-Mail</b>: </td>
      <td> 
        <input type="text" name="email" size="35">
      </td>
    </tr>
	<tr> 
      <td valign="top"><b>Phone</b>: </td>
      <td> 
        <input type="text" name="phone" size="35">
      </td>
	  <tr> 
      <td valign="top"><hr></td>
      
    </tr>
    </tr>
	
	<tr> 
      <td valign="top"><b>To:</b> </td>
      <td> 
        <?php echo $sendfax->Fields("totitle")?>&nbsp;<?php echo $sendfax->Fields("tofirstname")?>&nbsp;<?php echo $sendfax->Fields("tolastname")?>
      </td>
    </tr>
    
      <td valign="top"><b>Subject</b>: </td>
      <td> 
        <input type="text" name="subjectText" size="30" value="<?php echo $sendfax->Fields("subject")?>">
      </td>
    </tr>
    <tr> 
      <td valign="top"><b>Message Text:</b> </td>
      <td> Your name and address will be added at the end of the message.<br><textarea rows="25" name="msgText" cols="35"><?php echo $sendfax->Fields("text")?></textarea>
      </td>
    </tr>
    <tr> 
      <td valign="top"> 
   <input type="submit" name="Submit" value="Submit">
      </td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p><?php $toText =  $sendfax->Fields("totitle")." ".$sendfax->Fields("tofirstname")." ".$sendfax->Fields("tolastname")?>
  <input type="hidden" name="toText" value="<?php echo $toText; ?>">
   <input type="hidden" name="actionid" value="<?php echo $actionvar; ?>">
    <input type="hidden" name="toemail" value="<?php echo $sendfax->Fields("toemail")?>">
    <input type="hidden" name="bccText" value="<?php echo $sendfax->Fields("bcc")?>">
	<input type="hidden" name="MM_insert" value="true">
  </p>
  <input type="hidden" name="state" value="1">
</form>

<?php }

else {
include ("sendfax2.inc.php");}?>
