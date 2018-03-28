<?php
cn_snippet_messages();
cn_snippet_bc();
?>
<section>
	<div class="container">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#">Scripts</a></li>
			<!-- <li><a href="#">Routers</a></li> -->
		</ul>

		<form action="<?php echo PHP_SELF; ?>" method="POST">

			<?php cn_form_open('mod, opt, sub'); ?>
			<div>
                <textarea class="form-control" name="text" rows="15" style="font-family: Monospace;"><?php echo cn_htmlspecialchars($__text); ?></textarea>
            </div>
			<br/>

			<div class="well">

                <p></p>
                <div class="pull-right">
                    <select name="snippet" class="btn btn-default">
                        <?php foreach ($__list as $id => $_t) echo '<option '.(($id == $__snippet) ? 'selected' : '').'>'.cn_htmlspecialchars($id).'</option>'; ?>
                    </select>
                    <input class="btn btn-primary" type="submit" name="select" value="Select" />
                </div>
                <p>
                    <input class="btn btn-success" type="submit" value="Save changes" />
                    &nbsp;&nbsp;&nbsp;
                    <input type="text" class="form-control" style="display: inline-block; width: 200px;" value="" name="create" placeholder="New template name"/>
                    <input class="btn btn-primary" type="submit" value="Create new" />
                </p>

				<?php if ($__can_delete) { ?>
				    <input class="btn btn-primary" type="submit" name="delete" value="Delete snippet" />
				<?php } ?>
			</div>

		</form>

		<p style="text-align: right;">
            <a href="#" onclick="<?php echo cn_snippet_open_win(cn_url_modify(array('reset'), 'mod=help', 'section=snippets')); ?>" class="external">Understanding snippets</a>
        </p>

	</div>
</section>