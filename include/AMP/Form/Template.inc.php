<?php
// templates instruct QuickForm on how to render the various elements

class AMPFormTemplate {

    var $pattern_parts = array(
        'startrow' =>
			"\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\" class=\"%s\">",
        'endrow' =>
			"</td>\n\t</tr>",
        'endform' =>
			"</td>\n\t</tr>",
        'required' =>
			"<!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->\n",
        'error' =>
			"<!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t",
        'newcolumn' =>
			"</td>\n\t\t<td valign=\"top\" align=\"left\" class=\"%s\">",
        'spanlabelbr' =>
            "<span class=\"%1\$s\">{label}</span><br>",
        'spanlabel' =>
            "<span class=\"%1\$s\">{label}</span>",
        'label' =>
			"{label}",
        'element' =>
            "{element}",
        'spanelement' =>
            "{element}",
        'doublecolumn' =>
		    "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\">",
        'spanheader' =>
            "<span class=\"%s\">{header}</span>",
        'starttable' =>
            "<table class=\"%1\$s\"><tr><td>",
        'endtable' =>
            "</td></tr></table>"

    );

    var $pattern_defs = array(
        'default'   => array(
            'startrow',
            'required',
            'label',
            'newcolumn',
            'error',
            'element',
            'endrow'),

        'checkbox'  => array(
            'startrow',
            'required',
            'element',
            'newcolumn',
            'error',
            'label',
            'endrow'),

        'textarea'  => array(
            'doublecolumn',
            'starttable',
            'required',
            'spanlabelbr',
            'error',
            'element',
            'endtable', 
            'endrow' ),

        'header'    =>	array(
            'doublecolumn',
            'spanheader',
            'endrow'),

        'static'    =>  array(
            'doublecolumn',
            'starttable',
            'element',
            'endtable', 
            'endrow'),

        'submit'    => array(
            'doublecolumn',
            'starttable',
            'spanlabel',
            'element',
            'endtable', 
            'endrow'),

		'checkgroup'=> array(
            'doublecolumn',
            'starttable',
            'required',
            'spanlabelbr',
            'error',
            'element',
            'endtable',
            'endrow'),

		'group'=> array(
            'doublecolumn',
            'starttable',
            'required',
            'spanlabelbr',
            'error',
            'element',
            'endtable',
            'endrow')
    
        );

    var $patterns = array();

    /*
    var $patterns = array(

        'default'   =>
			"\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\" class=\"%s\">
			<!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
			{label}</td>\n\t\t<td valign=\"top\" align=\"left\" class=\"%s\">
			<!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t
			{element}</td>\n\t</tr>",
        'checkbox'  => 
            "\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\" class=\"%s\">
            <!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
            {element}</td>\n\t\t<td valign=\"top\" align=\"left\" class=\"%s\">
            <!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->
            \t{label}</td>\n\t</tr>",
        'textarea'  =>
		    "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"%1\$s\">
            <tr><td><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
            <span class=\"%1\$s\">{label}</span><br>\n\t\t<!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->
            \t{element}</td></tr></table></td>\n\t</tr>",
        'header'    =>	
            "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\" ><span class=\"%s\">{header}</span></td>\n\t</tr>",
        'static'    =>
		    "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"%s\">
            <tr><td>\t{element}</td></tr></table></td>\n\t</tr>",
        'submit'    =>
		    "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"%s\">
            <tr><td><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
            <b>{label}</b><br>\n\t\t<!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->
            \t{element}</td></tr></table></td>\n\t</tr>",
		'checkgroup'=>
            "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"%s\">
            <tr><td><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
            <span class=\"%s\">{label}</span><br>\n\t\t<!-- BEGIN error -->
            <span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t{element}</td></tr></table></td>\n\t</tr>",
		'group'=>
            "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"%s\">
            <tr><td><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
            <span class=\"%s\">{label}</span><br>\n\t\t<!-- BEGIN error -->
            <span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t{element}</td></tr></table></td>\n\t</tr>"
        );
    */

    var $templates = array();

    var $element_css_classes = array(
        'label' => 'form_label_col',
        'element' => 'form_data_col',
        'span'  =>  'form_span_col',
        'header'=>  'udm_header',
        'group' =>  'udm_group_label' 
    );

    var $element_css_keys = array(
        'default' => array( "label", "element" ),
        'textarea' => array('span'),
        'header' => array('header'),
        'static' => array('span'),
        'submit' => array('span'),
        'checkgroup' => array('span', 'group'),
        'group' => array('span', 'group')
        );
        

    function AMPFormTemplate() {
        $this->init();
    }

    function init() {
        $this->_buildPatterns();
    }

    //separator var is only used in TemplateSearch subclass

	function getTemplate( $type=null, $separator = null ) {
        if (!isset($type)) return false;
		$template_method =  "_getTemplate".ucfirst($type);
		if (!method_exists($this, $template_method)) $template_method = '_getBaseTemplate';
        return $this->$template_method( $type );

	}

    function setClass( $elementType, $class ) {
        $this->element_css_classes[ $elementType ] = $class;
    }

    ############################################
    ### Private Template Constuction Methods ###
    ############################################


	function _getTemplateWysiwyg() {
		return $this->_getBaseTemplate('textarea');
	}

	function _getTemplateRadiogroup() {
		return $this->_getBaseTemplate('checkgroup');
	}

    function _getBaseTemplate( $type ) {
        if (isset( $this->templates[$type] )) return $this->templates[$type];
        if (isset( $this->patterns[ $type ] )) return $this->_buildTemplate( $type );
        if (isset( $this->templates['default'] )) return $this->templates['default'];
		return $this->_buildTemplate('default');
    }

    function _buildTemplate( $type ) {
        $pattern = isset( $this->patterns[ $type ] ) ? 
                    $this->patterns[ $type ] : 
                    $this->patterns[ 'default' ];
        $css_keys = isset( $this->element_css_keys[ $type ] ) ?
                    $this->element_css_keys[ $type ] :
                    $this->element_css_keys[ 'default' ];
        $css_info = array_combine_key( $css_keys, $this->element_css_classes );
        $this->templates[ $type ] = vsprintf( $pattern, $css_info );
        return $this->templates[ $type ];
    }

    function _buildPatterns() {

        foreach ($this->pattern_defs as $key => $def ) {
            $this->patterns[ $key ] = $this->_buildPattern( $def );
        }
    }


    function _buildPattern( $pattern_def ) {
        $pattern_set = array_combine_key( $pattern_def, $this->pattern_parts );
        return join( "", $pattern_set );
    }


}
?>
