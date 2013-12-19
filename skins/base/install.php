<?php

list($pc, $permission_ok) = _GL('pc, permission_ok');
cn_snippet_messages();
?>
<form action="<?php echo PHP_SELF; ?>" method="POST">

    <table>
        <tr>
            <td valign="top">
                <table class="panel">
                    <tr>
                        <td>Username</td>
                        <td><input type="text" name="username" value="<?php echo REQ('username', 'POST'); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><input type="text" name="email" value="<?php echo REQ('email', 'POST'); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td><input type="password" name="password1" /></td>
                    </tr>
                    <tr>
                        <td>Confirm</td>
                        <td><input type="password" name="password2" /></td>
                    </tr>
                    <?php if ($permission_ok) { ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" value="Create admin Account" /></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
            <td valign="top" style="padding: 0 0 0 32px; font-size: 18px; color: #888;">
                <div>
                In order to start using the CMS,<br/>
                you must create an administrator account
                </div>

                <div class="the_permissions">
                    <h2>Permission check (check writable dirs)</h2>
                    <div><?php if ($pc['cdata']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata</div>
                    <div><?php if ($pc['uploads']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> uploads</div>
                    <div><?php if ($pc['cdata/news']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/news</div>
                    <div><?php if ($pc['cdata/users']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/users</div>
                    <div><?php if ($pc['cdata/plugins']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/plugins</div>
                    <div><?php if ($pc['cdata/btree']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/btree</div>
                    <div><?php if ($pc['cdata/backup']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/backup</div>
                    <div><?php if ($pc['cdata/log']) echo '<span class="perm-ok">[OK]</span>'; else echo '<span class="perm-fail">[FAIL]</span>'; ?> cdata/log</div>
                </div>
            </td>
        </tr>

    </table>
</form>