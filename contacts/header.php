<?php // require_once ("password/secure.php"); ?>
<html>
<head>
<title>Contact System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<link rel="stylesheet" href="site.css" type="text/css">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="unnamed1">
  <tr> 
   <?php if ($uselogo == 1) { ?><td rowspan="2"><img src="<?php echo $logo ?>"> </td><?php } ?>
    <td> <div align="center"><a href="allcontacts1.php" class="unnamed1">View 
        contacts</a></div></td>
    <td>&nbsp;</td>
    <td> <div align="center"><a href="contact_add.php" class="unnamed1">add contact</a></div></td>
    <td> <div align="center"><a href="search.php" class="unnamed1">search</a></div></td>
    <?php if ($userper[65] == 1 or $standalone == 1){{} ?>
    <td> <div align="center"><a href="admin.php" class="unnamed1">system admin</a></div></td>
    <?php if ($userper[65] == 1 or $standalone == 1){}} ?>
    <?php if ($userper[72] == 1 ){{} ?>
    <td> <div align="center"><a href="../system/" class="unnamed1">content system</a></div></td>
    <?php if ($userper[72] == 1 or $standalone == 1){}} ?>
    <td> <div align="center"><a href="logout.php" class="unnamed1">logout</a></div></td>
  </tr>
</table>
