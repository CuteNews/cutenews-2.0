<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__title; ?></title>
    <link href="<?php echo getoption('http_script_dir'); ?>/skins/images/favicon.ico" rel="shortcut icon" type="image/ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo getoption('http_script_dir'); ?>/libs/css/themes/<?php echo getoption('skin');?>/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo getoption('http_script_dir'); ?>/libs/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo getoption('http_script_dir'); ?>/skins/custom.css">
	<script src="<?php echo getoption('http_script_dir'); ?>/libs/js/jquery.js"></script>
	<script src="<?php echo getoption('http_script_dir'); ?>/libs/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo getoption('http_script_dir'); ?>/skins/cute.js"></script>
    <style>
    <?php if ($__style) {
        $_styles = spsep($__style);
        foreach ($_styles as $_style) {
            $f = fopen(SKIN . DIRECTORY_SEPARATOR . trim($_style), 'r');
            fpassthru($f);
            fclose($f);
        }
        unset($__style, $_styles, $_style);
    } ?>
    </style>
</head>
<body>

<?php echo $__content; ?>

</body>
</html>