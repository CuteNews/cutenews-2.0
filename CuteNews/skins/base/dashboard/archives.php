<?php

list($arch_list) = _GL('arch_list');
list($f_date_d, $f_date_m, $f_date_y, $t_date_d, $t_date_m, $t_date_y, $archive_id) = _GL('f_date_d, f_date_m, f_date_y, t_date_d, t_date_m, t_date_y, archive_id');

cn_snippet_messages();
cn_snippet_bc();

?>
<table class="std-table" width='100%'>
    <tr>
        <th>Date</th>
        <th>Count news</th>
        <th>First date</th>
        <th>Last date</th>
    </tr>

    <?php foreach ($arch_list as $id => $item) {?>
        <tr align="center" <?php if ($id ==  $archive_id) echo 'class="row_selected"'; ?>>
            <td><a href="<?php echo cn_url_modify('archive_id='.$id); ?>"><?php echo date('Y-m-d H:i:s', $id); ?></a></td>
            <td><?php echo $item['c']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $item['min']); ?></td>
            <td><?php echo date('Y-m-d H:i:s', $item['max']); ?></td>
        </tr>

    <?php } ?>
</table>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt, archive_id'); ?>

    <hr/>
    <table class="panel">

        <?php if (!$archive_id) { ?>

            <tr>
                <td>From</td>
                <td>
                    <select name="from_date_day"><?php echo $f_date_d; ?></select>
                    <select name="from_date_month"><?php echo $f_date_m; ?></select>
                    <select name="from_date_year"><?php echo $f_date_y; ?></select>
                </td>
            </tr>
            <tr>
                <td>To</td>
                <td>
                    <select name="to_date_day"><?php echo $t_date_d; ?></select>
                    <select name="to_date_month"><?php echo $t_date_m; ?></select>
                    <select name="to_date_year"><?php echo $t_date_y; ?></select>
                </td>
            </tr>
            <tr>
                <td align="right"><input type="checkbox" name="last_only" value="Y"/> </td>
                <td>Or archive last <input type="text" name="period" size="4" value="30" /> days</td>
            </tr>
            <tr><td>&nbsp;</td><td><input type="submit" value="Add to archive" /></td></tr>

        <?php } else { ?>

            <tr>
                <td align="right"><input type="radio" name="arch_action" value="extr"></td>
                <td>Extract archive</td>
            </tr>

            <tr>
                <td align="right"><input type="radio" name="arch_action" value="rm"></td>
                <td>Delete archive</td>
            </tr>
            <tr><td colspan="2" align="right"><input type="submit" value="Submit archive action" /></td></tr>

        <?php } ?>
    </table>

</form>