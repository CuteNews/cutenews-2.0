<?php

    list($pc, $permission_ok) = _GL('pc, permission_ok');
    cn_snippet_messages();

?>

<div class="container">
	<form action="<?php echo PHP_SELF; ?>" method="POST">
        <div class="row">
            <div class="col-sm-6">

                <h3>Create an administrator account</h3>
                <p>
				<small>
					In order to start using the CMS,<br/>
					you must create an administrator account
                </small>
                </p>

                <div class="form-group">
                    <label for="user">User Name:</label>
                    <input id="user" type="text" class="form-control" name="username" value="<?php echo REQ('username', 'POST'); ?>" required autofocus />
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input id="email" type="text" class="form-control" name="email" value="<?php echo REQ('email', 'POST'); ?>" required />
                </div>

                <div class="form-group">
                    <label for="pass">Password:</label>
                    <input id="pass" type="password" class="form-control" name="password1" required />
                </div>

                <div class="form-group">
                    <label for="pass2">Confirm Password:</label>
                    <input id="pass2" type="password" class="form-control" name="password2" required />
                </div>

                <?php if ($permission_ok) { ?>
                    <input type="submit" class="btn btn-success" value="Create admin Account" />
                <?php } ?>

            </div>

			<div class="col-sm-6">
                <div class="the_permissions">
                    <h3>Permission check (check writable directories)</h3>
                    <div><?php if ($pc['cdata']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata</div>
                    <div><?php if ($pc['uploads']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> uploads</div>
                    <div><?php if ($pc['cdata/news']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/news</div>
                    <div><?php if ($pc['cdata/users']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/users</div>
                    <div><?php if ($pc['cdata/plugins']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/plugins</div>
                    <div><?php if ($pc['cdata/btree']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/btree</div>
                    <div><?php if ($pc['cdata/backup']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/backup</div>
                    <div><?php if ($pc['cdata/log']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/log</div>
                </div>
            </div>
        </div>
	</form>
</div>
