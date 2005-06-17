<?php
function e($type, $msg, $file, $line)
{
	if ($type != 8) {
	// read some environment variables
	// these can be used to provide some additional debug information
	global $HTTP_HOST, $HTTP_USER_AGENT, $REMOTE_ADDR, $REQUEST_URI;

	// construct the error string 
	$errorString = "Date: " . date("d-m-Y H:i:s", mktime()) . "\n";
	$errorString .= "Error type: $type\n";
	$errorString .= "Error message: $msg\n";
	$errorString .= "Script: $file($line)\n";
	$errorString .= "Host: $HTTP_HOST\n";
	$errorString .= "Client: $HTTP_USER_AGENT\n";
	$errorString .= "Client IP: $REMOTE_ADDR\n";
	$errorString .= "Request URI: $REQUEST_URI\n\n";
	
	// log the error string to the specified log file
	
	// discard current buffer contents
	// and turn off output buffering
	ob_end_clean();

	// display error page
	echo "<html><head><basefont face=Arial></head><body>";

	echo "<h1>Error!</h1>\n\n<pre>";

	echo $errorString . "</pre>\n\n";

	echo "<p>We're sorry, but this page could not be displayed because of an internal error. The error has been recorded and will be rectified as soon as possible. Our apologies for the inconvenience.</p> <p> <a href=/>Click here to go back to the main menu.</a></p>";

	echo "<pre>";
	print_r( debug_backtrace() );
	echo "</pre>";

	echo "</body></html>";
	
	// exit		
	exit();
	}
}

set_error_handler( "e" );

require_once("Connections/freedomrising.php");
require_once("contact_functions.php");

// Get all the contacts from the database.
$contacts = getContactRecords( $dbcon );

// Get all the custom field names, mapped in appropriate campaign sections.
$campaigns = getContactCustomFields( $dbcon );

$entries = "";
$campaign_header = array();
$field_header = array();
foreach ( $campaigns as $campaign => $fields ) {

  // build the campaigns definition header, including blank spaces for each
  // field within the campaign.
  $campaign_header[] = $campaign;
  for ( $i = 1; $i < count( $fields ); $i++ ) {
    $campaign_header[] = '';
  }

  // the fields definition header.
  foreach ( $fields as $field ) {
    $field_header[] = $field;
  }

}

$default_header = null;
foreach ( $contacts as $contact ) {

  $entry = array();

  // Ignore the campaigns, and just fill in the fields.
  foreach ( $campaigns as $fields ) {

    foreach ( $fields as $field ) {

      if ( isset( $contact[ $field ] ) ) {

        $entry[] = $contact[ $field ];
        unset( $contact[ $field ] );

      } else {
        $entry[] = '';
      }

    }

  }

  // Fill in the header with the default field names. Make space in the
  // campaigns header.
  if ( !$default_header ) {

    foreach ( array_keys( $contact ) as $header ) {
      $field_header_tmp[] = $header;
      $campaign_header_tmp[] = '';
    }
    
    $field_header = array_merge( $field_header_tmp, $field_header );
    $campaign_header = array_merge( $campaign_header_tmp, $campaign_header );
    $campaign_header[0] = 'Default Fields';

    $default_header = 1;

  }

  $out = array_map( "csvEscape",  array_merge( $contact, $entry ) );

  $entries .= join( ",", $out ) . "\n";

}

$header  = "# AMP Contacts Database CSV export\n";
$header .= "# Generated " . date( "r" ) . "\n";
$header .= join( ",", array_map( "csvEscape", $campaign_header ) ) . "\n";
$header .= join( ",", array_map( "csvEscape", $field_header ) ) . "\n";

$output = $header . $entries;

header( 'Content-type: text/csv' );
header( 'Content-Disposition: attachment; filename=AMPContactExport' . date( "Ymd" ) . '.csv' );

print $output;

ob_end_flush();

function csvEscape( $text ) {

  $match   = array( "\"",   "\n", "\r" );
  $replace = array( "\"\"", " ",  " " );

  $text = str_replace( $match, $replace, $text );

  return '"' . $text . '"';

}

?>
