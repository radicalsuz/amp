<?php

/*****
 *
 * Label Printing Library for AMP
 * gratitude to creators of PDF_Label class, FPDF class, and POSTNetBarcode script
 *
 * usage:
 * list2labels() - converts an existing search to mailing labels
 *
 *****/

require_once( 'AMP/UserData/Search.inc.php' );
require_once( 'PDF/PostNet.php' );
require_once( 'PDF/Avery.php' );

define( 'UDM_OUTPUT_LABELS_DEFAULT_TYPE', '5160' );
define( 'UDM_OUTPUT_LABELS_DEFAULT_FILENAME', 'ampLabels.pdf' );

function udm_output_labels ( &$udm, $options = array() ) {

    $type = ( isset( $options[ 'label_type' ] ) ) ?
                $options[ 'label_type' ] : UDM_OUTPUT_LABELS_DEFAULT_TYPE;

    $filename = ( isset( $options[ 'label_filename' ] ) ) ?
                $options[ 'label_filename' ] : UDM_OUTPUT_LABELS_DEFAULT_FILENAME;

    $labels = new PDF_Label_PostNet( $type );

    $count  = 0;
    $zcount = 0;
    foreach ( $udml->current_list as $entry ) {

        $label = array();

        // Don't bother if we don't have a street address.
        if ( !$entry[ 'Street' ] ) continue;

        if ( $inclTitle )   $label[] = $entry['occupation'];
        if ( $inclCompany ) $label[] = $entry['Company'];
        $label[] = $entry['Street'];
        $label[] = $entry['Street_2'];
        $label[] = $entry['Street_3'];
        $label[] = $entry['City'] . ", " . $entry['State'] . "  " . $entry['Zip'];
        if ( !preg_match( "/^U[^\b]*[\. ]?S[^\b]*[\. ]?A[^\b]*/", $entry['Country'] ) )
            $label[] = $entry['Country'];

        $zipBarcode = $




}

class UserLabels extends UserList {	

	function list2labels ($doc_name='labels.pdf', $label_type='5160', $include_title=false, 	
		$include_company=false) {
		$labels= new PDF_Label($label_type);
		$labels->Open();
		$entry_count=0;
		foreach($this->current_list as $currentrow) {
			$new_entry='';
			$new_entry=$currentrow['Name']."\n";
			if ($currentrow['occupation']>''&&$include_title) {$new_entry.=$currentrow['occupation']."\n";}
			if ($currentrow['Company']>''&&$include_company){
				$new_entry.=$currentrow['Company']."\n";}
			$new_entry.=$currentrow['Street']."\n";
			if ($currentrow['Street_2']>'') {$new_entry.=$currentrow['Street_2']."\n";}
			if ($currentrow['Street_2']>'') {$new_entry.=$currentrow['Street_3']."\n";}
			$new_entry.=$currentrow['City'].", ";
			$new_entry.=$currentrow['State']."  ";
			$new_entry.=$currentrow['Zip'];
			if ($currentrow['Country']>''&&$currentrow['Country']!="USA"&&$currentrow['Country']!="U.S.A."&&substr($currentrow['Country'],0,13)!="United States"){
				$new_entry.="\n".$currentrow['Country'];
			}
			if ($currentrow['Street']!=''&&$currentrow['Zip']!='') {
				$entry_count++;
				$myzip = $labels->ParseZipCode($currentrow['Zip']);
				if ($myzip!="") {
					$validzips++;
				}
				$labels->Add_PDF_Label($new_entry, $myzip);
			}
		}
		$new_entry=$validzips." labels printed with bar codes\n$entry_count total labels printed";
		$labels->Add_PDF_Label($new_entry);
		$labels->Output($doc_name, 'I');
	}
}
