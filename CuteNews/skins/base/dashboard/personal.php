<?php

list($member, $acl_write_news, $accesslevel, $personal_more) = _GL('member, acl_write_news, acl_desc, personal_more');

$username       = $member['name'];
$nickname       = $member['nick'];
$usermail       = $member['email'];
$written_news   = $member['cnt'];
$register_date  = $member['id'];
$hide_email     = $member['e-hide'];
$ban_times      = $member['ban'];

cn_snippet_messages();
cn_snippet_bc();

?>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt'); ?>

    <table class="std-table" width="100%">

        <tr><th colspan="2" align="left">General options</th></tr>
        <tr>
            <td align="right">Username</td>
            <td><input type="text" name="username" disabled="disabled" style="background: #f0f0f0; width: 250px;" value="<?php echo cn_htmlspecialchars($username); ?>" /></td>
        </tr>

        <tr class="row">
            <td align="right">Email</td>
            <td>
                <input type="text" name="editmail" disabled="disabled" style="background: #f0f0f0; width: 250px;" value="<?php echo cn_htmlspecialchars($usermail); ?>">
                <input type="checkbox" name="edithidemail" <?php if ($hide_email) echo 'checked="checked"'; ?>> Hide my e-mail from visitors
            </td>
        </tr>

        <tr>
            <td align="right">New Password</td>
            <td><input type="password" name="editpassword"> </td>
        </tr>

        <tr class="row">
            <td align="right">Confirm Password</td>
            <td><input type="password" name="confirmpassword"> Confirm new password</td>
        </tr>

        <tr>
            <td align="right">Nickname</td>
            <td><input type=text style="width: 350px;" name="editnickname" value="<?php echo cn_htmlspecialchars($nickname); ?>"></td>
        </tr>

        <!-- more personal data -->
        <?php if (is_array($personal_more)) foreach ($personal_more as $name => $pdata) { ?>

            <tr>
                <td valign="top" align="right" style="padding: 12px 4px 0 0;"><?php echo $pdata['name']; ?></td>
                <td valign="top">
                    <?php if ($pdata['type'] == 'text') { ?>
                        <input type="text" style="width: 500px;" name="more[<?php echo $name; ?>]" value="<?php echo cn_htmlspecialchars($pdata['value']); ?>">
                    <?php } elseif ($pdata['type'] == 'textarea') { ?>
                        <textarea style="width: 500px; height: 100px;" name="more[<?php echo $name; ?>]"><?php echo cn_htmlspecialchars($pdata['value']); ?></textarea>
                    <?php } ?>
                </td>
            </tr>

        <?php } ?>

        <tr>
            <td>&nbsp;</td>
            <td><input type=submit value="Save Changes" accesskey="s"></td>
        </tr>

        <tr><th colspan="2" align="left">User statistics</th></tr>

        <tr>
            <td align="right">Registration date </td>
            <td><?php echo date('Y-m-d H:i:s', $register_date); ?></td>
        </tr>

        <tr>
            <td align="right">Access Level</td>
            <td><?php echo ucfirst($accesslevel); ?></td>
        </tr>

        <?php if ($acl_write_news) { ?>
        <tr>
            <td align="right">Written news</td>
            <td><?php echo intval($written_news); ?></td>
        </tr>
        <?php } ?>

    </table>
</form>
