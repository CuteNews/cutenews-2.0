<?php

define('CurrentDir', dirname(__FILE__));
include CurrentDir . '/classes/migrations.php';
include CurrentDir . '/classes/cutenews2x.php';

/*
 * Execute migration from 2.0.3 to 2.0.4 version
 */

$migrations = new Migrations();
$cutenews = new Cutenews2x(CurrentDir);

$migrations->load_template(CurrentDir . '/template.html');

if (isset($_GET['proceed'])) {

    $migrations->add_log('Migration start');

    // Search upload dir
    $uploads = $cutenews->get_upload_dir();
    $migrations->add_log("Search avatars... uploads dir is located at $uploads");

    // ...
    $dirs = $migrations->get_directories($uploads);
    foreach ($dirs as $value) {

        $file = $uploads . $value;
        if (is_file($file)) {

            $isize = getimagesize($file);
            $w = isset($isize[0]) ? $isize[0] : 0;
            $h = isset($isize[1]) ? $isize[1] : 0;
            $mime = isset($isize['mime']) ? $isize['mime'] : '';

            // is not correct!
            if (!$w || !$h || !preg_match('/(jpg|jpeg|gif|png|bmp|icon|tiff)/i', $mime)) {

                unlink($file);
                clearstatcache();

                if (file_exists($file)) {
                    $migrations->add_log("Incorrect file $value, <span>delete file '$file'!</span>");
                }
            }
        }
    }

    $migrations->add_log("Migration OK");
    $migrations->content('<div class="notify">' . $migrations->get_logs() . '</div>');
}

echo $migrations->parse();

?>