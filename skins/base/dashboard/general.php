<?php

    if (function_exists('disk_total_space') && function_exists('disk_free_space')) {

        $ds = disk_total_space("/");
        $fs = disk_free_space("/");

        $symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        $exp     = intval(log($ds) / log(1024));
        $ds_t    = sprintf('%.2f '.$symbols[$exp], ($ds / pow(1024, floor($exp))));
        $free    = intval((1 - $fs / $ds) * 100);

    } else {

        $free = 0;
        $ds_t = 0;
    }

    list($dashboard, $username, $greeting_message) = _GL('dashboard, username, greeting_message');
?>

<section>
	<div class="container">
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
        <div class="lead"><script type="text/javascript">cn_greetings();</script>, <?php echo $username; ?>! <?php echo $greeting_message; ?></div><!--reemplazo greet por lead bootstrap-->

    <?php } ?>
    </div>
</section>

<section>
	<div class="container">
		<h2><?php echo i18n('Site options'); ?></h2>
		<div class="options well">
			<?php foreach ($dashboard as $id => $item) { ?>

				<div class="opt-item">
					<a href="<?php echo cn_url_modify("mod=".$item['mod'], "opt=".$item['opt']); ?>">
						<div><img src="skins/images/<?php echo $item['img']; ?>" width="60" /></div>
						<div><?php echo $item['name']; ?></div>
					</a>
				</div>

			<?php } ?>
		</div>
	</div>
</section>

<section>
	<div class="container">

		<h2><?php echo i18n('Statistics'); ?></h2>
        <div class="options well">

            <div><?php echo i18n('Disk usage'); ?> (<?php echo $ds_t; ?>)</div>
            <div class="progress">

              <div
                  class="progress-bar progress-bar-striped active"
                  role="progressbar"
                  aria-valuenow="<?php echo $free; ?>"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: <?php echo $free; ?>%"><?php echo $free; ?>% Free
              </div>
            </div>
        </div>
    </div>
</section>

<section>
	<div class="container">
    <?php if (test('Cvm')) { ?>

        <h2><?php echo i18n('Misc links'); ?></h2>
        <div class="options well">

            <a href="example.php" target="blank">Example</a> &middot;
            <a href="docs/readme.html" target="blank">Readme</a> &middot;
            <a href="docs/usage.html" target="blank">Usage</a> &middot;
            <a href="docs/release.html" target="blank">Release notes</a>

        </div>
        <div style="clear: both"></div>

    <?php } ?>
	</div>
</section>
