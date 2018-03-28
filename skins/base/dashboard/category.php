<?php

	list($category_id, $categories, $category_name, $category_memo, $category_icon, $category_acl, $category_parent, $groups) = _GL('category_id, categories, category_name, category_memo, category_icon, category_acl, category_parent, groups');

	cn_snippet_messages();
	cn_snippet_bc();
?>

<section>
<div class="container">

<!-- show categories -->
<form role="form" class="form-inline" action="<?php echo PHP_SELF; ?>" method="POST">
	<div class="container">
		<div class="row">
			<div class="col-sm-6">

				<?php cn_form_open('mod, opt'); ?>
				<input class="form-control" type="hidden" name="category_id" value="<?php echo $category_id; ?>" />

				<table class="table table-bordered table-striped table-hover">
					<tr><th>ID</th> <th>Name</th> <th>Memo</th>  <th>Icon</th> <th>Restriction</th> </tr>

					<?php
                    if ($categories) {

                        foreach ($categories as $id => $category) {

                            $acl_message = array();
                            $acls = spsep($category['acl']);

                            foreach ($acls as $grp) {
                                $acl_message[] = ucfirst($groups[$grp]['N']);
                            }

                            $acl_message = join(', ', $acl_message);

					    ?>
						<tr<?php if ($id == $category_id) echo ' class="row_selected"'; ?>>

							<td><?php echo intval($id); ?></td>
							<td><?php echo str_repeat('&ndash;', $category['level']); ?> <a href="<?php echo cn_url_modify("category_id=$id"); ?>"><?php echo cn_htmlspecialchars($category['name']); ?></a></td>
							<td><?php echo cn_htmlspecialchars($category['memo']); ?></td>
							<td class="text-center"><?php if ($category['icon']) echo '<img style="max-width: 128px; max-height: 128px;" src="'.cn_htmlspecialchars($category['icon']).'" />'; else echo '---'; ?></td>
							<td align="text-center"><?php echo $acl_message ? $acl_message : '---'; ?></td>

						</tr>
					    <?php }

                    } else { ?>

                        <tr><td colspan="5" class="pull-center">No one category exists</td> </tr>

                    <?php } ?>

				</table>

				<div class="pull-left">
					<img border="0" src="skins/images/help_small.gif" >
					<a  href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=categories'); ?>">What are categories and How to use them</a>
				</div>

			</div>
			<div class="col-sm-6">

				<table class="table table-bordered table-striped table-hover">
					<tr>
						<td>Name <span class="required">*</span></td>
						<td><input class="form-control" type="text" name="category_name" value="<?php echo cn_htmlspecialchars($category_name); ?>" required/></td>
					</tr>

					<tr>
						<td>Memo</td>
						<td><input class="form-control" type="text" name="category_memo" value="<?php echo cn_htmlspecialchars($category_memo); ?>"/>
							<div>alternative for name (visual)</div>
						</td>
					</tr>

					<tr>
						<td>Parent</td>
						<td>
                            <select class="form-control" name="category_parent">
								<option value="0">-- None --</option>
								<?php
                                foreach ($categories as $id => $category) {
                                    if ($category_id != $id) {
                                        echo '<option ' . ($category_parent == $id ? 'selected' : '') . ' value="'.$id.'">' . cn_htmlspecialchars($category['name']) . '</option>';
                                    }
								} ?>
							</select>
						</td>
					</tr>

					<tr>
						<td>Icon</td>
						<td><input class="form-control" type="text" id="category_icon" name="category_icon" value="<?php echo cn_htmlspecialchars($category_icon); ?>"/>
							<a class="external" href="#" onclick="<?php echo cn_snippet_open_win(cn_url_modify('mod=media','opt=inline','faddm=C','callback=category_icon'), array('w' => 640)); ?>">Media manager</a>
						</td>
					</tr>

					<tr>
						<td>Groups</td>
						<td>
							<?php foreach ($groups as $id => $name) { ?>
							    <input type="checkbox" name="category_acl[]" <?php if ($category_acl&& in_array($id, $category_acl)) echo 'checked'; ?> value="<?php echo $id; ?>"/> <?php echo cn_htmlspecialchars($name['N']); ?>
							<?php } ?>
						</td>
					</tr>

					<tr><td>&nbsp;</td>
						<td>
							<div class="pull-left">
								<?php if(!$category_id){ ?>
									<input class="btn btn-primary" type="submit" onclick="actionClick('a');" value="Add category" />&nbsp;
								<?php } else { ?>
									<input class="btn btn-primary" type="submit" onclick="actionClick('e');" value="Edit category" />&nbsp;
									<input class="btn btn-primary" type="submit" onclick="actionClick('c');" value="Cancel edit" />&nbsp;
								<?php } ?>
							</div>
							<div class="pull-right">
								<?php if($category_id){ ?>
									<input class="btn btn-primary" type="submit" onclick="actionClick('d');" value="Delete category" />
								<?php } ?>
							</div>
							<input type="hidden" id="mode" name="mode" value=""/>
						</td></tr>

				</table>


				<script type="text/javascript">
					function actionClick(m)
					{
						var input=document.getElementById('mode');
						input.setAttribute('value',m);
					}
				</script>
			</div>
		</div>
	</div>
</form>
</div>
</section>
