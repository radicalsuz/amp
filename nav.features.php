 <?php 
 /*********************
11-01-2003  v3.01
Module:  Navigation
Description:  system features
Called From: nav database
SYS VARS: isanarticle, MM_id, MM_type
To Do:  
*********************/ 
  global $MM_id, $isanarticle, $_GET, $MM_type, $navalign;
  
  if ($navalign=="r") { ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    
    <td valign="middle"><div align="right"><a href="javascript:openform('mailto.php')" class="sidelist">E-Mail 
        This Page</a></div></td><td width="15" valign="middle"><img src="img/email.gif" align="top"></td>
  </tr>
  <?php if (("$isanarticle") == ("1") ) { 
  if (($MM_id) != ("1")) { ?>
  <tr> 
    
    <td valign="middle"><div align="right"><a href="print_article.php?<?php 
if ($_GET[id])  {echo "&id=$_GET[id]"		;	}
if ($_GET['list'])  {echo "&list=".$_GET['list']		;	}
//if ($class)  {echo "&class=".$class		;	}
if ($_GET[type])  {echo "&type=$_GET[type]"		;	}
if ($_GET[rel2])  {echo "&rel2=$_GET[rel2]"		;	}
if ($_GET[rel1])  {echo "&rel1=$_GET[rel1]"		;	}
			?>" class="sidelist">Printer Safe Version</a></div></td><td width="15" valign="middle"><img src="img/print.gif" align="top"></td>
  </tr> <?php }?>
  
  <?php } ?>
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
  <?php if (("$isanarticle") == ("1") ) { 
  if (($MM_id) != ("1")) { ?>
  <tr> 
    <td width="15" valign="middle"><img src="img/print.gif" align="top"></td>
    <td valign="middle"><a href="print_article.php?<?php 
if ($_GET[id])  {echo "&id=$_GET[id]"		;	}
if ($_GET['list'])  {echo "&list=".$_GET['list']		;	}
//if ($class)  {echo "&class=".$class		;	}
if ($_GET[type])  {echo "&type=$_GET[type]"		;	}
if ($_GET[rel2])  {echo "&rel2=$_GET[rel2]"		;	}
if ($_GET[rel1])  {echo "&rel1=$_GET[rel1]"		;	}
			?>" class="sidelist">Printer 
      Safe Version</a></td>
  </tr> <?php }?>
  
  <?php } ?>
  <tr>
    <td valign="middle"><img src="img/sitemap.gif" width="21" height="19"></td>
    <td valign="middle"><a href="sitemap.php" class="sidelist">Site Map</a></td>
  </tr>
 
</table>

<?php }?>