<?php cn_snippet_bc(); ?>

<section>
	<div class="container">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#">Latest comments</a></li>
		</ul>
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-hover">

				<tr>
                    <th>DateTime</th>
                    <th>Comment</th>
                    <th>IP</th>
                    <th>Email</th>
                    <th>User</th>
                </tr>

			    <?php foreach ($__list as $item) { ?>
				<tr<?php echo !$item[2]['id'] ? ' class="unabled" ' : ''; ?>>

					<td><nobr><?php echo date('Y-m-d H:i:s', $item[1]); ?></nobr></td>
					<td><?php
						if (!$item[2]['id']) {
							echo ' ---comment deleted--- ';
						} else {
							echo '<a target="_blank" href="'.$item[3].'">'.cn_htmlspecialchars(clever_truncate($item[2]['c'])).'</a>';
						}
                    ?>
					</td>
					<td align="center"><?php echo cn_htmlspecialchars($item[2]['ip']); ?></td>
					<td><?php echo cn_htmlspecialchars($item[2]['e']); ?></td>
					<td><?php echo cn_htmlspecialchars($item[2]['u']); ?></td>
				</tr>
			<?php } ?>
			</table>
		</div>

		<p>Total written comments: <b><?php echo $__count; ?></b></p>
	</div>
</section>