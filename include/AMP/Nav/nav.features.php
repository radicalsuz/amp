 <?php 
 /*********************
11-01-2003  v3.01
Module:  Navigation
Description:  system features
Called From: nav database

To Do:  
*********************/ 
global  $navalign;
  
if ($navalign=="r") { ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    
    <td valign="middle"><div align="right"><a href="javascript:openform('mailto.php')" class="sidelist">E-Mail 
        This Page</a></div></td><td width="15" valign="middle"><img src="img/email.gif" align="top"></td>
  </tr>
  <tr> 
    
    <td valign="middle"><div align="right"><a href="<?php echo $_SERVER["REQUEST_URI"] ; if ($_SERVER["QUERY_STRING"]) {echo "&";} else {echo "?";} ?>printsafe=1" class="sidelist">Printer Safe Version</a></div></td><td width="15" valign="middle"><img src="img/print.gif" align="top"></td>
  </tr> 
  <tr>
    
    <td valign="middle"> <div align="right"><a href="sitemap.php" class="sidelist">Site 
        Map</a></div></td><td valign="middle"><img src="img/sitemap.gif" width="21" height="19"></td>
  </tr>
 
</table>
  <?php
}
else {
  
   ?> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="15" valign="middle"><img src="img/email.gif" align="top"></td>
    <td valign="middle"><a href="javascript:openform('mailto.php')" class="sidelist">E-Mail 
      This Page</a></td>
  </tr>

  <tr> 
    <td width="15" valign="middle"><img src="img/print.gif" align="top"></td>
    <td valign="middle"><a href="<?php echo $_SERVER["REQUEST_URI"] ; if ($_SERVER["QUERY_STRING"]) {echo "&";} else {echo "?";} ?>printsafe=1" class="sidelist">Printer 
      Safe Version</a></td>
  </tr> 
  

  <tr>
    <td valign="middle"><img src="img/sitemap.gif" width="21" height="19"></td>
    <td valign="middle"><a href="sitemap.php" class="sidelist">Site Map</a></td>
  </tr>
 
</table>

<?php }?>