<?php
require_once('WYSIWYG/editor.php');

class UserDataPlugin_HTMLEditor_Output extends UserDataPlugin {

    var	$short_name = 'HTMLEditor_Output';
	var	$long_name = 'WYSIWYG Textarea replacement';
	var	$description = 'This plugin allows you to replace textarea fields in a form with a WYSIWYG (What You See Is What You Get) Editor, which provides html formatting options and the option to cut and paste from other applications.';
    var $available = false;
	var $options = array ('fieldname'=>  array(
		'type'=>'text',
		'available'=>true,
		'label'=>'Field to show WYSIWYG Editor',
		'default'=>null)
		);
	
	var $fieldset = array();

	function UserDataPlugin_HTMLEditor_Output(&$udm,$plugin_instance=NULL) {
		$this->init($udm,$plugin_instance);
    }

	function get_javascript() {
		$browser = getBrowser();
		switch($browser) {
			case 'mozilla':
				$this->javascript[] = get_javascript_htmlarea($this->fieldset);
				break;
		}
		return parent::get_javascript();
	}

	function execute($options=null){
        if(!isset($this->udm->form)) {
            return false;
        }
 
        if (!isset($options['fieldname'])) return false;
        $fieldname = $options['fieldname'];
        
		#need to keep this updated to generate proper javascript for htmlarea
		$this->fieldset[] = $fieldname;

		$browser = getBrowser();
        //the assumption here is that this plugin is being called from 
        //the Build plugin, the form has already defined an element of type
        //'textarea' and we will modify this element as necessary

/* maybe we do it this way at some point
		$element =& $this->udm->form->getElement($fieldname);
		$element->do_browser_specific_field_adjustment();
*/
        $element =& $this->udm->form->getElement($fieldname);
        $fDef = $this->udm->fields[$fieldname];

        //Size settings - perform for all variations
        $size = $fDef['size'];
        
        $columns=80;
        $rows=20;

        if ( is_numeric( $size ) && $size ) {
            if ( strpos( $size, ':' ) ) {
                $tmpArray = split( ':', $fDef['size'] );
                $rows = $tmpArray['0'];
            } else {
                $rows=$size;
            }
        }


        $element->setRows($rows);
        $element->setCols($columns);

        switch ($browser) {
            case 'mozilla':
				$element->updateAttributes(array('id' => $fieldname, 'wrap' => "VIRTUAL", 'style' => "'width:100%"));
				return $element;
                break;
            case 'win/ie':
                //get HTML for iFrame Field
                $new_value = $this->iFrame_HTML($fieldname, $fDef);

                //remove the existing textarea
                $old_element = $this->udm->form->removeElement($fieldname);

                //add in the raw HTML element
                $editor_element = $this->udm->form->addElement('html', $new_value);

                //add the hidden element which will carry the value
                $new_element = $this->udm->form->addElement('hidden', $fieldname, null, $fDef['defaults']);
                break;
            default:
                //leave the field as a text area        
				return false;
		}
    }

    function iFrame_HTML ($fieldname, $fDef) {
        
        //we have to fake the whole rendering process because of the
        //stupid Iframe -- this is a wicked hack
        //hopefully we can delete all this bs when we upgrade our
        //WYSIWYG editor to HTMLArea2
        $new_value = 
            "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\"><table class=\"form_span_col\">
            <tr><td>";
        if ($fDef['required']) {
            $new_value .="<span style=\"color: #ff0000\">*</span>";
        }
        $new_value .="<b>".$fDef['label']."</b><br>\n\t\t";
        if ($fieldError = $this->udm->form->getElementError($fieldname)){
            $new_value .= "
                <span style=\"color: #ff0000\">".$fieldError."</span><br />";
        }
        $new_value .= "\t
            <IFRAME src=\"/scripts/FCKeditor/fckeditor.html?FieldName=".$fieldname."\" 
                    width=\"550\" height=\"500\" frameborder=\"no\" scrolling=\"no\">
            </IFRAME>
        </td></tr></table></td>\n\t</tr>";

        return $new_value;
    }

}


?>
