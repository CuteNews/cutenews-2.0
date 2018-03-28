<?php  global $morefields; ?>
<?php if ($morefields) { ?>
<div>
	<label>More Fields:</label>
	<div class="well">
		<?php foreach ($morefields as $section => $items) { ?>

			<?php if ($section !== '#basic') { ?>

                <a class="btn btn-default" data-toggle="collapse" data-target="#efl_<?php echo md5($section); ?>"><?php echo $section; ?></a>
                <div id="efl_<?php echo md5($section); ?>" class="well collapse" style="">

			<?php } ?>

            <?php foreach ($items as $name => $item) { ?>

                <div class="form-group">

                    <?php if ($item['type'] == 'text') { ?>

                        <label><?php echo $name ?> <?php if ($item['req']) echo '<span class="required">*</span>'; ?></label>
                        <input type="text" class="form-control" style="display:inline; width:200px" value="<?php echo cn_htmlspecialchars($item['#value']); ?>" name="faddm[<?php echo $name; ?>]" >

                    <?php } elseif ($item['type'] == 'image') { ?>

                        <label><?php echo $name ?> <?php if ($item['req']) echo '<span class="required">*</span>'; ?></label>
                        <input id="faddm_<?php echo $name; ?>" type="text" class="form-control" style="display:inline; width:45%" value="<?php echo isset($item['#value'])? cn_htmlspecialchars($item['#value']):''; ?>" name="faddm[<?php echo $name; ?>]" />
                        <a class="external btn btn-primary" href="#" onclick="<?php echo cn_snippet_open_win(cn_url_modify('mod=media','opt=inline', 'faddm=Y', 'callback=faddm_'.$name), array('w' => 1000)); ?>">Select</a>

                    <?php } elseif ($item['type'] == 'select') { ?>

                        <label><?php echo $name ?> <?php if ($item['req']) echo '<span class="required">*</span>'; ?></label>
                        <select name="faddm[<?php echo $name; ?>]" class="selectpicker" data-hide-disabled="true" data-live-search="false" data-size="5" data-style="btn-primary" data-width="fit">
                        <?php
                            $opts = spsep($item['meta'], ';');
                            foreach ($opts as $opt)
                            {
                                $lr = explode('=', $opt, 2);
                                if (count($lr) == 2) $r = $lr[1]; else $r = $lr[0];

                                // show option
                                echo '<option value="'.cn_htmlspecialchars($lr[0]).'" '.(($lr[0] === $item['#value']) ? 'selected' : '').'>'.cn_htmlspecialchars($r).'</option>';
                            }
                        ?>
                        </select>

                    <?php } elseif ($item['type'] == 'checkbox') { ?>

                        <input type="checkbox" class="chkbox-fix" value="Y" <?php if ($item['#value']) echo 'checked'; ?> name="faddm[<?php echo $name; ?>]" />
                        <label><?php echo $name ?> <?php if ($item['req']) echo '<span class="required">*</span>'; ?></label>

                    <?php } elseif ($item['type'] == 'price') { ?>

                        <label><?php echo $name ?> <?php if ($item['req']) echo '<span class="required">*</span>'; ?></label>
                        <input type="text" class="form-control" style="display:inline; width:200px" value="<?php printf('%.2f', $item['#value']); ?>" name="faddm[<?php echo $name; ?>]" />

                    <?php } ?>

                    <?php if ($item['desc']) {
                        echo '<small>'.cn_htmlspecialchars($item['desc']).'</small>';
                    }
                    ?>

                </div>

            <?php } ?>
			<?php if ($section !== '#basic') { ?></div><?php } ?>

		<?php } ?>
	</div>
</div>
<?php } ?>