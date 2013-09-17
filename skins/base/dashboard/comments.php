<table class="std-table" width="100%">
    <tr> <th>Comment</th> <th>DateTime</th> <th>IP</th> <th>Email</th> <th>User</th></tr>
<?php foreach ($__list as $item) { if ($item[1]['id']) { ?>
    <tr>
        <td><a target="_blank" href="example.php?id=<?php echo intval($item[0]); ?>&amp;edit_id=<?php echo intval($item[1]['id']); ?>"><?php echo cn_htmlspecialchars(word_truncate($item[1]['c'])); ?></a></td>
        <td align="center"><?php echo date('Y-m-d H:i:s', $item[1]['id']); ?></td>
        <td align="center"><?php echo cn_htmlspecialchars($item[1]['ip']); ?></td>
        <td><?php echo cn_htmlspecialchars($item[1]['e']); ?></td>
        <td><?php echo cn_htmlspecialchars($item[1]['u']); ?></td>
    </tr>
<?php } } ?>
</table>