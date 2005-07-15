<?php

/*****
 *
 * AMP UserData Copy Module
 *
 * (c) 2004 Radical Designs
 * Written by Austin Putman, austin@radicaldesigns.org
 *
 *****/

//ob_start();
require_once('AMP/BaseDB.php');
require_once('AMP/System/UserData/Module/Copy.inc.php');
require_once('AMP/UserData/Input.inc.php');


$modin = (isset($_REQUEST['modin'])&&$_REQUEST['modin'])?$_REQUEST['modin']:false;
$new_name =(isset($_REQUEST['core_name'])&&$_REQUEST['core_name'])?$_REQUEST['core_name']:false; 

if ($modin) {
    $udm = new UserDataInput($dbcon, $modin);
    if ($new_name) {
        //proceed to make a copy
        $copier = new AMPSystem_UserData_Module_Copy ($dbcon, $modin);
        $copier->setOverride('name', $new_name, $udm->name);
        if($new_copy = $copier->execute()) {
            ampredirect("modinput4_edit.php?modin=".$new_copy);
        } else {
            $output = "Copy failed: ".$copier->ErrorMsg();
        }
        $output = $copier->ErrorMsg();
    } else {
        $output = '
            <p>In order to copy this form, we need to assign it a new name.</p>

            <form name="copy_structure" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                <input type="hidden" name="modin" value="'.$_REQUEST['modin'].'" />
                <p>
                    Make a copy of the '.$udm->name.' form, and <label>name it</label>
                    <input type="text" name="core_name" />
                    <input type="submit" value="Go" />
                </p>
            </form>
            ';
    }
} else {
        
        $output ='Sorry, try <a href="modinput4_list.php">editing the form</a> you&rsquo;d like to copy, and use the Copy Form Template menu option.';
}
    

if (isset($output)) {
    require_once('Connections/freedomrising.php');
    require_once('header.php');

    print "<H2>Copy Form Settings</H2>";

    print $output;

    include('footer.php');
}


?>
