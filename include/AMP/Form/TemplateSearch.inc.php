<?php

require_once ( 'AMP/Form/Template.inc.php' );

class AMPFormTemplate_Search extends AMPFormTemplate {

    var $pattern_defs = array(
        'default' => array('spanlabel', 'spanelement'),
        'checkbox' => array( 'spanelement', 'spanlabel' ),
        'static' => array( 'label' ),
        'header'    =>	array('startrow','spanheader','endrow')

        );

    var $element_css_classes = array (
        'label' => 'searchform_label',
        'element' => 'searchform_element',
        'header' => 'searchform_header',
        'table' => 'searchform_table' );

    var $element_css_keys = array(
        'default' => array ('label', 'element'),
        'checkbox' => array( 'element', 'label'),
        'header' => array( 'table', 'header' ),
        'static' => array( 'header' ),
        'separator' => array ('table' )
        );
    
    function AMPFormTemplate_Search() {
        $this->init();
    }

	function getTemplate( $type=null, $separator = null ) {
        if (!isset($type)) return false;
		$template_method =  "_getTemplate".ucfirst($type);
		if (!method_exists($this, $template_method)) $template_method = '_getBaseTemplate';
        $template = $this->$template_method( $type );

        if ($separator) $template = $this->_addSeparator( $template, $separator );

        return $template;

	}

    function _addSeparator( $template, $separator ) {
        if ($separator == 'endform' ) return $template . $this->pattern_parts['endform'];

        $separator_pattern = $this->pattern_parts[ $separator ];
        $separator_template = $separator_pattern;
        if (!(strpos( $separator_pattern, 'class=')===FALSE)) {
            $css_keys = array_combine_key ($this->element_css_keys[ 'separator' ], $this->element_css_classes );
            $separator_template = vsprintf( $separator_pattern, $css_keys );
        }
        return $separator_template . $template ;
    }
        
}
?>
