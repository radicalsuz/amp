<?php
require_once( 'AMP/System/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');
require_once ( 'AMP/UserData/Set.inc.php' );

class ScheduleItem_Form extends AMPSystem_Form {

    function ScheduleItem_Form() {
        $name = "AMP_ScheduleItem";
        $this->init( $name );
        if ($this->addFields( $this->getFields())) {
            $this->setDynamicValues();
        }
    }

    function setResource( $resource_name ) {
        $this->resource_name = $resource_name;
    }

    function setDynamicValues() {
        $reg = &AMP_Registry::instance();
        $userset = new UserDataSet( $reg->getDbcon(), 50, TRUE);
        $userset->doAction('Search');
        $this->setFieldValueSet( 'owner_id',  $userset->getNameLookup());
    }

    function getFields() {
        $fieldsource = & new AMPSystem_XMLEngine( "Modules/Schedule/Fields" );

        if ( $fields = $fieldsource->readData() ) return $fields;

        return false;

    }
}
?>

