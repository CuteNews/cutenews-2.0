<?php

    list($errors_result, $regusername, $regnickname, $regemail) = _GL('errors_result, regusername, regnickname, regemail');

?>
<style>
    .form-register {
      max-width: 330px;
      padding: 15px;
      margin: 0 auto;
    }
    .form-register .form-register-heading,
    .form-register .checkbox {
      margin-bottom: 10px;
    }
    .required { color: red; }
</style>

<section>
	<div class="container">
		<form class="form-register" name=login action="<?php echo PHP_SELF; ?>?register" method="post">

			<h2 class="form-register-heading">Please Register</h2>
			<input type="hidden" name="action" value="register">
            <?php if ($errors_result) { ?>Errors: <ol><?php foreach ($errors_result as $result) echo "<li style='color: red; font-weight: bold;'>$result</li>"; ?></ol><hr/><?php } ?>
            <div class="form-group">
                <label for="usr">User Name:</label> <span class="required">*</span>
                <input id="usr" tabindex="1" class="form-control" type="text" name="regusername" value="<?php echo cn_htmlspecialchars($regusername); ?>" required autofocus >
            </div>

            <div class="form-group">
                <label for="nick">Nickname:</label>
                <input id="nick" tabindex="1" class="form-control" type="text" name="regnickname" value="<?php echo cn_htmlspecialchars($regnickname); ?>" required >
            </div>

            <div class="form-group">
                <label for="regpassword">Password:</label> <span class="required">*</span>
                <input tabindex="1" class="form-control" type="password" name="regpassword" id="regpassword" onkeyup="password_strength();" required>
                <input type="text" class="form-control" id="pass_msg" disabled="true" value="Password Strength">
                <div id="password_strength"></div>
            </div>

            <div class="form-group">
                <label for="confpassword"> Confirm Password:</label>  <span class="required">*</span>
                <input id="confpassword" tabindex="1" class="form-control" type="password" name="confirm" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>	<span class="required">*</span>
                <input id="email" tabindex="1" class="form-control" type="text" name="regemail" value="<?php echo cn_htmlspecialchars($regemail); ?>" required>
            </div>

            <div class="form-group">
                <label for="captcha">Captcha:</label> <span class="required">*</span>
                <input id="captcha" tabindex="1" class="form-control" type="text" name="captcha" required>
            </div>

            <div class="form-group">
               <a href="#" class="btn btn-default" onclick="getId('capcha').src='captcha.php?r='+Math.random(); return(false);">Refresh captcha</a>
               <img src="captcha.php" id="capcha" alt="">
            </div>

            <div class="form-group">
               <input accesskey="s" class="btn btn-primary" type="submit"  value='Register'>
            </div>

		</form>
	</div>
</section>		