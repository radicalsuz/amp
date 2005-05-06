<?php

Class Payments {
	var $dbcon;
	var $payment_ID;
	var $user_ID;
	var $payment;
	var $fields;
	var $data;

	function Payments($dbcon,$payment_ID) {
		$this->dbcon = $dbcon;
		$this->payment_ID = $payment_ID;
		$this->set_payment_type_values();
		$this->_register_fields();
	}
	
	
#a function that builds the udm fun to add to a form
	function _register_fields () {

        $fields = &$this->fields;

        // We only need to worry about the fields attached to this instance,
        // since fields are attached directly to specific instances of plugins.
		$fields['Credit_Card_Info'] = array('type'=>'header', 'label'=>'Credit Card Information', 'public'=>false,  'enabled'=>true);
		$fields['Amount'] = array('type'=>'select', 'label'=>'Amount', 'required'=>true, 'public'=>false, 'size'=>40, 'enabled'=>true);
		$fields['Credit_Card_Number'] = array('type'=>'text', 'label'=>'Credit Card Number', 'required'=>true, 'public'=>false, 'size'=>40, 'enabled'=>true);
		$fields['Credit_Card_Type'] = array('type'=>'select', 'label'=>'Credit Card Type', 'required'=>true, 'public'=>false, 'size'=>40, 'values'=>'Visa,Master Card,Discover,Amex','enabled'=>true);
		$fields['Credit_Card_Expiration'] = array('type'=>'text', 'label'=>'Credit Card Expiration (mm/yyyy)', 'required'=>true, 'public'=>false, 'size'=>40, 'enabled'=>true);


		$fields['Payment_Type']=array('type'=>'hidden',  'public'=>true, 'values'=>$this->payment_ID, 'enabled'=>true);
		
    }


#a function that adds the indivudal payment into the db	
	function add_payment() {
		$sql="Insert into payment (user_ID,payment_type_ID,Credit_Card_Type,Credit_Card_Number,Credit_Card_Expiration,Amount,Date_Submitted,Date_Processed,Time_Requested,Status) VALUES('".$this->data['user_ID']."','".$this->payment_ID."','".$this->data['Credit_Card_Type']."','".$this->data['Credit_Card_Number']."','".$this->data['Credit_Card_Expiration']."','".$this->data['Amount']."',NOW(),NOW(),NOW(),'Awaiting Approval')";
		$this->dbcon->Execute($sql) or DIE($sql.$this->dbcon->ErrorMsg());
		$this->payment = $this->dbcon->Insert_ID();
	
	}


#A function that chnges the patment status of a paymnet	

	function payment_status($status) {
		$this->dbcon->Execute("Update payment set Status = '".$status."', time_responded = NOW()
 where id = ".$this->payment) or DIE($sql.$this->dbcon->ErrorMsg());
	}
	
# a function that loads the all of the payment processing information that we will need at another time

	function set_payment_type_values(){
		$sql = "Select t.*, m.*  from payment_type t ,payment_merchant m where t.merchant_ID = m.id = ".$this->payment_ID;
		$R = $this->dbcon->Execute($sql) or DIE($sql.$this->dbcon->ErrorMsg());

		$payment_info['Acount_Type'] = $R->Fields("Acount_Type");
		$payment_info['Account_Username'] = $R->Fields("Account_Username");
		$payment_info['Account_Password'] = $R->Fields("Account_Password");
		$payment_info['Server'] = $R->Fields("Server");
		$payment_info['Payment_Method'] = $R->Fields("Payment_Method");
		$payment_info['Payment_Transaction'] = $R->Fields("Payment_Transaction");
		$payment_info['trans_key'] = $R->Fields("trans_key");

		$payment_info['name'] = $R->Fields("name");
		$payment_info['Alert_Customer'] = $R->Fields("Alert_Customer");
		$payment_info['Alert_Merchant'] = $R->Fields("Alert_Merchant");
		$payment_info['Email_Alert'] = $R->Fields("Email_Alert");
		$payment_info['Thank_You_Email'] = $R->Fields("Thank_You_Email");
		$payment_info['Amount'] = $R->Fields("Amount");
		//to do: add amount array
	}
	
# processes the payment

	function process_payment($data){
	
		$this->data = $data;
	
		//add a payment to the payment table wth status awaiting approval
		$this->add_payment();
		//charge the card
		$results = $this->charge_card();
		if ($results["return_code"] ==1) {$status = 'Approved'; }
		if ($results["return_code"] ==2) {$status = 'Declined'; }
		if ($results["return_code"] ==3) {$status = 'Error'; }
		
		//update the payment status
		$this->payment_status($status);
		
		//return message based on results
		return $status;
	}
	


#charges the card

	function charge_card() {

		$arrPerson = array('First_Name'=>$this->data['First_Name'],
							'Last_Name'=>$this->data['Last_Name'],
							'Address'=>$this->data['Street'],
							'City'=>$this->data['City'],
							'State'=>$this->data['State'],
							'Zip'=>$this->data['Zip'],
							'Email'=>$this->data['Email']
		);
		
		$ChargeResult=$this->ChargeCreditCard( $arrPerson,
										$this->data['Amount'],
										$this->data['Credit_Card_Number'],
										$this->payment_info['name'],
										$this->data['Credit_Card_Expiration'],
										$this->payment_info['Acount_Type'],
										$this->payment_info['Account_Username'],
										$this->payment_info['Account_Password'],
										$strPartner,
										$this->payment,
										$this->payment_info['Alert_Merchant'],
										$this->payment_info['Alert_Customer'],
										$this->payment_info['Payment_Transaction'],
										$this->payment_info['Payment_Method'],
										0);
		//print ("Result Code: ".$ChargeResult["return_code"]."<br>");
		//print ("Result Text: ".$ChargeResult["return_reason"]."<br>");
		return $ChargeResult;
		
	}
	
	
	
	
	
	/*******************************************************************************************
	 *
	 *		Credit Card Function Library
	 *		Name:		ccfunctions.inc
	 *		Author:		Eric Sammons, Vansam Software (www.vansam.com)
	 *		Email:		eric@vansam.com
	 *		Date:		10/23/2001
	 *		Version:	0.4.1
	 *
	 *		Contents:
	 *			Functions:	Encrypt
	 *						Description:	Function to encrypt string
	 *
	 *						Decrypt
	 *						Description:	Function to decrypt encrypted string
	 *
	 *						hex2bin
	 *						Description:	Function to convert from hex to bin (used by Decrypt)
	 *
	 *						Debug
	 *						Description:	Allows debugging on the server
	 *
	 *						SecurePostIt
	 *						Description:	Performs post of data via curl, which
	 *										allows for https posting
	 *										- requires curl & openSSL installed
	 *
	 *						ChargePayFlow
	 *						Description:	Charges credit card with Verisign 
	 *										Payflow Pro Processor
	 *										- requires Verisign SDK installed
	 *
	 *						ChargeAuthorizeNet
	 *						Description:	Charge credit card with Authorize.net
	 *										
	 *						ChargeCreditCard
	 *						Description:	Generic function to charge cc
	 *										Calls either ChargePayFlow or
	 *										ChargeAuthorizeNet
	 *
	 *		Notes:
	 *			(1) For encrypting Credit Card Numbers in database:
	 *				(1a) Must have libmcrypt installed on server 
	 *					(ftp://mcrypt.hellug.gr/pub/mcrypt/libmcrypt/)
	 *				(1b) Must have mcrypt support compiled into PHP 
	 *			(2) Must have curl & OpenSSL installed on server 
	 *					(http://curl.haxx.se & www.openssl.org)
	 *				(2a) Must set path to curl in $curl in SecurePostIt 
	 *					 or have CURL support compiled into PHP
	 *			(3) To use Verisign PayFlow Pro, must have PayFlow SDK installed
	 *				(3a) Must set $PathToSDK in ChargePayFlow
	 *			(4) Must set DEBUG=1 to use Debug function
	 *			(5) Need to have key for using encrypt/decrypt functions
	 *
	 *		To Do:
	 *			(1) Handle AVS from credit card processors.
	 *			(2) Better error-handling.
	 *			(3) Add additional processors, such as iTransact and ECHO
	 *			
	 *		Sample Uses:
	 *
	 *			(1)	Accept billing information via secure (https) web page
	 *			(2) If storing credit card in database, use:
	 *					encrypt($strCCNumber, $strKey) 
	 *				to encrypt number.
	 *			(2a)If grabbing credit card from database, use:
	 *					decrypt($strCCNumber, $strKey)
	 *				to decrypt number.
	 *			(3) Charge the Card:
	 *				 $strReturnCodes=ChargeCreditCard	
	 *								($fltAmount,			// Amount to charge 
	 *								$intCCNumber,			// Decrypted credit card number
	 *								$strDescription,		// Description of charge
	 *								$strCCExpireDate,		// Expiration date of card, mm/yyyy
	 *								$strProcessor,			// Processor: either "PF" or "AN"
	 *								$strLogin,				// Username with processor
	 *								$strPassword,			// Password with processor
	 *								$strPartner,			// Partner ID (Verisign only)	
	 *								$strTransactionID,		// Transaction ID
	 *								$strTransactionType,	// Transaction Type:
	 *														//		AUTH_ONLY, 
	 *														//		AUTH_CAPTURE,
	 * 														//		CAPTURE, 
	 *														//		PRIOR_AUTH_CAPTURE,
	 *  													//		CREDIT, 
	 *														//		VOID
	 * 								$strMethod,				// Method Type:
	 *														//		CC (Credit Card)
	 *								$intTestMode			// Test Mode: 0 (live) or 1 (test)		
	 *								);	
	 *
	 *			(4) Process Response:
	 *				$strReturnCodes["return_code"];		// 1=approved; 2=declined; 3=error
	 *				$strReturnCodes["return_reason"];	// reason for return code
	 *				$strReturnCodes["return_id"];		// ID of transaction
	 *				$strReturnCodes["auth_code"];		// 6-digit bank approval code
	 *
	 *******************************************************************************************/
	
	
	
	/*********************************************************
	 *     Function: debug
	 *  Description: This function will be used to display
	 *               debugging information.
	 *               
	 *       Inputs: $strString (required)- Debug string.
	 *               $strFile - This is the file where the debug
	 *                          function is called from.
	 *               $strLine - This is the line that the
	 *                          debug function is called on.
	 *
	 **********************************************************/
	function Debug ($strString, $strFile="", $strLine="") {
		if (DEBUG) {
			if ($strString && $strFile && $strLine) {
				print "<br><font color=\"Blue\"><b>DEBUG</b>(<font color=\"Red\" size=\"-1\"><b>File</b>: $strFile; <font color=\"Green\"><b>Line</b>: $strLine</font></font>): $strString</font><br>";
			} elseif ($strString && $strFile && (!$strLine)) {
				print "<br><font color=\"Blue\"><b>DEBUG</b>(<font color=\"Red\" size=\"-1\"><b>File</b>: $strFile</font>): $strString</font><br>";
			} else {     
				print "<br><font color=\"Blue\"><b>DEBUG</b>: $strString</font><br>";
			}
		}//End of if debug.
	}//End of debug function.
	
	/******************************************************
	 *     Function: hex2bin
	 *       Author: Eric Sammons (found on PHP web site)
	 * Date Created: 10/24/01
	 *
	 *  Description: Converts a hex string to bin
	 *               
	 *       Inputs:	$data (required)- string to convert.
	 *
	 ********************************************************/
	
	function hex2bin($data) {
	
		$len = strlen($data);
		for($i=0;$i<$len;$i+=2) {
			$newdata .= pack("C",hexdec(substr($data,$i,2)));
		}
		return $newdata;
	} // End of hex2bin
	
	/********************************************************************
	 *     Function: encrypt
	 *       Author: Eric Sammons
	 * Date Created: 10/03/01
	 *
	 *  Description: Encrypts a string with BLOWFISH encryption	
	 *					(can change MCRYPT_BLOWFISH to different algorithm)
	 *               
	 *       Inputs: $strString (required)- string to encrypt.
	 *				 $strKey (required) - key to use in encryption
	 *
	 *	   Requires: mcrypt compiled into PHP (libmcrypt installed), 
	 *			     with blowfish cipher & ecb mode
	 *
	 *********************************************************************/
	
	function encrypt ($strString, $strKey) {
		
		Debug("Encrypted Start: ".$strString);
		if ($strString=="") {
			return $strString;
		}
		$iv = mcrypt_create_iv (mcrypt_get_iv_size (MCRYPT_BLOWFISH, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$enString=mcrypt_ecb(MCRYPT_BLOWFISH, $strKey, $strString, MCRYPT_ENCRYPT, $iv);
		$enString=bin2hex($enString);
		Debug ("Encrypted: ".$enString);
		return ($enString);
		
	} // End of encrypt function
	
	
	/********************************************************************
	 *     Function: decrypt
	 *       Author: Eric Sammons
	 * Date Created: 10/03/01
	 *
	 *  Description: Decrypts a string with BLOWFISH encryption
	 *				(can change MCRYPT_BLOWFISH to different algorithm)
	 *               
	 *       Inputs: $strString (required)- string to decrypt.
	 *				 $strKey (required) - key to use in decryption
	 *
	 *	   Requires: mcrypt compiled into PHP (libmcrypt installed), 
	 *			     with blowfish cipher & ecb mode
	 *
	 *********************************************************************/
	
	function decrypt ($strString, $strKey) {
		
		Debug("Decrypted Start: ".$strString);
		if ($strString=="") {
			return $strString;
		}
		$iv = mcrypt_create_iv (mcrypt_get_iv_size (MCRYPT_BLOWFISH, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$strString=hex2bin($strString);
		$deString=mcrypt_ecb(MCRYPT_BLOWFISH, $strKey, $strString, MCRYPT_DECRYPT, $iv);
		Debug ("Decrypted: ".$deString);
		return ($deString);
	
	}
	
	/*********************************************************************
	 *
	 *  SecurePostIt
	 *	Author: Eric Sammons
	 *	Date: 10/23/01
	 *
	 *  Purpose: uses curl (http://curl.haxx.se) to securely post to 
	 *			 remote page
	 *
	 *********************************************************************/
	function SecurePostIt($ascVarStream, $strURL)
	{
		$strRequestBody = ""; 
		while (list($key, $val) = each($ascVarStream)) 
		{ 
			if($strRequestBody != "") 
				$strRequestBody.= "&"; 
			$strRequestBody.= $key."=".$val; 
		} 
	
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_URL, $strURL);
		curl_setopt ($ch, CURLOPT_POST, $strRequestBody);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $strRequestBody);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,0);
		$return_string = curl_exec ($ch);
		curl_close ($ch);
		
		Debug ("return_string: ".$return_string,__FILE__,__LINE__);
		if ($return_string=="") {
			$message="Error: Could not post to remote system.";
			return $message;
		}
		return $return_string;
		
	/* If PHP is compiled with CURL support, then use the below code.
	 * Note: there is a bug in PHP 4.0.6 that does not allow this to work over 
	 * secure (https) connections
	 * Note 2: Using the exec function is a potential security risk. Logged in users
	 * would be able to see the data (which includes the cc number) with the ps command
	 * during the execution of the program
	
	
	
		$curl="/usr/local/bin/curl";
		Debug('$strRequestBody: '.$strRequestBody."<br>");
		$strCmd = $curl." -d \"$strRequestBody\" -m 60 -s -S $strURL";
		Debug('$strCmd: '.$strCmd."<br>");
	
		exec($strCmd, $return_string, $return_val);
		
		for($i=0;$i<count($return_string);$i++)
		{
			Debug("return_string".$return_string[$i]);
		}
		if ($return_val!=0) {
			$message="Error: Could not post to remote system.";
			return $message;
		}
		return $return_string[0];
	*/
	} // End function SecurePostIt
	
	
	/***********************************************************************************
	   *     Function: ChargePayflow
	   *       Author: Eric Sammons
	   * Date Created: 10/21/01
	   *
	   *  Description: This function will open the gateway
	   *               to Verisign Payflow and charge a credit card.
	   *               
	   *               
	   *       Inputs: $fltAmount - charge amount.
	   *               $intCCNumber - credit card number.
	   *               $strDescription - Charge Description.
	   *               $strCCExpireDate - Credit Card expiration date.
	   *               $strLogin - Verisign PayFlow Login.
	   *               $strPassword - Verisign PayFlow password.
	   *			   $strPartner - PayFlow Parter
	   *               $strTransactionID - This is the transaction ID.
	   *               $strTransactionType - S, A, C, or D
	   *               $strMethod - This is the method used for the transaction
	   *                            Credit Card or Direct Check.  Default will be
	   *                            C.
	   *			   $intTestMod - Either "0"-off, or "1"-on                            
	   *
	   ************************************************************************************/
	   function ChargePayflow ($fltAmount, 
								  $intCCNumber, 
								  $strDescription, 
								  $strCCExpireDate,
								  $strLogin, 
								  $strPassword, 
								  $strPartner,
								  $strTransactionID, 
								  $strTransactionType="S",
								  $strMethod="C",
								  $intTestMode=0)
		  {
	
			$PathToSDK="/usr/local/lib/verisign/payflowpro/linux/";
	
			 $strCCExpireDate = ereg_replace("/", "", $strCCExpireDate);
			 $strParameters="USER=".$strLogin;
			 $strParameters.="&VENDOR=".$strLogin;
			 $strParameters.="&PARTNER=".$strPartner;
			 $strParameters.="&PWD=".$strPassword;
			 $strParameters.="&TRXTYPE=".$strTransactionType;
			 $strParameters.="&TENDER=".$strMethod;
			 $strParameters.="&ACCT=".$intCCNumber;
			 $strParameters.="&EXPDATE=".$strCCExpireDate;
			 $strParameters.="&AMT=".$fltAmount;
			 if ($strTransactionID!="") {
				 $strParameters.="&ORIGID=".$strTransactionID;
			 }			 
			
			 if ($intTestMode==1) {
				 $DestURL="test-payflow.verisign.com";
				 $Port=443;
			 } else {
				 $DestURL="payflow.verisign.com";
				 $Port=443;
			 }
			 
			 $strCmd = $PathToSDK."bin/pfpro ".$DestURL." ".$Port." \"".$strParameters."\"";
			 Debug('$strCmd: '.$strCmd,__FILE__,__LINE__);
	
			 putenv("LD_LIBRARY_PATH=".$PathToSDK."lib");
			 putenv("PFPRO_CERT_PATH=".$PathToSDK."certs");
			 $strResult = popen($strCmd, 'r');
	
			 if($strResult) {
				while(!feof($strResult)) {
				   $strResponse = $strResponse.fgets($strResult, 1024);
				}
				Debug('$strResponse: '.$strResponse,__FILE__, __LINE__);
			 }
			 pclose($strResult);
			 
			 $arrReturnCodes = explode("&", $strResponse);
	
			 for ($i=0;$i<count($arrReturnCodes);$i++) {
				$ascHoldArray=explode("=", $arrReturnCodes[$i]);
				$ascField=$ascHoldArray[0];
				$ascCodeReturnArray[$ascField]=$ascHoldArray[1];
			 } 
			Debug('$ascCodeReturnArray[RESULT]: '.$ascCodeReturnArray[RESULT]);
			
			if ($ascCodeReturnArray[RESULT]=="0") {
				$ReturnArray["return_code"]=1;
			} elseif ($ascCodeReturnArray[RESULT]=="12") {
				$ReturnArray["return_code"]=2;
			} else {
				$ReturnArray["return_code"]=3;
			}
			$ReturnArray["return_reason"]=$ascCodeReturnArray["RESPMSG"];
			$ReturnArray["return_id"]=$ascCodeReturnArray["PNREF"];
			$ReturnArray["auth_code"]=$ascCodeReturnArray["AUTHCODE"];
	
			return $ReturnArray;     
	
		  }  //End of function ChargePayflow.
	
	   /***********************************************************************************
	   *     Function: ChargeAuthorizeNet
	   *       Author: Eric Sammons
	   * Date Created: 10/21/01
	   *
	   *  Description: This function will open the gateway
	   *               to authorize net and charge a credit card.
	   *               
	   *               
	   *       Inputs: $fltAmount - charge amount.
	   *               $intCCNumber - credit card number.
	   *               $strDescription - Charge Description.
	   *               $strCCExpireDate - Credit Card expiration date.
	   *               $strLogin - Authorize Net Login.
	   *               $strPassword - Authorize Net password.
	   *               $strTransactionID - This is the transaction ID.
	   *               $strTransactionType - AUTH_ONLY, CAPTURE, etc.
	   *               $strMethod - This is the method used for the transaction
	   *                            Credit Card or Direct Check.  Default will be
	   *                            CC.
	   *			   $intTestMod - Either "0"-off, or "1"-on                            
	   *
	   ************************************************************************************/
	
	   function ChargeAuthorizeNet ($arrPerson,
								  $fltAmount, 
								  $intCCNumber, 
								  $strDescription, 
								  $strCCExpireDate,
								  $strLogin, 
								  $strPassword, 
								  $strTransactionID, 
								  $strMerchantEmail,
								  $strEmailCustomer="TRUE",
								  $strTransactionType="AUTH_ONLY",
								  $strMethod="CC",
								  $intTestMode=0)
		  {
	
			$ascVars['x_Version'] = urlencode("3.1"); 
			$ascVars['x_relay_response'] = urlencode("FALSE");
			$ascVars['x_Delim_Data'] = urlencode("TRUE");
			$ascVars['x_Delim_Char'] = urlencode(",");
			$ascVars['x_Encap_Char'] = urlencode("");
			$ascVars['x_Type'] = urlencode($strTransactionType);
			$ascVars['x_Method'] = urlencode($strMethod);
			$ascVars['x_Login'] = urlencode($strLogin);
			$ascVars['x_Password'] = urlencode($strPassword);
			$ascVars['x_tran_key'] = urlencode($strTransactionID);
	
			$ascVars['x_Amount'] = urlencode($fltAmount);
			$ascVars['x_Card_Num'] = urlencode($intCCNumber);
			$ascVars['x_Exp_Date'] = urlencode($strCCExpireDate);
	
			$ascVars['x_First_Name'] = urlencode($P['First_Name']);
			$ascVars['x_Last_Name'] = urlencode($P['Last_Name']);
			$ascVars['x_Address'] = urlencode($P['Address']);
			$ascVars['x_City'] = urlencode($P['City']);
			$ascVars['x_State'] = urlencode($P['State']);
			$ascVars['x_Zip'] = urlencode($P['Zip']);
			$ascVars['x_Email'] = urlencode($P['Email']);
			
			$ascVars['x_Email_Customer'] = urlencode($strEmailCustomer);
			$ascVars['x_Merchant_Email'] = urlencode($strMerchantEmail);
			 
			 if ($intTestMode==1)
				 $ascVars["x_Test_Request"]		  ="TRUE"; 
			 
			 $strResult = $this->SecurePostIt($ascVars, "https://secure.authorize.net/gateway/transact.dll"); 
			 Debug('$strResult: '.$strResult,__FILE__,__LINE__);
		  
			 if (substr($strResult, 0, 5) =="Error") {
				$ascCodeReturnArray["return_code"]=3;
				$ascCodeReturnArray["return_reason"]=$strResult;
				return $ascCodeReturnArray;
			 } else { 
				$arrReturnCodes = explode(",", $strResult);
				Debug('$arrReturnCodes[0]: '.$arrReturnCodes[0],__FILE__,__LINE__);
	
				$ascCodeReturnArray["error"]=1;
				$ascCodeReturnArray["x_response_code"]=$arrReturnCodes[0];
				$ascCodeReturnArray["x_response_reason_text"]=$arrReturnCodes[3];
				$ascCodeReturnArray["x_trans_id"]=$arrReturnCodes[6];
				$ascCodeReturnArray["x_auth_code"]=$arrReturnCodes[4];
	
				$ReturnArray["return_code"]=$ascCodeReturnArray["x_response_code"];
				$ReturnArray["return_reason"]=$ascCodeReturnArray["x_response_reason_text"];
				$ReturnArray["return_id"]=$ascCodeReturnArray["x_trans_id"];
				$ReturnArray["auth_code"]=$ascCodeReturnArray["x_auth_code"];
	
			 } 
	
			 return $ReturnArray;     
	
		  }  //End of function ChargeAuthorizeNet.
	
	   /***********************************************************************************
	   *     Function: ChargeCreditCard
	   *       Author: Eric Sammons
	   * Date Created: 10/22/01
	   *
	   *  Description: This function will call the appropriate processor
	   *				then charge the card at that processor
	   *               
	   *               
	   *       Inputs: $fltAmount - charge amount.
	   *               $intCCNumber - credit card number.
	   *               $strDescription - Domain Name and # Years registered.
	   *			   $strProcessor - either PF (PayFlow Pro) or AN (Authorize.Net)
	   *               $strCCExpireDate - Credit Card expiration date (format mm/yyyy).
	   *               $strLogin - Login.
	   *               $strMethod - This is the method used for the transaction
	   *                            Credit Card or Direct Check.  
	   *               $strPassword - This is the password.
	   *               $strTransactionID - This is the transaction ID.
	   *               $strTransactionType -	AUTH_ONLY, AUTH_CAPTURE,
	   *										CAPTURE, PRIOR_AUTH_CAPTURE,
	   *										CREDIT, VOID
	   *
	   *	   Returns: $ascReturnCodes - array of following values:
	   *					return_code - 1=approved, 2=declined, 3=error
	   *					return_reason - reason for return code
	   *					return_id - transaction ID 
	   *					auth_code - 6 digit bank approval code
	   *
	   ************************************************************************************/
	   function ChargeCreditCard ($arrPerson,
									$fltAmount, 
								  $intCCNumber, 
								  $strDescription, 
								  $strCCExpireDate,
								  $strProcessor,
								  $strLogin, 
								  $strPassword, 
								  $strPartner="VeriSign",
								  $strTransactionID, 
								  $strMerchantEmail,
								  $strEmailCustomer,
								  $strTransactionType,
								  $strMethod,
								  $intTestMode=0)
		  {
		
			$this->Debug("Processor: ".$strProcessor);
			$this->Debug("Expire: ".$strCCExpireDate);
			if ($strProcessor=="PF") {
				if ($strMethod=="CC") {
					$strMethod=="C";
				} else {
					$ascReturnCodes["return_code"]="3";
					$ascReturnCodes["return_reason"]="Error: Invalid Method Type";
					return $ascReturnCodes;
				}
				if ($strTransactionType=="AUTH_ONLY") {
					$strTransactionType="A";
				} elseif ($strTransactionType=="PRIOR_AUTH_CAPTURE") {
					$strTransactionType="D";
				} elseif ($strTransactionType=="VOID") {
					$strTransactionType="V";
				} elseif ($strTransactionType=="AUTH_CAPTURE") {
					$strTransactionType="S";
				} elseif ($strTransactionType=="CREDIT") {
					$strTransactionType="C";
				} else {
					$ascReturnCodes["return_code"]="3";
					$ascReturnCodes["return_reason"]="Error: Invalid Transaction Type";
					return $ascReturnCodes;
				}
	
				$ascReturnCodes=$this->ChargePayflow(
								  $arrPerson,
								  $fltAmount, 
								  $intCCNumber, 
								  $strDescription,
								  $strCCExpireDate,
								  $strLogin,
								  $strPassword,
								  $strTransactionID,
								  $strMerchantEmail,
								  $strEmailCustomer,
								  $strTransactionType,
								  $strMethod,
								  $intTestMode
								  );
				
			} elseif ($strProcessor=="AN") {
	
			
				$ascReturnCodes = $this->ChargeAuthorizeNet (
								  $arrPerson,
								  $fltAmount, 
								  $intCCNumber, 
								  $strDescription,
								  $strCCExpireDate,
								  $strLogin,
								  $strPassword,
								  $strTransactionID,
								  $strMerchantEmail,
								  $strEmailCustomer,
								  $strTransactionType,
								  $strMethod,
								  $intTestMode
								  );
			} else {
			  $ascReturnCodes["return_code"]="3";
			  $ascReturnCodes["return_reason"]="Error: No processor selected";
			}
	
			return $ascReturnCodes;
		  }  //End of function ChargeCreditCard.
}
?>