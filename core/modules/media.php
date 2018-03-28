<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

add_hook('index/invoke_module', '*media_invoke');

function usort_by_name_asc($a, $b)
{
    return $a['name'] < $b['name'] ? -1 : 1;
}

function resize_image($source, $nw, $nh, $is_thumb=true)
{   
    $result = array('msg' => '', 'status' => TRUE);

    $path_parts = pathinfo($source);
    list($w, $h, $mime) = getimagesize($source);

    if ($w == 0 || $h == 0) {

        $result['msg'] = 'Illegal image ['.$path_parts['basename'].']';
        $result['status'] = FALSE;
        return $result;
    }
    
    // Check file type
    if ($mime == IMAGETYPE_JPEG) {
        $di = imagecreatefromjpeg($source);    
    } elseif ($mime == IMAGETYPE_PNG) {
        $di = imagecreatefrompng($source);
    } elseif ($mime == IMAGETYPE_GIF) {
        $di = imagecreatefromgif($source);
    } else {
        $result['status'] = FALSE;
        $result['msg']    = 'Unrecognized image type for '.$path_parts['basename'];
        return $result;
    }
    
    // Autosize
    if ($nw == 0) {
        $nw = $w * ($nh / $h);
    } elseif ($nh == 0) {
        $nh = $h * ($nw / $w);
    }

    $dt = imagecreatetruecolor($nw, $nh);
    imagefilledrectangle($dt, 0, 0, $nw, $nh, 0xFFFFFF);

    // Calculate size factors
    $sf = max(array($nw / $w, $nh / $h));

    imagecopyresampled($dt, $di, ($nw - $w * $sf) / 2, ($nh - $h * $sf) / 2, 0, 0, $w * $sf, $h * $sf, $w, $h);       
    $new_thumb_file_name = $path_parts['dirname'] . DIRECTORY_SEPARATOR . '.thumb.' . $path_parts['basename'];
    $result['msg'] = 'Thumbnail created for ['.$path_parts['basename'].']';
    
    if (!$is_thumb) {

        $new_thumb_file_name = $path_parts['dirname'] . DIRECTORY_SEPARATOR . 'resized_' . $path_parts['basename'];
        $result['msg'] = 'Image ['.$path_parts['basename'].'] successfully resized';
    }

    imagejpeg($dt, $new_thumb_file_name);
    imagedestroy($di); imagedestroy($dt);

    return $result;
}

function get_sizes_form($title,$action='thumb')
{
    $popup_form = '<div class="big_font">'.i18n($title).'</div><p>';
    $popup_form .= '<input type="hidden" name="'.$action.'_rms" value="'.cn_htmlspecialchars(serialize($_POST['rm'])).'">';
    $popup_form .= 'Width <input name="'.$action.'_size_w" value="256"> ';
    $popup_form .= 'Height <input name="'.$action.'_size_h" value="256"></p>';    
    return $popup_form;
}

function do_resize_image($root_dir, $is_thumb=true)
{
    $act = 'thumb';
    if (!$is_thumb) {
        $act='resize';
    }
    
    $rms = unserialize(REQ($act.'_rms', 'POST'));

    $wt = intval(REQ($act.'_size_w', 'POST'));
    $ht = intval(REQ($act.'_size_h', 'POST'));

    if ($wt == 0 && $ht == 0) {

        cn_throw_message('Enter correct width or height', 'e');

    } else {

        foreach ($rms as $vp) {

            $fn = $root_dir . $vp;
            if (is_dir($fn)) {
                continue;
            }

            // ignore already exists thumbnails
            if (!preg_match('/\.thumb\./', $fn)) {

                $resize_result = resize_image($fn, $wt, $ht, $is_thumb);
                if($resize_result['status']) {
                    cn_throw_message($resize_result['msg']);
                } else {
                    cn_throw_message($resize_result['msg'],'w');
                    continue;                                                       
                }
            }
        }
    }    
}

function preparation_path($path)
{
    if (substr($path, -1, 1) == DIRECTORY_SEPARATOR) {
        return substr($path, 0, -1);
    }

    return $path;
}

function media_invoke()
{
    $popup_form = '';

    list($path, $opt) = GET('folder, opt', 'GETPOST');
    list($do_action, $pending) = GET('do_action, pending', 'POST');
    
    // Change default uploads dir
    $udir = cn_path_construct(SERVDIR,'uploads');
    if (getoption('uploads_dir')) {
        $udir = preparation_path(getoption('uploads_dir'));
    }

    $edir  = getoption('uploads_ext') ? getoption('uploads_ext') : getoption('http_script_dir') . '/uploads';
    $dfile = cn_path_construct($udir, $path);

    // Remove root identifier    
    $path       = preparation_path($path);

    // Path detection
    $path       = preg_replace('/[^a-z0-9\/_\\\]/i', '-', $path);
    $root_dir   = cn_path_construct($udir, $path) .DIRECTORY_SEPARATOR;
    $just_uploaded = array();

    // Get path struct
    $pathes = spsep($path, DIRECTORY_SEPARATOR);
    if (isset($pathes[0]) && $pathes[0] === '') {
        unset($pathes[0]);
    }

    // Do upload files
    if (request_type('POST')) {

        cn_dsi_check();

        // Allowed Exts.
        $AE = spsep(getoption('allowed_extensions'));

        // Generate thumbnail after upload
        $thumbnail_with_upload = getoption('thumbnail_with_upload');

        // UPLOAD FILES
        if (REQ('upload', 'POST'))
        {
            list($overwrite) = GET('overwrite');
            
            $is_uploaded = FALSE;

            // ----------------------
            // Try for fopen url upload
            // ----------------------
            if ($upload_from_inet = REQ('upload_from_inet'))
            {
                if (ini_get('allow_url_fopen'))
                {
                    // Get filename
                    $url_name = spsep($upload_from_inet, '/');
                    $url_name = $url_name[ count($url_name) - 1 ];

                    $url_name = preg_replace('/(%20|\s|\?|&|\/)/', '_', $url_name);
                    $url_name = str_replace('%', '_', $url_name);

                    // resolve filename
                    $c_file = $dfile . $url_name;

                    // Overwrite [if can], or add file
                    if (($overwrite && file_exists($c_file)) || !file_exists($c_file))
                    {
                        // Use context for disable error notices
                        if (function_exists('stream_context_create'))
                        {
                            $context = stream_context_create(array(
                                'http' => array('ignore_errors' => true)
                            ));

                            $fw = fopen($upload_from_inet, 'rb', false, $context);
                        }
                        else
                        {
                            // Read file
                            $fw = fopen($upload_from_inet, 'rb');
                        }

                        // --------- (fetch content) ------
                        ob_start();
                        fpassthru($fw); 
                        $file_image = ob_get_clean();
                        fclose($fw);
                        // ---------
                        
                        // write2disk
                        if ($wf = fopen($c_file, 'w'))
                        {
                            fwrite($wf, $file_image);
                            fclose($wf);
                        }

                        // check image
                        $isize = getimagesize($c_file);
                        $w = isset($isize[0]) ? $isize[0] : 0;
                        $h = isset($isize[1]) ? $isize[1] : 0;

                        if ($w && $h && preg_match('/(jpg|jpeg|gif|png|bmp|icon|tiff)/i', $isize['mime']))
                        {
                            cn_throw_message('File uploaded');

                            $max_width = getoption('max_thumbnail_width');
                            if ($w > $max_width && $thumbnail_with_upload)
                            {
                                $resize_result = resize_image($c_file, $max_width, 0);
                                cn_throw_message($resize_result['msg'], $resize_result['status']?'n':'w');
                            }

                            $is_uploaded = TRUE;

                            $just_uploaded[$url_name] = TRUE;
                        }
                        else
                        {
                            cn_throw_message("Wrong image file", 'e');
                            unlink($c_file);
                        }
                    }
                    else
                    {
                        cn_throw_message("Can't overwrite or save", 'e');
                    }
                }
                else 
                {
                    cn_throw_message('allow_url_fopen=0, check server configurations');
                }
            }

            // ----------------------
            // Upload from local
            // ----------------------

            foreach ($_FILES['upload_file']['name'] as $id => $name) 
            {                
                if ($name)
                {
                    $ext = NULL;
                    if (preg_match('/\.(\w+)$/i', $name, $c)) {
                        $ext = strtolower($c[1]);
                    }
                    
                    // Check allowed ext
                    if ($ext && in_array($ext, $AE)) {

                        // encode url
                        $name = str_replace('%2F', '/', urlencode($name));

                        // encoded? replace filename
                        if (strpos($name, '%') !== FALSE) {
                            $name = str_replace('%', '', strtolower($name));
                        }

                        // check file for exist
                        if (file_exists($c_file = $dfile . $name))
                        {
                            if ($overwrite) {
                                cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] overwritten', 'w');
                            } else {
                                cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] already exists', 'e');
                                continue;
                            }
                        }

                        // Check for valid image
                        $tmpfile = $_FILES['upload_file']['tmp_name'][$id];
                        $isize   = getimagesize($tmpfile);

                        // Error in image size - is not image?
                        if (empty($isize[0]) && empty($isize[1])) {
                            cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] is not image!', 'e');
                        }
                        else {

                            // Upload file to server
                            if (move_uploaded_file($_FILES['upload_file']['tmp_name'][$id], $c_file))
                            {
                                $just_uploaded[$name] = TRUE;

                                cn_throw_message('File uploaded [<b>'.cn_htmlspecialchars($name).'</b>]');

                                $max_width = getoption('max_thumbnail_width');
                                list($w, $h) = getimagesize($c_file);

                                if ($w > $max_width && $thumbnail_with_upload)
                                {
                                    $resize_result = resize_image($c_file, $max_width, 0);
                                    cn_throw_message($resize_result['msg'], $resize_result['status']?'n':'w');
                                }

                            } else {

                                cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] not uploaded! Please, check upload_max_filesize in PHP settings.', 'e');
                            }
                        }
                    }
                    else
                    {
                        cn_throw_message('File extension ['.cn_htmlspecialchars($ext).'] not allowed', 'e');
                    }
                }
                elseif (!$is_uploaded)
                {
                    cn_throw_message('No selected files for upload','e');
                }
            }
        }
        // MAKE ACTION WITH MEDIA FILES
        elseif ($do_action || $pending)
        {
            list($rm) = GET('rm', 'POST');

            // action --> delete entries
            if ($do_action == 'delete')
            {
                if (empty($rm))
                {
                    cn_throw_message('No files selected', 'w');
                }
                else
                {                                    
                    foreach ($rm as $file)
                    {
                        if (file_exists($cfile = $dfile . $file))
                        {
                            if (is_dir($cfile))
                            {
                                rmdir($cfile);
                            }
                            else
                            {
                                //get thumbnail path
                                $path_parts=  pathinfo($cfile);
                                $thumbnail_path=$path_parts['dirname'].DIRECTORY_SEPARATOR.'.thumb.'.$path_parts['basename'];
                                if(file_exists($thumbnail_path))
                                {
                                    unlink($thumbnail_path);
                                }
                                unlink($cfile);
                            }
                        }

                        if (file_exists($cfile))
                        {
                            cn_throw_message('File ['.cn_htmlspecialchars($cfile).'] not deleted!', 'e');
                        }
                        else
                        {
                            cn_throw_message('File ['.cn_htmlspecialchars($file).'] deleted successfully');
                        }
                    }
                }
            }
            // --------------------
            // view --> select dir
            elseif ($do_action == 'create')
            {
                $popup_form = i18n('Enter directory name').' <input type="text" name="new_dir" value="" />';
            }
            // action --> process create
            elseif ($pending == 'create')
            {
                $new_dir_arr=GET('new_dir', 'POST');
                $new_folder =array_pop($new_dir_arr);

                $new_folder = preg_replace('/[^a-z0-9_]/i', '-', $new_folder);

                if ($new_folder)
                {
                    $cfile = $dfile . $new_folder;

                    if (is_dir($cfile))
                    {
                        cn_throw_message('Folder ['.$new_folder.'] already exists!', 'e');
                    }
                    else
                    {
                        mkdir($cfile);
                        if (!is_dir($cfile))
                        {
                            cn_throw_message('Folder ['.cn_htmlspecialchars($cfile).' not created]', 'e');
                        }
                        else
                        {
                            cn_throw_message('Folder ['.$new_folder.'] created!');
                        }
                    }
                }
                else
                {
                    cn_throw_message('Specify folder name', 'w');
                }

                $popup_form = '';
            }
            // ------------------
            elseif ($do_action == 'rename')
            {
                if ($rm)
                {
                    $popup_form = '<div class="big_font">'.i18n('Rename file to').'</div>';
                    $popup_form .= i18n('Tip: Write new file name').'<br />';
                    $popup_form .= '<table>';

                    foreach ($rm as $id => $fn)
                    {
                        $hfn = cn_htmlspecialchars($fn);
                        $popup_form .= '<tr><td align="right" class="indent"><b>'.$hfn.'</b><td>';
                        $popup_form .= '<td><input type="hidden" name="ids['.$id.']" value="'.$hfn.'"/>&rarr;</td>';
                        $popup_form .= '<td><input style="width: 300px;" type="text" name="place['.$id.']" value="'.$hfn.'" /> ';
                        $popup_form .= '</td></tr>';
                    }

                    $popup_form .= '</table>';
                }
                else 
                {
                    cn_throw_message('Select files to rename', 'w');
                }
            }
            // action: process move
            elseif ($pending == 'rename')
            {
                // ...
                list($ids, $place) = GET('ids, place', 'POST');

                // prevent illegal moves
                $safe_dir = scan_dir($root_dir);
                foreach ($safe_dir as $id => $v) 
                {
                    $safe_dir[$id] = md5($v);
                }

                // do move all files / dirs
                foreach ($ids as $id => $file)
                {
                    if (in_array(md5($file), $safe_dir))
                    {
                        $filename =$place[$id]; 
                        if (strpos($filename, '\\') || strpos($filename, '/'))
                        {
                            cn_throw_message(i18n('The name of file [%1] should not contain special characters',cn_htmlspecialchars($file)), 'e');
                            continue;
                        }
                        $renameto = $root_dir . $filename;
                        $thumb=$root_dir.'.thumb.'.$file;
                        // do move
                        if (rename($root_dir .  $file, $renameto))
                        {
                            if(file_exists($thumb))
                            {
                                rename($thumb, $root_dir.'.thumb.'.$filename);
                            }
                            cn_throw_message(i18n('File [%1] renamed to [%2]', cn_htmlspecialchars($file), cn_htmlspecialchars($filename)) );
                        }
                        else
                        {
                            cn_throw_message(i18n('File [%1] not renamed', cn_htmlspecialchars($file)), 'e');
                        }
                    }
                }
            }
             // ------------------
            elseif ($do_action == 'move')
            {
                if ($rm)
                {
                    $popup_form = '<div class="big_font">'.i18n('Move files to').'</div>';
                    $popup_form .= i18n('Tip: You can select the folder to move the file').'<br />';
                    $popup_form .= '<table>';
                    $folders=array();
                    $dirs=  scan_dir($root_dir);
                    foreach($dirs as $entry)
                    {
                        if(is_dir($root_dir. $entry)&&!($entry==='..'||$entry==='.'))
                        {
                            $folders[]=$entry;
                        }
                    }                                        
                    foreach ($rm as $id => $fn)
                    {
                        $hfn = cn_htmlspecialchars($fn);
                        $popup_form .= '<tr><td align="right" class="indent"><b>'.$hfn.'</b><td>';
                        $popup_form .= '<td><input type="hidden" name="ids['.$id.']" value="'.$hfn.'"/>&rarr;</td>';
                        $popup_form .= '<td>';
                        $cnt_folders=count($folders);
                        if($cnt_folders!=0&&!($cnt_folders==1 && in_array($hfn, $folders)))
                        {
                            $popup_form .= '<select name="place_folder_'.$id.'">';
                            foreach ($folders as $dirn)
                            {
                                if($dirn!=$hfn)
                                {
                                    $popup_form .= '<option value="'.$dirn.'">'.$dirn.'</option>';
                                }
                            }
                            $popup_form .= '</select>';                        
                        }                        
                        if($root_dir!=$udir)
                        {
                            $popup_form .= '<nobr><input type="checkbox" onclick="javascript:hideFolderList(this,'.$id.')" name="moveup['.$id.']" value="Y" /> Move up</nobr>';
                        }
                        else 
                        {
                            $popup_form .= '<nobr> X Move up (You are in root folder)</nobr>';
                        }
                        $popup_form .='</td></tr>';
                    }

                    $popup_form .= '</table>';
                }
                else 
                {
                    cn_throw_message('Select files to move', 'w');
                }
            }
            // action: process move
            elseif ($pending == 'move')
            {
                // ...
                list($ids, $moveup) = GET('ids, moveup', 'POST');
                
                // prevent illegal moves
                $safe_dir = scan_dir($root_dir); 
                foreach ($safe_dir as $id => $v)
                {
                    $safe_dir[$id] = md5($v);
                }

                // do move all files / dirs
                foreach ($ids as $id => $file)
                {
                    list($place_folder)=GET('place_folder_'.$id);
                    
                    if (in_array(md5($file), $safe_dir))
                    {
                        $NF = '';
                        $foldername= preg_replace('/\.\//i', '', $place_folder);

                        // move this file up
                        if (isset($moveup[$id]) && count($pathes) > 0)
                        {
                            $nwfolder = dirname($root_dir);
                            $foldername='up folder';
                        }
                        else
                        {
                            $nwfolder = $root_dir. ( $NF = isset($rm[0]) ? $rm[0] : '' ) . DIRECTORY_SEPARATOR . $foldername;
                            if ($rm[0]) 
                            {
                                $NF = $rm[0].DIRECTORY_SEPARATOR ;
                            }
                        }   
                        $moveto = $nwfolder . DIRECTORY_SEPARATOR . $file;
                        //check for image thumbnail
                        $thumb=$root_dir.'.thumb.'.$file;                       
                        // do move
                        if (rename($root_dir .  $file, $moveto))
                        {
                            if(file_exists($thumb))
                            {
                                rename($thumb, $nwfolder.DIRECTORY_SEPARATOR.'.thumb.'.$file);
                            }
                            cn_throw_message(i18n('File [%1] moved to [%2]', cn_htmlspecialchars($file), cn_htmlspecialchars($foldername)) );
                        }
                        else
                        {
                            cn_throw_message(i18n('File [%1] not moved', cn_htmlspecialchars($file)), 'e');
                        }
                    }
                }
            }                      
            // ------------------
            elseif ($do_action == 'thumb')
            {
                if(!empty($_POST['rm']))
                {
                    $popup_form = get_sizes_form('Make thumbnails',$do_action);
                }
                else
                {
                    cn_throw_message('Select files to make thumbnail', 'w');
                }
            }
            elseif ($pending == 'thumb')
            {
                do_resize_image($root_dir);
            }
            // action: resize
            elseif ($do_action == 'resize')
            {
                if(!empty($_POST['rm']))
                {                
                    $popup_form = get_sizes_form('Resize source image',$do_action);
                }
                else
                {
                    cn_throw_message('Select files to resize', 'w');
                }                
            }
            elseif ($pending == 'resize')
            {
                do_resize_image($root_dir,false);
            }            
            
            // ------------------
            // check for plugin
            elseif (!hook('media/post_action'))
            {
                msg_info("Action error");
            }
        }
    }

    // Check dir exists    
    if (is_dir($root_dir))
    {        
        $raw_files = scan_dir($root_dir);
    }
    else
    {
        cn_throw_message('Dir not exists', 'e');
        $raw_files = array();
    }

    $dirs = $files = array();

    foreach ($raw_files as $file)
    {
        if(preg_match('/avatar_/', $file))
        {
            continue;
        }
        
        $file_location = "$root_dir/$file";

        if (is_dir($file_location))
        {
            $dirs[] = array
            (
                'url'  => "$path/$file",
                'name' => $file
            );
        }
        elseif(filesize(cn_path_construct($udir,$path).$file)!=0)
        {
            list($w, $h) = getimagesize(cn_path_construct($udir,$path).$file);

            $is_thumb = preg_match('/\.thumb\./', $file);
            
            $files[] = array
            (
                'name'          => $file,
                'url'           => $edir . '/'. ($path? $path . '/' : '') . $file,
                'thumb'         => file_exists($root_dir.'/.thumb.'.pathinfo($file,PATHINFO_BASENAME)) ? $edir . '/'. ($path? $path . '/' : '') . '.thumb.' . pathinfo($file,PATHINFO_BASENAME) : '',
                'local'         => ($path? $path . '/' : '') . $file,
                'just_uploaded' => isset($just_uploaded[$file]) ? TRUE : FALSE,
                'is_thumb'      => $is_thumb,
                'w'             => $w,
                'h'             => $h,
                'fs'            => round(filesize($file_location)/1024, 1),
            );            
        }
    }

    uasort($dirs,  'usort_by_name_asc');
    uasort($files, 'usort_by_name_asc');

    // Top level (dashboard)
    cn_bc_add('Dashboard', cn_url_modify(array('reset')));
    cn_bc_add('Media manager', cn_url_modify());    
    cn_assign("files, dirs, path, pathes, popup_form, root_dir, is_inline", $files, $dirs, $path, $pathes, $popup_form, $root_dir, ($opt == 'inline') ? 1 : 0);

    if ($opt === 'inline') {
        echo exec_tpl('window', 'title=Quick insert image', 'style=media/style.css', 'content='.exec_tpl('media/general'));

    } else {
        echoheader('-@media/style.css', 'Media manager');
        echo exec_tpl('media/general');
        echofooter();
    }
}