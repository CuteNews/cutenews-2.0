<?php

list($member, $acl_write_news, $accesslevel, $personal_more) = _GL('member, acl_write_news, acl_desc, personal_more');

$username       =isset($member['name'])? $member['name']:'';
$nickname       =isset($member['nick'])? $member['nick']:'';
$avatar_url     =isset($member['avatar'])? (getoption('uploads_ext') ? getoption('uploads_ext') : getoption('http_script_dir') . '/uploads').'/'.$member['avatar']:'';
$usermail       =isset($member['email'])? $member['email']:'';
$written_news   =isset($member['cnt'])?  $member['cnt']:0;
$register_date  =isset($member['id'])?  $member['id']:0;
$hide_email     =isset($member['e-hide'])?  $member['e-hide']:0;
$ban_times      =isset($member['ban'])?  $member['ban']:0;

$callback='personal';

cn_snippet_messages();
cn_snippet_bc();

?>

<form action="<?php echo PHP_SELF; ?>" enctype="multipart/form-data" method="POST">

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
                <input type="checkbox" name="edithidemail" <?php if ($hide_email) { echo 'checked="checked"'; } ?>> Hide my e-mail from visitors
            </td>
        </tr>

        <tr>
            <td align="right">New Password</td>
            <td><input type="password" name="editpassword"> </td>
        </tr>

        <tr class="row">
            <td align="right">Confirm New Password</td>
            <td><input type="password" name="confirmpassword"></td>
        </tr>

        <tr>
            <td align="right">Nickname</td>
            <td><input type=text style="width: 350px;" name="editnickname" value="<?php echo cn_htmlspecialchars($nickname); ?>"></td>
        </tr>

        <tr>
            <td align="right">Avatar</td>
            <td>
                <img src="<?=$avatar_url; ?>" width="50" height="50" /><br/>
                <input type="file" name="avatar_file" style="width: 350px;"/>
            </td>
        </tr>
        
        <!-- more personal data -->
        <?php if (is_array($personal_more)) { 
            foreach ($personal_more as $name => $pdata) { ?>

            <tr>
                <td valign="top" align="right" style="padding: 12px 4px 0 0;"><?php echo $pdata['name']; ?></td>
                <td valign="top">
                    <?php if ($pdata['type'] == 'text') { ?>
                        <input type="text" style="width: 500px;" name="more[<?php echo $name; ?>]" value="<?=(isset($pdata['value'])? cn_htmlspecialchars($pdata['value']):'');?>">
                    <?php } elseif ($pdata['type'] == 'textarea') { ?>
                        <textarea style="width: 500px; height: 100px;" name="more[<?php echo $name; ?>]"><?=(isset($pdata['value'])? cn_htmlspecialchars($pdata['value']):'');?></textarea>
                    <?php } ?>
                </td>
            </tr>

        <?php } } ?>

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
