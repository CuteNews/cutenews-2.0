<?php

list($lang_token, $lang, $list, $tkn, $phraseid, $translate) = _GL('lang_token, lang, list, tkn, phraseid, translate');

$exid = REQ('exid');

cn_snippet_bc();
cn_snippet_messages();

?>

<!-- selection -->
<form action="<?php echo PHP_SELF; ?>" method="POST">
    <?php cn_form_open('mod, opt'); ?>

    <p>
        Select language <sup><a href="#" title="Create new language file (e.g. fr.txt) in ./core/lang with 666, 664 or 644 permission" onclick="return(tiny_msg(this));">?</a></sup>
        <select name="lang_token">
            <?php foreach ($list as $token) { ?>
                <option <?php if ($token == $lang_token) echo 'selected="selected"'; ?>><?php echo $token; ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="Select" />
    </p>
</form>

<!-- operations -->
<?php if ($lang_token) { ?>

    <form action="<?php echo PHP_SELF; ?>" method="POST">
        <?php cn_form_open('mod, opt, lang_token, exid'); ?>
        <input type="hidden" name="modifica" value="Y" />

        <table class="panel" width="100%">

            <?php if ($exid) { ?>
                <tr>
                    <td align="right"><input type="checkbox" name="create_phrase" value="Y" /></td>
                    <td>Create new phrase</td>
                </tr>
            <?php } ?>

            <tr>
                <td align="right"><?php if ($exid) echo 'Phrase / ID'; else echo 'Phrase'; ?></td>
                <td><input type="text" style="width: 450px;" name="phraseid" value="<?php echo $phraseid; ?>" /> </td>
            </tr>

            <tr>
                <td align="right">Translate</td>
                <td><input type="text" style="width: 650px;" name="translate" value="<?php echo $translate; ?>" /> </td>
            </tr>

            <?php if ($exid) { ?>
                <tr>
                    <td align="right"><input type="checkbox" name="delete_phrase" value="Y" /></td>
                    <td>Delete phrase</td>
                </tr>
            <?php } ?>

            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value="<?php if ($exid) echo 'Edit'; else echo 'Create'; ?>"/> </td>
            </tr>
        </table>
        <br/>

        <table class="std-table" width="100%">

            <tr><th>ID</th> <th>Translate</th></tr>
            <?php foreach ($tkn as $id => $tran) { ?>

                <tr <?php if ($id == $exid) echo 'class="row_selected"'; ?>>
                    <td><a href="<?php echo cn_url_modify('lang_token='.$lang_token, 'exid='.$id); ?>"><?php echo $id; ?></a></td>
                    <td><?php echo cn_htmlspecialchars($tran); ?></td>
                </tr>

            <?php } ?>

        </table>

        <br/>
        <div><input type="submit" name="submit" value="Submit" /></div>

    </form>

<?php } ?>