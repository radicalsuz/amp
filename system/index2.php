<?php
  require("Connections/freedomrising.php");
 
?>
<html>
<head>
<title><?php echo $SiteName ?> Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="managment.css" type="text/css">
<body bgcolor="#FFFFFF" text="#000000" link="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="650" border="0" align="center" cellpadding="10" cellspacing="10">
  <tr bgcolor="#006699"> 
    <td colspan="3"><table width="630" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td bgcolor="#006699"><font color="#FFFFFF" face="Verdana, 
Arial, Helvetica, sans-serif" size="5"><?php echo $SiteName ; ?></font><font color="#FFFFFF" face="Verdana, 
Arial, Helvetica, sans-serif" size="5"><b><br>
            Administration</b></font> </td>
          <td align="right" valign="bottom" bgcolor="#006699" class="toplinks"><b class="toplinks"><a href="flushcache.php" class="toplinks">RESET 
            CACHE</a>
            <?php if ($userper[73] == 1){{} ?>
            : <a href="../contacts/" class="toplinks">CONTACT SYSTEM</a> 
            <?php if ($userper[73] == 1){}} ?>
            : <a href="html.html" target="_blank" class="toplinks">HTML TIPS</a> 
            : <a href="logout.php" class="toplinks">LOGOUT</a> : <a href="javascript:void(0)" ONCLICK="open('help.php','miniwin','location=1,scrollbars=1,resizable=1,width=550,height=400') " class="toplinks">HELP</a>  
            </b></font></td>
        </tr>
      </table>
      
    </td>
  </tr>
  <tr> 
 
    <td width="33%" valign="top">  <p class="sidetitle">Home Page</p>
      <a href="article_list.php?&class=2" class="side">View/Edit Homepage </a> 
      <br> <a href="article_fpedit.php" class="side">Add Homepage Content </a> <br>
	  <a href="article_list.php?&fpnews=1" class="side"> Homepage News</a> <br>
	  <a href="module_nav_edit.php?id=2" class="side">Home Page Navigation</a><br> 
      <?php if ($userper[10] == 1){{} ?>
      <p class="sidetitle">Content</p>
      <?php if ($userper[1] == 1){{} ?>
      <a href="articlelist.php" class="side">View/Edit Content </a> <br> <a href="article_list.php" class="side">View/Edit 
      All Content</a><br> 
      <?php if ($userper[1] == 1){}} ?>
      <?php if ($userper[2] == 1){{} ?>
      <a href="article_edit.php" class="side">Add Content</a><br> 
	  <a href="module_nav_edit.php?id=1" class="side">Content Navigation</a><br>
      <?php if ($userper[2] == 1){}} ?>
      <p class="sidetitle">Sections</p>
      <?php if ($userper[9] == 1){{} ?>
      <a href="edittypes.php" class="side">View/Edit Sections</a><br> 
      <?php if ($userper[9] == 1){}} ?>
      <?php if ($userper[4] == 1){{} ?>
      <a href="type_edit.php" class="side">Add Section</a><br> 
      <?php if ($userper[4] == 1){}} ?>
      <?php if ($userper[6] == 1){{} ?>
      <a href="subtype_add.php" class="side">Add Sub Section</a><br> 
      <?php if ($userper[6] == 1){}} ?>
      <?php if ($userper[7] == 1){{} ?>
      <a href="catagory_add.php" class="side">Add 3rd level</a><br> 
      <?php if ($userper[7] == 1){}} ?>
      <?php if ($userper[8] == 1){{} ?>
      <a href="class.php" class="side">Add Class</a><br> 
      <?php if ($userper[8] == 1){}} ?></P>
      <?php if ($userper[10] == 1){}} ?>
      <?php if ($userper[11] == 1){{} ?>
      <p class="sidetitle">Calendar 
      <p class="side"> 
        <?php if ($userper[13] == 1){{} ?>
        <a href="calendar_gxlist.php" class="side">View/Edit Current Events</a><br>
        <?php if ($userper[13] == 1){}} ?>
        <?php if ($userper[14] == 1){{} ?>
        <a href="calendar_gxlist.php?old=1" class="side">View/Edit Old Events</a><br>
        <?php if ($userper[14] == 1){}} ?>
        <?php if ($userper[12] == 1){{} ?>
        <a href="calendar_gxedit.php" class="side">Add Event</a><br>
		 <a href="calendar_type_list.php" class="side">Calendar Types</a><br>
        <?php if ($userper[12] == 1){}} ?>
        <!--	<a href="calendar_area.php" class="side">Add Area</a><br>
				<a href="calendar_arealist.php" class="side">View/Edit Areas</a><br> -->
      </p>
      <?php if ($userper[11] == 1){}} ?>
      <!-- <p class="sidetitle">News &amp; Updates 
            
          <p class="side"> <a href="newswire_list.php" class="side">View/Edit 
            News-Updates</a><br>
            <a href="newswire_add.php" class="side">Add News-Updates</a><br>
            <a href="newswiretype_list.php" class="side">Edit/Add News-Updates 
            Types</a></p> -->
      <?php if ($userper[22] == 1){{} ?>
      <p class="sidetitle">FAQ System</p>
      <p class="side"> 
        <?php if ($userper[23] == 1){{} ?>
        <a href="faq_list.php" class="side">View/Edit FAQ<br>
        </a> 
        <?php if ($userper[23] == 1){}} ?>
        <?php if ($userper[24] == 1){{} ?>
        <a href="faq_edit.php" class="side">Add FAQ</a><br>
        <?php if ($userper[24] == 1){}} ?>
        <?php if ($userper[25] == 1){{} ?>
        <a href="faqtype_list.php" class="side">View/Edit FAQ Types</a> 
        <?php if ($userper[25] == 1){}} ?>
      </p>
      <?php if ($userper[22] == 1){}} ?>
      <?php if ($userper[26] == 1){{} ?>
      <p class="sidetitle">Links</p>
      <p class="side"> 
        <?php if ($userper[27] == 1){{} ?>
        <a href="link_list.php" class="side">View/Edit Links</a><br>
        <?php if ($userper[27] == 1){}} ?>
        <?php if ($userper[28] == 1){{} ?>
        <a href="link_edit.php" class="side">Add Links</a><br>
        <?php if ($userper[28] == 1){}} ?>
        <?php if ($userper[29] == 1){{} ?>
        <a href="linktype_list.php" class="side">View/Edit Link Type</a><br>
        <?php if ($userper[29] == 1){}} ?>
        <?php if ($userper[30] == 1){{} ?>
        <a href="linktype_add.php" class="side">Add Link Type</a> 
        <?php if ($userper[30] == 1){}} ?>
      </p>
      <?php if ($userper[26] == 1){}} ?>
      <?php if ($userper[31] == 1){{} ?>
      <p class="sidetitle">Action Item </p>
      <p class="side"> 
        <?php if ($userper[32] == 1){{} ?>
        <a href="sendfax_list.php" class="side">View/Edit Action</a><br>
        <?php if ($userper[32] == 1){}} ?>
        <?php if ($userper[33] == 1){{} ?>
        <a href="sendfax_edit.php" class="side">Add Action</a> <br>
        <?php if ($userper[33] == 1){}} ?>
        <?php if ($userper[31] == 1){}} ?>
      </p> </td>
    <td width="33%" valign="top"><p class="side"> 
        <?php if ($userper[56] == 1){{} ?>
      <p class="sidetitle">Local Groups 
      <p class="side"> 
        <?php if ($userper[57] == 1){{} ?>
        <a href="moddata_list.php?modin=2" class="side">View/Edit Groups</a><br>
        <?php if ($userper[57] == 1){}} ?>
        <?php if ($userper[58] == 1){{} ?>
        <a href="moddata.php?modin=2" class="side">Add Group</a><br>
        <?php if ($userper[58] == 1){}} ?>
        <?php if ($userper[56] == 1){}} ?>
        <?php if ($userper[59] == 1){{} ?>
      <p class="sidetitle">Endorsements 
      <p class="side"> 
        <?php if ($userper[60] == 1){{} ?>
        <a href="moddata_list.php?modin=1" class="side">View/Edit Endorsements</a><br>
        <?php if ($userper[60] == 1){}} ?>
        <?php if ($userper[61] == 1){{} ?>
        <a href="../modinput.php?modin=1" class="side">Endorse</a><br>
        <?php if ($userper[61] == 1){}} ?>
        <?php if ($userper[59] == 1){}} ?>
      </p>
      <p class="side">
        <?php if ($userper[74] == 1){{} ?>
      <p class="sidetitle">Petitions 
      <p class="side"> 
        <?php if ($userper[75] == 1){{} ?>
        <a href="petition_list.php" class="side">View/Edit Petitions</a><br>
        <?php if ($userper[75] == 1){}} ?>
        <?php if ($userper[76] == 1){{} ?>
        <a href="petition_edit.php" class="side">Add Petition</a><br>
        <?php if ($userper[76] == 1){}} ?>
      </p>
      <?php if ($userper[74] == 1){}} ?>
      <?php if ($userper[78] == 1){{} ?>
      <p class="sidetitle">Trainers Bank 
      <p class="side"> 
        <?php if ($userper[79] == 1){{} ?>
        <a href="moddata_list.php?modin=4" class="side">View/Edit Trainers</a><br>
        <?php if ($userper[79] == 1){}} ?>
        <?php if ($userper[80] == 1){{} ?>
        <a href="../modinput.php?modin=4" class="side">Add Trainers</a><br>
        <?php if ($userper[80] == 1){}} ?>
      </p>
      <?php if ($userper[79] == 1){}} ?>
      <?php if ($userper[81] == 1){{} ?>
      <p class="sidetitle">Speakers Bank 
      <p class="side"> 
        <?php if ($userper[82] == 1){{} ?>
        <a href="moddata_list.php?modin=4" class="side">View/Edit Speakers</a><br>
        <?php if ($userper[82] == 1){}} ?>
        <?php if ($userper[83] == 1){{} ?>
        <a href="../modinput.php?modin=4" class="side">Add Speakers</a><br>
        <?php if ($userper[83] == 1){}} ?>
      </p>
      <?php if ($userper[81] == 1){}} ?>
      <?php if ($userper[18] == 1){{} ?>
      <p class="sidetitle">Media Sign In</p>
      <p class="side"> 
        <?php if ($userper[19] == 1){{} ?>
        <a href="moddata_list.php?modin=7" class="side">View/Edit Media Sign In</a><br>
        <?php if ($userper[19] == 1){}} ?>
        <?php if ($userper[20] == 1){{} ?>
        <a href="export.php?id=7" class="side">download as a file</a> 
        <?php if ($userper[20] == 1){}} ?>
      </p>
      <?php if ($userper[18] == 1){}} ?>
      <?php if ($userper[42] == 1){{} ?>
      <p class="sidetitle">Housing Board</p>
      <p class="side"> <a href="housing_list.php" class="side">View/Edit 
        Housing</a> <br>
        <a href="hosusing_edit.php" class="side">Add Housing</a></p>
      <?php if ($userper[42] == 1){}} ?>
	  <?php if ($userper[84] == 1){{} ?>				 
          <p class="sidetitle">Voulenteers
			<p class="side">

 				<a href="moddata_list.php?modin=8" class="side">View/Edit Volunteers</a><br>

				<a href="../modinput.php?modin=8" class="side">Add Volunteer</a><br>
			
			
 	<?php if ($userper[84] == 1){}} ?>
    </td>
	<td width="33%" valign="top">
      <?php if ($userper[34] == 1){{} ?>
      <p class="sidetitle">Photo Gallery </p>
      <p class="side"> 
        <?php if ($userper[35] == 1){{} ?>
        <a href="photo_list.php" class="side">View/Edit Gallery</a><br>
        <?php if ($userper[35] == 1){}} ?>
        <?php if ($userper[36] == 1){{} ?>
        <a href="photo_edit.php" class="side">Add Image</a> <br>
        <?php if ($userper[36] == 1){}} ?>
        <?php if ($userper[37] == 1){{} ?>
                <a href="photo_typelist.php" class="side">Gallery Types</a> <br>
        <?php if ($userper[37] == 1){}} ?>
      </p>
      <?php if ($userper[34] == 1){}} ?>
	  <?php if ($userper[41] == 1){{} ?>
      <p class="sidetitle">Ride Board</p>
      <p class="side"> <a href="ride_list.php" class="side">View/Edit Rideboard</a><br>
        <a href="ride_edit.php" class="side">Add Ride</a></p>
      <?php if ($userper[41] == 1){}} ?>
      <?php if ($userper[38] == 1){{} ?>
      <p class="sidetitle">E-Mail Alert 
      <p class="side"> 
        <?php if ($userper[39] == 1){{} ?>
        <a href="email_lists.php" class="side">View/Edit/Send to List</a><br>
        <a href="email_search.php" class="side">Build Custom List</a><br>
        <a href="email_search.php" class="side">Search List</a><br>
        <?php if ($userper[39] == 1){}} ?>
        <?php if ($userper[40] == 1){{} ?>
        <a href="email_listsedit.php" class="side">Add List</a><br>
        <?php if ($userper[40] == 1){}} ?>
        <a href="emailedit.php" class="side">Add Subscriber</a><br>
        <a href="email_list.php" class="side">View All Subscribers</a> </p>
      <?php if ($userper[38] == 1){}} ?>
      
      <!--  <p class="sidetitle">Poll System</p>
            <p class="side"><a href="poll_list.php" class="side">Activate Poll</a><br>
              <a href="../poll/admin.php" class="side">View/Edit/Add Poll</a></p>-->
      <!--     <p class="sidetitle">Endorsements</p>
            <p class="side"><a href="endorse_list.php" class="side">View/Edit 
              Endorsements</a></p>
		 <!-- 	   <p class="sidetitle">Pledges</p>
            <p class="side"><a href="pledge_list.php" class="side">View/Edit 
              Pledge</a></p> -->
      <!--    <p class="sidetitle">Local Groups</p>
            <p class="side"><a href="groups_list.php" class="side">Veiw/Edit Local 
              Groups</a><br>
              <a href="../groupadd.php" class="side">Add Local Group</a><br>
              <a href="calendar_arealist.php" class="side">View/Edit Area</a><br>
			   <a href="calendar_area.php" class="side">Add Area</a><br>
          </p> -->
         <?php if ($userper[85] == 1){{} ?>
    <p class="sidetitle">Docs and Images
    <p class="side"> 
     
      <a href="docdir.php" class="side">View Documents</a><br>
    <a href="doc_upload.php" class="side">Upload Documents</a><br>
       <a href="imgdir.php" class="side">View Images</a><br>
    <a href="imgup.php" class="side">Upload Images</a>
    </p>
    <?php if ($userper[85] == 1){}} ?>
	  
	  <?php if ($userper[53] == 1){{} ?>
      <p class="sidetitle">User Data Mods 
      <p class="side"> 
        <?php if ($userper[54] == 1){{} ?>
        <a href="modfields_list.php" class="side">View/Edit User Data Mods</a><br>
        <?php if ($userper[54] == 1){}} ?>
        <?php if ($userper[55] == 1){{} ?>
        <a href="modfields2.php" class="side">Add User Data Mod</a><br>
        <?php if ($userper[55] == 1){}} ?>
      </p>
      <?php if ($userper[53] == 1){}} ?>
      <?php if ($userper[44] == 1){{} ?>
      <p class="sidetitle">System Settings</p>
      <p class="side"> 
        <?php if ($userper[45] == 1){{} ?>
        <a href="moduletext_list.php" class="side">View Edit Module Text</a><br>
        <?php if ($userper[45] == 1){}} ?>
        <?php if ($userper[46] == 1){{} ?>
        <a href="moduletext_edit.php" class="side">Add Module Text</a> <br>
        <?php if ($userper[46] == 1){}} ?>
        <?php if ($userper[47] == 1){{} ?>
        <a href="nav_list.php" class="side">View/Edit Nav File</a> <br>
        <?php if ($userper[47] == 1){}} ?>
        <?php if ($userper[48] == 1){{} ?>
        <a href="nav_edit.php" class="side">Add Nav File</a><br>
        <?php if ($userper[48] == 1){}} ?>
        <?php if ($userper[49] == 1){{} ?>
        <a href="template_list.php" class="side">View Edit Design Template</a> <br>
		 <a href="template_edit.php" class="side">Add Design Template</a> <br>
        <?php if ($userper[49] == 1){}} ?>
        <?php if ($userper[77] == 1){{} ?>
        <a href="permissions_list.php" class="side">System Permisssions</a> <br>
        <?php if ($userper[77] == 1){}} ?>
        <?php if ($userper[51] == 1){{} ?>
        <a href="user_list.php" class="side">System Users</a> <br>
        <?php if ($userper[51] == 1){}} ?>
        <?php if ($userper[52] == 1){{} ?>
        <a href="sysvar.php" class="side">System Settings</a> 
        <?php if ($userper[52] == 1){}} ?>
      </p>
      <?php if ($userper[44] == 1){}} ?>
    </td>
  </tr>
  <tr> 
    <td class="manegment"> </td>
    <td> </td>
  </tr>
  <tr bgcolor="#006699"> 
    <td colspan="3"><p align="center"> <font color="#000000" face="Verdana, Arial, 
Helvetica, sans-serif" size="1"> <font color="#FFFFFF">AMP<?php echo $sysversion ?></font></font><font color="#FFFFFF" face="Verdana, Arial, 
Helvetica, sans-serif" size="1"> for <?php echo $SiteName ; ?> <br>
        Please report problems to <a 
href="mailto:<?php echo $admEmail ?>" class="toplinks"><?php echo $admEmail ?></a></font> </p >
      </td>
  </tr>
</table>
  
  
