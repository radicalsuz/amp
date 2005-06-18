<?php

/**
 * Credit Card Validation Solution, PHP Edition,
 * English language error messages.
 *
 * @package    CreditCardValidationSolution
 * @author     Daniel Convissor <danielc@AnalysisAndSolutions.com>
 * @copyright  The Analysis and Solutions Company, 2002-2003
 * @version    $Name: rel-5-12 $ $Id: ccvs_en.inc,v 1.2 2003/05/07 03:58:25 danielc Exp $
 * @link       http://www.ccvs.info/
 */

/** */
$CCVSErrNumberString = 'Number isn\'t a string';
$CCVSErrVisa14       = 'Visa usually has 16 or 13 digits, but you entered 14';
$CCVSErrUnknown      = 'First four digits, %s, indicate unknown card type';
$CCVSErrAccepted     = 'Programmer improperly used the Accepted argument';
$CCVSErrNoAccept     = 'We don\'t accept %s cards';
$CCVSErrShort        = 'Number is missing %s digit(s)';
$CCVSErrLong         = 'Number has %s too many digit(s)';
$CCVSErrChecksum     = 'Number failed checksum test';
$CCVSErrMonthString  = 'Month isn\'t a string';
$CCVSErrMonthFormat  = 'Month has invalid format';
$CCVSErrYearString   = 'Year isn\'t a string';
$CCVSErrYearFormat   = 'Year has invalid format';
$CCVSErrExpired      = 'Card has expired';

?>
