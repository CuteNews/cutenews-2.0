<?php

list($users, $section, $st, $per_page, $grp) = _GL('users, section, st, per_page, grp');
list($user_name, $user_nick, $user_email, $user_acl, $is_edit, $delete) = _GL('user_name, user_nick, user_email, user_acl, is_edit');

cn_snippet_messages();
cn_snippet_bc();

?>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt, section'); ?>

    <!-- add / modify form -->
    <table class="panel" width="100%">

        <tr>
            <td align="right">Username <span class="required">*</span></td>
            <td><input type="text" name="user_name" style="width: 350px;" value="<?php echo cn_htmlspecialchars($user_name); ?>"/></td>
        </tr>

        <tr>
            <td align="right">Password <?php if (!$user_name||($user_name&&!$is_edit)) { ?><span class="required">*</span><?php } ?></td>
            <td><input style="width: 350px;" type="password" name="user_pass" value=""/> 
                <?php if ($user_name&&$is_edit) { ?>
                write password to change<br/>
                <span style="color: #808080; font-size:10px;">password not changed if field stay blank</span>
                <?php } ?>                
            </td>
        </tr>

        <tr>
            <td align="right">Confirm <?php if (!$user_name||($user_name&&!$is_edit)) { ?><span class="required">*</span><?php } ?></td>
            <td><input style="width: 350px;" type="password" name="user_confirm" value=""/></td>
        </tr>

        <tr>
            <td align="right">Nickname</td>
            <td><input style="width: 650px;" type="text" name="user_nick" value="<?php echo cn_htmlspecialchars($user_nick); ?>"/></td>
        </tr>

        <tr>
            <td align="right">Email <span class="required">*</span></td>
            <td><input style="width: 650px;" type="text" name="user_email" value="<?php echo cn_htmlspecialchars($user_email); ?>"/></td>
        </tr>

        <tr>
            <td align="right"><input type="checkbox" name="delete" value="Y"/> </td>
            <td>Delete current user?</td>
        </tr>

        <tr>
            <td align="right">Access level</td>
            <td>
                <select name="user_acl">
                    <?php foreach ($grp as $grp_id => $item) { ?>
                        <option value="<?php echo $grp_id; ?>"<?php if ($user_acl == $grp_id) echo ' selected="selected"'; ?>><?php echo cn_htmlspecialchars(ucfirst($item['N'])); ?></option>
                    <?php } ?>
                </select>
                <button name="add" value="add">Add</button>
                <?php if ($user_name && $is_edit) { ?><button name="edit" value="edit">Edit</button><?php } ?>
            </td>

        </tr>

    </table>

</form>
<br/>

<ul class="sysconf_top">

    <li<?php if ($section == 0) echo ' class="selected"'; ?>><a href="<?php echo cn_url_modify('user_name,st', 'section'); ?>" >Everyone</a></li>
    <?php foreach ($grp as $grp_id => $item) { ?>
        <li<?php if ($section == $grp_id) echo ' class="selected"'; ?>><a href="<?php echo cn_url_modify('user_name,st', 'section='.$grp_id); ?>"><?php echo cn_htmlspecialchars(ucfirst($item['N'])); ?></a></li>
    <?php } ?>

</ul>
<table class="std-table" width="100%">
    <tr>
        <th>Username</th>
        <th>Regdate</th>
        <th>Written news</th>
        <th>Access</th>
    </tr>
    <?php if ($users) foreach ($users as $id => $user) { ?>
        <tr <?php if ($user_name == $user['name']) echo 'class="row_selected"'; ?>>
            <td width="400px"><a href="<?php echo cn_url_modify('user_name='.$user['name']); ?>"><?php echo cn_htmlspecialchars($user['name']); ?></td>
            <td align="center"><?php echo date('Y-m-d H:i', $user['id']); ?></td>
            <td align="center"><?php echo intval($user['cnt']); ?></td>
            <td align="center"><?php echo cn_htmlspecialchars(ucfirst($grp[ $user['acl'] ]['N'])); ?></td>
        </tr>
    <?php } else { ?><tr><td colspan="5">No users found</td> </tr><?php } ?>
</table>

<!-- paginate -->
<?php cn_snippet_paginate($st, $per_page, count($users)); ?>


<div style="text-align: right; margin: 16px 0 0 0;"><a class="external" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=users'); ?>" href="#">Understanding user levels</a></div>
