<?php

list($template_parts, $all_templates, $template_text, $template, $sub, $can_delete) = _GL('template_parts, all_templates, template_text, template, sub, can_delete');

cn_snippet_messages();
cn_snippet_bc();

?>
<section>
	<div class="container">

		<form action="<?php echo PHP_SELF; ?>" method="POST">

			<?php cn_form_open('mod, opt, template, sub'); ?>
			<input type="hidden" name="select" value="Y" />

			<div class="well">
				<select name="template" class="selectpicker" data-hide-disabled="true" data-live-search="true" data-size="5" data-style="btn-default" data-width="fit">
					<?php foreach ($all_templates as $id => $ud) { ?>
						<option value="<?php echo $id; ?>" <?php if ($template == $id) echo 'selected'; ?> ><?php echo ucfirst($id); ?></option>
					<?php } ?>
				</select>

				<input class="btn btn-primary" type="submit" value="Select template" />
			</div>
		</form>

		<div>
			<ul class="nav nav-tabs">
				<?php foreach ($template_parts as $id => $template_name) { ?>
					<li <?php if ($sub == $id) echo 'class="active"'; ?>><a href="<?php echo cn_url_modify('sub='.$id); ?>"><?php echo $template_name; ?></a></li>
				<?php } ?>
			</ul>
			<div style="clear: left;"></div>
		</div>

		<form role="form" class="" action="<?php echo PHP_SELF; ?>" method="POST">

			<!-- view template data -->
			<?php if ($template && $sub) { ?>

				<?php if (!getoption('ckeditor2template')) { ?>
                    <script>
                        window.onload = function() {
                            var mixedMode = {
                                name: "htmlmixed",
                                scriptTypes: [{
                                    matches: /\/x-handlebars-template|\/x-mustache/i,
                                    mode: null
                                }, {
                                    matches: /(text|application)\/(x-)?vb(a|script)/i,
                                    mode: "vbscript"
                                }
                                ]
                            };

                            var editor = CodeMirror.fromTextArea(document.getElementById("template_text"), {
                                mode: mixedMode,
                                selectionPointer: true,
                                lineNumbers: true,
                            });

                            };
                    </script>
				<?php } ?>

				<?php cn_form_open('mod, opt, template, sub'); ?>
				<textarea class="form-control" id="template_text" style="height: 300px; font: 11px/1.2em Monospace;" name="save_template_text"><?php echo cn_htmlspecialchars($template_text); ?></textarea>
				<?php if (getoption('ckeditor2template')) { cn_snippet_ckeditor('template_text'); }?>

			<?php } ?>

			<!-- template actions -->
			<?php if ($template) { ?>

				<?php cn_form_open('mod, opt, template, sub'); ?>
				<div class="well">

					<input class="btn btn-success" type="submit" value="Save template" />
					<input class="form-control" style="display: inline; width: 300px" type="text" name="template_name" value="" />
					<input class="btn btn-primary" type="submit" name="create" value="Clone template" />

					<?php if ($can_delete) { ?>
						<input class="btn btn-primary" type="submit" name="delete" value="Delete" />
					 <?php } else { ?>
						<input class="btn btn-primary" type="submit" name="reset" value="Reset" />
					<?php } ?>

				</div>

			<?php } ?>
		</form>


		<div class="pull-right">
			<a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=templates'); ?>" class="external">Understanding Templates</a>
			&nbsp;&nbsp;
			<a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=tplvars', array('w' => 720, 'h' => 640, 'l' => 'auto')); ?>" class="external">Template variables</a>
		</div>
	</div>
</section>