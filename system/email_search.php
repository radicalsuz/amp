<?php 
 $modid=9;
  require("Connections/freedomrising.php");
 
 ##################get the list of lists ########################

 

################POPULATE FORM  ######################

$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
?>
				 <?php include("header.php");?>
      <?php 
 ################ FORM DATA  ######################				  
				  ?>
      <form method="POST" action="mailman.php" name="form1">
      
        <table width="98%" border=0 align="center" cellpadding=2 cellspacing=0>
          <tr valign="baseline" class="banner"> 
            <td colspan="2" align="right" nowrap class="form"> <div align="left">Build 
                Custom List</div></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">First Name:</td>
            <td><input type="text" name="firstname" value="" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Last Name:</td>
            <td><input type="text" name="lastname" value="" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">E-mail:</td>
            <td> <input type="text" name="email" value="" size="32"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">City:</td>
            <td> <input type="text" name="city" value="" size="32"> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">State:</td>
            <td> 
              <?php statelist("state") ;?>
              &nbsp;&nbsp;Zip 
              <input type="text" name="zip" value="" size="15"> </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" nowrap class="form">Country:</td>
            <td> <input type="text" name="country" value="" size="32"> </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" nowrap class="form">Student</td>
            <td><input type="checkbox" name="student" value="1"></td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" nowrap class="form">Receive E-Mails in HTML</td>
            <td><input type="checkbox" name="html" value="1" ></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td><input type="submit" name="search" value="Search"> </td>
          </tr>
        </table>
      </form>

      <?php include("footer.php"); ?>

	