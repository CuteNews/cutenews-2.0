{if $MSG}<div class="user_login_messages">{$MSG}</div>{/if}
<form class="user_login_form" action="{$PHP_SELF}" method="POST">
    <input type="hidden" name="widget_personal_action" value="login" />
    <input type="hidden" name="widget_personal_csrf" value="{$CSRF}" />
    <input type="hidden" name="widget_personal_keep" value="{$KEEP}" />
    <div class="user_login_name"><input type="text" name="widget_personal_username" value="{$username}" /> Login</div>
    <div class="user_login_pass"><input type="password" name="widget_personal_password" value="" /> Password</div>
    <label for="rememberme" title="Remember me for 30 days, Do not use on Public-Terminals!">
        <input id="rememberme" type="checkbox" value="yes" {$rememberme} style="border:0px;" name="widget_personal_rememberme">Remember Me</label>
    <div class="user_login_submit"><input type="submit" value="Login" /></div>
</form>