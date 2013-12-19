<?php if ($__saved) { ?><div style="padding: 8px; background: #40e040;"><b>Changes saved</b></div><?php } ?>
<div style="padding: 8px; background: #f0f0f0;"><b>Edit path:</b> <?php echo $__path .'/'. $__edit; ?></div>
<form action="<?php echo PHP_SELF; ?>" method="POST">
    <?php cn_form_open('opt, mod, sub, path, edit'); ?>
    <div><textarea name="save_conf" style="width: 790px; height: 400px;"><?php echo cn_htmlspecialchars($__template); ?></textarea></div>
    <div><input type="submit" value="Save changes" /></div>
</form>