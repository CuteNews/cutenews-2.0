<?php

list($logs, $st, $num,$isfin, $section) = _GL('logs, st, num,isfin, section');

$st  = intval($st);
$num = intval($num);

cn_snippet_bc();

?>

<ul class="sysconf_top">
    <li<?php if (!$section) echo ' class="selected"'; ?>><a href="<?php echo cn_url_modify('section'); ?>">System</a></li>
    <li<?php if ($section === 'user') echo ' class="selected"'; ?>><a href="<?php echo cn_url_modify('section=user'); ?>">User</a></li>
</ul>

<table class="std-table" width="100%">
    <tr>
        <th width="75">Date</th>
        <th>Message</th>
    </tr>

    <?php foreach ($logs as $item) { ?>

        <tr>
            <td width="75" style="color: #707070;"><nobr><?php echo $item['date']; ?></nobr></td>
            <td><?php echo cn_htmlspecialchars($item['msg']); ?></td>
        </tr>
    <?php } ?>

</table>

<p style='color: #808080;'>You may manually clean log there ./cdata/log/<?php if (!$section) echo "error_dump.log"; else echo 'user.log'; ?></p>

<div>
    <?php 
    if ($st-$num >= 0) 
        echo '<a href="'.cn_url_modify('st='.($st-$num)).'">&lt;&lt; Prev</a>'; 
    else 
        echo '&lt;&lt; Prev' 
    ?>
    &nbsp;[<?php echo $st; ?>]&nbsp;
    <?php
        if(!$isfin)
            echo '<a href="'.cn_url_modify('st='.($st+$num)).'">Next &gt;&gt;</a>';
        else 
            echo 'Next &gt;&gt;';
    ?>
    <!--a href="<?php /*echo cn_url_modify('st='.($st+$num));*/ ?>">Next &gt;&gt;</a-->
</div>

