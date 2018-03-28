<?php

list($catlist) = _GL('catlist');

foreach ($catlist as $id => $var) { ?>
	<div class="checkbox">
		<input type="checkbox" name="__append[cats][<?php echo $id; ?>]" value="Y" />
			<?php echo cn_htmlspecialchars($var['name']); ?>
		&nbsp;&nbsp;
	</div>
<?php } ?>
