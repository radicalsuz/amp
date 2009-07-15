<?php
require_once( 'AMP/User/Profile/Profile.php');

class AMP_User_Data extends AMP_User_Profile {
    var $datatable = 'userdata';
    var $_class_name = 'AMP_User_Data';

    function AMP_User_Data( $dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

    function replace_image_references( $existing_name, $new_name ) {
        $image_fields = AMP_lookup( 'userdata_image_fields');
        if( !$image_fields ) return false;
        $user_data = new AMP_User_Data( AMP_dbcon( ));
        foreach( $image_fields as $image_field_desc ) {
            $action = $image_field_desc['fieldname'] . "=" . $user_data->dbcon->qstr( $new_name );
            $crit = $user_data->makeCriteria( array( 'modin' => $image_field_desc['modin'] ));
            array_push( $crit, $user_data->_makeCriteriaEquals( $image_field_desc['fieldname'], $existing_name ));
            $scope = join( ' AND ', $crit);

            $user_data->update_all( $action, $scope );
        }

    }
}
?>
