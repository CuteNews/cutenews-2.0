<?php

list($wlist, $word, $replace, $is_repl_opt) = _GL('wlist, word, replace, repopt');

cn_snippet_bc();

?>
<?php
    if(!$is_repl_opt){
?>
<div style="color:#FF0000; font-size: 10px;">
    For working word replacement need turn on option: Use word replace module.<br/>
    For more information contact with site administrator.
</div>
<?php
    }
?>
<form action="<?php echo PHP_SELF;?>" method="POST">

    <?php cn_form_open('mod, opt'); ?>
    <table class="std-table" width="100%">
        <tr><th>Word</th> <th>Replace</th></tr>

        <?php if (is_array($wlist) && $wlist) foreach ($wlist as $name => $var) { ?>

            <tr <?php if ($word == $name) echo 'class="row_selected"'; ?>>
                <td><a href="<?php echo cn_url_modify('word='.$name); ?>"><?php echo cn_htmlspecialchars($name); ?></a></td>
                <td><?php echo cn_htmlspecialchars($var); ?></td>
            </tr>

        <?php } else { ?><tr><td colspan="2">Entries not found</td></tr><?php } ?>

    </table>

    <br/>
    <table class="panel">
        <tr><td align="right">Word</td> <td><input type="text" style="width: 350px;" name="word" value="<?php echo cn_htmlspecialchars($word); ?>"/></td></tr>
        <tr><td align="right">Replace</td> <td><input type="text" style="width: 350px;" name="replace" value="<?php echo cn_htmlspecialchars($replace); ?>"/></td></tr>
        <tr><td align="right"><input type="checkbox" name="delete" value="Y"/></td> <td>Delete word</td></tr>
        <tr><td>&nbsp;</td> <td><input type="submit" name="submit" value="Submit"/></td></tr>
    </table>

</form>