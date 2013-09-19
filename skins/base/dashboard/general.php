<?php

    if (function_exists('disk_total_space') && function_exists('disk_free_space'))
    {
        $ds = disk_total_space("/");
        $fs = disk_free_space("/");

        $symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        $exp     = intval(log($ds) / log(1024));
        $ds_t    = sprintf('%.2f '.$symbols[$exp], ($ds / pow(1024, floor($exp))));
        $free    = intval((1 - $fs / $ds) * 100);
    }
    else
    {
        $free = 0;
        $ds_t = 0;
    }

    list($dashboard, $username, $greeting_message) = _GL('dashboard, username, greeting_message');
?>

<div class="wrapper">

    <?php if (test('Cvm')) { ?>

        <script type="text/javascript">
            function cn_greetings()
            {
                var display;
                var datetoday = new Date();
                var timenow = datetoday.getTime();

                datetoday.setTime(timenow);
                var thehour = datetoday.getHours();

                if (thehour < 9 )      display = "Morning";
                else if (thehour < 12) display = "Day";
                else if (thehour < 17) display = "Afternoon";
                else if (thehour < 20) display = "Evening";
                else display = "Night";

                var greeting = ("Good " + display);
                document.write(greeting);
            }
        </script>
        <div class="greet"><script type="text/javascript">cn_greetings();</script>, <?php echo $username; ?>! <?php echo $greeting_message; ?></div>

    <?php } ?>

    <h2 style="margin-top: 0;"><?php echo i18n('Site options'); ?></h2>
    <div class="options">
        <?php foreach ($dashboard as $id => $item) { ?>

            <div class="opt-item">
                <a href="<?php echo cn_url_modify("mod=".$item['mod'], "opt=".$item['opt']); ?>">
                    <div><img src="skins/images/<?php echo $item['img']; ?>" width="48" /></div>
                    <div><?php echo $item['name']; ?></div>
                </a>
            </div>

        <?php } ?>
    </div>

    <div style="clear: both"></div>

    <?php if (test('Cvm')) { ?>

        <h2><?php echo i18n('Misc links'); ?></h2>
        <div class="options">

            <a href="example.php" target="blank">Example</a> &middot;
            <a href="docs/readme.html">Readme</a> &middot;
            <a href="docs/usage.html">Usage</a> &middot;
            <a href="docs/release.html">Release notes</a>

        </div>

        <div style="clear: both"></div>

        <h2><?php echo i18n('Statistics'); ?></h2>
        <div class="options">

            <?php if ($free) { ?>
                <div><?php echo i18n('Disk usage'); ?> (<?php echo $ds_t; ?>)</div>
                <div class="a"><div class="b" style='width: <?php echo $free; ?>%'><?php echo $free; ?>%</div></div>
                <div style="clear: left;"></div>
            <?php } ?>

        </div>

    <?php } ?>

</div>
