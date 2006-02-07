<?php 
/*********************
 * contactus.php
 *
 * Standard Contact Page
 * @author Austin Putman <austin@radicaldesigns.org>
 * @version AMP 3.5.8
 * @date    2006-02-06
 */ 

if ( !defined( 'AMP_MODULE_ID_CONTACT_US')) define( 'AMP_MODULE_ID_CONTACT_US', 17 );
if ( !defined( 'AMP_INTROTEXT_ID_CONTACT_US')) define( 'AMP_INTROTEXT_ID_CONTACT_US', 52 );
if ( !defined( 'AMP_INTROTEXT_ID_CONTACT_US_RESPONSE')) define( 'AMP_INTROTEXT_ID_CONTACT_US_RESPONSE', 53 );
$modid = AMP_MODULE_ID_CONTACT_US;

require_once("AMP/BaseDB.php");
require_once( 'Modules/Contact/Form.inc.php');

$form = &new ContactForm( );
$showForm = !( $form->submitted( ) && $form->validate( ));
$intro_id = $showForm ? AMP_INTROTEXT_ID_CONTACT_US :
                        AMP_INTROTEXT_ID_CONTACT_US_RESPONSE ;

require_once("AMP/BaseTemplate.php");
require_once("AMP/BaseModuleIntro.php");  
if ( !isset( $MM_email_contact)) $MM_email_contact = false;
if ( !defined( 'AMP_SITE_EMAIL_CONTACT')) define( 'AMP_SITE_EMAIL_CONTACT', $MM_email_contact );

$form->Build( );
if ( $showForm ) {
    $form->enforceRequiredFields( );
    print $form->output( );
} elseif ( AMP_SITE_EMAIL_CONTACT ){
  require_once( 'AMP/System/Email.inc.php');
  $email_maker = &new AMPSystem_Email( );
  $email_maker->setRecipient( AMP_SITE_EMAIL_CONTACT );
  $data = $form->getValues( );
  $email_maker->setMessage( $data['message'] );
  $email_maker->setSender( $data['sender_email'] );
  $email_maker->setSubject( $data['subject'] );
  $email_maker->execute( );

} else {
    print AMP_TEXT_ERROR_TOOL_NOT_CONFIGURED;
}

require_once("AMP/BaseFooter.php");
/*
if ( (isset($_POST['send']) && $_POST["send"]) && $MM_email_contact ) {    
	mail ( $MM_email_contact, $_POST["subject"], $_POST["message"], "From: ".$_POST["email"]." \nX-Mailer: My PHP Script\n");
		
}

if (!isset($_POST['thank']) || $_POST["thank"] == NULL) { ?>
<form method="post" action="<?php $PHP_SELF."?thank=1" ; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="5" class="form">
  <tr> 
    <td valign="top">Your E-Mail:</td>
    <td><input name="email" type="text" id="email" size="40"></td>
  </tr>
  <tr> 
    <td valign="top">Subject:</td>
    <td><input name="subject" type="text" id="subject" size="40"></td>
  </tr>
  <tr valign="top"> 
    <td colspan="2"><p>Message:<br>
          <textarea name="message" cols="48" rows="25" wrap="VIRTUAL" id="message"></textarea>
          <input name="send" type="hidden" id="send" value="1">
		    <input name="thank" type="hidden" id="send" value="1">
        </p>
        <p>
          <input type="submit" name="Submit" value="Send E-Mail">
        </p></td>
  </tr>
</table>
</form>
<?php
}
*/ 
?>
