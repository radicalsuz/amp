<?php

require_once ( 'Modules/Blast/ComponentMap.inc.php');

class ComponentMap_BlastList extends ComponentMap {

    var $heading = "Email Lists";
    var $nav_name = "blast";
    
    var $paths = array( 
        'fields'    => 'Modules/Blast/List/Fields.xml',
        'list'      => 'Modules/Blast/List/List.inc.php',
        'form'      => 'Modules/Blast/List/Form.inc.php',
        'source'    => 'Modules/Blast/List.inc.php');
    var $components = array(
        'form' => 'BlastList_Form',
        'list' => 'BlastList_List',
        'source' => 'BlastList' );

}
?>
