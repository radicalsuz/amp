<?php
   /**
    * AMP_Form_Element_Captcha
    *
    * based on the PhpCaptcha library as detailed below
    * @version 3.5.9
    */
   /***************************************************************/
   /* PhpCaptcha - A visual and audio CAPTCHA generation library
   
      Software License Agreement (BSD License)
   
      Copyright (C) 2005-2006, Edward Eliot.
      All rights reserved.
      
      Redistribution and use in source and binary forms, with or without
      modification, are permitted provided that the following conditions are met:

         * Redistributions of source code must retain the above copyright
           notice, this list of conditions and the following disclaimer.
         * Redistributions in binary form must reproduce the above copyright
           notice, this list of conditions and the following disclaimer in the
           documentation and/or other materials provided with the distribution.;
         * Neither the name of Edward Eliot nor the names of its contributors 
           may be used to endorse or promote products derived from this software 
           without specific prior written permission of Edward Eliot.

      THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS" AND ANY
      EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
      WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
      DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
      DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
      (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
      LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
      ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
      (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
      SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
   
      Last Updated:  18th April 2006                               */
   /***************************************************************/
   
   /************************ Documentation ************************/
   /*
   
   Documentation is available at http://www.ejeliot.com/pages/2
   
   */
   /************************ Default Options **********************/
   
   // start a PHP session - this class uses sessions to store the generated 
   // code. Comment out if you are calling already from your application
   //session_start();
   
   // class defaults - change to effect globally
   $cache = &AMP_get_cache( );
   $captcha_key_value = AMP_System_Cache::identify( 'php_captcha', AMP_SYSTEM_UNIQUE_VISITOR_ID );
   
   define('CAPTCHA_SESSION_ID', $captcha_key_value );
   define('CAPTCHA_WIDTH', 200); // max 500
   define('CAPTCHA_HEIGHT', 50); // max 200
   define('CAPTCHA_NUM_CHARS', 5);
   define('CAPTCHA_NUM_LINES', 70);
   define('CAPTCHA_CHAR_SHADOW', false);
   define('CAPTCHA_OWNER_TEXT', AMP_SITE_URL );
   define('CAPTCHA_CHAR_SET', ''); // defaults to A-Z
   define('CAPTCHA_CASE_INSENSITIVE', true);
   //define('CAPTCHA_BACKGROUND_IMAGES', '');
   define('CAPTCHA_BACKGROUND_IMAGES', AMP_LOCAL_PATH . '/img/captcha_bg1.jpg,'. AMP_LOCAL_PATH . '/img/captcha_bg2.jpg' );
   define('CAPTCHA_MIN_FONT_SIZE', 16);
   define('CAPTCHA_MAX_FONT_SIZE', 25);
   define('CAPTCHA_USE_COLOUR', true);
   define('CAPTCHA_FILE_TYPE', 'jpeg');
   define('CAPTCHA_FLITE_PATH', '/usr/local/bin/flite');
   define('CAPTCHA_AUDIO_PATH', '/tmp/'); // must be writeable by PHP process
   
   /************************ End Default Options **********************/
   
   // don't edit below this line (unless you want to change the class!)
   
   class PhpCaptcha {
      var $oImage;
      var $aFonts;
      var $iWidth;
      var $iHeight;
      var $iNumChars;
      var $iNumLines;
      var $iSpacing;
      var $bCharShadow;
      var $sOwnerText;
      var $aCharSet;
      var $bCaseInsensitive;
      var $vBackgroundImages;
      var $iMinFontSize;
      var $iMaxFontSize;
      var $bUseColour;
      var $sFileType;
      var $sCode = '';

      
      function PhpCaptcha(
         $aFonts, // array of TrueType fonts to use - specify full path
         $iWidth = CAPTCHA_WIDTH, // width of image
         $iHeight = CAPTCHA_HEIGHT // height of image
      ) {
         // get parameters
         $this->aFonts = $aFonts;
         $this->SetNumChars(CAPTCHA_NUM_CHARS);
         $this->SetNumLines(CAPTCHA_NUM_LINES);
         $this->DisplayShadow(CAPTCHA_CHAR_SHADOW);
         $this->SetOwnerText(CAPTCHA_OWNER_TEXT);
         $this->SetCharSet(CAPTCHA_CHAR_SET);
         $this->CaseInsensitive(CAPTCHA_CASE_INSENSITIVE);
         $this->SetBackgroundImages(CAPTCHA_BACKGROUND_IMAGES);
         $this->SetMinFontSize(CAPTCHA_MIN_FONT_SIZE);
         $this->SetMaxFontSize(CAPTCHA_MAX_FONT_SIZE);
         $this->UseColour(CAPTCHA_USE_COLOUR);
         $this->SetFileType(CAPTCHA_FILE_TYPE);   
         $this->SetWidth($iWidth);
         $this->SetHeight($iHeight);
      }
      
      function CalculateSpacing() {
         $this->iSpacing = (int)($this->iWidth / $this->iNumChars);
      }
      
      function SetWidth($iWidth) {
         $this->iWidth = $iWidth;
         if ($this->iWidth > 500) $this->iWidth = 500; // to prevent perfomance impact
         $this->CalculateSpacing();
      }
      
      function SetHeight($iHeight) {
         $this->iHeight = $iHeight;
         if ($this->iHeight > 200) $this->iHeight = 200; // to prevent performance impact
      }
      
      function SetNumChars($iNumChars) {
         $this->iNumChars = $iNumChars;
         $this->CalculateSpacing();
      }
      
      function SetNumLines($iNumLines) {
         $this->iNumLines = $iNumLines;
      }
      
      function DisplayShadow($bCharShadow) {
         $this->bCharShadow = $bCharShadow;
      }
      
      function SetOwnerText($sOwnerText) {
         $this->sOwnerText = $sOwnerText;
      }
      
      function SetCharSet($vCharSet) {
         // check for input type
         if (is_array($vCharSet)) {
            $this->aCharSet = $vCharSet;
         } else {
            if ($vCharSet != '') {
               // split items on commas
               $aCharSet = explode(',', $vCharSet);
            
               // initialise array
               $this->aCharSet = array();
            
               // loop through items 
               foreach ($aCharSet as $sCurrentItem) {
                  // a range should have 3 characters, otherwise is normal character
                  if (strlen($sCurrentItem) == 3) {
                     // split on range character
                     $aRange = explode('-', $sCurrentItem);
                  
                     // check for valid range
                     if (count($aRange) == 2 && $aRange[0] < $aRange[1]) {
                        // create array of characters from range
                        $aRange = range($aRange[0], $aRange[1]);
                     
                        // add to charset array
                        $this->aCharSet = array_merge($this->aCharSet, $aRange);
                     }
                  } else {
                     $this->aCharSet[] = $sCurrentItem;
                  }
               }
            }
         }
      }
      
      function CaseInsensitive($bCaseInsensitive) {
         $this->bCaseInsensitive = $bCaseInsensitive;
      }
      
      function SetBackgroundImages($vBackgroundImages) {
         if ( $vBackgroundImages ) {
             $this->vBackgroundImages = explode( ',', $vBackgroundImages );
         }
      }
      
      function SetMinFontSize($iMinFontSize) {
         $this->iMinFontSize = $iMinFontSize;
      }
      
      function SetMaxFontSize($iMaxFontSize) {
         $this->iMaxFontSize = $iMaxFontSize;
      }
      
      function UseColour($bUseColour) {
         $this->bUseColour = $bUseColour;
      }
      
      function SetFileType($sFileType) {
         // check for valid file type
         if (in_array($sFileType, array('gif', 'png', 'jpeg'))) {
            $this->sFileType = $sFileType;
         } else {
            $this->sFileType = 'jpeg';
         }
      }
      
      function DrawLines() {
         for ($i = 0; $i < $this->iNumLines; $i++) {
            // allocate colour
            if ($this->bUseColour) {
               $iLineColour = imagecolorallocate($this->oImage, rand(100, 250), rand(100, 250), rand(100, 250));
            } else {
               $iRandColour = rand(100, 250);
               $iLineColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
            }
            
            // draw line
            imageline($this->oImage, rand(0, $this->iWidth), rand(0, $this->iHeight), rand(0, $this->iWidth), rand(0, $this->iHeight), $iLineColour);
         }
      }
      
      function DrawOwnerText() {
         // allocate owner text colour
         $iBlack = imagecolorallocate($this->oImage, 0, 0, 0);
         // get height of selected font
         $iOwnerTextHeight = imagefontheight(2);
         // calculate overall height
         $iLineHeight = $this->iHeight - $iOwnerTextHeight - 4;
         
         // draw line above text to separate from CAPTCHA
         imageline($this->oImage, 0, $iLineHeight, $this->iWidth, $iLineHeight, $iBlack);
         
         // write owner text
         imagestring($this->oImage, 2, 3, $this->iHeight - $iOwnerTextHeight - 3, $this->sOwnerText, $iBlack);
         
         // reduce available height for drawing CAPTCHA
         $this->iHeight = $this->iHeight - $iOwnerTextHeight - 5;
      }
      
      function GenerateCode() {
         // reset code
         $this->sCode = '';
         
         // loop through and generate the code letter by letter
         for ($i = 0; $i < $this->iNumChars; $i++) {
            if (count($this->aCharSet) > 0) {
               // select random character and add to code string
               $this->sCode .= $this->aCharSet[array_rand($this->aCharSet)];
            } else {
               // select random character and add to code string
               $this->sCode .= chr(rand(65, 90));
            }
         }
         
         // save code in session variable
         $captcha_code = $this->bCaseInsensitive ? strtoupper( $this->sCode ) : $this->sCode;
         AMP_cache_set( CAPTCHA_SESSION_ID, $captcha_code );
         AMP_Form_Element_Captcha::create( CAPTCHA_SESSION_ID, $this->sCode );
         //$_SESSION[CAPTCHA_SESSION_ID] = $captcha_code;
      }
      
      function DrawCharacters() {
         // loop through and write out selected number of characters
         for ($i = 0; $i < strlen($this->sCode); $i++) {
            // select random font
            $sCurrentFont = $this->aFonts[array_rand($this->aFonts)];
            
            // select random colour
            if ($this->bUseColour) {
               $iTextColour = imagecolorallocate($this->oImage, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
               //$iTextColour = mt_rand( 0, 255 );
            
               if ($this->bCharShadow) {
                  // shadow colour
                  $iShadowColour = imagecolorallocate($this->oImage, rand(0, 100), rand(0, 100), rand(0, 100));
               }
            } else {
               $iRandColour = rand(0, 100);
               $iTextColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
            
               if ($this->bCharShadow) {
                  // shadow colour
                  $iRandColour = rand(0, 100);
                  $iShadowColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
               }
            }
            
            // select random font size
            $iFontSize = rand($this->iMinFontSize, $this->iMaxFontSize);
            
            // select random angle
            $iAngle = rand(-30, 30);
            
            // get dimensions of character in selected font and text size
            $aCharDetails = imageftbbox($iFontSize, $iAngle, $sCurrentFont, $this->sCode[$i], array());
            
            // calculate character starting coordinates
            $iX = $this->iSpacing / 4 + $i * $this->iSpacing;
            $iCharHeight = $aCharDetails[2] - $aCharDetails[5];
            $iY = $this->iHeight / 2 + $iCharHeight / 4; 
            
            // write text to image
            imagefttext($this->oImage, $iFontSize, $iAngle, $iX, $iY, $iTextColour, $sCurrentFont, $this->sCode[$i], array());
            
            if ($this->bCharShadow) {
               $iOffsetAngle = rand(-30, 30);
               
               $iRandOffsetX = rand(-5, 5);
               $iRandOffsetY = rand(-5, 5);
               
               imagefttext($this->oImage, $iFontSize, $iOffsetAngle, $iX + $iRandOffsetX, $iY + $iRandOffsetY, $iShadowColour, $sCurrentFont, $this->sCode[$i], array());
            }
         }
      }
      
      function WriteFile($sFilename) {
         if ($sFilename == '') {
            // tell browser that data is jpeg
            header("Content-type: image/$this->sFileType");
         }
         
         switch ($this->sFileType) {
            case 'gif':
               $sFilename != '' ? imagegif($this->oImage, $sFilename) : imagegif($this->oImage);
               break;
            case 'png':
               $sFilename != '' ? imagepng($this->oImage, $sFilename) : imagepng($this->oImage);
               break;
            default:
               $sFilename != '' ? imagejpeg($this->oImage, $sFilename) : imagejpeg($this->oImage);
         }
      }
      
      function Create($sFilename = '') {
         // check for required gd functions
         if (!function_exists('imagecreate') || !function_exists("image$this->sFileType") || ($this->vBackgroundImages != '' && !function_exists('imagecreatetruecolor'))) {
             // $this->GenerateCode( );
             trigger_error( 'needed libraries not loaded for captcha ' . $this->sCode );
            return false;
         }
         
         // get background image if specified and copy to CAPTCHA
         if (is_array($this->vBackgroundImages) || $this->vBackgroundImages != '') {
            // create new image
            $this->oImage = imagecreatetruecolor($this->iWidth, $this->iHeight);
            
            // create background image
            if (is_array($this->vBackgroundImages)) {
               $iRandImage = array_rand($this->vBackgroundImages);
               $oBackgroundImage = imagecreatefromjpeg($this->vBackgroundImages[$iRandImage]);
            } else {
               $oBackgroundImage = imagecreatefromjpeg($this->vBackgroundImages);
            }
            
            // copy background image
            imagecopy($this->oImage, $oBackgroundImage, 0, 0, 0, 0, $this->iWidth, $this->iHeight);
            
            // free memory used to create background image
            imagedestroy($oBackgroundImage);
         } else {
            // create new image
            $this->oImage = imagecreate($this->iWidth, $this->iHeight);
         }
         
         // allocate white background colour
         imagecolorallocate($this->oImage, 255, 255, 255);
         
         // check for owner text
         if ($this->sOwnerText != '') {
            $this->DrawOwnerText();
         }
         
         // check for background image before drawing lines
         if (!is_array($this->vBackgroundImages) && $this->vBackgroundImages == '') {
            $this->DrawLines();
         }
         
         $this->GenerateCode();
         $this->DrawCharacters();
         $this->Distort( );
         
         // write out image to file or browser
         $this->WriteFile($sFilename);
         
         // free memory used in creating image
         imagedestroy($this->oImage);
         
         return true;
      }

      function Distort( ) {
            for ( $j = 1; $j<200; $j++) {
                $color = imagecolorallocate( $this->oImage, mt_rand( 50, 200 ), mt_rand( 50, 200 ), mt_rand( 50, 200 ));
                imagesetpixel( $this->oImage, mt_rand( 0, $this->iWidth ), mt_rand( 0, $this->iHeight ), $color );
            }
      }
      
      // call this method statically
      function Validate($sUserCode, $bCaseInsensitive = true) {
         if ($bCaseInsensitive) {
            $sUserCode = strtoupper($sUserCode);
         }
         
         //if (!empty($_SESSION[CAPTCHA_SESSION_ID]) && $sUserCode == $_SESSION[CAPTCHA_SESSION_ID]) {
         $cached_code = AMP_cache_get( CAPTCHA_SESSION_ID );
         //trigger_error( 'found cached code ' . $cached_code . 'vs '. $sUserCode );
         if ( !$cached_code ) {
             $cached_code = AMP_Form_Element_Captcha::validate( CAPTCHA_SESSION_ID ) ;
         }
         //trigger_error( 'found cached code ' . $cached_code . 'vs '. $sUserCode );

         if ( $cached_code && ( $sUserCode == $cached_code )) {
            // clear to prevent re-use
            //unset($_SESSION[CAPTCHA_SESSION_ID]);
            AMP_cache_delete( CAPTCHA_SESSION_ID );
            AMP_Form_Element_Captcha::delete( CAPTCHA_SESSION_ID );
            
            return true;
         }
         
         return false;
      }
   }
   
   // this class will only work correctly if a visual CAPTCHA has been created first using PhpCaptcha
   class AudioPhpCaptcha {
      var $sFlitePath;
      var $sAudioPath;
      var $sCode;
      
      function AudioPhpCaptcha(
         $sFlitePath = CAPTCHA_FLITE_PATH, // path to flite binary
         $sAudioPath = CAPTCHA_AUDIO_PATH // the location to temporarily store the generated audio CAPTCHA
      ) {
         $this->SetFlitePath($sFlitePath);
         $this->SetAudioPath($sAudioPath);
         
         // retrieve code if already set by previous instance of visual PhpCaptcha
         if ($cached_code = AMP_cache_get( CAPTCHA_SESSION_ID )) {
            $this->sCode = $cached_code;
         }
      }
      
      function SetFlitePath($sFlitePath) {
         $this->sFlitePath = $sFlitePath;
      }
      
      function SetAudioPath($sAudioPath) {
         $this->sAudioPath = $sAudioPath;
      }
      
      function Mask($sText) {
         $iLength = strlen($sText);
         
         // loop through characters in code and format
         $sFormattedText = '';
         for ($i = 0; $i < $iLength; $i++) {
            // comma separate all but first and last characters
            if ($i > 0 && $i < $iLength - 1) {
               $sFormattedText .= ', ';
            } elseif ($i == $iLength - 1) { // precede last character with "and"
               $sFormattedText .= ' and ';
            }
            $sFormattedText .= $sText[$i];
         }
         
         $aPhrases = array(
            "The %1\$s characters are as follows: %2\$s",
            "%2\$s, are the %1\$s letters",
            "Here are the %1\$s characters: %2\$s",
            "%1\$s characters are: %2\$s",
            "%1\$s letters: %2\$s"
         );
         
         $iPhrase = array_rand($aPhrases);
         
         return sprintf($aPhrases[$iPhrase], $iLength, $sFormattedText);
      }
      
      function Create() {
         $sText = $this->Mask($this->sCode);
         $sFile = md5($this->sCode.time());
         
         // create file with flite
         shell_exec("$this->sFlitePath -t \"$sText\" -o $this->sAudioPath$sFile.wav");
         
         // set headers
         header('Content-type: audio/x-wav');
         header("Content-Disposition: attachment;filename=$sFile.wav");
         
         // output to browser
         echo file_get_contents("$this->sAudioPath$sFile.wav");
         
         // delete temporary file
         @unlink("$this->sAudioPath$sFile.wav");
      }
   }
   
   // example sub class
   class PhpCaptchaColour extends PhpCaptcha {
      function PhpCaptchaColour($aFonts, $iWidth = CAPTCHA_WIDTH, $iHeight = CAPTCHA_HEIGHT) {
         // call parent constructor
         parent::PhpCaptcha($aFonts, $iWidth, $iHeight);
         
         // set options
         $this->UseColour(true);
      }
   }

   require_once( 'AMP/System/Data/Item.inc.php');

   class AMP_Form_Element_Captcha extends AMPSystem_Data_Item {
       var $datatable = 'form_captchas';
       var $class_name = 'AMP_Form_Element_Captcha';
       var $id_field = 'session';

        function AMP_Form_Element_Captcha( &$dbcon, $id = null ) {
            $this->init( $dbcon, $id );
        }

        function create( $session_id, $captcha_value ) {
            $dbcon = AMP_Registry::getDbcon( );
            $item = new AMP_Form_Element_Captcha( $dbcon ) ;
            AMP_Form_Element_Captcha::delete( $session_id );

            $item_data = array( 'session' => $session_id, 'captcha' => $captcha_value );
            $item->setData( $item_data );
            $result = $item->save( );
            if ( $result ) return $item->id;
            return false;
        }

        function makeCriteriaSession( $session_id ) {
            return 'session=' . $this->dbcon->qstr( $session_id );
        }

        function makeCriteriaOlderThan( $timestamp ) {
            return 'UNIX_TIMESTAMP( issued_at ) < ' . ( $timestamp );
        }

        function delete( $session_id ) {
            $dbcon = AMP_Registry::getDbcon( );
            $item = &new AMP_Form_Element_Captcha( $dbcon, $session_id ) ;
            $result = false;
            if ( $item->hasData( )) {
                $result = $item->deleteData( $session_id );
            }

            $random = mt_rand( 0, 10 );
            if ( $random != 1 ) return $result; 

            $age_criteria = array( 'olderThan' => ( time( ) - 6000 ));
            $result = $item->deleteByCriteria( $age_criteria );
            return $result;
        }

        function getCaptcha( ) {
            return $this->getData( 'captcha' );
        }

        function validate( $session_id ) {
            $item = &new AMP_Form_Element_Captcha( AMP_Registry::getDbcon( ), $session_id );
            if ( $item->hasData( )) {
                return $item->getCaptcha( );
            }
            return false; 
        }

   }
?>
