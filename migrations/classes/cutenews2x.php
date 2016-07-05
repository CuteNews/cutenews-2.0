<?php

class Cutenews2x
{
    var $current_dir;

    function __construct($dir)
    {
        $this->current_dir = $dir;
    }

    function get_upload_dir()
    {
        $dirbase = realpath($this->current_dir . '/../');
        $dirfile = $dirbase . DIRECTORY_SEPARATOR . 'cdata' . DIRECTORY_SEPARATOR . 'conf.php';

        if (file_exists($dirfile)) {

            list(,$conf) = explode("\n", file_get_contents($dirfile), 2);
            $conf = unserialize(base64_decode($conf));

            if (isset($conf['%site']['uploads_dir'])) {
                return $conf['%site']['uploads_dir'];
            }
        }

        // By Default upload dir
        return $dirbase . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    }
}