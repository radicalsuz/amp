<?php

class AMPSystem_XMLEngine {

    var $AMP_Object_Type;
    var $filename;

    function AMPSystem_XMLEngine( $filename, $AMP_Object_Type = null ) {
        $this->AMP_Object_Type = $AMP_Object_Type;
        $this->filename = $filename;
    }


    function readData() {
        require_once('XML/Unserializer.php');
        $xmlEngine = & new XML_Unserializer();

        $file_data = $this->getFile( );
        if ( !$file_data ) return false;

		$status = $xmlEngine->unserialize( $file_data );
        if (!PEAR::isError($status)) {
            return $xmlEngine->getUnserializedData();
        }
		trigger_error($status->getMessage());
        return false;
    }

    function save( $data ) {
        require_once('XML/Serializer.php');
        $xmlEngine = & new XML_Serializer( array(  XML_SERIALIZER_OPTION_DEFAULT_TAG => 'values', XML_SERIALIZER_OPTION_INDENT => '    '));
        $xmlresult = $xmlEngine->serialize( $data );

        $locale = AMP_LOCAL_PATH . '/custom/' . $this->describeFile('_');
        //$this->saveFile( $xmlEngine->getSerializedData(), AMP_pathFlip( $locale ));
        $this->saveFile( $xmlEngine->getSerializedData(), ( $locale ));
    }

    function describeFile($separator = DIRECTORY_SEPARATOR) {

        if ((!isset ($this->AMP_Object_Type)) || !$this->AMP_Object_Type) {
			if (substr( $this->filename, -4) == '.xml') return $this->filename;
			return $this->filename . '.xml';
		}
        return $this->AMP_Object_Type .$separator . $this->filename . '.xml';
    }
    function locateFile ( ) {
        $test_locations = array(
            $this->describeFile('_') , 
            ('AMP/System/' . $this->describeFile()),
            ('Modules/' . $this->describeFile())
        );

        foreach ($test_locations as $loc_attempt) {
            if ( file_exists_incpath ($loc_attempt)) return $loc_attempt;
        }
        return false;
    }


    function saveFile( $data, $filename ) {
        $fp = fopen( $filename , "w+"); 
        $test = fwrite($fp, $data); 
        fclose ($fp); 		
//        if ( AMP_SYSTEM_FILE_OWNER ) chown( $filename, AMP_SYSTEM_FILE_OWNER );
    }

    function getFile ( ) {
        //$fullpath = AMP_pathFlip( $this->locateFile() );
        $fullpath = ( $this->locateFile() );
        if (!file_exists_incpath( $fullpath )) return false;
        return file_get_contents( $fullpath, true );
    }

}
?>
