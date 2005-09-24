<?php

define(  'AMP_CONTENT_URL_VOTERGUIDE', 'voterguide.php');

require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'Modules/VoterGuide/Position/Set.inc.php');

class VoterGuide extends AMPSystem_Data_Item {

    var $datatable = 'voterguides';
    var $_positions;
    var $_positions_key = 'voterguidePositions';

    function VoterGuide ( &$dbcon, $id=null ) {
        $this->_positionSet = &new VoterGuidePositionSet( $dbcon );
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( $this->_positions_key );
    }

    function readData( $guide_id ) {
        if ( !( $result = PARENT::readData( $guide_id ))) return $result;
        $this->_positionSet->readGuide( $guide_id );
        if ( !$this->_positionSet->hasData()) return $result;
        $this->mergeData( array( $this->_positions_key => $this->_positionSet->getArray()));
        return $result;
    }

    function save() {
        if ( !( $result=PARENT::save())) return $result;
        if ( $result = $this->_positionSet->reviseGuide( $this->getData( $this->_positions_key ), $this->id ) ) return $result;

        $this->addError( $this->_positionSet->getErrors( ));
        return false;

    }

    function &getDisplay( ) {
        require_once( 'Modules/VoterGuide/Display.inc.php');
        return new VoterGuide_Display( $this );
    }

    function getFooter() {
        return $this->getData( 'footer' );
    }

    function getCity( ) {
        return $this->getData( 'city' );

    }

    function getState( ) {
        return $this->getData( 'state' );

    }

    function getLocation( ) {
        $output = "";
        if ( $output = $this->getCity( )) {
            $output .= ", ";
        }
        return $output . $this->getState( );
    }

    function getBlurb( ) {
        return $this->getData( 'blurb' );
    }

    function getItemDate( ) {
        return $this->getData( 'election_date' );
    }

    function &getImageRef( ) {
        if (! ($img_path = $this->getImageFileName())) return false;
        require_once( 'AMP/Content/Image.inc.php');
        return new Content_Image( $img_path );
    }

    function getImageFileName( ){
        return $this->getData( 'picture' );
    }

    function getAffiliation( ) {
        return $this->getData( 'affiliation');
    }

    function &getDocumentRef( ) {
        if (!($doc = $this->getDocumentFileName() )) return false;
        require_once ( 'AMP/Content/Article/DocumentLink.inc.php' );
        return new DocumentLink( $doc );
    }

    function getDocumentFileName( ){
        return $this->getData( 'filelink');
    }

    function getURL( ) {
        if ( !$this->id ) return AMP_CONTENT_URL_VOTERGUIDE;
        return AMP_Url_AddVars( AMP_CONTENT_URL_VOTERGUIDE, 'id='.$this->id );
    }

    function getTitle( ) {
        return $this->getName( );
    }
}
?>
