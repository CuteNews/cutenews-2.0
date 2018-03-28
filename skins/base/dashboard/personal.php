<?php

list($member, $acl_write_news, $accesslevel, $personal_more) = _GL('member, acl_write_news, acl_desc, personal_more');

$username       = isset($member['name'])? $member['name']:'';
$nickname       = isset($member['nick'])? $member['nick']:'';
$avatar_url     = isset($member['avatar'])? (getoption('uploads_ext') ? getoption('uploads_ext') : getoption('http_script_dir') . '/uploads').'/'.$member['avatar']:'';
$usermail       = isset($member['email'])? $member['email']:'';
$written_news   = isset($member['cnt'])?  $member['cnt']:0;
$register_date  = isset($member['id'])?  $member['id']:0;
$hide_email     = isset($member['e-hide'])?  $member['e-hide']:0;
$ban_times      = isset($member['ban'])?  $member['ban']:0;

$callback = 'personal';

cn_snippet_messages();
cn_snippet_bc();

?>
<section>
	<div class="container">

		<form style="margin: 8px 16px;" role="form" action="<?php echo PHP_SELF; ?>" enctype="multipart/form-data" method="POST">
		<div class="row">
			<div class="col-sm-7">

                <?php cn_form_open('mod, opt'); ?>

                <h2>General options</h2>
                <div class="form-group">
                    <label>User Name:</label>
                    <input class="form-control" type="text" name="username" disabled="disabled" value="<?php echo cn_htmlspecialchars($username); ?>" />
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <p><input class="form-control" type="text" name="editmail" disabled="disabled" value="<?php echo cn_htmlspecialchars($usermail); ?>"></p>
                    <p><input type="checkbox" name="edithidemail" <?php if ($hide_email) { echo 'checked="checked"'; } ?>> Hide my e-mail from visitors</p>
                </div>

                <div class="form-group">
                    <label>New Password:</label>
                    <input class="form-control" type="password" name="editpassword">
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input class="form-control" type="password" name="confirmpassword">
                </div>

                <div class="form-group">
                    <label>Nickname</label>
                    <input class="form-control" type="text" name="editnickname" value="<?php echo cn_htmlspecialchars($nickname); ?>">
                </div>

                <div class="form-group">
                <label>Avatar</label>

                    <?php if ($avatar_url) { ?>
                        <p><img src="<?php echo $avatar_url; ?>" width="50" height="50" /></p>
                    <?php } ?>

                    <p><input type="file" name="avatar_file" /></p>
                </div>

                <!-- more personal data -->
                <?php
                if (is_array($personal_more)) {

                    foreach ($personal_more as $name => $pdata) {

                        echo '<label>'.$pdata['name'].'</label>';
                        if ($pdata['type'] == 'text') { ?>

                            <div class="form-group">
                                <input class="form-control" type="text"  name="more[<?php echo $name; ?>]" value="<?php echo (isset($pdata['value'])? cn_htmlspecialchars($pdata['value']):'');?>">
                            </div>

                        <?php } elseif ($pdata['type'] == 'textarea') { ?>

                            <div class="form-group">
                                <textarea class="form-control" name="more[<?php echo $name; ?>]"><?php echo (isset($pdata['value'])? cn_htmlspecialchars($pdata['value']):'');?></textarea>
                            </div>

                        <?php
                        }
                    }
                } ?>

                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="Save Changes" accesskey="s">
                </div>

			</div>

			<div class="col-sm-5">

				<h2>User statistics</h2>
                <div class="well">

                    <div>
                        <label>Registration date: </label>
                        <?php echo date('Y-m-d H:i:s', $register_date); ?>
                    </div>

                    <div>
                        <label>Access Level:</label>
                        <?php echo ucfirst($accesslevel); ?>
                    </div>

                    <div>
                    <?php if ($acl_write_news) { ?>

                        <label>Written news:</label>
                        <?php echo intval($written_news); ?>

                    <?php } ?>
                    </div>

                </div>
			</div>
        </div>
		</form>
	</div>
</section>
