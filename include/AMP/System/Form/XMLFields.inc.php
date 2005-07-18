<?php

class AMPSystem_Form_XMLFields {

    var $AMP_Object_Type;
    var $DataDescription = "Fields";

    function AMPSystem_Form_XMLFields( $AMP_Object_Type, $DataDescription = "Fields" ) {
        $this->AMP_Object_Type = $AMP_Object_Type;
        $this->DataDescription = $DataDescription;
    }


    function readData() {
        require_once('XML/Unserializer.php');
        $xmlEngine = & new XML_Unserializer();

        if ($xmlEngine->unserialize( $this->getFile() ) ){;
            return $xmlEngine->getUnserializedData();
        }
        return false;
    }

    function save( $data ) {
        require_once('XML/Serializer.php');
        $xmlEngine = & new XML_Serializer();
        $xmlresult = $xmlEngine->serialize( $data );

        $locale = AMP_LOCAL_PATH . '/custom/' . $this->AMP_Object_Type .'_' . $this->DataDescription .'.xml';
        $this->saveFile( $xmlEngine->getSerializedData(), $locale );
    }

    function locateFile ( ) {
        $xmlfilename = $this->DataDescription.'.xml';
        $test_locations = array(
            ($this->AMP_Object_Type .'_'. $xmlfilename),
            ('AMP/System/' . $this->AMP_Object_Type . '/' . $xmlfilename),
            ('Modules/' . $this->AMP_Object_Type . '/' . $xmlfilename)
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
    }

    function getFile ( ) {
        $fullpath = $this->locateFile();
        if (!file_exists_incpath( $fullpath )) return false;
        return file_get_contents( $fullpath, true );
    }

}
?>
