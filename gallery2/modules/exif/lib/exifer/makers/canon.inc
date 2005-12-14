<?php
//================================================================================================
//================================================================================================
//================================================================================================
/*
	Exifer
	Extracts EXIF information from digital photos.
	
	Copyright © 2003 Jake Olefsky
	http://www.offsky.com/software/exif/index.php
	jake@olefsky.com
	
	Please see exif.php for the complete information about this software.
	
	------------
	
	This program is free software; you can redistribute it and/or modify it under the terms of 
	the GNU General Public License as published by the Free Software Foundation; either version 2 
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
	See the GNU General Public License for more details. http://www.gnu.org/copyleft/gpl.html
*/
//================================================================================================
//================================================================================================
//================================================================================================


//=================
// Looks up the name of the tag for the MakerNote (Depends on Manufacturer)
//====================================================================
function lookup_Canon_tag($tag) {
	
	switch($tag) {
		case "0001": $tag = "Settings 1";break;
		case "0004": $tag = "Settings 4";break;
		case "0006": $tag = "ImageType";break;
		case "0007": $tag = "FirmwareVersion";break;
		case "0008": $tag = "ImageNumber";break;
		case "0009": $tag = "OwnerName";break;
		case "000c": $tag = "CameraSerialNumber";break;	
		case "000f": $tag = "CustomFunctions";break;	
		
		default: $tag = "unknown:".$tag;break;
	}
	
	return $tag;
}

//=================
// Formats Data for the data type
//====================================================================
function formatCanonData($type,$tag,$intel,$data,$exif,&$result) {
	$place = 0;
	
	if($type=="ASCII") {
		$data = preg_replace('/\0/', '', $data);
		$result = $data;
		
	} else if($type=="URATIONAL" || $type=="SRATIONAL") {
		$data = bin2hex($data);
		if($intel==1) $data = intel2Moto($data);
		$top = hexdec(substr($data,8,8));
		$bottom = hexdec(substr($data,0,8));
		if($bottom!=0) $data=$top/$bottom;
		else if($top==0) $data = 0;
		else $data=$top."/".$bottom;
	
		if($tag=="0204") { //DigitalZoom
			$data=$data."x";
		} 
		
	} else if($type=="USHORT" || $type=="SSHORT" || $type=="ULONG" || $type=="SLONG" || $type=="FLOAT" || $type=="DOUBLE") {
		
		$data = bin2hex($data);
		$result['RAWDATA'] = $data;

		if($tag=="0001") { //first chunk
			$result['Bytes']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//0
			if ($result['Bytes'] != strlen($data) / 2) {
				/* Something's wrong with this data chunk */
				return $result;
			}
			$result['Macro']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//1
				switch ($result['Macro']) {
				case 1: $result['Macro'] = "Macro"; break;
				case 2: $result['Macro'] = "Normal"; break;
				default: $result['Macro'] = "Unknown"; break;
				}
			$result['SelfTimer']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//2
				if($result['SelfTimer']==0) {
					$result['SelfTimer'] = "Off";
				} else {
					$result['SelfTimer'] = $result['SelfTimer'] . "/10s";
				}
			$result['Quality']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//3
				switch ($result['Quality']) {
				case 2: $result['Quality'] = "Normal"; break;
				case 3: $result['Quality'] = "Fine"; break;
				case 5: $result['Quality'] = "Superfine"; break;
				default: $result['Quality'] = "Unknown"; break;
				}
			$result['Flash']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//4
				switch ($result['Flash']) {
				case 0: $result['Flash'] = "Off"; break;
				case 1: $result['Flash'] = "Auto"; break;
				case 2: $result['Flash'] = "On"; break;
				case 3: $result['Flash'] = "Red-eye reduction"; break;
				case 4: $result['Flash'] = "Slow synchro"; break;
				case 5: $result['Flash'] = "Auto + Red-eye reduction"; break;
				case 6: $result['Flash'] = "On + Red-eye reduction"; break;
				case 16: $result['Flash'] = "External Flash"; break;
				default: $result['Flash'] = "Unknown"; break;
				}
			$result['DriveMode']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//5
				switch ($result['DriveMode']) {
				case 0: $result['DriveMode'] = "Single/Timer"; break;
				case 1: $result['DriveMode'] = "Continuous"; break;
				default: $result['DriveMode'] = "Unknown"; break;
				}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//6
			$result['FocusMode']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//7
				switch ($result['FocusMode']) {
				case 0: $result['FocusMode'] = "One-Shot"; break;
				case 1: $result['FocusMode'] = "AI Servo"; break;
				case 2: $result['FocusMode'] = "AI Focus"; break;
				case 3: $result['FocusMode'] = "Manual"; break;
				case 4: $result['FocusMode'] = "Single"; break;
				case 5: $result['FocusMode'] = "Continuous"; break;
				case 6: $result['FocusMode'] = "Manual"; break;
				default: $result['FocusMode'] = "Unknown"; break;
				}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//8
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//9
			$result['ImageSize']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//10
				switch ($result['ImageSize']) {
				case 0: $result['ImageSize'] = "Large"; break;
				case 1: $result['ImageSize'] = "Medium"; break;
				case 2: $result['ImageSize'] = "Small"; break;
				default: $result['ImageSize'] = "Unknown"; break;
				}
			$result['EasyShooting']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//11
				switch ($result['EasyShooting']) {
				case 0: $result['EasyShooting'] = "Full Auto"; break;
				case 1: $result['EasyShooting'] = "Manual"; break;
				case 2: $result['EasyShooting'] = "Landscape"; break;
				case 3: $result['EasyShooting'] = "Fast Shutter"; break;
				case 4: $result['EasyShooting'] = "Slow Shutter"; break;
				case 5: $result['EasyShooting'] = "Night"; break;
				case 6: $result['EasyShooting'] = "Black & White"; break;
				case 7: $result['EasyShooting'] = "Sepia"; break;
				case 8: $result['EasyShooting'] = "Portrait"; break;
				case 9: $result['EasyShooting'] = "Sport"; break;
				case 10: $result['EasyShooting'] = "Macro/Close-Up"; break;
				case 11: $result['EasyShooting'] = "Pan Focus"; break;
				default: $result['EasyShooting'] = "Unknown"; break;
				}
			$result['DigitalZoomRatio']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//12
				switch ($result['DigitalZoomRatio']) {
				case 0:
				case 65535: $result['DigitalZoomRatio'] = "None"; break;
				case 1: $result['DigitalZoomRatio'] = "2x"; break;
				case 2: $result['DigitalZoomRatio'] = "4x"; break;
				default: $result['DigitalZoomRatio'] = "Unknown"; break;
				}
			$result['Contrast']=intel2Moto(substr($data,$place,4));$place+=4;//13
				switch ($result['Contrast']) {
				case "ffff": $result['Contrast'] = "Low"; break;
				case "0000": $result['Contrast'] = "Normal"; break;
				case "0001": $result['Contrast'] = "High"; break;
				default: $result['Contrast'] = "Unknown"; break;
				}
			$result['Saturation']=intel2Moto(substr($data,$place,4));$place+=4;//14
				switch ($result['Saturation']) {
				case "ffff": $result['Saturation'] = "Low"; break;
				case "0000": $result['Saturation'] = "Normal"; break;
				case "0001": $result['Saturation'] = "High"; break;
				default: $result['Saturation'] = "Unknown"; break;
				}
			$result['Sharpness']=intel2Moto(substr($data,$place,4));$place+=4;//15
				switch ($result['Sharpness']) {
				case "ffff": $result['Sharpness'] = "Low"; break;
				case "0000": $result['Sharpness'] = "Normal"; break;
				case "0001": $result['Sharpness'] = "High"; break;
				default: $result['Sharpness'] = "Unknown"; break;
				}
			$result['ISO']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//16
				switch ($result['ISO']) {
				case 0: $result['ISO'] = $exif['SubIFD']['ISOSpeedRatings']; break;
				case 15: $result['ISO'] = "Auto"; break;
				case 16: $result['ISO'] = "50"; break;
				case 17: $result['ISO'] = "100"; break;
				case 18: $result['ISO'] = "200"; break;
				case 19: $result['ISO'] = "400"; break;
				default: $result['ISO'] = "Unknown"; break;
				}
			$result['MeteringMode']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//17
				if($result['MeteringMode']==3) $result['MeteringMode'] = "Evaluative";
				if($result['MeteringMode']==4) $result['MeteringMode'] = "Partial";
				if($result['MeteringMode']==5) $result['MeteringMode'] = "Center-weighted";
			$result['FocusType']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//18
				switch ($result['FocusType']) {
				case 0: $result['FocusType'] = "Manual"; break;
				case 1: $result['FocusType'] = "Auto"; break;
				case 3: $result['FocusType'] = "Close-up (Macro)"; break;
				case 8: $result['FocusType'] = "Locked (Pan Mode)"; break;
				default: $result['FocusType'] = "Unknown"; break;
				}
			$result['AFPointSelected']=intel2Moto(substr($data,$place,4));$place+=4;//19
				switch ($result['AFPointSelected']) {
				case "3000": $result['AFPointSelected'] = "None (MF)"; break;
				case "3001": $result['AFPointSelected'] = "Auto-selected"; break;
				case "3002": $result['AFPointSelected'] = "Right"; break;
				case "3003": $result['AFPointSelected'] = "Center"; break;
				case "3004": $result['AFPointSelected'] = "Left"; break;
				default: $result['AFPointSelected'] = "Unknown (" . $result['AFPointSelected'] . ")"; break;
				}
			$result['ExposureMode']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//20
				switch ($result['ExposureMode']) {
				case 0: $result['ExposureMode'] = "Easy Shooting"; break;
				case 1: $result['ExposureMode'] = "Program"; break;
				case 2: $result['ExposureMode'] = "Tv"; break;
				case 3: $result['ExposureMode'] = "Av"; break;
				case 4: $result['ExposureMode'] = "Manual"; break;
				case 5: $result['ExposureMode'] = "Auto-DEP"; break;
				default: $result['ExposureMode'] = "Unknown"; break;
				}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//21
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//22
			$result['LongFocalLength']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//23
				$result['LongFocalLength'] .= " focal units";
			$result['ShortFocalLength']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//24
				$result['ShortFocalLength'] .= " focal units";
			$result['FocalUnits']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//25
				$result['FocalUnits'] .= " per mm";
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//26
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//27
			$result['FlashActivity']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//28
				switch ($result['FlashActivity']) {
				case 0: $result['FlashActivity'] = "Did not fire"; break;
				case 1: $result['FlashActivity'] = "Fired"; break;
				default: $result['FlashActivity'] = "Unknown"; break;
				}
			$flashDetails=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//29
				$arr = array();
				if ($flashDetails & 0x0008) { $arr[] = "FP sync enabled"; } // bit 4
				if ($flashDetails & 0x0040) { $arr[] = "2nd/rear-curtain sync used"; } // bit 7
				if ($flashDetails & 0x0400) { $arr[] = "FP sync used"; }    // bit 11
				if ($flashDetails & 0x1000) { $arr[] = "internal flash"; }  // bit 13
				if ($flashDetails & 0x2000) { $arr[] = "external E-TTL"; }  // bit 14
				if (!empty($arr)) {
					$result['FlashDetails'] = join(', ', $arr);
				}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//30
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//31
			$anotherFocusMode=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//32
			if (strpos(strtoupper($exif['IFD0']['Model']), "G1") !== false) {
				switch ($anotherFocusMode) {
				case 0: $result['FocusMode'] = "Single"; break;
				case 1: $result['FocusMode'] = "Continuous"; break;
				default: $result['FocusMode'] = "Unknown"; break;
				}
			}
			
		} else if($tag=="0004") { //second chunk
			$result['Bytes']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//0
			if ($result['Bytes'] != strlen($data) / 2) {
				return $result;
			}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//1
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//2
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//3
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//4
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//5
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//6
			$result['WhiteBalance']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//7
				switch ($result['WhiteBalance']) {
				case 0: $result['WhiteBalance'] = "Auto"; break;
				case 1: $result['WhiteBalance'] = "Sunny"; break;
				case 2: $result['WhiteBalance'] = "Cloudy"; break;
				case 3: $result['WhiteBalance'] = "Tungsten"; break;
				case 4: $result['WhiteBalance'] = "Flourescent"; break;
				case 5: $result['WhiteBalance'] = "Flash"; break;
				case 6: $result['WhiteBalance'] = "Custom"; break;
				default: $result['WhiteBalance'] = "Unknown"; break;
				}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//8
			$result['SequenceNumber']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//9
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//10
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//11
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//12
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//13
			$afPointUsed=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//14
				$arr = array();
				if ($afPointUsed & 0x0001) { $arr[] = "right"; }  // bit 0
				if ($afPointUsed & 0x0002) { $arr[] = "center"; } // bit 1
				if ($afPointUsed & 0x0004) { $arr[] = "left"; }   // bit 2
				if ($afPointUsed & 0x0800) { $arr[] = "12"; }     // bit 12
				if ($afPointUsed & 0x1000) { $arr[] = "13"; }     // bit 13
				if ($afPointUsed & 0x2000) { $arr[] = "14"; }     // bit 14
				if ($afPointUsed & 0x4000) { $arr[] = "15"; }     // bit 15
				if (!empty($arr)) {
					$result['AFPointUsed'] = join(", ", $arr);
				}
			$result['FlashBias']=intel2Moto(substr($data,$place,4));$place+=4;//15
				switch ($result['FlashBias']) {
				case "ffc0": $result['FlashBias'] = "-2 EV"; break;
				case "ffcc": $result['FlashBias'] = "-1.67 EV"; break;
				case "ffd0": $result['FlashBias'] = "-1.50 EV"; break;
				case "ffd4": $result['FlashBias'] = "-1.33 EV"; break;
				case "ffe0": $result['FlashBias'] = "-1 EV"; break;
				case "ffec": $result['FlashBias'] = "-0.67 EV"; break;
				case "fff0": $result['FlashBias'] = "-0.50 EV"; break;
				case "fff4": $result['FlashBias'] = "-0.33 EV"; break;
				case "0000": $result['FlashBias'] = "0 EV"; break;
				case "000c": $result['FlashBias'] = "0.33 EV"; break;
				case "0010": $result['FlashBias'] = "0.50 EV"; break;
				case "0014": $result['FlashBias'] = "0.67 EV"; break;
				case "0020": $result['FlashBias'] = "1 EV"; break;
				case "002c": $result['FlashBias'] = "1.33 EV"; break;
				case "0030": $result['FlashBias'] = "1.50 EV"; break;
				case "0034": $result['FlashBias'] = "1.67 EV"; break;
				case "0040": $result['FlashBias'] = "2 EV"; break;
				default: $result['FlashBias'] = "Unknown"; break;
				}
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//16
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//17
			$result['Unknown']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//18
			$result['SubjectDistance']=hexdec(intel2Moto(substr($data,$place,4)));$place+=4;//19
				$result['SubjectDistance'] .= "/100 m";
			
		} else if($tag=="0008") { //image number
			if($intel==1) $data = intel2Moto($data);
			$data=hexdec($data);
			$result = round($data/10000)."-".$data%10000;
		
		} else if($tag=="000c") { //camera serial number
			if($intel==1) $data = intel2Moto($data);
			$data=hexdec($data);
			$result = "#".bin2hex(substr($data,0,16)).substr($data,16,16);
		}
		
	} else if($type=="UNDEFINED") {
		
	} else {
		$data = bin2hex($data);
		if($intel==1) $data = intel2Moto($data);
	}
	
	return $data;
}



//=================
// Cannon Special data section
// Useful:  http://www.burren.cx/david/canon.html
//====================================================================
function parseCanon($block,&$result,$seek, $globalOffset) {	
	$place = 0; //current place
		
	if($result['Endien']=="Intel") $intel=1;
	else $intel=0;
	
	$model = $result['IFD0']['Model'];
	
		//Get number of tags (2 bytes)
	$num = bin2hex(substr($block,$place,2));$place+=2;
	if($intel==1) $num = intel2Moto($num);
	$result['SubIFD']['MakerNote']['MakerNoteNumTags'] = hexdec($num);
	
	//loop thru all tags  Each field is 12 bytes
	for($i=0;$i<hexdec($num);$i++) {
		
			//2 byte tag
		$tag = bin2hex(substr($block,$place,2));$place+=2;
		if($intel==1) $tag = intel2Moto($tag);
		$tag_name = lookup_Canon_tag($tag);
		
			//2 byte type
		$type = bin2hex(substr($block,$place,2));$place+=2;
		if($intel==1) $type = intel2Moto($type);
		lookup_type($type,$size);
		
			//4 byte count of number of data units
		$count = bin2hex(substr($block,$place,4));$place+=4;
		if($intel==1) $count = intel2Moto($count);
		$bytesofdata = $size*hexdec($count);
	
		if($bytesofdata<=0) {
			return; //if this value is 0 or less then we have read all the tags we can
		}
		
			//4 byte value of data or pointer to data
		$value = substr($block,$place,4);$place+=4;

		if($bytesofdata<=4) {
			$data = $value;
		} else {
			$value = bin2hex($value);
			if($intel==1) $value = intel2Moto($value);
			$v = fseek($seek,$globalOffset+hexdec($value));  //offsets are from TIFF header which is 12 bytes from the start of the file
			if($v==0) {
				$data = fread($seek, $bytesofdata);
			} else if($v==-1) {
				$result['Errors'] = $result['Errors']++;
			}
		}
		$formated_data = formatCanonData($type,$tag,$intel,$data,$result,$result['SubIFD']['MakerNote'][$tag_name]);

		if($result['VerboseOutput']==1) {
			//$result['SubIFD']['MakerNote'][$tag_name] = $formated_data;
			if($type=="URATIONAL" || $type=="SRATIONAL" || $type=="USHORT" || $type=="SSHORT" || $type=="ULONG" || $type=="SLONG" || $type=="FLOAT" || $type=="DOUBLE") {
				$data = bin2hex($data);
				if($intel==1) $data = intel2Moto($data);
			}
			$result['SubIFD']['MakerNote'][$tag_name."_Verbose"]['RawData'] = $data;
			$result['SubIFD']['MakerNote'][$tag_name."_Verbose"]['Type'] = $type;
			$result['SubIFD']['MakerNote'][$tag_name."_Verbose"]['Bytes'] = $bytesofdata;
		} else {
			//$result['SubIFD']['MakerNote'][$tag_name] = $formated_data;
		}
	}
}


?>
