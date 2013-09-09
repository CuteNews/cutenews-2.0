<?php
global $morefields;
?>

<div class="section">
    <?php foreach ($morefields as $section => $items) { ?>

        <?php if ($section !== '#basic') { ?>
            <div class="expand_toggle"><a href="#" onclick="DoDiv('efl_<?php echo md5($section); ?>');">toggle <b><?php echo $section; ?></b></a></div>
            <div id="efl_<?php echo md5($section); ?>" class="expand_field" style="display: none;">
        <?php } ?>

        <?php foreach ($items as $name => $item) { ?>

            <?php if (!in_array($item['type'], array('checkbox', 'price', 'select'))) { ?>
                <div class="name"><?php echo $name ?> <?php if ($item['req']) echo '<span class="req">*</span>'; ?></div>
            <?php } ?>

            <?php if ($item['type'] == 'text') { ?>

                <div><input type="text" style="width: 100%" value="<?php echo cn_htmlspecialchars($item['#value']); ?>" name="faddm[<?php echo $name; ?>]" ></div>

            <?php } elseif ($item['type'] == 'image') { ?>

                <div>
                    <input id="faddm_<?php echo $name; ?>" type="text" style="width: 500px" value="<?php echo cn_htmlspecialchars($item['#value']); ?>" name="faddm[<?php echo $name; ?>]" />
                    <a class="external" href="#" onclick="<?php echo cn_snippet_open_win(cn_url_modify('mod=media','opt=inline', 'faddm=Y', 'callback=faddm_'.$name), array('w' => 1000)); ?>">Select resource</a>
                </div>

            <?php } elseif ($item['type'] == 'select') { ?>

                <div style="margin: 4px 0 4px 0;">
                    <select name="faddm[<?php echo $name; ?>]">
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
                    <span class="name"><?php echo $name ?> <?php if ($item['req']) echo '<span class="req">*</span>'; ?></span>
                </div>

            <?php } elseif ($item['type'] == 'checkbox') { ?>

                <div>
                    <input type="checkbox" class="chkbox-fix" value="Y" <?php if ($item['#value']) echo 'checked'; ?> name="faddm[<?php echo $name; ?>]" />
                    <span class="name"><?php echo $name ?> <?php if ($item['req']) echo '<span class="req">*</span>'; ?></span>
                </div>

            <?php } elseif ($item['type'] == 'price') { ?>

                <div>
                    <input type="text" style="width: 75px" value="<?php printf('%.2f', $item['#value']); ?>" name="faddm[<?php echo $name; ?>]" />
                    <span class="name"><?php echo $name ?> <?php if ($item['req']) echo '<span class="req">*</span>'; ?></span>
                </div>

            <?php } ?>

            <?php if ($item['desc']) echo '<div style="font-size: 10px; color: #808080; margin: 0 0 15px 0;">'.cn_htmlspecialchars($item['desc']).'</div>'; ?>

        <?php } ?>
        <?php if ($section !== '#basic') { ?></div><?php } ?>

    <?php } ?>
</div>