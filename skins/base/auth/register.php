<?php

    list($errors_result, $regusername, $regnickname, $regemail) = _GL('errors_result, regusername, regnickname, regemail');

?>
<style>.required { color: red; }</style>

<form  name=login action="<?php echo PHP_SELF; ?>?register" method="post">
    <input type="hidden" name="action" value="register">
    <table>

        <tr>
            <td colspan="3"><?php if ($errors_result) { ?>Errors: <ol><?php foreach ($errors_result as $result) echo "<li style='color: red; font-weight: bold;'>$result</li>"; ?></ol><hr/><?php } ?></td>
        </tr>

        <tr>
            <td width=85>Username: <span class="required">*</span></td>
            <td colspan="2"><input tabindex="1" type="text" name=regusername value="<?php echo cn_htmlspecialchars($regusername); ?>" style="width:134px" size="20"></td>
        </tr>

        <tr>
            <td width=85>Nickname:</td>
            <td colspan="2"><input tabindex="1" type="text" name=regnickname value="<?php echo cn_htmlspecialchars($regnickname); ?>" style="width:134px" size="20"></td>
        </tr>

        <tr>
            <td width=85>Password: <span class="required">*</span></td>
            <td>
                <div><input tabindex="1" type="password" name=regpassword id="regpassword" onkeyup="password_strength();" style="width:134px" size="20"></div>
                <div id="password_strength"></div></td>
            <td>&nbsp;<input type="text" style="border: none; width: 150px;" id="pass_msg" disabled="true" value="Enter password"></td>
        </tr>

        <tr>
            <td width=85>Confirm:  <span class="required">*</span></td>
            <td colspan="2"><input tabindex="1" type="password" name="confirm" style="width:134px" size="20"></td>
        </tr>

        <tr>
            <td width=85>Email: <span class="required">*</span></td>
            <td colspan="2"><input tabindex="1" type="text" name="regemail" value="<?php echo cn_htmlspecialchars($regemail); ?>" style="width:134px" size="20"></td>
        </tr>

        <tr>
            <td width=85>Captcha: <span class="required">*</span></td>
            <td colspan="2"><input tabindex="1" type="text" name="captcha" style="width:134px" size="20"></td>
        </tr>

        <tr>
            <td width=85><a href="#" style="border-bottom: 1px dotted #000080;" onclick="getId('capcha').src='captcha.php?r='+Math.random(); return(false);">Refresh code</a></td>
            <td colspan="2"><img src="captcha.php" id="capcha" alt=""></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td colspan="2"><input accesskey="s" type=submit style="background-color: #F3F3F3;" value='Register'></td>
        </tr>

    </table>
</form>