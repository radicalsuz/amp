<?php

/*****
 *
 * AMP UserData Copy Module
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

//ob_start();
$mod_name='udm';
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'utility.functions.inc.php');
require("Connections/freedomrising.php");

include('header.php');

if (!isset($_REQUEST['modin'])) {

    print 'Sorry, try <a href="modinput4_list.php">editing the form</a> you&rsquo;d like to copy, and use the &ldquo;Copy&rdquo; link.';

} elseif (!isset($_REQUEST['core_name'])) {

    ?>

    <p>In order to copy this form, we need to assign it a new name.</p>

    <form name="copy_structure" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
        <input type="hidden" name="modin" value="<?=$_REQUEST['modin']?>" />
        <p>
            Make a copy of the <?=$udm->name?> form, and <label>name it</label>
            <input type="text" name="core_name" />
            <input type="submit" value="Go" />
        </p>
    </form>

    <?php

} else {

    // Fetch the form instance specified by submitted modin value.
    $udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ], true );
    $udm->doPlugin( "QuickForm", "build_admin" );

    if ($new_modin = $udm->doPlugin( "AMPsystem", "copy_admin" )) {

        header("Location: modinput4_edit.php?modin=".$new_modin);

    } else {

        print "There was a problem copying the form. Please contact your administrator.";

    }
}

include('footer.php');

?>
