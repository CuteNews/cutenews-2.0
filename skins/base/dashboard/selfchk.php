<?php

list($errors) = _GL('errors');
cn_snippet_bc();

?>
<section>
	<div class="container">
		<table class="table table-bordered table-striped table-hover">
			<tr><th>Message</th> <th>File</th> <th>Permission</th> </tr>
			<?php foreach ($errors as $error) { ?>
				<tr>
					<td class="cn"><?php echo $error['msg']; ?></td>
					<td><?php echo $error['file']; ?></td>
					<td class="cn"><?php echo $error['perm']; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</section>
