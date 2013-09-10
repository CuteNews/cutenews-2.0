<?php

list($widgets, $plugins, $widget_settings, $widget_current, $plugin_current, $s_widget) = _GL('widgets, plugins, widget_settings, widget_current, plugin_current, s_widget');

cn_snippet_messages();
cn_snippet_bc();

if ($plugins) { ?>

<div class="wigets-sidebar">
    <h2>Widgets</h2>
    <ul>
        <?php foreach ($widgets as $widget) { ?>
            <li<?php if ($widget['selected']) echo ' class="sl"'; ?>><a href="<?php echo cn_url_modify('selected='.$widget['md5']); ?>"><?php echo ucfirst($widget['group']).' / '.ucfirst($widget['name']); ?></a></li>
        <?php } ?>
    </ul>
    <h2>Plugins</h2>
    <?php foreach ($plugins as $plugin) { ?>
        <li<?php if ($plugin['selected']) echo ' class="sl"'; ?>><a href="<?php echo cn_url_modify('selected='.$plugin['md5']); ?>"><?php echo ucfirst($plugin['name']); ?></a></li>
    <?php } ?>
</div>

<div class="widget-settings">
    <form action="<?php echo PHP_SELF; ?>" method="POST">

        <?php cn_form_open('mod, opt, selected'); ?>

        <?php if ($widget_current) { ?>

            <h1>Widget "<?php echo ucfirst($widget_current); ?>" settings</h1>
            <input type="hidden" name="widget_name" value="<?php echo $widget_current; ?>" />

            <hr/>
            <?php echo $widget_settings; ?>
            <hr/>
            <div><input type="submit" name="submit_widget" value="Submit changes" /></div>

        <?php } elseif ($plugin_current) { ?>

            <h1>"<?php echo ucfirst($plugin_current); ?>" plugin settings</h1>
            <input type="hidden" name="plugin_name" value="<?php echo cn_htmlspecialchars($plugin_current); ?>" />
            <hr>
            <div><input type="checkbox" name="delete" style="vertical-align: middle;"> Remove plugin</div>
            <hr>
            <div><input type="submit" name="submit_plugin" value="Submit changes" /></div>

        <?php } else { ?>

            <p>Select widget or plugin...</p>

        <?php } ?>

    </form>

</div>
<?php } else { ?><hr/><h3>No plugins found</h3> Upload new plugin to ./cdata/plugins<?php } ?>