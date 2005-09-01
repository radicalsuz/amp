<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_IntroText extends AMPSystem_ComponentMap {

    var $heading = "Intro Text";
    var $nav_name = "tools";

    var $paths = array(
        'form' => 'AMP/System/IntroText/Form.inc.php',
        'list' => 'AMP/System/IntroText/List.inc.php',
        'copier' => 'AMP/System/IntroText/Copy.inc.php',
        'source' => 'AMP/System/IntroText.inc.php' );

    var $components = array (
        'form' => 'AMPSystem_IntroText_Form',
        'list' => 'AMPSystem_IntroText_List',
        'copier' => 'AMPSystem_IntroText_Copy',
        'source' => 'AMPSystem_IntroText' );

}
?>
