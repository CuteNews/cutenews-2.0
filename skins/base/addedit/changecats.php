<?php

list($catlist) = _GL('catlist');

foreach ($catlist as $id => $var) { ?>
    <input style="vertical-align: middle;" type="checkbox" name="__append[cats][<?php echo $id; ?>]" value="Y" /> <?php echo cn_htmlspecialchars($var['name']); ?>
    &nbsp;&nbsp;
<?php } ?>
