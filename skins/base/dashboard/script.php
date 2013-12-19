<?php cn_snippet_messages(); cn_snippet_bc(); ?>

<ul class="sysconf_top">
    <li class="selected"><a href="#">Scripts</a></li>
    <!-- <li><a href="#">Routers</a></li> -->
</ul>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt, sub'); ?>
    <div><textarea name="text" style="width: 770px; height: 400px;"><?php echo cn_htmlspecialchars($__text); ?></textarea></div>
    <br/>
    <div class="panel">
        <input type="submit" value="Save changes" />

        <select name="snippet">
            <?php foreach ($__list as $id => $_t) echo '<option '.(($id == $__snippet) ? 'selected' : '').'>'.cn_htmlspecialchars($id).'</option>'; ?>
        </select>

        <input type="submit" name="select" value="Select" />

        <input type="text" value="" name="create" style="width: 250px;"/>
        <input type="submit" value="Create new" />

        <?php if ($__can_delete) { ?>
        <input type="submit" name="delete" value="Delete snippet" />
        <?php } ?>
    </div>

</form>
<p style="text-align: right;"><a href="#" onclick="<?php echo cn_snippet_open_win(cn_url_modify(array('reset'), 'mod=help', 'section=snippets')); ?>" class="external">Understanding snippets</a></p>