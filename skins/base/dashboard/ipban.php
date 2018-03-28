<?php

list($list) = _GL('list');

cn_snippet_messages();
cn_snippet_bc();

?>

<section>
	<div class="container">
		<form action="<?php echo PHP_SELF; ?>" method="POST">

			<?php cn_form_open('mod, opt'); ?>
			<table class="table table-bordered table-striped table-hover">

				<?php cn_snippet_show_list_head('IP|'.i18n('Times been blocked').'|'.i18n('Unblock')); ?>
				<?php foreach ($list as $ip => $item) { ?>
					<tr>
						<td><?php echo $ip; ?></td>
						<td width="200"><?php echo $item[0]; ?></td>
						<td width="150">[<a href="<?php echo cn_url_modify('unblock='.$ip, cn_snippet_digital_signature('a')); ?>" onclick="return(confirm('Confirm unblock'));">unblock</a>]</td>
					</tr>
				<?php } ?>
			</table>

			<table class="table table-bordered table-striped table-hover">
				<tr>
					<td align="right" height="25" style="vertical-align: middle;"><?php echo i18n('IP/Name Address');?>:&nbsp;</td>
					<td height="25">
                        <input type="text" name="add_ip">
                        <input class="btn btn-primary" type="submit" value="<?php echo i18n('Block IP / Name');?>">
                        &nbsp;
                        example: <i>129.32.31.44</i> or <i>129.32.*.*</i>, or <i>username</i>
                    </td>
				</tr>
			</table>

		</form>
	</div>
<section>