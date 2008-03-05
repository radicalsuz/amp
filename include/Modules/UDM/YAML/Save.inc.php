<?php

require_once ('AMP/UserData/Plugin/Save.inc.php');
require_once ('spyc/spyc.php');

class UserDataPlugin_Save_YAML extends UserDataPlugin_Save {

    var $name = 'Save YAML Data';
    var $description = 'Save user data to a YAML file';
    var $available = true;

    var $options = array( 
            'save_to_folder' => array( 
                'type' => 'text',
                'label' => 'Folder to save files to',
                'default' => 'yaml',
                'available'=>true,
            )
        );

    function UserDataPlugin_Save_YAML ( &$udm , $plugin_instance=null) {
        $this->init( $udm, $plugin_instance );
    }



    function save( $data ) {
        $yaml_data = $this->udm->getData( ) ;
        if ( isset( $yaml_data['Zip']) && $yaml_data['Zip']) {
            $yaml_data['Zip'] = '"'.$yaml_data['Zip'].'"';
        }
        $yaml_dump = Spyc::YAMLDump( $yaml_data );

        $filename = $this->get_filename( $this->udm->uid );
        if ( !is_writeable( $this->get_foldername( ))) {
            trigger_error( sprintf( AMP_TEXT_ERROR_FILE_WRITE_FAILED, $filename ));
            return false;
        }

        $f = fopen(  $filename, 'w');
        fwrite(  $f, $yaml_dump );
        fclose(  $f );
        define ( "FORM_{$this->udm->instance}_YAML_FILENAME", $filename );
        return true;

    }

    function get_foldername( ) {
        $options = $this->getOptions( );
        return AMP_pathFlip( AMP_LOCAL_PATH . '/' . $options['save_to_folder']);
    }

    function get_filename( $id ) {
        $secret = substr( sha1( time( ) . $_SERVER['HTTP_USER_AGENT'] ), 0, 15);
        return $this->get_foldername( ) . DIRECTORY_SEPARATOR . $id. '-' . $secret . '.yml';
    }

    function getSaveFields(  ) {
        return array_keys( $this->udm->fields);
    }


}

?>

