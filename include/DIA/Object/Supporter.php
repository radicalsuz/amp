<?php

require_once('DIA/Object.php');

class DIA_Supporter extends DIA_Object {

    var $_table = 'supporter';

	function getSupporterKey() {
		return $this->getProperty('supporter_KEY');
	}

	function setSupporterKey($supporter_key) {
		return $this->setProperty('supporter_KEY', $supporter_key);
	}

	function getOrganizationKey() {
		return $this->getProperty('organization_KEY');
	}

	function setOrganizationKey($organization_key) {
		return $this->setProperty('organization_KEY', $organization_key);
	}

	function getChapterKey() {
		return $this->getProperty('chapter_KEY');
	}

	function setChapterKey($chapter_key) {
		return $this->setProperty('chapter_KEY', $chapter_key);
	}

	function getLastModified() {
		return $this->getProperty('Last_Modified');
	}

	function setLastModified($last_modified) {
		return $this->setProperty('Last_Modified', $last_modified);
	}

	function getDateCreated() {
		return $this->getProperty('Date_Created');
	}

	function setDateCreated($date_created) {
		return $this->setProperty('Date_Created', $date_created);
	}

	function getTitle() {
		return $this->getProperty('Title');
	}

	function setTitle($title) {
		return $this->setProperty('Title', $title);
	}

	function getFirstName() {
		return $this->getProperty('First_Name');
	}

	function setFirstName($first_name) {
		return $this->setProperty('First_Name', $first_name);
	}

	function getMI() {
		return $this->getProperty('MI');
	}

	function setMI($middle_initial) {
		return $this->setProperty('MI', $middle_initial);
	}

	function getMiddleInitial() {
		return $this->getMI();
	}

	function setMiddleInitial($middle_initial) {
		return $this->setMI($middle_initial);
	}

	function getLastName() {
		return $this->getProperty('Last_Name');
	}

	function setLastName($last_name) {
		return $this->setProperty('Last_Name', $last_name);
	}

	function getSuffix() {
		return $this->getProperty('Suffix');
	}

	function setSuffix($suffix) {
		return $this->setProperty('Suffix', $suffix);
	}

	function getEmail() {
		return $this->getProperty('Email');
	}

	function setEmail($email) {
		return $this->setProperty('Email', $email);
	}

	function getPassword($encode = null) {
		return $this->getProperty('Password');
	}

	function setPassword($password, $decode = null) {
		return $this->setProperty('Password', $password);
	}

	function getReceiveEmail() {
		return $this->getProperty('Receive_Email');
	}

	function setReceiveEmail($receive_email) {
		return $this->setProperty('Receive_Email', $receive_email);
	}

	function getEmailStatus() {
		return $this->getProperty('Email_Status');
	}

	function setEmailStatus($email_status) {
		return $this->setProperty('Email_Status', $email_status);
	}

	function getEmailPreference() {
		return $this->getProperty('Email_Preference');
	}

	function setEmailPreference($email_preference) {
		return $this->setProperty('Email_Preference', $email_preference);
	}

	function getSoftBounceCount() {
		return $this->getProperty('Soft_Bounce_Count');
	}

	function setSoftBounceCount($soft_bounce_count) {
		return $this->setProperty('Soft_Bounce_Count', $soft_bounce_count);
	}

	function getHardBounceCount() {
		return $this->getProperty('Hard_Bounce_Count');
	}

	function setHardBounceCount($hard_bounce_count) {
		return $this->setProperty('Hard_Bounce_Count', $hard_bounce_count);
	}

	function getLastBounce() {
		return $this->getProperty('Last_Bounce');
	}

	function setLastBounce($last_bounce) {
		return $this->setProperty('Last_Bounce', $last_bounce);
	}

	function getReceivePhoneBlasts() {
		return $this->getProperty('Receive_Phone_Blasts');
	}

	function setReceivePhoneBlasts($receive_phone_blasts) {
		return $this->setProperty('Receive_Phone_Blasts', $receive_phone_blasts);
	}

	function getPhone() {
		return $this->getProperty('Phone');
	}

	function setPhone($phone) {
		return $this->setProperty('Phone', $phone);
	}

	function getCellPhone() {
		return $this->getProperty('Cell_Phone');
	}

	function setCellPhone($cell_phone) {
		return $this->setProperty('Cell_Phone', $cell_phone);
	}

	function getPhoneProvider() {
		return $this->getProperty('Phone_Provider');
	}

	function setPhoneProvider($phone_provider) {
		return $this->setProperty('Phone_Provider', $phone_provider);
	}

	function getWorkPhone() {
		return $this->getProperty('Work_Phone');
	}

	function setWorkPhone($work_phone) {
		return $this->setProperty('Work_Phone', $work_phone);
	}

	function getPager() {
		return $this->getProperty('Pager');
	}

	function setPager($pager) {
		return $this->setProperty('Pager', $pager);
	}

	function getHomeFax() {
		return $this->getProperty('Home_Fax');
	}

	function setHomeFax($home_fax) {
		return $this->setProperty('Home_Fax', $home_fax);
	}

	function getWorkFax() {
		return $this->getProperty('Work_Fax');
	}

	function setWorkFax($work_fax) {
		return $this->setProperty('Work_Fax', $work_fax);
	}

	function getStreet() {
		return $this->getProperty('Street');
	}

	function setStreet($street) {
		return $this->setProperty('Street', $street);
	}

	function getStreet2() {
		return $this->getProperty('Street_2');
	}

	function setStreet2($street_2) {
		return $this->setProperty('Street_2', $street_2);
	}

	function getStreet3() {
		return $this->getProperty('Street_3');
	}

	function setStreet3($street_3) {
		return $this->setProperty('Street_3', $street_3);
	}

	function getCity() {
		return $this->getProperty('City');
	}

	function setCity($city) {
		return $this->setProperty('City', $city);
	}

	function getState() {
		return $this->getProperty('State');
	}

	function setState($state) {
		return $this->setProperty('State', $state);
	}

	function getZip() {
		return $this->getProperty('Zip');
	}

	function setZip($zip) {
		return $this->setProperty('Zip', $zip);
	}

	function getPrivateZipPlus4() {
		return $this->getProperty('PRIVATE_Zip_Plus_4');
	}

	function setPrivateZipPlus4($private_zip_plus_4) {
		return $this->setProperty('PRIVATE_Zip_Plus_4', $private_zip_plus_4);
	}

	function getCounty() {
		return $this->getProperty('County');
	}

	function setCounty($county) {
		return $this->setProperty('County', $county);
	}

	function getRegion() {
		return $this->getProperty('Region');
	}

	function setRegion($region) {
		return $this->setProperty('Region', $region);
	}

	function getCountry() {
		return $this->getProperty('Country');
	}

	function setCountry($country) {
		return $this->setProperty('Country', $country);
	}

	function getLatitude() {
		return $this->getProperty('Latitude');
	}

	function setLatitude($latitude) {
		return $this->setProperty('Latitude', $latitude);
	}

	function getLongitude() {
		return $this->getProperty('Longitude');
	}

	function setLongitude($longitude) {
		return $this->setProperty('Longitude', $longitude);
	}

	function getOrganization() {
		return $this->getProperty('Organization');
	}

	function setOrganization($organization) {
		return $this->setProperty('Organization', $organization);
	}

	function getDepartment() {
		return $this->getProperty('Department');
	}

	function setDepartment($department) {
		return $this->setProperty('Department', $department);
	}

	function getOccupation() {
		return $this->getProperty('Occupation');
	}

	function setOccupation($occupation) {
		return $this->setProperty('Occupation', $occupation);
	}

	function getInstantMessengerService() {
		return $this->getProperty('Instant_Messenger_Service');
	}

	function setInstantMessengerService($instant_messenger_service) {
		return $this->setProperty('Instant_Messenger_Service', $instant_messenger_service);
	}

	function getInstantMessengerName() {
		return $this->getProperty('Instant_Messenger_Name');
	}

	function setInstantMessengerName($instant_messenger_name) {
		return $this->setProperty('Instant_Messenger_Name', $instant_messenger_name);
	}

	function getWebPage() {
		return $this->getProperty('Web_Page');
	}

	function setWebPage($web_page) {
		return $this->setProperty('Web_Page', $web_page);
	}

	function getAlternativeEmail() {
		return $this->getProperty('Alternative_Email');
	}

	function setAlternativeEmail($alternative_email) {
		return $this->setProperty('Alternative_Email', $alternative_email);
	}

	function getOtherData1() {
		return $this->getProperty('Other_Data_1');
	}

	function setOtherData1($other_data_1) {
		return $this->setProperty('Other_Data_1', $other_data_1);
	}

	function getOtherData2() {
		return $this->getProperty('Other_Data_2');
	}

	function setOtherData2($other_data_2) {
		return $this->setProperty('Other_Data_2', $other_data_2);
	}

	function getOtherData3() {
		return $this->getProperty('Other_Data_3');
	}

	function setOtherData3($other_data_3) {
		return $this->setProperty('Other_Data_3', $other_data_3);
	}

	function getNotes() {
		return $this->getProperty('Notes');
	}

	function setNotes($notes) {
		return $this->setProperty('Notes', $notes);
	}

	function getSource() {
		return $this->getProperty('Source');
	}

	function setSource($source) {
		return $this->setProperty('Source', $source);
	}

	function getSourceDetails() {
		return $this->getProperty('Source_Details');
	}

	function setSourceDetails($source_details) {
		return $this->setProperty('Source_Details', $source_details);
	}

	function getSourceTrackingCode() {
		return $this->getProperty('Source_Tracking_Code');
	}

	function setSourceTrackingCode($source_tracking_code) {
		return $this->setProperty('Source_Tracking_Code', $source_tracking_code);
	}

	function getTrackingCode() {
		return $this->getProperty('Tracking_Code');
	}

	function setTrackingCode($tracking_code) {
		return $this->setProperty('Tracking_Code', $tracking_code);
	}

	function getStatus() {
		return $this->getProperty('Status');
	}

	function setStatus($status) {
		return $this->setProperty('Status', $status);
	}

	function getUid() {
		return $this->getProperty('uid');
	}

	function setUid($uid) {
		return $this->setProperty('uid', $uid);
	}

	function getTimezone() {
		return $this->getProperty('Timezone');
	}

	function setTimezone($timezone) {
		return $this->setProperty('Timezone', $timezone);
	}

}

?>
