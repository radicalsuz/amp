<?php

require_once( "Connections/freedomrising.php" );
require_once( "XML/Serializer.php" );

$dbcon->SetFetchMode( ADODB_FETCH_ASSOC );

$user_sql = "SELECT * FROM phplist_user_user";
$attribute_sql = "SELECT * FROM phplist_user_user_attribute LEFT JOIN phplist_user_attribute ON phplist_user_user_attribute.attributeid = phplist_user_attribute.id";
$list_sql = "SELECT * FROM phplist_listuser";

$user_rs = $dbcon->Execute( $user_sql );
$attr_rs = $dbcon->Execute( $attribute_sql );
$list_rs = $dbcon->Execute( $list_sql );

while ( $row = $user_rs->FetchRow() ) {
    $subdata[ $row[ 'id' ] ] = $row;
}

while ( $row = $attr_rs->FetchRow() ) {
    $attrname = str_replace( " ", "_", $row['name'] );
    $subdata[ $row[ 'userid' ] ][ $attrname ] = $row[ 'value' ];
}

while ( $row = $list_rs->FetchRow() ) {
    $lists[ $row['userid'] ][] = $row[ 'listid' ];
    $subdata[ $row[ 'userid' ] ][ 'lists' ] = "____LIST" . $row['userid'] . "____";
}

$serializer = new XML_Serializer();
$serializer->setOption( "addDecl", true );
$serializer->setOption( "indent", "    " );
$serializer->setOption( "rootName", "users" );
$serializer->setOption( "defaultTagName", "user" );

$result = $serializer->serialize( $subdata );

print "<pre>";
print_r( $subdata );


if ( $result === true ) {
    $xmlout = $serializer->getSerializedData();
}

$listserializer = new XML_Serializer();
$listserializer->setOption( "indent", "    " );
$listserializer->setOption( "defaultTagName", "list" );

foreach ( $lists as $userid => $listids ) {

    $listserializer->serialize( $listids );
    $listout = $listserializer->getSerializedData();

    $xmlout = str_replace( "____LIST{$userid}____", $listout, $xmlout );

}

print htmlentities( $xmlout );

print "</pre>";

?>
