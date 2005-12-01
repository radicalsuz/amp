<?phprequire_once ( 'AMP/System/D/Users/David/Sites/sms_data/grab.phpata/Item.inc.php' ); class AMPSystem_Podcast extends AMPSystem_Data_Item {    var $datatable = "podcast";    var $name_field = 'name';    function AMPSystem_Podcast ( &$dbcon, $id=null ) {        $this->init( $dbcon, $id );    }     }

class Podcast {

    function Podcast( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function convert_time($base) {
        $base = trim($base);
        $sec = substr($base, -2 ) ;
        $min = (60 * substr($base, 0,-3 ) );
        $time = $min + $sec ;
       return $time;
    
    }
    
    
}?>