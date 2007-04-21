<?php
ampredirect( AMP_SYSTEM_URL_FORMS );
/*
$mod_name='udm';
require_once('Connections/freedomrising.php');
require_once('header.php');

$sql = "SELECT name, id FROM userdata_fields ORDER BY name ASC";
$rs  = $dbcon->Execute( $sql ) or die( "Couldn't retrieve module information: " . $dbcon->ErrorMsg() );

$rowColors = array( '#dddddd', '#eeeeee' );

?>

<h2>User Data Modules</h2>

<table align="center" cellpadding="1" cellspacing="1" width="95%">

  <tr class=intitle>
    <th align="left">Module</th>
	<th align="left">ID</th>
    <th colspan="2" align="left">Actions</th>
    <th colspan="2" align="left">Data</th>
  </tr>

  <?php

    $i = 0;
    while ( $udmEntry = $rs->FetchRow() ) {

        ?>

  <tr bgcolor="<?php print $rowColors[ $i++ ]; ?>">
    <td><?php print $udmEntry['name']; ?></td>
    <td><?php print $udmEntry['id'];   ?></td>
    <td><a href="modinput4_edit.php?modin=<?php print $udmEntry['id']; ?>">edit</a></td>
    <td><a href="modinput4_delete.php?modin=<?php print  $udmEntry['id']; ?>">delete</a></td>
    <td><a href="modinput4_data.php?modin=<?php print $udmEntry['id']; ?>">view</a></td>
    <td><a href="modinput4_view.php?modin=<?php print  $udmEntry['id']; ?>">insert</a></td>
  </tr>

        <?php

        if ( $i >= count( $rowColors ) ) $i = 0;

    }

  ?>

</table>

<?php

include ("footer.php");
*/

?>
