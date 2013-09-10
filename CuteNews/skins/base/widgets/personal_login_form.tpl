{if $MSG}<div class="user_login_messages">{$MSG}</div>{/if}
<form class="user_login_form" action="{$PHP_SELF}" method="POST">
    <input type="hidden" name="widget_personal_action" value="login" />
    <input type="hidden" name="widget_personal_csrf" value="{$CSRF}" />
    <input type="hidden" name="widget_personal_keep" value="{$KEEP}" />
    <div class="user_login_name"><input type="text" name="widget_personal_username" value="{$username}" /> Login</div>
    <div class="user_login_pass"><input type="password" name="widget_personal_password" value="" /> Password</div>
    <div class="user_login_submit"><input type="submit" value="Login" /></div>
</form>