<form action="<?php echo PHP_SELF; ?>" method="POST">

    <input type="hidden" name="register" />
    <input type="hidden" name="lostpass" />

    <table>
        <tr><td>Username</td><td>Email</td><td>Secret word (optional)</td></tr>
        <tr><td><input style="width: 150px;" type="text" name="username"/>
            <td><input style="width: 200px;" type="text" name="email"/></td>
            <td><input style="width: 150px;" type="text" name="secret"/></td>
            <td><input type="submit" value="Send me the Confirmation"></td>
        </tr>
    </table>

    <br/>
    <h3>Tips:</h3>
    <ol>
        <li>If the username and email match in our users database, and email with furher instructions will be sent to you.</li>
        <li>2. A secret word to protect against unauthorized distribution of letters, as an attacker can get your name and e-mail.
               A secret word can not contain spaces!</li>
    </ol>

</form>