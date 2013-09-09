<?php

list($list) = _GL('list');

cn_snippet_messages();
cn_snippet_bc();

?>
<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt'); ?>
    <table class="std-table" width="100%">

        <?php cn_snippet_show_list_head('IP|Times been blocked|Unblock'); ?>
        <?php foreach ($list as $ip => $item) { ?>
            <tr>
                <td><?php echo $ip; ?></td>
                <td align="center"><?php echo $item[0]; ?></td>
                <!-- <td align="center"><?php echo $item[1]; ?></td> -->
                <td align="center">[<a href="<?php echo cn_url_modify('unblock='.$ip, cn_snippet_digital_signature('a')); ?>" onclick="return(confirm('Confirm unblock'));">unblock</a>]</td>
            </tr>
        <?php } ?>
    </table>

    <table width="100%" class="std-table panel">
        <tr>
            <td align="right" height="25">IP Address:&nbsp;</td>
            <td height="25"> <input type="text" name="add_ip"> <input type="submit" value="Block IP / Name"> example: <i>129.32.31.44</i> or <i>129.32.*.*</i>, or <i>username</i> </td>
        </tr>
    </table>

</form>