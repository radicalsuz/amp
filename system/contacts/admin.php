<?php   
  
  require_once("../Connections/freedomrising.php");?>
  <?php include ("header.php");?>
<h2>System Administration</h2>
 <?php if ( AMP_Authorized( AMP_PERMISSION_CONTACTS_CAMPAIGNS)){ ?>
<p> <a href="admin_camfieldsphp.php">Add</a> | <a href="admin_camfieldsall.php">Edit 
  </a>Campaigns</p><p> <a href="admin_fieldsedit.php">Add</a> | <a href="admin_fields.php">Edit 
  </a>Fields</p>
   <?php } ?>
    <?php if (AMP_Authorized( AMP_PERMISSION_CONTACT_REGION)) { ?>
        <p> <a href="admin_regions.php">Add</a> | <a href="admin_regionsall.php">Edit</a> Regions</p>
    <?php }
    if (AMP_Authorized( AMP_PERMISSION_CONTACT_TYPE)) { ?> 
        <p> <a href="admin_types.php">Add</a> | <a href="admin_typesall.php">Edit</a> Contact Types</p>
    <?php }
    if ( AMP_Authorized( AMP_PERMISSION_CONTACT_SOURCE )) { ?> 
        <p> <a href="admin_source.php">Add</a> | <a href="admin_sourceall.php">Edit</a> Sources</p>
    <?php }
    if (AMP_Authorized( AMP_PERMISSION_CONTACT_USER)) { ?>
        <p> <a href="admin_users.php">Add</a> | <a href="admin_usersall.php">Edit</a> Users</p>
    <?php }
    if (AMP_Authorized( AMP_PERMISSION_CONTACT_OUTLOOK )) { 
        <p><a href="outlook.php">Import | Export</a> Outlook Address Book</p>
    <?php } 

  include ("footer.php");
  
  ?>
  

