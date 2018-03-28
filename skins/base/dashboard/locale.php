<?php

list($lang, $langs, $words, $selected, $translate) = _GL('lang, langs, words, selected, translate');

$exid = REQ('exid');

cn_snippet_bc();
cn_snippet_messages();

?>
<section>
	<div class="container">

		<!-- selection -->
		<form action="<?php echo PHP_SELF;?>" method="POST">

			<?php cn_form_open('mod, opt'); ?>

			<label>Select language</label>
			<sup><a href="#" title="Create new language file (e.g. fr.txt) in ./core/lang with 666, 664 or 644 permission" onclick="return(tiny_msg(this));">?</a></sup>

			<select name="lang" class="btn btn-default">
				<?php foreach ($langs as $name) { ?>
					<option <?php echo ($name == $lang) ? 'selected="selected"' : ''; ?>><?php echo $name;?></option>
				<?php } ?>
			</select>

			<input class="btn btn-primary" type="submit" value="Select" />

		</form>

		<!-- operations -->
		<?php if ($lang) { ?>

            <br/>
			<form action="<?php echo PHP_SELF; ?>" method="POST">

				<?php cn_form_open('mod, opt, lang, selected'); ?>
				<input type="hidden" name="modifica" value="Y" />

				<table class="table table-bordered table-striped table-hover">

                    <?php if ($selected) { ?>

                        <tr><td align="right">Translate</td>
                            <td><input type="text" style="width: 650px;" name="translate" value="<?php echo $translate; ?>" /> </td>
                        </tr>

                        <tr><td align="right"><input type="checkbox" name="delete" value="Y" /></td>
                            <td>Delete phrase</td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" class="btn btn-success" name="action" value="Edit"/>
                                <input type="submit" class="btn btn-warning" name="action" value="Cancel" />
                            </td>
                        </tr>

                    <?php } else { ?>

                        <tr><td align="right">Phrase</td>
                            <td><input type="text" style="width: 450px;" name="phrase" required value="" /></td>
                        </tr>

                        <tr><td align="right">Translate</td>
                            <td><input type="text" style="width: 650px;" name="translate" value="" required /></td>
                        </tr>

                        <tr><td>&nbsp;</td>
                            <td><input type="submit" class="btn btn-success" name="action" value="Add"/></td>
                        </tr>

                    <?php } ?>

				</table>
				<br/>

                <!-- Show translation list -->
                <?php if ($words) { ?>
				<table class="table table-bordered table-striped table-hover">

					<tr><th width="100">Token</th><th>Translate</th></tr>
					<?php foreach ($words as $name => $translation) { ?>

						<tr <?php if ($name == $selected) echo 'class="row_selected"'; ?>>
							<td><a href="<?php echo cn_url_modify('lang='.$lang, 'selected='.$name); ?>"><?php echo $name;?></a></td>
							<td><?php echo cn_htmlspecialchars($translation);?></td>
						</tr>

					<?php } ?>

				</table>
                <?php } else { ?>

                    <h3 align="center">No translations yet</h3><br/><br/>

                <?php } ?>

			</form>

		<?php } ?>
	</div>
</section>