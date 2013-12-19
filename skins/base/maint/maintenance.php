<?php

list($sub) = _GL('sub');

cn_snippet_bc();

?>
<ul class="sysconf_top">
    <li<?php if ($sub == 'migrate') echo " class='selected'"; ?>><a href="<?php echo cn_url_modify('sub=migrate'); ?>">Migration</a></li>
    <li<?php if ($sub == 'sysconf') echo " class='selected'"; ?>><a href="<?php echo cn_url_modify('sub=sysconf', 'path'); ?>">System conf</a></li>

    <!-- disabled for newer version -->
    <?php /* ?>
    <li<?php if ($sub == 'repair') echo " class='selected'"; ?>><a href="<?php echo cn_url_modify('sub=repair'); ?>">Repair</a></li>
    <li<?php if ($sub == 'diff') echo " class='selected'"; ?>><a href="<?php echo cn_url_modify('sub=diff'); ?>">Diff</a></li>
    <?php */ ?>

</ul>
<div style="clear: both;"></div>