<?php

list($template_parts, $all_templates, $template_text, $template, $sub, $can_delete) = _GL('template_parts, all_templates, template_text, template, sub, can_delete');

cn_snippet_messages();
cn_snippet_bc();

?>
<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt, template, sub'); ?>

    <input type="hidden" name="select" value="Y" />
    <div class="panel">
        <select name="template">
            <?php foreach ($all_templates as $id => $ud) { ?>
                <option value="<?php echo $id; ?>" <?php if ($template == $id) echo 'selected'; ?>><?php echo ucfirst($id); ?></option>
            <?php } ?>
        </select>

        <input type="submit" value="Select template" />
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

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <!-- view template data -->
    <?php if ($template && $sub) { ?>

        <?php cn_form_open('mod, opt, template, sub'); ?>
        <textarea id="template_text" style="width: 100%; height: 480px; font: 12px/1.2em Monospace;" name="save_template_text"><?php echo cn_htmlspecialchars($template_text); ?></textarea>
        <?php if (getoption('ckeditor2template')) cn_snippet_ckeditor('template_text'); ?>

    <?php } ?>

    <!-- template actions -->
    <?php if ($template) { ?>

        <?php cn_form_open('mod, opt, template, sub'); ?>
        <div class="panel">
            <input type="submit" value="Save template" />
            <input type="text" style="width: 250px;" name="template_name" value="" />
            <input type="submit" name="create" value="Clone template" />

            <?php if ($can_delete) { ?>
                <input type="submit" name="delete" value="Delete" />
             <?php } else { ?>
                <input type="submit" name="reset" value="Reset" />
            <?php } ?>

            <?php echo cn_htmlspecialchars(ucfirst($template)); ?>
        </div>

    <?php } ?>
</form>


<div style="text-align: right; margin: 16px 0 0 0">
    <a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=templates'); ?>" class="external">Understanding Templates</a>
    &nbsp;&nbsp;
    <a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=tplvars', array('w' => 720, 'h' => 640, 'l' => 'auto')); ?>" class="external">Template variables</a>
</div>