<?php   
  
  require_once("../Connections/freedomrising.php");?>
  <?php include ("header.php");?>
<h2>System Administration</h2>
 <?php if ($userper[66] == 1 or $standalone == 1){{} ?>
<p> <a href="admin_camfieldsphp.php">Add</a> | <a href="admin_camfieldsall.php">Edit 
  </a>Campaigns</p><p> <a href="admin_fieldsedit.php">Add</a> | <a href="admin_fields.php">Edit 
  </a>Fields</p>
   <?php if ($userper[66] == 1 or $standalone == 1){}} ?>
    <?php if ($userper[67] == 1 or $standalone == 1){{} ?>
<p> <a href="admin_regions.php">Add</a> | <a href="admin_regionsall.php">Edit</a> 
  Regions</p>
     <?php if ($userper[67] == 1 or $standalone == 1){}} ?>
    <?php if ($userper[68] == 1 or $standalone == 1){{} ?>
<p> <a href="admin_types.php">Add</a> | <a href="admin_typesall.php">Edit</a> 
  Contact Types</p>
     <?php if ($userper[68] == 1 or $standalone == 1){}} ?>
    <?php if ($userper[69] == 1 or $standalone == 1){{} ?>
  <p> <a href="admin_source.php">Add</a> | <a href="admin_sourceall.php">Edit</a> 
  Sources</p>
     <?php if ($userper[69] == 1 or $standalone == 1){}} ?>
    <?php if ($userper[70] == 1 or $standalone == 1){{} ?>

 <p> <a href="admin_users.php">Add</a> | <a href="admin_usersall.php">Edit</a> 
  Users</p>
     <?php if ($userper[70] == 1 or $standalone == 1){}} ?>
    <?php if ($userper[71] == 1 or $standalone == 1){{} ?>
<p><a href="outlook.php">Import | Export</a> Outlook Address 
  Book</p>
  <?php if ($userper[71] == 1 or $standalone == 1){}} 
  
  include ("footer.php");?>
  

