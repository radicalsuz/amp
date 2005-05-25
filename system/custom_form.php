<?php

/* * * * * * * * *
 *  Custom Form display page
 *  allows system-side Posts for defined forms
 *  Dependencies:
 *  AMP/CustomForm.php  - defines the AMP_CustomForm class using
 *  HTML::QuickForm engine
 *
 *  URL vars: formname, action=list (shows list of records)
 *  Author: austin@radicaldesigns.org
 *  5/23/2005
 */

require_once('Connections/freedomrising.php');
require_once('utility.functions.inc.php');

//Check for the existence of a set of custom forms
$formfile='custom.forms.inc.php';
if (file_exists_incpath($formfile)) {
    require_once ($formfile);

    //Create an instance of a Custom form definition
    if ($_REQUEST['formname']) {
        $formname = 'AMP_CustomForm_'.$_REQUEST['formname'];
        if (class_exists($formname)) {
            $form = & new $formname($dbcon,true);
        }
    }
}


if (isset($form)) {
    //Check if the form was Submitted with the Save or Delete buttons
    //whether the record ID was already set
    $sub = isset($_REQUEST['btnCustomFormSubmit']) ? $_REQUEST['btnCustomFormSubmit'] : false;
    $del = isset($_REQUEST['btnCustomFormDelete']) ? $_REQUEST['btnCustomFormDelete'] : false;
    $id = isset($_REQUEST['id'])?$_REQUEST['id']: false;

    //Delete record on request and show the list
    if ($del && $id ) {
        if ($form->delData($id)) {
            $_REQUEST['action']='list';
            $form->message = "Selected item deleted";
        } else {
            $form->message = "Deletion failed";
        }
    }

    //Show the list if requested
    if ($_REQUEST['action']=='list') {
        $output = $form->listpage();

    } else {
        //Show the form

        if ($sub ) {
            //if the form was submitted, attempt to save data
            if ($form->saveData()) {
                header("Location:".$form->redirect);
            }
        // if an id is specified, read the record
        } elseif (!$sub && $id) {
            $form->getData($id);
        }

        //Output the form

        $output = "<h2>Add/Edit " . str_replace("_", " ",$form->name) . "</h2>";
        $output .= $form->output();
    }
    
} else {
    //Display an error message if the form was not defined
    $output = "<h2>Use Custom Form</h2>";
    $output .= "The custom form you requested, '".$_REQUEST['formname']."' is unavailable.";
}
require_once('header.php');

print $output;


require_once('footer.php');
?>
    
    
}
