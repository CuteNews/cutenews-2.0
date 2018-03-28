<?php

    list($registered, $reg_site_key, $version_holder) = _GL('registered, reg_site_key, version_holder');

?>
<section>
	<div class="container">
		<div class="well">

			<div>Help &amp; Support</div>
			<div>
				<p>
					If you face any problem the first thing you should do is to pay your attention to <a href="<?php echo PHP_SELF; ?>?mod=help">CuteNews internal Help Documentation</a>.
					If it doesn't help, visit our <a href="http://cutephp.com/forum/" target="_blank">support
						forum</a>. Read <a target="_blank" href="docs/readme.html">README.html</a> file before it.
				</p>

				<ol>
					<li><a href="<?php echo PHP_SELF; ?>?mod=help">Internal Help Documentation</a></li>
					<li><a href="http://cutephp.com/cutenews/readme.html" target=_blank>Readme.html File (online)</a></li>
					<li><a href="http://cutephp.com/forum/" target=_blank>Support Forums</a></li>
				</ol>

				<div><strong>Your version:</strong> Cutenews v<?php echo VERSION.' (Build ID - '.VERSION_ID.')'; ?></div>
				<div><strong>License version:</strong> <?php echo $version_holder;?></div>
				<br/>
				<div><strong>Important note:</strong> no formal official support is provided apart from the support forum found on this website and maintained by other users.</div>
			</div>

			<?php if ($registered) { ?>

				<div><b>License ID:</b> <?php echo $reg_site_key; ?></div>

				<form action="http://www.hotscripts.com/rate/21011.html" method="POST" target="_blank">
					<label for="rating">Please rate our script <a href="http://www.hotscripts.com/?RID=11206" >@ HotScripts.com</a> if you like it. &nbsp;</label>
					<select id="rating" name=rating size=1>
						<option value=5 selected>Excellent!</option>
						<option value=4>Very Good</option>
						<option value=3>Good</option>
						<option value=2>Fair</option>
						<option value=1>Poor</option>
					</select>
					<input class="btn btn-primary" type="submit" value="Cast My Vote!">
					<input type="hidden" name="sexternal" value="1">
				</form>

			<?php } else { ?>

                <br/>
				<div>Unregistered</div>
				<div><span style="color: red;">&raquo; &raquo; &raquo; Your version of CuteNews is Unregistered</span> -
					<b><a href="http://cutephp.com/cutenews/register/" target=_blank>Find out how to register it</a></b>
					by registering your version, all 'powered by CuteNews...' lines will be removed
				</div>

			<?php } ?>

		</div>
	</div>
</section>
