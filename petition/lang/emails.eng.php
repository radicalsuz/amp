<?php

# Emails
	// Alert Message sent to friends
	$email_message = "Please consider adding your name to the on-line petition: $title. 

Please go to $base_url/index.php?id=$id \n add your name and pass it on!

This message is being sent to you by a friend.  Your name has not been added to a mailing list and you will not be contacted again.  

$organization is merely providing a means for the public to raise concerns about this issue.  If you have received this note in error, we apologize.";
	
$confirm_email_message = "
Hello,

To finish signing the $title, please click the link below. This should open a browser window and allow you to confirm that your email address is valid and that you have endorsed the appeal.  Thanks..
	$base_url/verify.php?id=$id&PID=$PID&Verified=$Verified

You can also copy this URL into any web browser to confirm that you recieved this message.

If you would like to check if your email has confirmed or if you would like more information about this appeal, please go to this URL:
	$base_url/index.php?id=$id";
?>