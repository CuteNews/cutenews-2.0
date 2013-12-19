<?php

    list($files, $dirs, $path, $pathes, $popup_form, $root_dir) = _GL('files, dirs, path, pathes, popup_form, root_dir');

    $ckeditor = REQ('CKEditorFuncNum');
    $inline = REQ('opt', 'GETPOST') == 'inline' ? TRUE : FALSE;
    $path_dir = '';

    // Keep this parameters
    $KeepString = 'mod, opt, folder, CKEditorFuncNum, callback, style, faddm';

    // Set GET for dir links after file operations
    cn_set_GET($KeepString);

    cn_snippet_messages();

    // BC only for stand-alone form
    if (REQ('opt') !== 'inline') cn_snippet_bc();
?>

<script type="text/javascript">

    function add_file_node()
    {
        var fnode = document.getElementById('file_node');
        fnode.innerHTML = fnode.innerHTML + '<div><input type="file" name="upload_file[]" /> <a href="#" onclick="return(remove_it_node(this));">&ndash; remove</a></div>';
        return false;
    }

    function remove_it_node(obj)
    {
        obj.parentNode.remove();
        return false;
    }

    function parentInsert(obj, cb)
    {
        var w = parseInt(getId('image_width').value);
        var h = parseInt(getId('image_height').value);
        var a = getId('image_alt').value;
        var p = getId('image_popup').checked;

        // resouse (field additional)
        <?php if (REQ('faddm')) { ?>
            var out = obj.href;
        <?php } else { ?>
            var out = '[img' + (w != 0?' width='+w : '') + (p?' popup=Y' : '') + (h?' height='+h : '')  + (a?' alt='+a : '') +']' + obj.title + '[/img]';
        <?php } ?>

        // style: add or replace
        <?php if (REQ('style', 'GETPOST') == 'add')
             echo 'insertAtCursor(opener.document.getElementById(cb), out);';
        else
             echo 'opener.document.getElementById(cb).value = out;';
        ?>

        window.close();
        return false;
    }

    function insertCKEditor($T)
    {
        window.opener.CKEDITOR.tools.callFunction("<?php echo intval($ckeditor); ?>", $T.href);
        window.close();
        return false;
    }

</script>

<!-- media breadcrumbs -->
<div class="media_path">

    <?php

    if (count($pathes))
        echo '<a href="'.cn_url_modify("folder").'">uploads</a> ';
    else
        echo 'uploads';

    $cp = array();
    foreach ($pathes as $path_folder)
    {
        $cp[] = $path_folder;
        $path_dir = join('/', $cp);
        echo '&rarr; <a href="'.cn_url_modify("folder=".$path_dir).'">'.$path_folder.'</a>';
    } ?>
</div>


<!-- UPLOAD FORMS --->
<form action="<?php echo PHP_SELF; ?>" method="POST" enctype="multipart/form-data"><?php cn_form_open($KeepString); ?>

    <!-- image configs -->
    <div class="panel-media">
        <table>

            <?php if (ini_get('allow_url_fopen')) { ?>
                <tr><td>Upload by URL [<a href="#" title="Must be EXACT link to image, else not uploaded" onclick="return(tiny_msg(this));">?</a>]</td><td><input type="text" style="width: 400px;" name="upload_from_inet" /> (optional)</td></tr>
            <?php } else { ?>Upload by url not allowed by server<?php } ?>

            <?php if (!$ckeditor && $inline) { ?>
                <tr><td>Image width</td><td><input type="text" style="width: 150px;" id="image_width" name="image_width" value="<?php echo intval(REQ('image_width')); ?>" /></td></tr>
                <tr><td>Image height</td><td><input type="text" style="width: 150px;" id="image_height"  name="image_height" value="<?php echo intval(REQ('image_height')); ?>" /></td></tr>
                <tr><td>Image alt</td><td><input type="text" style="width: 250px;" id="image_alt" name="image_alt" value="<?php echo cn_htmlspecialchars(REQ('image_alt')); ?>"/></td></tr>
                <tr><td>Popup</td><td><input type="checkbox" id="image_popup" name="image_popup" <?php echo REQ('image_popup') ? 'checked' : ''; ?> value="Y" /></td></tr>
            <?php } ?>

        </table>

    </div>

    <div style="float: right">
        <a href="#" onclick="return(add_file_node());">Add another file input box</a>
        <input style="vertical-align: middle;" type="checkbox" name="overwrite" value="Y"> Overwrite
        <input type="submit" name="upload" value="Upload file(s)"/>
    </div>

    <div id="file_node"><input type="file" name="upload_file[]" /></div>
    <br/>
</form>

<!-- MEDIA FILES -->
<form action="<?php echo PHP_SELF; ?>" method="POST"><?php cn_form_open($KeepString); ?>

    <table class="std-table wide" width="100%">
        <tr><th>ICON</th><th width="400px">Image</th> <th>Width, px</th> <th>Height, px</th> <th>Size, kb.</th> <th><input type="checkbox" name="master_box" title="Check All" onclick="check_uncheck_all('rm[]');" /></th></tr>

        <!-- make UP button -->
        <?php if ($cp) {

            $prev_folder = join('/', array_slice($cp, 0, -1));
            ?><tr><td align="center">&nbsp;</td><td colspan="5"><a href="<?php echo cn_url_modify("folder=$prev_folder"); ?>">..</a></td> </tr>

        <?php } ?>

        <!-- show dirs -->
        <?php foreach ($dirs as $dir) { $next_folder = join('/', array_merge( $cp, array($dir['name']))); ?>
            <tr>
                <td align="center"><img src="skins/images/dir.png" /></td>
                <td colspan="4"><a href="<?php echo cn_url_modify("folder=$next_folder"); ?>" style="color: #C04000"><b><?php echo $dir['name']; ?></b></a></td>
                <td align="center"><input type="checkbox" name="rm[]" value="<?php echo cn_htmlspecialchars($dir['name']); ?>" /></td>
            </tr>
        <?php } ?>

        <!-- show files -->
        <?php if (is_array($files)) foreach ($files as $file) { ?>
            <tr<?php

                    if ($file['is_thumb']) echo ' style="background: #f0f0f0; '.(getoption('show_thumbs') ? '' : 'display: none;').'" ';
                    elseif ($file['just_uploaded']) echo ' style="background: #f0fff0" ';

                ?>>
                <td align="center"><a href="<?php echo $file['url']; ?>" target="_blank">
                <?php
                    if ($file['w'] == 0) echo 'n/a';
                    elseif ($file['thumb']) { ?><img src="<?php echo $file['thumb']; ?>" width="32" /><?php }
                    elseif ($file['w'] < 368 || $file['is_thumb']) { ?><img src="<?php echo $file['url']; ?>" width="32" /><?php }
                    else echo "view";
                ?></a>
                </td>
                <td>
                    <?php if ($ckeditor) { ?>
                        <a target="_blank" href="<?php echo $file['url']; ?>" onclick="return(insertCKEditor(this));"><?php echo $file['name']; ?></a>
                    <?php } else { ?>
                        <a target="_blank" href="<?php echo $file['url']; ?>" title="<?php echo $file['local']; ?>" <?php if ($inline) { ?> onclick="return(parentInsert(this, '<?php echo REQ('callback', 'GETPOST'); ?>'));"<?php } ?>><?php echo $file['name']; ?></a>
                    <?php } ?>
                </td>
                <td align="center"><?php echo $file['w']; ?></td>
                <td align="center"><?php echo $file['h']; ?></td>
                <td align="center"><?php echo $file['fs']; ?></td>
                <td align="center"><input type="checkbox" name="rm[]" value="<?php echo cn_htmlspecialchars($file['name']); ?>" /></td>
            </tr>
        <?php } if (empty($files)) { ?><tr><td colspan="6" align="center"><b>Files not found</b></td></tr><?php } ?>

    </table>

    <!-- Action not work with popup -->
    <?php if ($popup_form) { ?>

        <input type="hidden" name="pending" value="<?php echo cn_htmlspecialchars(REQ('do_action', 'POST')); ?>" />
        <div class="media_popup_form"><?php echo $popup_form; ?> <input type="submit" value="Submit"></div>

    <?php } else { ?>

        <div class="media_rgt_button">
            Action
            <select name="do_action">
                <option value="move">Move</option>
                <option value="rename">Rename</option>
                <option value="delete">Delete</option>
                <option value="create">Create directory</option>
                <option value="thumb">Thumbnail / Resize</option>
                <?php hook('template/media/select_options'); ?>
            </select>
            <input type="submit" value="Run" />
        </div>

    <?php } ?>


</form>

<div style="clear: both;"></div>
