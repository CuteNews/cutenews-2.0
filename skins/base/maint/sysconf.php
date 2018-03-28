
<section>
	<div class="container">

		<?php

list($config, $path) = _GL('config, path');

			echo '<div class="breadcrumb"> <p><b>PATH:</b> root/'.cn_htmlspecialchars($path ? $path : '').'</p></div>';

			?>



		<table class="table table-bordered table-striped table-hover">

			<tr><th>Section</th> <th>Variable</th></tr>
			<?php foreach ($config as $u => $v) {
				?>

				<tr>
					<td><?php echo cn_htmlspecialchars($u); ?></td>
					<td>

					<?php
						if (is_array($v))
						{
							echo '<a href="'.cn_url_modify('path='.$path.'/'.$u).'">click to expand &rarr;</a>';
							$edit = FALSE;
						}
						elseif (is_numeric($v))
						{
							echo $v;
							$edit = TRUE;
						}
						elseif (strlen($v) > 128)
						{
							echo cn_htmlspecialchars(clever_truncate($v, 128));
							$edit = TRUE;
						}
						else
						{
							echo cn_htmlspecialchars($v);
							$edit = TRUE;
						}

						if ($edit) echo ' [<a href="#" onclick="'.cn_snippet_open_win(cn_url_modify('edit='.$u), array('w' => 800, 'h' => 550, 'l' => 'auto')).'" class="external">edit</a>]';
					?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</section>
