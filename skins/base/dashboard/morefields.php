<?php

list($list) = _GL('list');
list($type, $name, $desc, $meta, $group, $req) = _GL('type, name, desc, meta, group, req');

cn_snippet_messages();
cn_snippet_bc();

?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				<h2>Add Field</h2>
				<form action="<?php echo PHP_SELF; ?>" method="POST">

					<?php cn_form_open('mod, opt'); ?>
                    <div class="form-group">

                        <label>Field type</label>
                        <select class="form-control" name="type">
                            <option value="text">Text</option>
                            <option value="image" <?php if ($type == 'image') echo 'selected'; ?>>Image/Resource</option>
                            <option value="select" <?php if ($type == 'select') echo 'selected'; ?>>Select</option>
                            <option value="checkbox" <?php if ($type == 'checkbox') echo 'selected'; ?>>Checkbox</option>
                            <option value="price" <?php if ($type == 'price') echo 'selected'; ?>>Price</option>
                        </select>

                    </div>

                    <div class="form-group">
                        <label>Name</label> <span class="required">*</span>
                        <input class="form-control" type="text"  name="name" value="<?php echo cn_htmlspecialchars($name); ?>" required/>
                    </div>

                    <div class="form-group">
                    <label>Group</label>
                        <input class="form-control" type="text" name="group" value="<?php echo cn_htmlspecialchars($group); ?>"/>
                        <small>Grouping allows you to group fields in different blocks</small>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <input class="form-control"  type="text" name="desc" value="<?php echo cn_htmlspecialchars($desc); ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Meta</label>
                        <input class="form-control" type="text" name="meta" value="<?php echo cn_htmlspecialchars($meta); ?>"/>
                        <small>Meta used by SELECT box, enter value1;value2;...;valueN</small>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" id="lbl_required_field" name="req" <?php if ($req) echo 'checked'; ?> value="Y"/>
                        <label for="lbl_required_field">Required</label>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" name="remove" value="Y" id="lbl_delete_field"/>
                        <label for="lbl_delete_field">Delete field</label>
                    </div>

                    <input class="btn btn-primary" type="submit" value="Add / Replace field" />

				</form>
			</div>

			<div class="col-sm-6">

				<h2>Fields</h2>
				<table class="table table-bordered table-striped table-hover">

					<tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Desc</th>
                        <th>Meta</th>
                        <th>Group</th>
                        <th>Required</th>
                    </tr>

					<?php if ($list) foreach ($list as $_name => $item) { ?>

                        <tr <?php if ($name == $_name) echo 'class="row_selected"'; ?>>
							<td align="center"><a href="<?php echo cn_url_modify("extr_name=$_name"); ?>"><?php echo cn_htmlspecialchars($_name); ?></a></td>
							<td align="center" ><?php echo $item['type']; ?></td>
							<td><?php echo cn_htmlspecialchars($item['desc']); ?></td>
							<td><?php echo cn_htmlspecialchars($item['meta']); ?></td>
							<td align="center"><?php echo $item['grp']; ?></td>
							<td align="center"><?php echo $item['req'] ? "YES" : ''; ?></td>
						</tr>

					<?php } else { ?><tr><td colspan="6">No entries found</td></tr><?php } ?>

				</table>
			</div>

		</div>
	</div>
</section>