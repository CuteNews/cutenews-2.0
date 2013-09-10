<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $__header_title; ?></title>
    <script type="text/javascript" src="<?php echo getoption('http_script_dir'); ?>/skins/cute.js"></script>
    <style>

        /* Basic notify style */
        .cn_error_list .cn_error_item { background: #ffa0a0; padding: 0 0 0 4px; margin: 1px 0 1px 200px; }
        .cn_error_list .cn_error_item div { background: #ff8080; padding: 4px; color: white; }
        .cn_error_list .cn_error_item div b { color: #ffffc0; }

        .cn_notify_list .cn_notify_item { background: #80c080; padding: 0 0 0 4px; margin: 1px 0 1px 200px;  }
        .cn_notify_list .cn_notify_item div {  background: #30a030; padding: 4px; color: #f0fff0;  }
        .cn_notify_list .cn_notify_item div b { color: #a0ffa0; }

        .cn_warnings_list .cn_warnings_item { background: #efe080; padding: 0 0 0 4px; margin: 1px 0 1px 200px; }
        .cn_warnings_list .cn_warnings_item div { background: #fff080; padding: 4px; color: black; }
        .cn_warnings_list .cn_warnings_item div b { color: #804000; }

        /* Aux styles */
        <?php if ($__header_style)
        {
            $f = fopen(SKIN.'/'.$__header_style, 'r');
            fpassthru($f);
            fclose($f);
        }
        ?>
    </style>
</head>
<body>

