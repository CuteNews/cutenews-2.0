<?php

if (!defined('EXEC_TIME')) die('Access restricted');

/**
 * SimpleCaptcha class
 * PHP 4.x classes
 */
class SimpleCaptcha
{

    /** Width of the image */
    var $width  = 200;

    /** Height of the image */
    var $height = 70;

    /**
     * Path for resource files (fonts, words, etc.)
     *
     * "resources" by default. For security reasons, is better move this
     * directory to another location outise the web server
     *
     */
    var $resourcesPath = 'resources';

    /** Min word length (for non-dictionary random text generation) */
    var $minWordLength = 5;

    /**
     * Max word length (for non-dictionary random text generation)
     * 
     * Used for dictionary words indicating the word-length
     * for font-size modification purposes
     */
    var $maxWordLength = 8;

    /** Sessionname to store the original text */
    var $session_var = 'captcha';

    /** Background color in RGB-array */
    var $backgroundColor = array(255, 255, 255);

    /** Foreground colors in RGB-array */
    var $colors = array(
        array(27,78,181), // blue
        array(22,163,35), // green
        array(214,36,7),  // red
    );

    /** Shadow color in RGB-array or null */
    var $shadowColor = null; //array(0, 0, 0);

    /**
     * Font configuration
     *
     * - font: TTF file
     * - spacing: relative pixel space between character
     * - minSize: min font size
     * - maxSize: max font size
     */
    var $fonts = array(
        'Heineken' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 34, 'font' => 'Heineken.ttf'),
    );

    /** Wave configuracion in X and Y axes */
    var $Yperiod    = 12;
    var $Yamplitude = 14;
    var $Xperiod    = 11;
    var $Xamplitude = 5;

    /** letter rotation clockwise */
    var $maxRotation = 8;

    /**
     * Internal image size factor (for better image quality)
     * 1: low, 2: medium, 3: high
     */
    var $scale = 2;

    /** 
     * Blur effect for better image quality (but slower image processing).
     * Better image results with scale=3
     */
    var $blur = false;

    /** Debug? */
    var $debug = false;
    
    /** Image format: jpeg or png */
    var $imageFormat = 'jpeg';

    /** GD image */
    var $im;

    function CreateImage($DisableHeaders = FALSE)
    {
        global $_SESS;

        $ini = microtime(true);

        // is GD not installed
        if ( !function_exists('imagecreatetruecolor') )
        {
            list($text, $reply) = $this->GetCaptchaText();

            $_SESS[$this->session_var] = $reply;

            if (!$DisableHeaders)
                cn_save_session();

            echo '<html><body style="font-size: 42px; font-family: Arial, Tahoma, Serif;">'.$reply.'</body></html>';
        }
        else
        {

            /** Initialization */
            $this->ImageAllocate();

            /** Text insertion */
            list($text, $reply) = $this->GetCaptchaText();
            $fontcfg  = $this->fonts[array_rand($this->fonts)];
            $this->WriteText($text, $fontcfg);

            $_SESS[$this->session_var] = $reply;

            if (!$DisableHeaders)
                cn_save_session();

            /** Transformations */
            $this->WaveImage();
            if ($this->blur && function_exists('imagefilter'))
            {
                imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
            }
            $this->ReduceImage();

            if ($this->debug)
            {
                imagestring($this->im, 1, 1, $this->height-8,
                    "$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
                    $this->GdFgColor
                );
            }

            /** Output */
            $this->WriteImage($DisableHeaders);
            $this->Cleanup();
        }
    }

    /**
     * Creates the image resources
     */
    function ImageAllocate()
    {
        // Cleanup
        if (!empty($this->im))
        {
            imagedestroy($this->im);
        }

        $this->im = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);

        // Background color
        $this->GdBgColor = imagecolorallocate($this->im,
            $this->backgroundColor[0],
            $this->backgroundColor[1],
            $this->backgroundColor[2]
        );
        imagefilledrectangle($this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor);

        // Foreground color
        $color           = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
        $this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

        // Shadow color
        if (!empty($this->shadowColor) && is_array($this->shadowColor) && sizeof($this->shadowColor) >= 3) {
            $this->GdShadowColor = imagecolorallocate($this->im,
                $this->shadowColor[0],
                $this->shadowColor[1],
                $this->shadowColor[2]
            );
        }
    }

    /**
     * Text generation
     *
     * @return string Text
     */
    function GetCaptchaText()
    {
        // use different captcha types
        $type = hook("select_captcha_types");
        if ($type == 0)
        {
            $text = $this->GetRandomCaptchaText();
            $reply = $text;
        }
        elseif ($type == 1)
        {
            $method = mt_rand(1,2);
            $a      = mt_rand(1,9);
            $b      = mt_rand(1,9);
            if ($method == 1)
            {
                $text = $a."+".$b.'=';
                $reply = ($a+$b);
            }
            else
            {
                $text = $a."-".$b.'=';
                $reply = ($a-$b);
            }
        }

        // #input $type = 3..n
        // #return $text and $reply
        list($text, $reply) = hook('captcha_text_type', array($text, $reply));

        return array($text, $reply);
    }

    /**
     * Random text generation
     *
     * @return string Text
     */
    function GetRandomCaptchaText($length = null)
    {
        if (empty($length)) {
            $length = rand($this->minWordLength, $this->maxWordLength);
        }

        $words  = "abcdefghijlmnopqrstvwyz";
        $vocals = "aeiou";

        $text  = "";
        $vocal = rand(0, 1);
        for ($i=0; $i<$length; $i++) {
            if ($vocal) {
                $text .= substr($vocals, mt_rand(0, 4), 1);
            } else {
                $text .= substr($words, mt_rand(0, 22), 1);
            }
            $vocal = !$vocal;
        }
        return $text;
    }

    /**
     * Text insertion
     */
    function WriteText($text, $fontcfg = array())
    {
        if (empty($fontcfg))
        {
            // Select the font configuration
            $fontcfg  = $this->fonts[array_rand($this->fonts)];
        }

        // Full path of font file
        $fontfile = $this->resourcesPath.'/fonts/'.$fontcfg['font'];


        /** Increase font-size for shortest words: 9% for each glyp missing */
        $lettersMissing = $this->maxWordLength-strlen($text);
        $fontSizefactor = 1+($lettersMissing*0.09);

        // Text generation (char by char)
        $x      = 20*$this->scale;
        $y      = round(($this->height*27/40)*$this->scale);
        $length = strlen($text);

        for ($i=0; $i<$length; $i++)
        {
            $degree   = rand($this->maxRotation*-1, $this->maxRotation);
            $fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
            $letter   = substr($text, $i, 1);

            if ($this->shadowColor) {
                $coords = imagettftext($this->im, $fontsize, $degree,
                    $x+$this->scale, $y+$this->scale,
                    $this->GdShadowColor, $fontfile, $letter);
            }
            $coords = imagettftext($this->im, $fontsize, $degree,
                $x, $y,
                $this->GdFgColor, $fontfile, $letter);
            $x += ($coords[2]-$x) + ($fontcfg['spacing']*$this->scale);
        }
    }

    /**
     * Wave filter
     */
    function WaveImage()
    {
        // X-axis wave generation
        $xp = $this->scale*$this->Xperiod*rand(1,3);
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->width*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                $i-1, sin($k+$i/$xp) * ($this->scale*$this->Xamplitude),
                $i, 0, 1, $this->height*$this->scale);
        }

        // Y-axis wave generation
        $k = rand(0, 100);
        $yp = $this->scale*$this->Yperiod*rand(1,2);
        for ($i = 0; $i < ($this->height*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1,
                0, $i, $this->width*$this->scale, 1);
        }
    }

    /**
     * Reduce the image to the final size
     */
    function ReduceImage()
    {
        $imResampled = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($imResampled, $this->im,
            0, 0, 0, 0,
            $this->width, $this->height,
            $this->width*$this->scale, $this->height*$this->scale
        );
        imagedestroy($this->im);
        $this->im = $imResampled;
    }

    /**
     * File generation
     */
    function WriteImage($DisableHeaders = FALSE)
    {
        if ($this->imageFormat == 'png' && function_exists('imagepng'))
        {
            if ($DisableHeaders == FALSE)
                header("Content-type: image/png");

            imagepng($this->im);
        }
        elseif (function_exists('imagejpeg'))
        {
            if ($DisableHeaders == FALSE)
                header("Content-type: image/jpeg");

            imagejpeg($this->im, null, 80);
        }
    }

    /**
     * Cleanup
     */
    function Cleanup()
    {
        imagedestroy($this->im);
    }
}
