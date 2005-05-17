<?php
require_once("FCKeditor/fckeditor.php");

function WYSIWYG($value,$html){
	$editor = get_textarea_editor();
	switch ($editor) {
	case 'htmlarea':
        $output = launch_htmlarea($value,$html);
		break;
	case 'FCKeditor':
        $output = launch_win($value,$html);
		break;
    default:
        $output = launch_nowysiwyg($value,$html);
    }
    return $output;
}

function get_textarea_editor() {
    global $browser_mo, $browser_ie, $browser_win, $browser_checked;
	if(!$browser_checked) {
		setBrowser();
	}

    if ($browser_mo && ($_COOKIE["AMPWYSIWYG"] != 'none'))  {
        return 'htmlarea';
    }
    elseif (($browser_ie) && ($browser_win) && ($_COOKIE["AMPWYSIWYG"] != 'none')) {
        return 'FCKeditor';
    }
    else {
        return false;
    }
}

function get_javascript_htmlarea($textarea_ids) {
	return javascript_htmlarea_setup()
		 . javascript_htmlarea_plugins()
		 . javascript_htmlarea_initEditor($textarea_ids);
}

function javascript_htmlarea_setup() {
    return '<script type="text/javascript">
		_editor_url = "http://'.$_SERVER['HTTP_HOST'].'/scripts/htmlarea/";
        _editor_lang = "en";
		</script>
		<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/scripts/htmlarea/htmlarea.js"></script>';
}

function javascript_htmlarea_plugins() {
	return '<script type="text/javascript">
        // WARNING: using this interface to load plugin
        // will _NOT_ work if plugins do not have the language
        // loaded by HTMLArea.

        // In other words, this function generates SCRIPT tags
        // that load the plugin and the language file, based on the
        // global variable HTMLArea.I18N.lang (defined in the lang file,
        // in our case "lang/en.js" loaded above).

        // If this lang file is not found the plugin will fail to
        // load correctly and nothing will work.

        HTMLArea.loadPlugin("TableOperations");
        HTMLArea.loadPlugin("SpellChecker");
        HTMLArea.loadPlugin("FullPage");
        HTMLArea.loadPlugin("CSS");
        HTMLArea.loadPlugin("ContextMenu");
		</script>';
}

function javascript_htmlarea_initEditor($textarea_ids) {
    $script = javascript_htmlarea_startInitEditor();

    if(is_array($textarea_ids)) {
        $timeout = 500;
        foreach ($textarea_ids as $id) {
            $script .= javascript_initEditor_contents($id, $timeout);
            $timeout += 1;
        }
    } else {
        $script .= javascript_initEditor_contents($textarea_ids);
    }

    $script .= javascript_htmlarea_endInitEditor();

    return $script;
}

function javascript_htmlarea_startInitEditor() {
    return '<script type="text/javascript">
            function initEditor() {';
            #var editor = null;
}

function javascript_htmlarea_endInitEditor() {
    return '    return false;
        }
    </script>';
}

function javascript_initEditor_contents($id, $timeout=500) {

    $editor_name = $id.'_editor';
    return '// create an editor for the "ta" textbox
            var '.$editor_name.' = new HTMLArea("'.$id.'");
            // register the FullPage plugin
            '.$editor_name.'.registerPlugin(FullPage);
            // register the SpellChecker plugin
            '.$editor_name.'.registerPlugin(TableOperations);
            // register the SpellChecker plugin
            //editor.registerPlugin(SpellChecker);
            setTimeout(function() {
                '.$editor_name.'.generate();
            }, '.$timeout.');';
}

function launch_htmlarea($value,$html=NULL) {
    $textarea_id = "articlemo";
    $textarea_name = "article";
    $htmlarea = write_htmlarea($textarea_id, $textarea_name, $value, $html)
                . '<input name="html" type="hidden" value="1">';
    return $htmlarea;
}

function write_htmlarea($textarea_id, $textarea_name, $value, $html=NULL) {
    $output = get_javascript_htmlarea($textarea_id)
            . write_htmlarea_textarea($textarea_id, $textarea_name, $value, $html);
    return $output;
}

function write_htmlarea_textarea($textarea_id, $textarea_name, $value, $html=NULL) {
    $textarea .= '<textarea id ="'.$textarea_id.'" name="'.$textarea_name.'" cols="80" rows="60" wrap="VIRTUAL" style="width:100%">';
    if ($html != "1") {
        $textarea .= nl2br($value);
    } else {
        $textarea .= $value;
    }
    $textarea .= '</textarea>';
    return $textarea;
}

function launch_win($value,$html=NULL) {
	$textarea_name = "article";

	return write_win($textarea_name, $value, $html);
}
 
function write_win($textarea_name,$value,$html=NULL) {
    if ($html != "1") {
        $textvalue = nl2br($value);
    } else {
        $textvalue = $value;
    }
	$output = write_fckeditor_html($textarea_name, $textvalue);
    $output .= '<input name="html" type="hidden" value="1"> ';
    return $output;
}

function write_fckeditor_html($textarea_name, $textarea_value) {
    $oFCKeditor =& new FCKeditor;
    $oFCKeditor->Value = $textvalue  ;
    $output = $oFCKeditor->ReturnFCKeditor( $textarea_name, '500', 500 ) ;
	return $output;
}

function launch_nowysiwyg($value,$html=NULL) {
    $output = '<input name="html" type="checkbox" value="1" ' ;
    if ($html == "1") { $output .= "CHECKED";}
    $output .= '>HTML Override <br> <textarea name="article" cols="65" rows="20" wrap="VIRTUAL">';
    $text2 = $value;
    if ($html == "1"){
        $text2 = str_replace("<BR>", "<BR>\r\n", $text2);
    }
    $output .= $text2 .'</textarea> ';
    return $output;
}

?>
