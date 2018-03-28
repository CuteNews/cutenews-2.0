<?php

    list($wlist, $word, $replace, $is_repl_opt) = _GL('wlist, word, replace, repopt');
    cn_snippet_bc();

?>

<section>
	<div class="container">
		<?php if (!$is_repl_opt){ ?>

			<p>
				For working word replacement need turn on option: Use word replace module.<br/>
				For more information contact with site administrator.
			</p>

		<?php } ?>
		<form action="<?php echo PHP_SELF;?>" method="POST">

			<?php cn_form_open('mod, opt'); ?>
			<table class="table table-bordered table-striped table-hover">

				<tr>
                    <th>Word</th>
                    <th>Replace</th>
                </tr>

				<?php if (is_array($wlist) && $wlist) foreach ($wlist as $name => $var) { ?>

					<tr <?php if ($word == $name) echo 'class="row_selected"'; ?>>
						<td><a href="<?php echo cn_url_modify('word='.$name); ?>"><?php echo cn_htmlspecialchars($name); ?></a></td>
						<td><?php echo cn_htmlspecialchars($var); ?></td>
					</tr>

				<?php } else { ?>

                    <tr><td colspan="2"><?php echo i18n('Entries not found');?></td></tr>

                <?php } ?>

			</table>

			<div class="form-group">
				<label>Word</label>
				<input class="form-control" type="text"  name="word" value="<?php echo cn_htmlspecialchars($word); ?>"/>
			</div>

			<div class="form-group">
				<label>Replace</label>
				<input class="form-control" type="text"  name="replace" value="<?php echo cn_htmlspecialchars($replace); ?>"/>
			</div>

			<div class="form-group">
				<label><input type="checkbox" name="delete" value="Y"/> <?php echo i18n('Delete word');?></label>
			</div>

            <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>

		</form>
	</div>
</section>