<?php
////////////////////////////////////////////
/* Label Printing Library for AMP
  * gratitude to creators of PDF_Label class, FPDF class, and POSTNetBarcode script
  *
  *usage:
  *list2labels() - converts an existing search to mailing labels
  *
  *
  *
  */
 if(class_exists('UserList')) {
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
 }
////////////////////////////////////////////////////
// PDF_Label 
//
// Class to print labels in Avery or custom formats
//
//
// Copyright (C) 2003 Laurent PASSEBECQ (LPA)
// Based on code by Steve Dillon : steved@mad.scientist.com
//
//-------------------------------------------------------------------
// VERSIONS :
// 1.0  : Initial release
// 1.1  : + : Added unit in the constructor
//        + : Now Positions start @ (1,1).. then the first image @top-left of a page is (1,1)
//        + : Added in the description of a label : 
//                font-size    : defaut char size (can be changed by calling Set_Char_Size(xx);
//                paper-size    : Size of the paper for this sheet (thanx to Al Canton)
//                metric        : type of unit used in this description
//                              You can define your label properties in inches by setting metric to 'in'
//                              and printing in millimiter by setting unit to 'mm' in constructor.
//              Added some labels :
//                5160, 5161, 5162, 5163,5164 : thanx to Al Canton : acanton@adams-blake.com
//                8600                         : thanx to Kunal Walia : kunal@u.washington.edu
//        + : Added 3mm to the position of labels to avoid errors 
// 1.2  : + : Added Set_Font_Name method
//        = : Bug of positioning
//        = : Set_Font_Size modified -> Now, just modify the size of the font
//        = : Set_Char_Size renamed to Set_Font_Size
////////////////////////////////////////////////////

/**
* PDF_Label - PDF label editing
* @package PDF_Label
* @author Laurent PASSEBECQ <lpasseb@numericable.fr>
* @copyright 2003 Laurent PASSEBECQ
**/

define('FPDF_FONTPATH', 'PDF/font/');
require_once('PDF/fpdf.php');

class PDF_Label extends FPDF {

    // Private properties
    var $_Avery_Name    = '';                // Name of format
    var $_Margin_Left    = 0;                // Left margin of labels
    var $_Margin_Top    = 0;                // Top margin of labels
    var $_X_Space         = 0;                // Horizontal space between 2 labels
    var $_Y_Space         = 0;                // Vertical space between 2 labels
    var $_X_Number         = 0;                // Number of labels horizontally
    var $_Y_Number         = 0;                // Number of labels vertically
    var $_Width         = 0;                // Width of label
    var $_Height         = 0;                // Height of label
    var $_Char_Size        = 10;                // Character size
    var $_Line_Height    = 10;                // Default line height
    var $_Metric         = 'mm';                // Type of metric for labels.. Will help to calculate good values
    var $_Metric_Doc     = 'mm';                // Type of metric for the document
    var $_Font_Name        = 'Arial';            // Name of the font

    var $_COUNTX = 1;
    var $_COUNTY = 1;


    // Listing of labels size
    //Non metric measurements cause problems - below didn't work
	//'5164'=>array('name'=>'5164',    'paper-size'=>'letter',    'metric'=>'in',    'marginLeft'=>0.148,    'marginTop'=>0.5,         'NX'=>2,    'NY'=>3,    'SpaceX'=>0.2031,    'SpaceY'=>0,    'width'=>4.0,        'height'=>3.33,        'font-size'=>12),
     
	
	var $_Avery_Labels = array (
        '5160'=>array('name'=>'5160',    'paper-size'=>'letter',    'metric'=>'mm',    'marginLeft'=>1.762,    'marginTop'=>10.7,        'NX'=>3,    'NY'=>10,    'SpaceX'=>3.175,    'SpaceY'=>0,    'width'=>66.675,    'height'=>25.4,        'font-size'=>8),
        '5161'=>array('name'=>'5161',    'paper-size'=>'letter',    'metric'=>'mm',    'marginLeft'=>0.967,    'marginTop'=>10.7,        'NX'=>2,    'NY'=>10,    'SpaceX'=>3.967,    'SpaceY'=>0,    'width'=>101.6,        'height'=>25.4,        'font-size'=>8),
        '5162'=>array('name'=>'5162',    'paper-size'=>'letter',    'metric'=>'mm',    'marginLeft'=>0.97,        'marginTop'=>20.224,    'NX'=>2,    'NY'=>7,    'SpaceX'=>4.762,    'SpaceY'=>0,    'width'=>100.807,    'height'=>35.72,    'font-size'=>8),
        '5163'=>array('name'=>'5163',    'paper-size'=>'letter',    'metric'=>'mm',    'marginLeft'=>1.762,    'marginTop'=>10.7,         'NX'=>2,    'NY'=>5,    'SpaceX'=>3.175,    'SpaceY'=>0,    'width'=>101.6,        'height'=>50.8,        'font-size'=>8),
        '5164'=>array('name'=>'5164',    'paper-size'=>'letter',    'metric'=>'mm',    'marginLeft'=>3.7592,    'marginTop'=>12.7,         'NX'=>2,    'NY'=>3,    'SpaceX'=>5.159,    'SpaceY'=>0,    'width'=>101.6,        'height'=>84.582,        'font-size'=>12),
        
		'8600'=>array('name'=>'8600',    'paper-size'=>'letter',    'metric'=>'mm',    'marginLeft'=>7.1,         'marginTop'=>19,         'NX'=>3,     'NY'=>10,     'SpaceX'=>9.5,         'SpaceY'=>3.1,     'width'=>66.6,         'height'=>25.4,        'font-size'=>8),
        'L7163'=>array('name'=>'L7163',    'paper-size'=>'A4',        'metric'=>'mm',    'marginLeft'=>5,        'marginTop'=>15,         'NX'=>2,    'NY'=>7,    'SpaceX'=>25,        'SpaceY'=>0,    'width'=>99.1,        'height'=>38.1,        'font-size'=>9)
    );

    // convert units (in to mm, mm to in)
    // $src and $dest must be 'in' or 'mm'
    function _Convert_Metric ($value, $src, $dest) {
        if ($src != $dest) {
            $tab['in'] = 39.37008;
            $tab['mm'] = 1000;
            return $value * $tab[$dest] / $tab[$src];
        } else {
            return $value;
        }
    }

    // Give the height for a char size given.
    function _Get_Height_Chars($pt) {
        // Array matching character sizes and line heights
        $_Table_Hauteur_Chars = array(6=>2, 7=>2.5, 8=>3, 9=>4, 10=>5, 11=>6, 12=>7, 13=>8, 14=>9, 15=>10);
        if (in_array($pt, array_keys($_Table_Hauteur_Chars))) {
            return $_Table_Hauteur_Chars[$pt];
        } else {
            return 100; // There is a prob..
        }
    }

    function _Set_Format($format) {
        $this->_Metric         = $format['metric'];
        $this->_Avery_Name     = $format['name'];
        $this->_Margin_Left    = $this->_Convert_Metric ($format['marginLeft'], $this->_Metric, $this->_Metric_Doc);
        $this->_Margin_Top    = $this->_Convert_Metric ($format['marginTop'], $this->_Metric, $this->_Metric_Doc);
        $this->_X_Space     = $this->_Convert_Metric ($format['SpaceX'], $this->_Metric, $this->_Metric_Doc);
        $this->_Y_Space     = $this->_Convert_Metric ($format['SpaceY'], $this->_Metric, $this->_Metric_Doc);
        $this->_X_Number     = $format['NX'];
        $this->_Y_Number     = $format['NY'];
        $this->_Width         = $this->_Convert_Metric ($format['width'], $this->_Metric, $this->_Metric_Doc);
        $this->_Height         = $this->_Convert_Metric ($format['height'], $this->_Metric, $this->_Metric_Doc);
        $this->Set_Font_Size($format['font-size']);
    }

    // Constructor
    function PDF_Label ($format, $unit='mm', $posX=1, $posY=1) {
        if (is_array($format)) {
            // Custom format
            $Tformat = $format;
        } else {
            // Avery format
            $Tformat = $this->_Avery_Labels[$format];
        }

        parent::FPDF('P', $Tformat['metric'], $Tformat['paper-size']);
        $this->_Set_Format($Tformat);
        $this->Set_Font_Name('Arial');
        $this->SetMargins(0,0); 
        $this->SetAutoPageBreak(false); 

        $this->_Metric_Doc = $unit;
        // Start at the given label position
        if ($posX > 1) $posX--; else $posX=0;
        if ($posY > 1) $posY--; else $posY=0;
        if ($posX >=  $this->_X_Number) $posX =  $this->_X_Number-1;
        if ($posY >=  $this->_Y_Number) $posY =  $this->_Y_Number-1;
        $this->_COUNTX = $posX;
        $this->_COUNTY = $posY;
    }

    // Sets the character size
    // This changes the line height too
    function Set_Font_Size($pt) {
        if ($pt > 3) {
            $this->_Char_Size = $pt;
            $this->_Line_Height = $this->_Get_Height_Chars($pt);
            $this->SetFontSize($this->_Char_Size);
        }
    }

    // Method to change font name
    function Set_Font_Name($fontname) {
        if ($fontname != '') {
            $this->_Font_Name = $fontname;
            $this->SetFont($this->_Font_Name);
        }
    }

    // Print a label
    function Add_PDF_Label($texte, $zip2bar='') {
        // We are in a new page, then we must add a page
        if (($this->_COUNTX ==0) and ($this->_COUNTY==0)) {
            $this->AddPage();
        }

        $_PosX = $this->_Margin_Left+($this->_COUNTX*($this->_Width+$this->_X_Space));
        $_PosY = $this->_Margin_Top+($this->_COUNTY*($this->_Height+$this->_Y_Space));
        $this->SetXY($_PosX+3, $_PosY+3);
        $this->MultiCell($this->_Width, $this->_Line_Height, $texte);
        //INCLUSION of POSTAL BAR CODES
		
		if ($zip2bar!='') {  //Check for zip code parameter
			$finalzip=$this->ParseZipCode($zip2bar); 
			if ($finalzip!='') { //make sure zip is valid 5/10 digit
				$totallines=substr_count($texte, "\n")+2;
				$this->POSTNETBarCode($_PosX+3, $_PosY+$this->_Height-6, $finalzip);
			}
		}
		$this->_COUNTY++;

        if ($this->_COUNTY == $this->_Y_Number) {
            // End of column reached, we start a new one
            $this->_COUNTX++;
            $this->_COUNTY=0;
        }

        if ($this->_COUNTX == $this->_X_Number) {
            // Page full, we start a new one
            $this->_COUNTX=0;
            $this->_COUNTY=0;
        }
    }

#}

#class PDF_POSTNET extends FPDF
#{
    // PUBLIC PROCEDURES

    // draws a bar code for the given zip code using pdf lines
    // triggers error if zip code is invalid
    // x,y specifies the lower left corner of the bar code
    function POSTNETBarCode($x, $y, $zipcode)
    {
        // Save nominal bar dimensions in user units
        // Full Bar Nominal Height = 0.125"
        $FullBarHeight = 9 / $this->k;
        // Half Bar Nominal Height = 0.050"
        $HalfBarHeight = 3.6 / $this->k;
        // Full and Half Bar Nominal Width = 0.020"
        $BarWidth = 1.44 / $this->k;
        // Bar Spacing = 0.050"
        $BarSpacing = 3.6 / $this->k;

        $FiveBarSpacing = $BarSpacing * 5;

        // 1 represents full-height bars and 0 represents half-height bars
        $BarDefinitionsArray = Array(
            1 => Array(0,0,0,1,1),
            2 => Array(0,0,1,0,1),
            3 => Array(0,0,1,1,0),
            4 => Array(0,1,0,0,1),
            5 => Array(0,1,0,1,0),
            6 => Array(0,1,1,0,0),
            7 => Array(1,0,0,0,1),
            8 => Array(1,0,0,1,0),
            9 => Array(1,0,1,0,0),
            0 => Array(1,1,0,0,0));
            
        // validate the zip code
        $this->_ValidateZipCode($zipcode);

        // set the line width
        $this->SetLineWidth($BarWidth);

        // draw start frame bar
        $this->Line($x, $y, $x, $y - $FullBarHeight);
        $x += $BarSpacing;

        // draw digit bars
        for($i = 0; $i < 5; $i++)
        {
            $this->_DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight, 
                $FullBarHeight, $BarDefinitionsArray, $zipcode{$i});
            $x += $FiveBarSpacing;
        }
        // draw more digit bars if 10 digit zip code
        if(strlen($zipcode) == 10)
        {
            for($i = 6; $i < 10; $i++)
            {
                $this->_DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight, 
                    $FullBarHeight, $BarDefinitionsArray, $zipcode{$i});
                $x += $FiveBarSpacing;
            }
        }
        
        // draw check sum digit
        $this->_DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight, 
            $FullBarHeight, $BarDefinitionsArray, 
            $this->_CalculateCheckSumDigit($zipcode));
        $x += $FiveBarSpacing;

        // draw end frame bar
        $this->Line($x, $y, $x, $y - $FullBarHeight);

    }

    // Reads from end of string and returns first matching valid
    // zip code of form DDDDD or DDDDD-DDDD, in that order.
    // Returns empty string if no zip code found.
    function ParseZipCode($stringToParse)
    {
        // check if string is an array or object
        if(is_array($stringToParse) || is_object($stringToParse))
        {
            return "";
        }

        // convert parameter to a string
        $stringToParse = strval($stringToParse);

        $lengthOfString = strlen($stringToParse);
        if ( $lengthOfString < 5 ) {
            return "";
        }
        
        // parse the zip code backward
        $zipcodeLength = 0;
        $zipcode = "";
        for ($i = $lengthOfString-1; $i >= 0; $i--)
        {
            // conditions to continue the zip code
            switch($zipcodeLength)
            {
            case 0:
            case 1:
            case 2:
            case 3:
                if ( is_numeric($stringToParse{$i}) ) {
                    $zipcodeLength += 1;
                    $zipcode .= $stringToParse{$i};
                } else {
                    $zipcodeLength = 0;
                    $zipcode = "";
                }
                break;
            case 4:
                if ( $stringToParse{$i} == "-" ) {
                    $zipcodeLength += 1;
                    $zipcode .= $stringToParse{$i};
                } elseif ( is_numeric($stringToParse{$i}) ) {
                    $zipcodeLength += 1;
                    $zipcode .= $stringToParse{$i};
                    break 2;
                } else {
                    $zipcodeLength = 0;
                    $zipcode = "";
                }
                break;
            case 5:
            case 6:
            case 7:
            case 8:
                if ( is_numeric($stringToParse{$i}) ) {
                    $zipcodeLength = $zipcodeLength + 1;
                    $zipcode = $zipcode . $stringToParse{$i};
                } else {
                    $zipcodeLength = 0;
                    $zipcode = "";
                }
                break;
            case 9:
                if ( is_numeric($stringToParse{$i}) ) {
                    $zipcodeLength = $zipcodeLength + 1;
                    $zipcode = $zipcode . $stringToParse{$i};
                    break;
                } else {
                    $zipcodeLength = 0;
                    $zipcode = "";
                }
                break;
            }
        }

        // return the parsed zip code if found
        if ( $zipcodeLength == 5 || $zipcodeLength == 10 ) {
            // reverse the zip code
            return strrev($zipcode);
        } else {
            return "";
        }

    }

    // PRIVATE PROCEDURES

    // triggers user error if the zip code is invalid
    // valid zip codes are of the form DDDDD or DDDDD-DDDD
    // where D is a digit from 0 to 9, returns the validated zip code
    function _ValidateZipCode($zipcode)
    {
        $functionname = "ValidateZipCode Error: ";

        // check if zipcode is an array or object
        if(is_array($zipcode) || is_object($zipcode))
        {
            trigger_error($functionname.
                "Zip code may not be an array or object.", E_USER_ERROR);
        }

        // convert zip code to a string
        $zipcode = strval($zipcode);

        // check if length is 5
        if ( strlen($zipcode) != 5 && strlen($zipcode) != 10 ) {
            trigger_error($functionname.
                "Zip code must be 5 digits or 10 digits including hyphen. len:".
                strlen($zipcode)." zipcode: ".$zipcode, E_USER_ERROR);
        }

        if ( strlen($zipcode) == 5 ) {
            // check that all characters are numeric
            for ( $i = 0; $i < 5; $i++ ) {
                if ( is_numeric( $zipcode{$i} ) == false ) {
                    trigger_error($functionname.
                        "5 digit zip code contains non-numeric character.",
                        E_USER_ERROR);
                }
            }
        } else {
            // check for hyphen
            if ( $zipcode{5} != "-" ) {
                trigger_error($functionname.
                    "10 digit zip code does not contain hyphen in right place.",
                    E_USER_ERROR);
            }
            // check that all characters are numeric
            for ( $i = 0; $i < 10; $i++ ) {
                if ( is_numeric($zipcode{$i}) == false && $i != 5 ) {
                    trigger_error($functionname.
                        "10 digit zip code contains non-numeric character.",
                        E_USER_ERROR);
                }
            }
        }

        // return the string
        return $zipcode;
    }

    // takes a validated zip code and 
    // calculates the checksum for POSTNET
    function _CalculateCheckSumDigit($zipcode)
    {
        // calculate sum of digits
        if( strlen($zipcode) == 10 ) {
            $sumOfDigits = $zipcode{0} + $zipcode{1} + 
                $zipcode{2} + $zipcode{3} + $zipcode{4} + 
                $zipcode{6} + $zipcode{7} + $zipcode{8} + 
                $zipcode{9};
        } else {
            $sumOfDigits = $zipcode{0} + $zipcode{1} + 
                $zipcode{2} + $zipcode{3} + $zipcode{4};
        }

        // return checksum digit
        if( ($sumOfDigits % 10) == 0 )
            return 0;
        else
            return 10 - ($sumOfDigits % 10);
    }

    // Takes a digit and draws the corresponding POSTNET bars.
    function _DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight, $FullBarHeight,
        $BarDefinitionsArray, $digit)
    {
        // check for invalid digit
        if($digit < 0 && $digit > 9)
            trigger_error("DrawDigitBars: invalid digit.", E_USER_ERROR);
        
        // draw the five bars representing a digit
        for($i = 0; $i < 5; $i++)
        {
            if($BarDefinitionsArray[$digit][$i] == 1)
                $this->Line($x, $y, $x, $y - $FullBarHeight);
            else
                $this->Line($x, $y, $x, $y - $HalfBarHeight);
            $x += $BarSpacing;
        }
    }

}
?>