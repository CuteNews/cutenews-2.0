<?php

list($archives, $name) = _GL('archives, name');

cn_snippet_messages();
cn_snippet_bc();

?>
<section>
	<div class="container">

		<p><b>Be careful:</b> creating back up may cause 'allowed memory limit' error.</p>
		<form action="<?php echo PHP_SELF; ?>" method="POST">

			<?php cn_form_open('mod, opt'); ?>
			<table class="table table-bordered table-striped table-hover">

				<?php cn_snippet_show_list_head('Name|Size (kb)|Date archived'); ?>
				<?php foreach ($archives as $archive) { ?>
				<tr>
					<td>
                        <?php echo $archive['name']; ?>
                        [<a href="<?php echo cn_url_modify('unpack='.$archive['name'], cn_snippet_digital_signature('a')); ?>" onclick="return(confirm('Unpack this archive? It replace all news'));">unpack</a>]
                    </td>
					<td><?php echo round($archive['size']/1024, 2); ?></td>
					<td><?php echo $archive['date']; ?></td>
				</tr>
				<?php } ?>

			</table>

			<div>
                <h4>Backup name</h4>
                <input type="text" class="form-control" name="backup_name" value="<?php echo $name; ?>" required/>
    			<p><b>Notice:</b> backup's name must contains a-z, 0-9 and _ symbols.</p>
                <br/>
				<input class="btn btn-primary" type="submit" name="create" value="Make backup" />
                &nbsp;&nbsp;&nbsp;
				<label style="font-weight: normal"><input type="checkbox" name="backup_sysonly" value="Y"/> Only system configs</label>
            </div>

		</form>
	</div>
</section>
<br/>