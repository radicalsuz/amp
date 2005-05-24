<?php
#make ical files
include_once('AMP/BaseDB.php');
include_once('iCal/class.iCal.inc.php');
$iCal = (object) new iCal('', 0, AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.'custom/iCal'); // (ProgrammID, Method (1 = Publish | 0 = Request), Download Directory)


	
	$sql = "SELECT UNIX_TIMESTAMP( date )  AS date, time,  org, contact1, email1, laddress, lcity, lstate, lcountry, lzip, shortdesc, event from calendar WHERE publish =1 AND date <= CURDATE(  ) ";
	$R=$dbcon->Execute("$sql")or DIE('error building ical file'.$dbcon->ErrorMsg());
	//$R->Fields("date")
	//$R->Fields("time")
	//$R->Fields("endtime")
	while (!$R->EOF) {
	
		$contact = $R->Fields("contact1");
		if ($R->Fields("org")) {$contact .= ', '.$R->Fields("org");}
		$organizer = (array) array($contact, $R->Fields("email1"));
		$starttime = $R->Fields("date"); 
		//$endtime = ;
		$location = $R->Fields("location").', '.$R->Fields("laddress"). ' '.$R->Fields("lcity").', '.$R->Fields("lstate").' '.$R->Fields("lzip").' '.$R->Fields("lcountry");
		$categories = array('');
		$description = $R->Fields("shortdesc");
		$title =$R->Fields("event");
		
		
		$iCal->addEvent(
						$organizer, // Organizer
						$starttime, // Start Time (timestamp; for an allday event the startdate has to start at YYYY-mm-dd 00:00:00)
						'allday', // End Time (write 'allday' for an allday event instead of a timestamp)
						$location, // Location
						1, // Transparancy (0 = OPAQUE | 1 = TRANSPARENT)
						$categories, // Array with Strings
						$description, // Description
						$title, // Title
						1, // Class (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
						'', // Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
						5, // Priority = 0-9
						0, // frequency: 0 = once, secoundly - yearly = 1-7
						'', // recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
						0, // Interval for frequency (every 2,3,4 weeks...)
						'', // Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
						0, // Startday of the Week ( 0 = Sunday - 6 = Saturday)
						'', // exeption dates: Array with timestamps of dates that should not be includes in the recurring event
						'',  // Sets the time in minutes an alarm appears before the event in the programm. no alarm if empty string or 0
						1, // Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
						$website, // optional URL for that event
						'en', // Language of the Strings
						'' // Optional UID for this event
					   );
		$R->MoveNext();
	}
	
	$iCal->writeFile();
				 // $iCal->deleteOldFiles(1);
				 //$iCal->outputFile('rdf');
				 
				  // echo "done";


?>
