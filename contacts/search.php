<?php
     
  
  require_once("Connections/freedomrising.php");  

?><?php
   $allusers=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $allusers_numRows=0;
   $allusers__totalRows=$allusers->RecordCount();
?><?php
   $region=$dbcon->Execute("SELECT id, title FROM region ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $region_numRows=0;
   $region__totalRows=$region->RecordCount();
?><?php
   $allclass=$dbcon->Execute("SELECT id, title FROM contacts_class ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $allclass_numRows=0;
   $allclass__totalRows=$allclass->RecordCount();
?><?php include("header.php"); ?>


<form name="form1" method="POST" action="results.php">
  <table width="95%" border="0" cellspacing="0" cellpadding="0" class="toplinks">
    <tr> 
      <td>Personal Information</td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="95%" border="0" cellspacing="0" cellpadding="0">
    <tr > 
      <td class="title">Title</td>
      <td class="title">First Name</td>
      <td class="title">Last Name</td>
    </tr>
    <tr> 
      <td> <input type="text" name="Suffix" size="10" value=""> </td>
      <td> <input type="text" name="FirstName" size="40" > </td>
      <td> <input type="text" name="LastName" size="40" > </td>
    </tr>
    <tr> 
      <td class="title">type </td>
      <td class="title">Organization </td>
      <td class="title">Position</td>
    </tr>
    <tr> 
      <td rowspan="3"> <select name="classid">
          <OPTION value="">none</OPTION>
          <?php
  if ($allclass__totalRows > 0){
    $allclass__index=0;
    $allclass->MoveFirst();
    WHILE ($allclass__index < $allclass__totalRows){
?>
          <OPTION VALUE="<?php echo  $allclass->Fields("id")?>"> 
          <?php echo  $allclass->Fields("title");?> </OPTION>
          <?php
      $allclass->MoveNext();
      $allclass__index++;
    }
    $allclass__index=0;  
    $allclass->MoveFirst();
  }
?>
        </select> </td>
      <td> <input type="text" name="Company" size="40" > </td>
      <td> <input type="text" name="JobTitle" size="40" > </td>
    </tr>
    <tr> 
      <td class="title">Phone</td>
      <td class="title">E-Mail</td>
    </tr>
    <tr> 
      <td> <input type="text" name="HomePhone" size="40" >
      </td>
      <td><input type="text" name="EmailAddress" size="40"> </td>
    </tr>
    <tr> 
      <td class="title">Entered By</td>
      <td class="title">Web Page</td>
      <td class="title">Campus</td>
    </tr>
    <tr> 
      <td> <select name="enteredby">
          <option value="">none </option>
          <?php
  if ($allusers__totalRows > 0){
    $allusers__index=0;
    $allusers->MoveFirst();
    WHILE ($allusers__index < $allusers__totalRows){
?>
          <OPTION VALUE="<?php echo $allusers->Fields("id")?>"> 
          <?php echo $allusers->Fields("name");?> </OPTION>
          <?php
      $allusers->MoveNext();
      $allusers__index++;
    }
    $allusers__index=0;  
    $allusers->MoveFirst();
  }
?>
        </select></td>
      <td ><input type="text" name="WebPage" size="40"> </td>
	  <td ><input type="text" name="campus" size="40"> </td>
    </tr>
    
    <tr valign="top" class="title"> 
      <td colspan="3" class="title">Notes </td>
    </tr>
    <tr valign="top"> 
      <td colspan="3"> <textarea name="notes" cols="65" wrap="VIRTUAL" rows="4"></textarea></td>
    </tr>
    <tr> 
      <td class="toplinks" colspan="3"> Address</td>
    </tr>
    <tr> 
      <td class="title">Region</td>
      <td class="title">Address</td>
      <td class="title">Address2</td>
    </tr>
    <tr> 
      <td rowspan="5"> <select name="regionid" >
          <option value="">none</option>
          <?php
  if ($region__totalRows > 0){
    $region__index=0;
    $region->MoveFirst();
    WHILE ($region__index < $region__totalRows){
?>
          <option value="<?php echo  $region->Fields("id")?>"> <?php echo  $region->Fields("title");?> 
          </option>
          <?php
      $region->MoveNext();
      $region__index++;
    }
    $region__index=0;  
    $region->MoveFirst();
  }
?>
        </select></td>
      <td> <input type="text" name="BusinessStreet" size="40" > </td>
      <td> <input type="text" name="BusinessStreet2" size="40" > </td>
    </tr>
    <tr> 
      <td class="title">City</td>
      <td class="title">State </td>
    </tr>
    <tr> 
      <td> <input type="text" name="BusinessCity" size="40"> </td>
      <td> <input type="text" name="BusinessState" size="10
	  " > </td>
    </tr>
    <tr> 
      <td class="title">Zip</td>
      <td class="title">Country</td>
    </tr>
    <tr> 
      <td> <input type="text" name="BusinessPostalCode" size="10
	  "> </td>
      <td> <input type="text" name="BusinessCountry" size="40" > </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td> <div align="right"></div></td>
      <td>&nbsp; </td>
    </tr>
    <tr> 
      <?php

$campaignrcd__MMColParam = "8000";
if (isset($HTTP_GET_VARS["camp"]))
  {$campaignrcd__MMColParam = $HTTP_GET_VARS["camp"];}

   $campaignrcd=$dbcon->Execute("SELECT * FROM campaigns WHERE id = " . ($campaignrcd__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $campaignrcd_numRows=0;
   $campaignrcd__totalRows=$campaignrcd->RecordCount();
   
   $allcamp=$dbcon->Execute("SELECT id, name FROM campaigns") or DIE($dbcon->ErrorMsg());
   $allcamp_numRows=0;
   $allcamp__totalRows=$allcamp->RecordCount();
?>
      <td colspan="3"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td class="title"> <div align="right">Search Campaign</div></td>
            <td class="title"> <select name="select" onChange="MM_jumpMenu('parent',this,0)">
                <OPTION VALUE="search.php">none</option>
                <?php
  if ($allcamp__totalRows > 0){
    $allcamp__index=0;
    $allcamp->MoveFirst();
    WHILE ($allcamp__index < $allcamp__totalRows){
?>
                <OPTION VALUE="search.php?camp=<?php echo  $allcamp->Fields("id")?>&PHPSESSID=<?php echo  $HTTP_GET_VARS["PHPSESSID"]?>"<?php if ($allcamp->Fields("id")==$campaignrcd->Fields("id")) echo "SELECTED";?>> 
                <?php echo  $allcamp->Fields("name");?> </OPTION>
                <?php
      $allcamp->MoveNext();
      $allcamp__index++;
    }
    $allcamp__index=0;  
    $allcamp->MoveFirst();
  }
?>
              </select> </td>
            <td class="title"> <div align="right"></div></td>
            <td class="title"> </td>
            <td class="title"> <div align="right"></div></td>
            <td class="title"> </td>
            <td class="title">&nbsp;</td>
            <td class="title">&nbsp;</td>
            <td class="title">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
  
 <?php if (isset($HTTP_GET_VARS["camp"])){ ?>
 
 
 
  <table width="95%" border="0" cellspacing="0" cellpadding="10">
    <tr> 
    <td> 
      
	  
	  
        <table width="100%" border="0" cellspacing="5" class="table">
          <tr class="toplinks"> 
            <td> 
              <p>Campaign Questions</p>
            </td>
            <td> 
              
            </td>
          </tr>
          <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field1text")?>
            </td>
            <td> 
              <?php if ($campaignrcd->Fields("1ftype") == ("1")){ ?>
              <input type="text" name="field1" size="40" >
              <?php }
			if ($campaignrcd->Fields("1ftype") == ("2")){ ?>
              <input type="checkbox" name="field1" value="1">
              <?php }
			  if ($campaignrcd->Fields("1ftype") == ("3")){ ?>
              <textarea name="field1" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php } ?>
            </td>
          </tr>
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field2text")?>
            </td>
            <td> 
			  <?php if ($campaignrcd->Fields("field2text")!= ($null)){ ?><?php if ($campaignrcd->Fields("2ftype") == ("1")){ ?>
              <input type="text" name="field2" size="40">
              <?php }
			 if ($campaignrcd->Fields("2ftype") == ("2")){ ?>
              <input  type="checkbox" name="field2" value="1">
              <?php }
			  if ($campaignrcd->Fields("2ftype") == ("3")){ ?>
              <textarea name="field2" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }} ?>
            </td>
          </tr>
		   <tr> 
            <td> 
			 
              <?php echo $campaignrcd->Fields("field3text")?>
            </td>
            <td> 
			  <?php if ($campaignrcd->Fields("field3text")!= ($null)){ ?><?php if ($campaignrcd->Fields("3ftype") == ("1")){ ?>
			  
              <input type="text" name="field3" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("3ftype") == ("2")){ ?>
              <input  type="checkbox" name="field3" value="1">
              <?php }
			  if ($campaignrcd->Fields("3ftype") == ("3")){ ?>
              <textarea name="field3" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php } }?>
            </td>
          </tr>
		  
		  		   
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field4text")?>
            </td>
            <td> 
			 <?php if ($campaignrcd->Fields("field4text")!= ($null)){ ?><?php if ($campaignrcd->Fields("4ftype") == ("1")){ ?>
              <input type="text" name="field4" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("4ftype") == ("2")){ ?>
              <input  type="checkbox" name="field4" value="1">
              <?php }
			  if ($campaignrcd->Fields("4ftype") == ("3")){ ?>
              <textarea name="field4" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }}?>
            </td>
          </tr>
		  
		    		   <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field5text")?>
            </td>
            <td> 
			 <?php if ($campaignrcd->Fields("field5text")!= ($null)){ ?><?php if ($campaignrcd->Fields("5ftype") == ("1")){ ?>
              <input type="text" name="field5" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("5ftype") == ("2")){ ?>
              <input  type="checkbox" name="field5" value="1">
              <?php }
			  if ($campaignrcd->Fields("5ftype") == ("3")){ ?>
              <textarea name="field5" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }} ?>
            </td>
          </tr>
		  		   
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field6text")?>
            </td>
            <td> 
			<?php if ($campaignrcd->Fields("field6text")!= ($null)){ ?><?php if ($campaignrcd->Fields("6ftype") == ("1")){ ?>
			   
              <input type="text" name="field6" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("6ftype") == ("2")){ ?>
              <input  type="checkbox" name="field6" value="1">
              <?php }
			  if ($campaignrcd->Fields("6ftype") == ("3")){ ?>
              <textarea name="field6" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }} ?>
            </td>
          </tr>
		  
		    		   <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field7text")?>
            </td>
            <td> 
			 <?php if ($campaignrcd->Fields("field7text")!= ($null)){ ?><?php if ($campaignrcd->Fields("7ftype") == ("1")){ ?>
              <input type="text" name="field7" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("7ftype") == ("2")){ ?>
              <input  type="checkbox" name="field7" value="1">
              <?php }
			  if ($campaignrcd->Fields("7ftype") == ("3")){ ?>
              <textarea name="field7" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }} ?>
            </td>
          </tr>
		  
		    		   
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field8text")?>
            </td>
            <td>  <?php if ($campaignrcd->Fields("field8text")!= ($null)){ ?><?php if ($campaignrcd->Fields("8ftype") == ("1")){ ?>
              <input type="text" name="field8" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("8ftype") == ("2")){ ?>
              <input  type="checkbox" name="field8" value="1">
              <?php }
			  if ($campaignrcd->Fields("8ftype") == ("3")){ ?>
              <textarea name="field8" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }} ?>
            </td>
          </tr>
		  
		  
		    		   <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field9text")?>
            </td>
            <td> 
			 <?php if ($campaignrcd->Fields("field9text")!= ($null)){ ?><?php if ($campaignrcd->Fields("9ftype") == ("1")){ ?>
              <input type="text" name="field9" size="40" value="">
              <span class="table">
              <?php }
			 if ($campaignrcd->Fields("9ftype") == ("2")){ ?>
              </span> 
              <input  type="checkbox" name="field9" value="1">
              <?php }
			  if ($campaignrcd->Fields("9ftype") == ("3")){ ?>
              <textarea name="field9" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php }} ?>
            </td>
          </tr>
		  
		    		   
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field10text")?>
            </td>
            <td> 
			 <?php if ($campaignrcd->Fields("field10text")!= ($null)){ ?><?php if ($campaignrcd->Fields("10ftype") == ("1")){ ?>
              <input type="text" name="field10" size="40" value="">
              <?php }
			 if ($campaignrcd->Fields("10ftype") == ("2")){ ?>
              <input  type="checkbox" name="field10" value="1">
              <?php }
			  if ($campaignrcd->Fields("10ftype") == ("3")){ ?>
              <textarea name="field10" wrap="VIRTUAL" cols="40" rows="5"></textarea>
              <?php } }?><?php echo $campaignrcd->Fields("id")?>
            </td>
          </tr>
		  
		  <input type="hidden" name="camid" value="<?php echo $campaignrcd->Fields("id")?>">
        </table>
		<?php
 
		} ?>
		
   
      &nbsp; </td>
  </tr>
</table>
  
  <p> 
    <input type="submit" name="Submit22" value="Search">
  </p>
  

</form>
</body>
</html>

<?php
  $allclass->Close();
?><?php
  $allusers->Close();
  $campaignrcd->Close();
  $allcamp->Close();
  $region->Close();
include ("footer.php");
?>

