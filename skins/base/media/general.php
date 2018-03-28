<?php

    list($files, $dirs, $path, $pathes, $popup_form, $root_dir, $is_inline) = _GL('files, dirs, path, pathes, popup_form, root_dir, is_inline');

    $callback = REQ('callback');
    $ckeditor = REQ('CKEditorFuncNum');
    $inline   = REQ('opt', 'GETPOST') == 'inline' ? TRUE : FALSE;
    $opts     = REQ('imgopts', 'GETPOST') == 'yes' ? TRUE : FALSE;

    $path_dir = '';

    // Keep this parameters
    $KeepString = 'mod, opt, folder, CKEditorFuncNum, callback, style, faddm, imgopts';

    // Set GET for dir links after file operations
    cn_set_GET($KeepString);

    cn_snippet_messages();
    
    // BC only for stand-alone form
    if (!$inline) {
        cn_snippet_bc();
    }    
?>

<script type="text/javascript">

    function add_file_node() {
        var fnode = document.getElementById('file_node');
        var ndiv=document.createElement('div');
        ndiv.innerHTML = '<input type="file" name="upload_file[]" /> <a href="#" onclick="return(remove_it_node(this));">&ndash; remove</a>';
        fnode.appendChild(ndiv);
        return false;
    }

    function remove_it_node(obj) {
        obj.parentNode.remove();
        return false;
    }

    function parentInsert(obj, cb) {

        // resouse (field additional)
        <?php if (REQ('faddm')) { ?>
            var out = obj.href;

        <?php } else { ?>
            var w = parseInt(getId('image_width').value);
            var h = parseInt(getId('image_height').value);
            var a = getId('image_alt').value;
            var p = getId('image_popup').checked;            
            
            var out = '[img' + (w != 0?' width='+w : '') + (p?' popup=Y' : '') + (h?' height='+h : '')  + (a?' alt='+a : '') +']' + obj.title + '[/img]';
        <?php } ?>

        // style: add or replace
        <?php if (REQ('style', 'GETPOST') == 'add') {
             echo 'insertAtCursor(opener.document.getElementById(cb), out);';
        } else {
             echo 'opener.document.getElementById(cb).value = out;';
        } ?>

        window.close();
        return false;
    }

    function insertCKEditor($T) {

        window.opener.CKEDITOR.tools.callFunction("<?php echo intval($ckeditor); ?>", $T.href);
        window.close();
        return false;
    }

    // Insert multiply
    function insertCKEditorForm() {

        var elm = document.getElementsByClassName('ckeditor_id');
        var tadd = {};

        // detect images
        for (var i in elm) {

            if (elm[i].tagName === 'INPUT' && elm[i].checked) {
                var url_add = elm[i].getAttribute('data-href')
                tadd[url_add] = elm[i];
            }
        }

        // insert images (by group)
        for (var url in tadd) {
            parentInsert(tadd[url], '<?php echo REQ('callback', 'GETPOST'); ?>')
        }

        window.close();
        return false;
    }

    function hideFolderList(obj,id) {

        var box=document.getElementsByName('place_folder_'+id)[0];        
        if (obj.checked) {
            box.style.display='none';
        } else {
            box.style.display='inline-block';
        }
    }

</script>
<section>
	<div class="container">

        <!-- UPLOAD FORMS --->
        <form action="<?php echo PHP_SELF; ?>" method="POST" enctype="multipart/form-data">

            <?php cn_form_open($KeepString); ?>

            <!-- image configs -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">

                    <?php if (ini_get('allow_url_fopen')) { ?>
                        <tr>
                            <td width="15%">Upload by URL [<a href="#" title="Must be EXACT link to image, else not uploaded" onclick="return(tiny_msg(this));">?</a>]</td>
                            <td><input class="form-control" type="text"  name="upload_from_inet" /> (optional)</td>
                        </tr>
                    <?php } else { ?>
                        Upload by url not allowed by server
                    <?php } ?>

                    <?php if (!$ckeditor && $opts) { ?>

                        <tr><td>Image width</td><td><input type="text"  id="image_width" name="image_width" value="<?php echo intval(REQ('image_width')); ?>" /></td></tr>
                        <tr><td>Image height</td><td><input type="text"  id="image_height"  name="image_height" value="<?php echo intval(REQ('image_height')); ?>" /></td></tr>
                        <tr><td>Image alt</td><td><input type="text"  id="image_alt" name="image_alt" value="<?php echo cn_htmlspecialchars(REQ('image_alt')); ?>"/></td></tr>
                        <tr><td>Popup</td><td><input type="checkbox" id="image_popup" name="image_popup" <?php echo REQ('image_popup') ? 'checked' : ''; ?> value="Y" /></td></tr>

                    <?php } ?>

                </table>
            </div>

            <div class="well" style="padding: 8px">

                <div id="file_node" class="form-group"><input type="file" name="upload_file[]" /></div>
                <div><a href="#" onclick="return(add_file_node());">Add another file input box</a></div>
                <br/>

                <div class="form-group">

                    <button class="btn btn-primary" type="submit" name="upload" value="Upload file(s)"><i class="fa fa-upload"></i> Upload file(s)</button>
                    &nbsp;&nbsp;
                    <label><input style="" type="checkbox" name="overwrite" value="Y"> Overwrite</label>

                </div>
            </div>

        </form>

        <!-- MEDIA FILES -->
        <form action="<?php echo PHP_SELF; ?>" method="POST">

            <?php cn_form_open($KeepString); ?>

            <!-- media breadcrumbs -->
            <div style="padding: 0 0 8px 0;" class="pull-right">
                <?php
                    $cp = array();
                    echo '<b>Current dir:</b> <a href="' . cn_url_modify("folder") . '">uploads</a> ';
                    foreach ($pathes as $path_folder) {

                        $cp[] = $path_folder;
                        $path_dir = join(DIRECTORY_SEPARATOR, $cp);
                        echo ' <i class="fa fa-angle-right"></i> <a href="' . cn_url_modify("folder=" . $path_dir) . '">' . $path_folder . '</a>';
                    }
                ?>
            </div>

            <!-- make UP button -->
            <?php if ($cp) {$prev_folder = join(DIRECTORY_SEPARATOR, array_slice($cp, 0, -1));?>

                <div class="form-group">
                    <a href="<?php echo cn_url_modify("folder=$prev_folder"); ?>" ><i class="fa fa-arrow-left"></i>&nbsp;<b class="btn btn-primary">Back</b></a>
                </div>
            <?php } ?>

            <div style="clear: right"></div>

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">

                    <tr>
                        <th width="10px">Icon</th>
                        <th width="400px">Image</th>
                        <th>Width, px</th>
                        <th>Height, px</th>
                        <th>Size, kb.</th>
                        <th width="25" style="text-align: center"><input type="checkbox" name="master_box" title="Check All" onclick="check_uncheck_all('rm[]');" /></th>
                    </tr>

                    <!-- show dirs -->
                    <?php foreach ($dirs as $dir) {
                        $next_folder = join(DIRECTORY_SEPARATOR, array_merge( $cp, array($dir['name'])));
                        ?>
                        <tr>
                            <td align="center"><img src="skins/images/dir.png" /></td>
                            <td colspan="4"><a href="<?php echo cn_url_modify("folder=$next_folder"); ?>"><b><?php echo $dir['name'];?></b></a></td>
                            <td align="center">
                            <?php if (!$is_inline) { ?>
                                <input type="checkbox" name="rm[]" value="<?php echo cn_htmlspecialchars($dir['name']); ?>" />
                            <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <!-- show files -->
                    <?php if (is_array($files)) {
                        foreach ($files as $file) {
                    ?>
                        <tr <?php

                        if ($file['is_thumb']) {
                            echo ' style="background: #f0f0f0; '.(getoption('show_thumbs') ? '' : 'display: none;').'" ';
                        } elseif ($file['just_uploaded']) {
                            echo ' style="background: #f0fff0" ';
                        } ?>>

                            <td align="center"><a href="<?php echo $file['url']; ?>" target="_blank">
                            <?php
                                if ($file['w'] == 0) {
                                    echo 'n/a';
                                }
                                elseif ($file['thumb']) { ?><img src="<?php echo $file['thumb']; ?>" width="32" /><?php }
                                elseif ($file['w'] < 368 || $file['is_thumb']) { ?><img src="<?php echo $file['url']; ?>" width="32" /><?php }
                                else {
                                    echo "view";
                                }
                            ?></a>
                            </td>

                            <td>
                                <?php if ($ckeditor) { ?>

                                    <a target="_blank" href="<?php echo $file['url'];?>" onclick="return(insertCKEditor(this));"><?php echo $file['name']; ?></a>

                                <?php } else { ?>

                                    <a
                                        target="_blank"
                                        href="<?php echo $file['url']; ?>"
                                        title="<?php echo $file['local']; ?>"
                                        <?php if ($inline) { ?>
                                            onclick="return(parentInsert(this, '<?php echo REQ('callback', 'GETPOST'); ?>'));"
                                        <?php } ?>
                                    ><?php echo $file['name']; ?></a>
                                <?php } ?>
                            </td>

                            <td><?php echo $file['w']; ?></td>
                            <td><?php echo $file['h']; ?></td>
                            <td><?php echo $file['fs']; ?></td>
                            <td align="center">
                                <input
                                    type="checkbox"
                                    name="rm[]"
                                    class="ckeditor_id"
                                    data-href="<?php echo $file['url'];?>"
                                    title="<?php echo cn_htmlspecialchars($file['name']); ?>"
                                    value="<?php echo cn_htmlspecialchars($file['name']); ?>"
                                />
                            </td>
                        </tr>

                    <?php } } if (empty($files)) { ?><tr><td colspan="6" align="center"><b>Files not found</b></td></tr><?php } ?>

                </table>
            </div>

            <?php if ($callback == 'category_icon') { ?>

                <!-- Action not work with popup category icon -->

            <?php } else if ($popup_form) { ?>

                <input type="hidden" name="pending" value="<?php echo cn_htmlspecialchars(REQ('do_action', 'POST')); ?>" />
                <div class="media_popup_form"><?php echo $popup_form; ?> <input type="submit" value="Submit"></div>

            <?php } else if ($is_inline) { ?>

                <?php if ($files && !$ckeditor) { ?>
                <p class="pull-right">
                    <button class="btn btn-primary" onclick="return insertCKEditorForm()">Add selected images</button>
                </p>
                <?php } ?>

            <?php } else  { ?>

                <div class="pull-right">
                    <div class="form-group">

                        <select class="form-control" style="display:inline; width:200px;" name="do_action">
                            <option value="">-- choose --</option>
                            <option value="move">Move</option>
                            <option value="rename">Rename</option>
                            <option value="delete">Delete</option>
                            <option value="create">Create directory</option>
                            <option value="thumb">Generate thumbnail</option>
                            <option value="resize">Resize source</option>
                            <?php hook('template/media/select_options'); ?>
                        </select>
                        <input class="btn btn-primary" type="submit" value="Run" />
                    </div>
                </div>
            <?php } ?>
        </form>
	</div>
</section>

