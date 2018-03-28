<?php

list($sub) = _GL('sub');

cn_snippet_bc();

?>
<section>
	<div class="container">
		<ul class="nav nav-tabs">
			<li<?php if ($sub == 'migrate') { echo " class='active'"; } ?>><a href="<?php echo cn_url_modify('sub=migrate'); ?>">Migration</a></li>
			<li<?php if ($sub == 'sysconf') { echo " class='active'"; } ?>><a href="<?php echo cn_url_modify('sub=sysconf', 'path'); ?>">System conf</a></li>
		</ul>
	</div>
</section>