<?php

require_once ('AMP/System/ComponentMap.inc.php' );

class ComponentMap_Blast extends AMPSystem_ComponentMap {

    var $heading = 'Email Blast';
    var $nav_name = 'email';

    var $paths = array(
        'fields' => 'AMP/System/Blast/Fields.xml',
        'form' => 'AMP/System/Blast/Form.inc.php',
        'source' => 'AMP/System/Blast.inc.php'
        );

    var $components = array(
        'form' => 'Blast_Form',
        'source' => 'AMPSystem_Blast'
        );
}

?>
