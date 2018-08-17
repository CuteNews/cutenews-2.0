<?php

list($catlist) = _GL('catlist');

foreach ($catlist as $id => $var) { ?>
	<div class="checkbox" style="padding: 0 0 0 24px">
		<div><input type="checkbox" name="__append[cats][<?php echo $id; ?>]" value="Y" /></div>
        <?php echo cn_htmlspecialchars($var['name']); ?>&nbsp;&nbsp;
	</div>
<?php } ?>
