<?php

/*****
 *
 * AMP Contact Database Utility Functions
 *
 * Provides database access functions for contact DB.
 * (c) 2004 - Radical Designs, www.radicaldesigns.org
 *
 * v0.1
 *
 * Author: Blaine Cook - blaine@radicaldesigns.org
 *
 * Licenced under the GPL.
 * See COPYING file for licensing information.
 *
 *****/

/*****
 *
 * array getContactRecords( ADOdb DB [, [int id] [int offset [, int num]] )
 *
 * Returns an array of associative arrays, each one representing a contact
 * record.
 *
 *****/

function getContactRecords( $DB = null, $id = null, $limit = null, $offset = null ) {

  // Create WHERE clause to narrow down our search.
  // This is combined with the default WHERE arguments by way of an AND
  // conjunction.
  //
  // currently, this is extremely simple. It should be extended to a
  // "query-by-example", with a contact object being passed to this function,
  // so that the query can be built based on the filled object attributes.

  $sqlw = '';
  if ( preg_match( "/^\d+$/", $id ) ) $sqlw .= "AND contacts2.id = '$id'";

  // LIMIT clause.

  $sqll = '';
  if ( preg_match( "/^\d+$/", $limit ) ) {

    $sqll = "LIMIT $limit";
    if ( preg_match( "/^\d+$/", $offset ) ) $sqll .= " OFFSET $offset";

  }

  $sql = "SELECT contacts2.*, " .
                "enterers.name AS enteredby, " .
                "modifiers.name AS modifiedby, " .
                "source.title AS source, " .
                "region.title AS region, " .
                "contacts_rel.value AS rel_value, " .
                "contacts_rel.fieldid AS rel_fieldid, " .
                "contacts_fields.name AS rel_fieldname " .
          "FROM contacts2, contacts_fields " .
            "LEFT JOIN users AS enterers ON contacts2.enteredby = enterers.id " .
            "LEFT JOIN users AS modifiers ON contacts2.modifiedby = modifiers.id " .
            "LEFT JOIN source ON contacts2.source = source.id " .
            "LEFT JOIN region ON contacts2.regionid = region.id " .
            "LEFT JOIN contacts_rel ON contacts2.id = contacts_rel.perid " .
          "WHERE " .
                "contacts_rel.fieldid = contacts_fields.id " .
                $sqlw .
          "ORDER BY contacts2.id, contacts_fields.id $sqll";

  $oldmode = $DB->fetchMode;
  $DB->SetFetchMode( ADODB_FETCH_ASSOC );
  $rs = $DB->Execute( $sql )
	or die("Couldn't get contact records: " . $DB->ErrorMsg() );
  $DB->SetFetchMode( $oldmode );

  $cfRes = getContactCustomFields( $DB );

  $customFields = array();
  foreach ( $cfRes as $campaign ) {
    foreach ( $campaign as $field ) {
      $customFields[ $field ] = ' ';
    }
  } 

  // Now that we have the results, cycle through and fetch all of the rows, and
  // combine all of the custom fields into a single array. Place each of these
  // arrays (eventually "contact-person-action" objects) into a single
  // return-array for use by whatever.

  $entries = array();

  $last_id = null;
  $entry = array();
  while ( !$rs->EOF ) {

    // Grab the current working ID. If it's different, we have a new person,
    // so put the old record into the array, and clean up the working space.
    $current_id = $rs->Fields( "id" );
    if ( $current_id != $last_id && $last_id ) {
      $entries[] = $entry;
      $entry = array();
    }

    if ( count( $entry ) == 0 ) {

      // We're on our first run of this, or the last run didn't work. Populate
      // the array with the current record.

      $entry = $rs->fields;
      unset( $entry[ 'rel_value' ], $entry[ 'rel_fieldid' ], $entry[ 'rel_fieldname' ] );

      $entry = array_merge( $entry, $customFields );

    }

    // Push in the custom fields.
    //
    // Currently we only support single-valued custom fields. At some point,
    // this support should be expanded for multi-valued custom fields.

    $fieldname = $rs->Fields( 'rel_fieldname' );
    $fieldvalue = $rs->Fields( 'rel_value' );
    if ($fieldvalue != "") $entry[ $fieldname ] = $fieldvalue;

    // All finished, so we re-seed the last_id and clean current_id.
    $last_id = $current_id;
    $current_id = null;

    $rs->MoveNext();

  }

  $entries[] = $entry;

  return $entries;

}

/*****
 *
 * array getContactRecord( ADODbConn DB, int id )
 * 
 * returns an array containing a complete contact record.
 *
 *****/

function getContactRecord( $DB = null, $id = null ) {

  return getContactRecords( $DB, $id );

}

/*****
 *
 * array getContactCustomFields( ADODbConn DB )
 *
 * Generates an ordered set of campaigns and their constituent fields.
 *
 *****/

function getContactCustomFields( $DB = null ) {

  if (!$DB) return array();

  $sql = "SELECT contacts_campaign.name AS campaign, " .
                "contacts_fields.name AS field " .
         "FROM contacts_campaign " .
          "LEFT JOIN contacts_fields " .
            "ON contacts_campaign.id = contacts_fields.camid " .
         "ORDER BY contacts_campaign.fieldorder, " .
                  "contacts_fields.fieldorder";

  $rs = $DB->Execute( $sql )
     or die( "Couldn't execute $sql " . $DB->ErrorMsg() );

  $lastCampaign = null;
  while ( !$rs->EOF ) {

    $thisCampaign = $rs->Fields( 'campaign' );

    if ( $lastCampaign != $thisCampaign && $lastCampaign ) {

      $retarray[ $lastCampaign ] = $fieldsTmp;
      $fieldsTmp = array();

    }

    $fieldsTmp[] = $rs->Fields( 'field' );

    $lastCampaign = $thisCampaign;
    $rs->MoveNext();

  }

  $retarray[ $lastCampaign ] = $fieldsTmp;

  return $retarray;
}

?>
