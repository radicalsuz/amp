CREATE TABLE `payment` (
  `id` int(11) NOT NULL auto_increment,
  `user_ID` int(11) NOT NULL default '0',
  `payment_item_ID` int(11) NULL,
  `payment_merchant_ID` int(11) NULL,
  `order_ID` int(11) NULL,
  `description` varchar(100) default NULL,
  `Name_On_Card` varchar(255) default NULL,
  `Payment_Type` varchar(20) default 'CreditCard',
  `Credit_Card_Type` enum('Visa','MasterCard','Amex','Discover') default NULL,
  `Credit_Card_Number` varchar(255) default NULL,
  `Credit_Card_Security_Code` varchar(255) default NULL,
  `Credit_Card_Expiration` varchar(255) default NULL,
  `Check_Number` varchar(15) NULL,
  `Check_Date` date NULL,
  `Check_Routing_Number` varchar(25) NULL,
  `Check_Account_Number` varchar(25) NULL,
  `Amount` float default NULL,
  `Date_Submitted` date default NULL,
  `Date_Processed` date default NULL,
  `Time_Requested` timestamp(14) NOT NULL,
  `Time_Responded` timestamp(14) NOT NULL,
  `Status` enum('Approved','Declined','Awaiting Approval','Error') default NULL,
  `Employer` varchar(255) default NULL,
  `Occupation` varchar(255) default NULL,
  `Billing_Street` varchar(255) default NULL,
  `Billing_Street2` varchar(255) default NULL,
  `Billing_City` varchar(255) default NULL,
  `Billing_State` varchar(255) default NULL,
  `Billing_Zip` varchar(255) default NULL,
  `Billing_Email` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


CREATE TABLE `payment_merchants` (
  `id` int(11) NOT NULL auto_increment,
  `Merchant` varchar(100) default NULL,
  `Account_Type` enum('PF','AN') default NULL,
  `Account_Username` varchar(100) default NULL,
  `Account_Password` varchar(100) default NULL,
  `Partner` varchar (100) default NULL,
  `Server` varchar(100) default NULL,
  `Notes` text,
  `Payment_Method` varchar(50) NOT NULL default 'CC',
  `Payment_Transaction` varchar(50) NOT NULL default 'AUTH_CAPTURE',
  `trans_key` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

/*
CREATE TABLE `payment_type` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_ID` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `description` text,
  `Amount` float default NULL,
  `Amount_Array` varchar(255) default NULL,
  `Amount_Other` float default NULL,
  `Tax_Status` varchar(255) default NULL,
  `Donation_Limit` float default NULL,
  `Thank_You_Email` text,
  `Email_Alert` varchar(50) default NULL,
  `Alert_Customer` varchar(50) default NULL,
  `Alert_Merchant` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
*/

CREATE TABLE `payment_items` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `Amount` float default NULL,
  `Amount_Array` varchar(255) default NULL,
  `Amount_Other` float default NULL,
  `Tax_Status` varchar(255) default NULL,
  `Donation_Limit` float default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
