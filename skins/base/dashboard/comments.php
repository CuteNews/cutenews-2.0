<?php cn_snippet_bc(); ?>

<ul class="sysconf_top">
    <li class="selected"><a href="#">Latest comments</a></li>
</ul>

<table class="std-table" width="100%">
    <tr> <th width="1">DateTime</th> <th>Comment</th>  <th>IP</th> <th>Email</th> <th>User</th></tr>
<?php foreach ($__list as $item) { ?>
    <tr <?php if (!$item[2]['id']) echo ' class="unabled" '; ?>>
        <td width="1" align="center"><nobr><?php echo date('Y-m-d H:i:s', $item[1]); ?></nobr></td>
        <td><?php
            if (!$item[2]['id'])
                echo ' ---comment deleted--- ';
            else
                echo '<a target="_blank" href="'.$item[3].'">'.cn_htmlspecialchars(word_truncate($item[2]['c'])).'</a>';
            ?>
        </td>
        <td align="center"><?php echo cn_htmlspecialchars($item[2]['ip']); ?></td>
        <td><?php echo cn_htmlspecialchars($item[2]['e']); ?></td>
        <td><?php echo cn_htmlspecialchars($item[2]['u']); ?></td>
    </tr>
<?php } ?>
</table>

<p>Total written comments: <b><?php echo $__count; ?></b></p>