<?php

list($archives, $name) = _GL('archives, name');

cn_snippet_messages();
cn_snippet_bc();

?>

<p><b>Be careful:</b> creating back up may cause `allowed memory limit` error</p>
<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt'); ?>

    <table class="std-table" width="100%">

        <?php cn_snippet_show_list_head('Name|Size (kb)|Date archived'); ?>

        <?php foreach ($archives as $archive) { ?>
        <tr>
            <td><?php echo $archive['name']; ?> [<a href="<?php echo cn_url_modify('unpack='.$archive['name'], cn_snippet_digital_signature('a')); ?>" onclick="return(confirm('Unpack this archive? It replace all news'));">unpack</a>]</td>
            <td align="center"><?php echo round($archive['size']/1024, 2); ?></td>
            <td align="center"><?php echo $archive['date']; ?></td>
        </tr>
        <?php } ?>

    </table>

    <hr/>
    <div>Backup name <input type="text"  style="width: 300px;" name="backup_name" value="<?php echo $name; ?>"/>
        <input type="checkbox" name="backup_sysonly" value="Y"/> Only system configs
        <input type="submit" name="create" value="Make backup" /></div>

</form>