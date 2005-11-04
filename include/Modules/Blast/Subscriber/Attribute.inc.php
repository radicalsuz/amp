<?

require_once( 'Modules/Blast/ComponentMap.inc.php');
require_once( 'AMP/System/Data/Item.inc.php');

class BlastSubscriber_Attribute extends AMPSystem_Data_Item {
    var $datatable = PHPLIST_TABLE_USER_ATTRIBUTE; 
    var $id_field = array( 'userid' , 'attributeid' );
    var $_attribute_defs =  array( 
            '1'=>'First_Name',
            '2'=>'Country',
            '20'=>'Zip',
            '19'=>'Company',
            '13'=>'Street',
            '14'=>'City',
            '12'=>'Last_Name',
            '22'=>'State',
            '23'=>'notes',
            '24'=>'Fax',
            '25'=>'Phone',
            '26'=>'Street_2',
            '27'=>'Web_Page'
        );

    function BlastSubscriber_Attribute( $dbcon ) {
         $this->init( $dbcon );
    }

    function getAttributeName( $id ) {
        if ( !isset( $this->_attribute_defs[$id])) return false;
        return $this->_attribute_defs[$id];
    }

    function getAttributeId( $name ) {
        $key = array_search( $name, $this->_attribute_defs );
        if ( $key === FALSE) return false;
        return $key;
    }

    function setAttribute( $value, $attribute_id, $user_id ) {
        return $this->setData(  array( 
            'attributeid' =>    $attribute_id,
            'userid'      =>    $user_id,
            'value'       =>    $value ));
    }

}

?>
