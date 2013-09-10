<?php

list($template_parts, $user_templates, $template_text, $template, $sub) = _GL('template_parts, user_templates, template_text, template, sub');

cn_snippet_messages();
cn_snippet_bc();

?>
<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt, template, sub'); ?>
    <div class="panel">
        <select name="template">
            <option value="default" <?php if ($template == 'default') echo 'selected'; ?>>Default</option>
            <option value="rss" <?php if ($template == 'rss') echo 'selected'; ?>>RSS</option>
            <option value="headlines" <?php if ($template == 'headlines') echo 'selected'; ?>>Headlines</option>
            <option value="mail" <?php if ($template == 'mail') echo 'selected'; ?>>Mail notifications</option>
            <?php foreach ($user_templates as $id => $ud) { ?>
                <option value="<?php echo $id; ?>" <?php if ($template == $id) echo 'selected'; ?>><?php echo $id; ?></option>
            <?php } ?>
        </select>

        <input type="submit" name="select" value="Select template" />
        &nbsp;&nbsp;&nbsp;
        <input type="text" name="template_name" value="" />
        <input type="submit" value="Create new" />
        <input type="submit" name="delete" value="Delete" />
    </div>

</form>

<br/>
<div>
    <ul class="sysconf_top">
        <?php foreach ($template_parts as $id => $template_name) { ?>
            <li <?php if ($sub == $id) echo 'class="selected"'; ?>><a href="<?php echo cn_url_modify('sub='.$id); ?>"><?php echo $template_name; ?></a></li>
        <?php } ?>
    </ul>
    <div style="clear: left;"> </div>
</div>

<?php if ($template && $sub) { ?>

    <form action="<?php echo PHP_SELF; ?>" method="POST">

        <?php cn_form_open('mod, opt, template, sub'); ?>
        <textarea id="template_text" style="width: 100%; height: 480px; font: 12px/1.2em Monospace;" name="save_template_text"><?php echo cn_htmlspecialchars($template_text); ?></textarea>
        <?php if (getoption('ckeditor2template')) cn_snippet_ckeditor('template_text'); ?>

        <p><input type="submit" name="save_button" value="Save template" /></p>
    </form>

<?php } ?>

<div style="text-align: right; margin: 16px 0 0 0">
    <a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=templates'); ?>" class="external">Understanding Templates</a>
    &nbsp;&nbsp;
    <a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=tplvars', array('w' => 720, 'h' => 640, 'l' => 'auto')); ?>" class="external">Template variables</a>
</div>