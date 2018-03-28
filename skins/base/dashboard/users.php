<?php

    list($users, $section, $st, $per_page, $grp) = _GL('users, section, st, per_page, grp');
    list($user_name, $user_nick, $user_email, $user_acl, $is_edit) = _GL('user_name, user_nick, user_email, user_acl, is_edit');

    cn_snippet_messages();
    cn_snippet_bc();

?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-4">
				<form role="form" action="<?php echo PHP_SELF; ?>" method="POST">

					<h2><?php echo $is_edit? 'Edit' : 'Add';?> User</h2>
                    <?php $required_on = (!$user_name || ($user_name && !$is_edit)); ?>

					<?php cn_form_open('mod, opt, section'); ?>
                    <div class="form-group">
                        <label>Username <span class="required">*</span></label>
                        <input class="form-control" type="text" name="user_name" value="<?php echo cn_htmlspecialchars($user_name); ?>" required/>
                    </div>

                    <div class="form-group">

                        <label>Password <?php if ($required_on) { ?><span class="required">*</span><?php } ?> </label>
                        <input class="form-control" type="password" name="user_pass" value="" <?php echo $required_on? 'required' : '';?>/>
                        <?php if ($user_name && $is_edit) { ?>
                            write password to change<br/>
                            <span>password not changed if field stay blank</span>
                        <?php } ?>

                    </div>

                    <div class="form-group">
                        <label>Confirm <?php if ($required_on) { ?><span class="required">*</span><?php } ?> </label>
                        <input class="form-control" type="password" name="user_confirm" value="" <?php echo $required_on? 'required' : '';?> />
                    </div>

                    <div class="form-group">
                        <label>Nickname </label>
                        <input class="form-control" type="text" name="user_nick" value="<?php echo cn_htmlspecialchars($user_nick); ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Email <span class="required">*</span> </label>
                        <input class="form-control" type="text" name="user_email" value="<?php echo cn_htmlspecialchars($user_email); ?>" required/>
                    </div>

                    <?php if ($user_name && $is_edit) { ?>
                    <div class="form-group">
                        <input type="checkbox" name="delete" value="Y"/>
                        <label>Delete current user?</label>
                    </div>
                    <?php } ?>

                    <div class="form-group">

                        <label>Access level </label>
                        <select name="user_acl" class="btn btn-default">
                        <?php foreach ($grp as $grp_id => $item) { ?>
                            <option value="<?php echo $grp_id; ?>"<?php if ($user_acl == $grp_id) echo ' selected="selected"'; ?>><?php echo cn_htmlspecialchars(ucfirst($item['N'])); ?></option>
                        <?php } ?>
                        </select>

                        <?php if ($user_name && $is_edit) { ?>
                            <button class="btn btn-primary" name="edit" value="edit">Edit</button>
                        <?php } else { ?>
                            <button class="btn btn-primary" name="add" value="add">Add</button>
                        <?php } ?>

                        <br/>
                        <br/>
                        <div><a class="external" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=users'); ?>" href="#">Understanding user levels</a></div>

                    </div>
				</form>
			</div>

			<div class="col-sm-8">

				<h2>Manage Users</h2>

				<ul class="nav nav-tabs">

					<li <?php if ($section == 0) echo ' class="active"'; ?>><a href="<?php echo cn_url_modify('user_name,st', 'section'); ?>" >Everyone</a></li>
					<?php foreach ($grp as $grp_id => $item) { ?>
						<li <?php if ($section == $grp_id) echo ' class="active"'; ?>><a href="<?php echo cn_url_modify('user_name,st', 'section='.$grp_id); ?>"><?php echo cn_htmlspecialchars(ucfirst($item['N'])); ?></a></li>
					<?php } ?>

				</ul>
				<table class="table table-bordered table-striped table-hover" >
					<tr>
						<th width="50">ID</th>
						<th>Username</th>
						<th>Regdate</th>
						<th>Written news</th>
						<th>Access</th>
					</tr>
					<?php
                    if ($users) {
                        foreach ($users as $id => $user)
                            if ($user) { ?>
                            <tr <?php if (isset($user['name']) && $user_name == $user['name']) echo 'class="row_selected"'; ?>>

                                <td><a href="<?php echo (isset($user['id'])? cn_url_modify('user_name='.$user['name']):''); ?>"><?php echo $user['id']; ?></td>
                                <td><a href="<?php echo (isset($user['name'])? cn_url_modify('user_name='.$user['name']):''); ?>"><?php echo (isset($user['name']) ? cn_htmlspecialchars($user['name']):''); ?></td>
                                <td><?php echo (isset($user['id'])? date('Y-m-d H:i', $user['id']):''); ?></td>
                                <td><?php echo (isset($user['cnt'])? intval($user['cnt']):0); ?></td>
                                <td><?php echo (isset($user['acl']) && isset($grp[ $user['acl'] ]['N']) ? cn_htmlspecialchars(ucfirst($grp[ $user['acl'] ]['N'])) : ''); ?></td>

                            </tr>
                        <?php }

                    } else {
                        ?><tr><td colspan="5">No users found</td> </tr>
                    <?php } ?>
				</table>

				<!-- paginate -->
				<?php cn_snippet_paginate($st, $per_page, count($users)); ?>

			</div>
		</div>
	</div>
</section>

