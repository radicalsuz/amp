<?php
## General Information

  
  $stuff=$dbcon->CacheExecute("SELECT * FROM petition where id =$id") or DIE($dbcon->ErrorMsg());
   $stuff_numRows=0;
   $stuff__totalRows=$stuff->RecordCount();
  
  

$title  = $stuff->Fields("title");
	
$meta_description  = 
	'$title';

$meta_keywords  = 
	'WTO, Trade, CFWTO';

// Old Data Varables
	$header_title = $title;
	$recipients = $stuff->Fields("addressedto");
	$remail = "";
	$author = $stuff->Fields("intsigner");
	$author_email = $stuff->Fields("intsignem");
	$scope = "Canada";
	
// Default Web Site Email
$owner_name=$stuff->Fields("intsigner");
	$owner_email=$stuff->Fields("intsignem");		//  Define Owner Email
	$organization=$stuff->Fields("org");
	$orgurl=$stuff->Fields("url");

$language_version  = 
	'English - English';

$charset  = 
	'iso-8859-1';

// Date petition was started
$sub_date = DoDate($stuff->Fields("datestarted"), 'F jS, Y');

$petition_started  = 
	"Posted: $sub_date ";
$day = 233; 						        // Day 
$month = 15;						     // Month 
$year = 2012;						// Year 

# Top Box
$verified_signatures  = 
	'Verified Signatures:';
$petition_enddate =$stuff->Fields("dateended");
//$petition_ends  = 	'End Date: $petition_enddate';



# Index Page
$recent_signatories = 
	'Recent Signatures';

# Sign-up Form
$from = 
	'from';

$name = 
	'Name';

$first_name = 
	'First Name';

$last_name = 
	'Last Name';

$group_affiliation = 
	'Group Affiliation';

$not_endorsement = 
	'(This does not imply group endorsement)';

$signor_email = 
	'Email Address';

$address = 
	'Address';

$city = 
	'City';
	
$province = 
	'Province';
	
$state = 
	'State';

// $country = 'Country';

$postal_code = 
	'Postal Code';

$comments = 
	'Comments';

$max255 = 
	'(Maximum size 255 characters)';

$required_fields = 
	'Required fields are shown in <B><font color="#FF0000">red</font></B>';

$updates_ok = 
	'Can we send you occasional updates on this issue?  <br><I>We will not share your email address with anyone.</I>';

$display_ok = 
	'Would you like your name to be displayed in public?  <br><I>Only name, city & comments will be publicly displayed</I>';

$yes = 
	'Yes';

$submit = 
	'Add my name to this petition';

$success = 
	'Successful Submission';
	if ($EmailConfirm != 1) {
	$successMessage = $success;}
	else {
$successMessage = "<b><strong><font color=red>Important:</font></strong> In a moment, an email with the subject \"Please Verify Signature for $header_title\" will be sent to the address you have provided. To finish signing this petition, you <strong>MUST</strong> follow the simple instructions in that email. (This verifies your identity,  boosting this petition's credibility. Thanks!) </center>"; }

// $acknowledgement = 
	' I endorse this pledge.';

//$important = 
//	'<B>Important:</B> In a moment, an email will be sent to the address you\'ve provided.  To finish signing, please follow the instructions.';

$privacy_note = 
	'Only your name, city, state and comments will be publicly displayed.  Please be assured that none of the information you provide will be used for any other purpose or passed to any other organization.';

# Verify email or resend note
$verify_box_text = 
	'If you wish to verify your signature, please fill in your email address';

$verify_status = 
	'Check your status!';

# Navigation
$view_only = 
	'View signatures';

$sign_now = 
	'sign now (if you haven\'t already)';

$if_not_sign_now = 
	'If you have not already done so, please <a href="index.php?id=$id&lang=$lang">sign the pledge!</a>';

$concerned_individuals = 
	'Concerned Individuals:';

$country_stats = 
	'Country Statistics';
	
$daily_stats  = 
	'Daily Statistics';

$faq = 
	'FAQ';

$donate = 
	'Support this pledge';

$back_to_top = 
	'Back to Top';

$next = 
	'Next';

$previous = 
	'Previous';

# Credits
$general_credits = 
	'This pledge was organized by <a href="http://$orgurl" target="_blank">$organization</a>.  Visit our web site to learn more about us.<br>';

// $translated_by = '';

  $stuff->Close();
?>
