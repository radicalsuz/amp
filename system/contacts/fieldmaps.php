<?php

# this file stores the mapping back and forth
# between the mysql contacts database
# and the fields names that outlook requires

$outlook_to_mysql = array(
	"First Name" => "FirstName",
	"Middle Name" => "MiddleName",
	"Last Name" => "LastName",
	"Suffix" => "Suffix",
	"Company" => "Company",
	"Department" => "Department",
	"Job Title" => "JobTitle",
	"Business Street" => "BusinessStreet",
	"Business Street 2" => "BusinessStreet2",
	"Business Street 3" => "BusinessStreet3",
	"Business City" => "BusinessCity",
	"Business State" => "BusinessState",
	"Business Postal Code" => "BusinessPostalCode",
	"Business Country" => "BusinessCountry",
	"Home Street" => "HomeStreet",
	"Home Street 2" => "HomeStreet2",
	"Home Street 3" => "HomeStreet3",
	"Home City" => "HomeCity",
	"Home State" => "HomeState",
	"Home Postal Code" => "HomePostalCode",
	"Home Country" => "HomeCountry",
	"Other Street" => "OtherStreet",
	"Other Street 2" => "OtherStreet2",
	"Other Street 3" => "OtherStreet3",
	"Other City" => "OtherCity",
	"Other State" => "OtherState",
	"Other Postal Code" => "OtherPostalCode",
	"Other Country" => "OtherCountry",
	"Business Fax" => "BusinessFax",
	"Business Phone" => "BusinessPhone",
	"Business Phone 2" => "BusinessPhone2",
	"Company Main Phone" => "CompanyMainPhone",
	"Home Fax" => "HomeFax",
	"Home Phone" => "HomePhone",
	"Home Phone 2" => "HomePhone2",
	"ISDN" => "ISDN",
	"Mobile Phone" => "MobilePhone",
	"Other Fax" => "OtherFax",
	"Other Phone" => "OtherPhone",
	"Pager" => "Pager",
	"Primary Phone" => "PrimaryPhone",
	"Radio Phone" => "RadioPhone",
	"TTY/TDD Phone" => "TTYTDDPhone",
	"Telex" => "Telex",
	"Account" => "Account",
	"Anniversary" => "Anniversary",
	"Assistant's Name" => "AssistantsName",
	"Billing Information" => "BillingInformation",
	"Birthday" => "Birthday",
	"Categories" => "Categories",
	"Children" => "Children",
	"Directory Server" => "DirectoryServer",
	"E-mail Address" => "EmailAddress",
	"E-mail Display Name" => "EmailDisplayName",
	"E-mail 2 Address" => "Email2Address",
	"E-mail 2 Display Name" => "Email2DisplayName",
	"E-mail 3 Address" => "Email3Address",
	"E-mail 3 Display Name" => "Email3DisplayName",
	"Notes" => "notes",
	"Web Page" => "WebPage"
);

$mysql_to_outlook = array_flip($outlook_to_mysql);
$mysql_fields     = array_values($outlook_to_mysql);
$outlook_fields   = array_keys($outlook_to_mysql);
$outlook_to_id    = array_flip($outlook_fields);

?>
