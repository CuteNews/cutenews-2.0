<?php

list($sub) = _GL('sub');

cn_snippet_bc();

?>
<ul class="sysconf_top">
    <li<?php if ($sub == 'migrate') { echo " class='selected'"; } ?>><a href="<?php echo cn_url_modify('sub=migrate'); ?>">Migration</a></li>
    <li<?php if ($sub == 'sysconf') { echo " class='selected'"; } ?>><a href="<?php echo cn_url_modify('sub=sysconf', 'path'); ?>">System conf</a></li>
</ul>
<div style="clear: both;"></div>