<?php

    $last_user_name = '';
    cn_snippet_messages();

?>
<style>
    .form-signin {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
        margin-bottom: 10px;
    }
    .form-signin .checkbox {
        font-weight: normal;
        margin: -5px 0 15px 0;
    }
    .form-signin .form-control {
        position: relative;
        height: auto;
        -webkit-box-sizing: border-box;
           -moz-box-sizing: border-box;
                box-sizing: border-box;
        padding: 10px;
        font-size: 16px;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input[type="text"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .form-signin .field-auth-row {
        padding: 4px 0;
    }
</style>

<section>
	<div class="container">
		<form class="form-signin" name="login" id="login_form" action='<?php echo PHP_SELF; ?>' method="post">

            <h2 class="form-signin-heading">Please sign in</h2>
            <input type="hidden" name="action" value="dologin">

            <div class="field-auth-row">
                <label for="login_username" class="sr-only">User:</label>
                <input type="text" id="login_username" class="form-control" name="username" placeholder="User" value="<?php echo $last_user_name; ?>" required autofocus>
            </div>

            <div class="field-auth-row">
                <label for="login_password" class="sr-only">Password</label>
                <input type="password" id="login_password" class="form-control" placeholder="Password" name="password" required>
            </div>

            <div class="checkbox">
                <label for=rememberme title="Remember me for 30 days, do not use on Public terminals!">
                    <input id="rememberme" type="checkbox" value="yes"  name="rememberme"> Remember me
                </label>
            </div>

            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <?php if (getoption('allow_registration')) { ?>
                <a class="btn btn-lg btn-primary btn-block" href="?register">Register</a>
            <?php } ?>

            <br/>
            <div style="text-align: center">
                <a href="?register&lostpass">(lost password)</a>
            </div>
		</form>
	</div> <!-- /container -->

</section>