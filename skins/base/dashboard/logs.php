<?php

list($logs, $st, $num,$isfin, $section) = _GL('logs, st, num,isfin, section');

$st  = intval($st);
$num = intval($num);

cn_snippet_bc();

?>
<section>
	<div class="container">
		<ul class="nav nav-tabs">
			<li<?php if (!$section) echo ' class="active"'; ?>><a href="<?php echo cn_url_modify('section'); ?>">System</a></li>
			<li<?php if ($section === 'user') echo ' class="active"'; ?>><a href="<?php echo cn_url_modify('section=user'); ?>">User</a></li>
		</ul>

		<table class="table table-bordered table-striped table-hover">

            <tr>
				<th width="75">Date</th>
				<th>Message</th>
			</tr>

			<?php foreach ($logs as $item) { ?>

				<tr>
					<td  ><nobr><?php echo $item['date']; ?></nobr></td>
					<td><?php echo cn_htmlspecialchars($item['msg']); ?></td>
				</tr>

			<?php } ?>

		</table>

		<p>You may manually clean log there ./cdata/log/<?php echo $section ?  'user.log' : 'error_dump.log'; ?></p>

		<div><?php

            echo ($st - $num >= 0) ? '<a href="'.cn_url_modify('st='.($st-$num)).'">&lt;&lt; Prev</a>' : '&lt;&lt; Prev';
            echo '&nbsp;['.$st.']&nbsp;';
            echo $isfin ? 'Next &gt;&gt;' : '<a href="'.cn_url_modify('st='.($st+$num)).'">Next &gt;&gt;</a>';
        ?>
		</div>
	</div>
</section>

