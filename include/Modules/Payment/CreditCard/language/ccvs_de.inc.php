<?php

/**
 * Credit Card Validation Solution, PHP Edition,
 * Deutschsprachige Fehlermeldungen.
 *
 * @package    CreditCardValidationSolution
 * @author     Daniel Convissor <danielc@AnalysisAndSolutions.com>
 * @author     Bernd Kreuss <bernd@phpwebclasses.org>
 * @copyright  The Analysis and Solutions Company, 2002-2003
 * @version    $Name: rel-5-12 $ $Id: ccvs_de.inc,v 1.3 2003/07/29 15:08:18 danielc Exp $
 * @link       http://www.ccvs.info/
 */

/** */
$CCVSErrNumberString = 'Nummer ist keine Zeichenkette';
$CCVSErrVisa14       = 'Visa hat normalerweise 16 oder 13 Stellen, aber Sie gaben eine 14 stellige Nummer ein';
$CCVSErrUnknown      = 'Den ersten vier Stellen (%s) zufolge handelt es sich um eine unbekannte Kartenart';
$CCVSErrAccepted     = 'Der Programmierer verwendete das \'accepted\' Argument falsch';
$CCVSErrNoAccept     = 'Wir nehmen keine %s-Karten';
$CCVSErrShort        = 'Es fehlen %s Stelle(n)';
$CCVSErrLong         = 'Nummer hat %s Stelle(n) zu viel';
$CCVSErrChecksum     = 'Prüfsummenfehler';
$CCVSErrMonthString  = 'Monat ist keine Zeichenkette';
$CCVSErrMonthFormat  = 'Monat hat ein unzulässiges Format';
$CCVSErrYearString   = 'Jahr ist keine Zeichenkette';
$CCVSErrYearFormat   = 'Jahr hat ein unzulässiges Format';
$CCVSErrExpired      = 'Karte ist abgelaufen';

?>
