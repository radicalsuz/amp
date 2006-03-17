<?php

######## WARNING ### THIS PLUGIN HAS NOT BEEN UPDATED OR TESTED FOR AMP BUILD 3.4 AND ABOVE ###
### IT WILL ALMOST CERTAINLY NOT WORK ####

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

class UserDataPlugin_Labels_Output extends UserDataPlugin {

    var $options = array(
        'labels_type' => array(
            'available' => true,
            'default'   => '5160',
            'type'      => 'text',
            'label'     => 'Avery Label #'
            ),
        'labels_filename' => array(
            'available' => true,
            'default'   => 'webLabels.pdf',
            'type'      => 'text',
            'label'     => 'Default Download Filename'
            )
        );

    var $available = false;

    function UserDataPlugin_Labels_Output ( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute( $options = null ) {
        $options = array_merge( $options, $this->getOptions() );

        $labels = new PDF_Label_PostNet( $options['labels_type'] );

        $count  = 0;
        $zcount = 0;

        $dataset = $this->udm->getData();

        foreach ( $dataset as $dataitem ) {

            $label = array();

            // Don't bother if we don't have a street address.
            if ( !$dataitem[ 'Street' ] ) continue;

            if ( $inclTitle )   $label[] = $dataitem['occupation'];
            if ( $inclCompany ) $label[] = $dataitem['Company'];
            $label[] = $dataitem['Street'];
            $label[] = $dataitem['Street_2'];
            $label[] = $dataitem['Street_3'];
            $label[] = $dataitem['City'] . ", " . $dataitem['State'] . "  " . $dataitem['Zip'];
            if ( !preg_match( "/^U[^\b]*[\. ]?S[^\b]*[\. ]?A[^\b]*/", $dataitem['Country'] ) )
                $label[] = $dataitem['Country'];

            $zipBarcode = $barcode; // this is just  a quick hack to make the admin interface work.

        }
    }

    function list2labels ($doc_name='labels.pdf', $label_type='5160', $include_title=false, 	
        $include_company=false) {
        $labels= new PDF_Label($label_type);
        $labels->Open();
        $entry_count=0;
        $dataset = $udm->getData();
        foreach($dataset as $currentrow) {
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
?>
