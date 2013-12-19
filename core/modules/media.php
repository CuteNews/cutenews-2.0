<?php if (!defined('EXEC_TIME')) die('Access restricted');

add_hook('index/invoke_module', '*media_invoke');

function resize_image($source,$nw,$nh)
{   
    $result=array('msg'=>'','status'=>TRUE);
    $path_parts=  pathinfo($source);  
    $ii = getimagesize($source);   
    list($w, $h) = $ii;
    
    if($w==0 || $h==0)
    {
        $result['msg']='Illegal image ['.$path_parts['basename'].']';
        $result['status']=FALSE;
        return $result;
    }
    
    //check file type
    $callfi=''; $di=NULL;
    if (preg_match('/jpeg/i', $ii['mime']))
    {
        $callfi = 'imagejpeg';
        $di = imagecreatefromjpeg($source);
    }
    elseif (preg_match('/png/i', $ii['mime']))
    {
        $callfi = 'imagepng';
        $di = imagecreatefrompng($source);
    }
    elseif (preg_match('/gif/i', $ii['mime']))
    {
        $callfi = 'imagegif';
        $di = imagecreatefromgif($source);
    }
    else
    {
        $result['msg']='Unrecognized image type for '.$path_parts['basename'];
        $result['status']=FALSE;
        return $result;
    }
    
    // Autosize
    $resize = FALSE;
    if ($nw == 0)
    {
        $resize = TRUE;
        $nw = $w * ($nh / $h);
    }
    elseif ($nh == 0)
    {
        $resize = TRUE;
        $nh = $h * ($nw / $w);
    }

    $dt = imagecreatetruecolor($nw, $nh);
    imagefilledrectangle($dt, 0, 0, $nw, $nh, 0xFFFFFF);

    // Calculate size factors
    $sf_x = $nw / $w;
    $sf_y = $nh / $h;

    $sf = max(array($sf_x, $sf_y));
    imagecopyresampled($dt, $di, ($nw - $w * $sf) / 2, ($nh - $h * $sf) / 2, 0, 0, $w * $sf, $h * $sf, $w, $h);

    if ($resize)
    {
        $callfi($dt, $source);
        $result['msg']='Image resized for ['.$path_parts['basename'].']';
    }
    else
    {          
        imagejpeg($dt, $path_parts['dirname'] . '/.thumb.'.$path_parts['filename'].'.jpeg');
        $result['msg']= 'Thumbnail created for ['.$path_parts['basename'].']';
    }

    imagedestroy($di);
    imagedestroy($dt);   
    
    return $result;
}

function media_invoke()
{
    $popup_form = '';
    list($path, $opt) = GET('folder, opt', 'GETPOST');
    list($do_action, $pending) = GET('do_action, pending', 'POST');

    // Path on server
    $folder = $path;
    if ($folder !== '') $folder = "$folder/";

    // Change default uploads dir
    $udir = getoption('uploads_dir') ? getoption('uploads_dir') : SERVDIR."/uploads";
    $edir = getoption('uploads_ext') ? getoption('uploads_ext') : getoption('http_script_dir') . '/uploads';

    $dfile = "$udir/$folder";

    // Remove root identifier
    if (substr($path, -1, 1) == '/') $path = substr($path, 0, -1);

    // Path detection
    $path       = str_replace('\\', '/', $path); // win-style
    $path       = preg_replace('/[^a-z0-9\/_]/i', '-', $path);
    $root_dir   = dirname("$udir/" . $path . '/index.html');
    $just_uploaded = array();

    // Get path struct
    $pathes = spsep($path, '/');
    if ($pathes[0] == '') unset($pathes[0]);

    // Do upload files
    if (request_type('POST'))
    {
        cn_dsi_check();

        // Allowed Exts.
        $AE = spsep(getoption('allowed_extensions'));
        //generate thumbnail after upload
        $thumbnail_with_upload=  getoption('thumbnail_with_upload');
        // UPLOAD FILES
        if (REQ('upload','POST'))
        {
            list($overwrite) = GET('overwrite');

            // Try for fopen url upload
            if ($upload_from_inet = REQ('upload_from_inet'))
            {
                if (ini_get('allow_url_fopen'))
                {
                    // Get filename
                    $url_name = spsep($upload_from_inet, '/');
                    $url_name = $url_name[count($url_name)-1];

                    // resolve filename
                    $c_file = $dfile . $url_name;

                    // Overwrite [if can], or add file
                    if ($overwrite && file_exists($c_file) || !file_exists($c_file))
                    {
                        // read file
                        $fw = fopen($upload_from_inet, 'r');
                        ob_start(); fpassthru($fw); $file_image = ob_get_clean();
                        fclose($fw);
                      
                        // write2disk
                        $w = fopen($c_file, 'w+');
                        fwrite($w, $file_image);
                        fclose($w);

                        // check image
                        list($w, $h) = getimagesize($c_file);
                        if ($w && $h)
                        {
                            cn_throw_message('File uploaded');
                            if($thumbnail_with_upload)
                            {
                                $resize_result=resize_image($c_file, 365, 0);
                                cn_throw_message($resize_result['msg'],$resize_result['status']?'n':'w');
                            }                            
                        }
                        else
                        {
                            cn_throw_message("Wrong image file", 'e');
                            unlink($c_file);
                        }
                    }
                    else
                        cn_throw_message("Can't overwrite or save", 'e');
                }
                else cn_throw_message('allow_url_fopen=0, check server configurations');
            }

            // Upload from local
            foreach ($_FILES['upload_file']['name'] as $id => $name) if ($name)
            {
                $ext = NULL;
                if (preg_match('/\.(\w+)$/i', $name, $c))
                    $ext = strtolower($c[1]);

                // Check allowed ext
                if ($ext && in_array($ext, $AE))
                {
                    // encode url
                    $name = str_replace('%2F', '/', urlencode($name));

                    // encoded? replace filename
                    if (strpos($name, '%') !== FALSE)
                        $name = str_replace('%', '', strtolower($name));

                    // check file for exist
                    if (file_exists($c_file = $dfile . $name))
                    {
                        if ($overwrite)
                        {
                            cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] overwritten', 'w');
                        }
                        else
                        {
                            cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] already exists', 'e');
                            continue;
                        }
                    }

                    if (move_uploaded_file($_FILES['upload_file']['tmp_name'][$id], $c_file))
                    {
                        $just_uploaded[$name] = TRUE;
                        cn_throw_message('File uploaded [<b>'.cn_htmlspecialchars($name).'</b>]');

                        if($thumbnail_with_upload)
                        {
                            $resize_result=resize_image($c_file, 365, 0);
                            cn_throw_message($resize_result['msg'],$resize_result['status']?'n':'w');
                        }
                    }
                    else
                        cn_throw_message('File ['.cn_htmlspecialchars($c_file).'] not uploaded!', 'e');
                }
                else
                    cn_throw_message('File extension ['.cn_htmlspecialchars($ext).'] not allowed', 'e');
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
                    cn_throw_message('No one file selected', 'w');
                }

                foreach ($rm as $file)
                {
                    if (file_exists($cfile = $dfile . $file))
                    {
                        if (is_dir($cfile))
                            @rmdir($cfile);
                        else
                            @unlink($cfile);
                    }

                    if (file_exists($cfile))
                        cn_throw_message('File ['.cn_htmlspecialchars($cfile).'] not deleted!', 'e');
                    else
                        cn_throw_message('File ['.cn_htmlspecialchars($file).'] deleted successfully');
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
                list($new_folder) = GET('new_dir', 'POST');

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
                            cn_throw_message('Folder ['.cn_htmlspecialchars($cfile).' not created]', 'e');
                        else
                            cn_throw_message('Folder ['.$new_folder.'] created!');
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
                else cn_throw_message('Select files to rename', 'w');
            }
            // action: process move
            elseif ($pending == 'rename')
            {
                // ...
                list($ids, $place) = GET('ids, place', 'POST');

                // prevent illegal moves
                $safe_dir = scan_dir($root_dir);
                foreach ($safe_dir as $id => $v) $safe_dir[$id] = md5($v);

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
                        $renameto = $root_dir . '/' . $filename;

                        // do move
                        if (rename($root_dir . '/' . $file, $renameto))
                            cn_throw_message(i18n('File [%1] renamed to [%2]', cn_htmlspecialchars($file), cn_htmlspecialchars($filename)) );
                        else
                            cn_throw_message(i18n('File [%1] not renamed', cn_htmlspecialchars($file)), 'e');
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
                        if(is_dir($root_dir.'/'. $entry)&&!($entry==='..'||$entry==='.'))
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
                            $popup_form .= '<select name="place_folder">';
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
                            $popup_form .= '<nobr><input type="checkbox" name="moveup['.$id.']" value="Y" /> Move up</nobr>';
                        }
                        else 
                        {
                            $popup_form .= '<nobr> X Move up (You are in root folder)</nobr>';
                        }
                        $popup_form .='</td></tr>';
                    }

                    $popup_form .= '</table>';
                }
                else cn_throw_message('Select files to move', 'w');
            }
            // action: process move
            elseif ($pending == 'move')
            {
                // ...
                list($ids, $place_folder, $moveup) = GET('ids, place_folder, moveup', 'POST');

                // prevent illegal moves
                $safe_dir = scan_dir($root_dir); 
                foreach ($safe_dir as $id => $v) $safe_dir[$id] = md5($v);

                // do move all files / dirs
                foreach ($ids as $id => $file)
                {
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
                            $nwfolder = $root_dir. ( $NF = isset($rm[0]) ? '/'.$rm[0] : '' ) . '/' . $foldername;
                            if ($rm[0]) $NF = $rm[0].'/' ;
                        }   
                        $moveto = $nwfolder . '/' . $file;

                        // do move
                        if (rename($root_dir . '/' . $file, $moveto))
                            cn_throw_message(i18n('File [%1] moved to [%2]', cn_htmlspecialchars($file), cn_htmlspecialchars($foldername)) );
                        else
                            cn_throw_message(i18n('File [%1] not moved', cn_htmlspecialchars($file)), 'e');
                    }
                }
            }                      
            // ------------------
            elseif ($do_action == 'thumb')
            {
                $popup_form = '<div class="big_font">'.i18n('Make thumbnails').'</div>Tip: To resize image, set 0 to width, or height<p>';
                $popup_form .= '<input type="hidden" name="thumb_rms" value="'.cn_htmlspecialchars(serialize($_POST['rm'])).'">';
                $popup_form .= 'Width <input name="thumb_size_w" value="256"> ';
                $popup_form .= 'Height <input name="thumb_size_h" value="256"></p>';
            }
            elseif ($pending == 'thumb')
            {
                $rms = unserialize(REQ('thumb_rms', 'POST'));
                
                $wt = intval(REQ('thumb_size_w', 'POST'));
                $ht = intval(REQ('thumb_size_h', 'POST'));
                
                if ($wt == 0 && $ht == 0)
                {
                    cn_throw_message('Enter correct width or height', 'e');
                }
                else
                {
                    foreach ($rms as $vp)
                    {                        
                        $fn = $root_dir . '/' . $vp;

                        $resize_result=  resize_image($fn, $wt, $ht);
                        if($resize_result['status'])
                        {
                            cn_throw_message($resize_result['msg']);
                        }
                        else
                        {
                            cn_throw_message($resize_result['msg'],'w');
                            continue;                                                       
                        }                                    
                    }
                }
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
        $file_location = "$root_dir/$file";

        if (is_dir($file_location))
        {
            $dirs[] = array
            (
                'url' => "$path/$file",
                'name' => $file
            );
        }
        else
        {
            list($w, $h) = getimagesize("$udir/$path/$file");

            $is_thumb = preg_match('/\.thumb\.(.*)\.jpeg/', $file);
            
            $files[] = array
            (
                'name'          => $file,
                'url'           => $edir . '/'. ($path? $path . '/' : '') . $file,
                'thumb'         => file_exists($root_dir.'/.thumb.'.pathinfo($file,PATHINFO_FILENAME).'.jpeg') ? $edir . '/'. ($path? $path . '/' : '') . '.thumb.' . pathinfo($file,PATHINFO_FILENAME).'.jpeg' : '',
                'local'         => ($path? $path . '/' : '') . $file,
                'just_uploaded' => isset($just_uploaded[$file]) ? TRUE : FALSE,
                'is_thumb'      => $is_thumb,
                'w'             => $w,
                'h'             => $h,
                'fs'            => round(filesize($file_location)/1024, 1),
            );
        }
    }

    // Top level (dashboard)
    cn_bc_add('Dashboard', cn_url_modify(array('reset')));
    cn_bc_add('Media manager', cn_url_modify());

    cn_assign("files, dirs, path, pathes, popup_form, root_dir", $files, $dirs, $path, $pathes, $popup_form, $root_dir);

    if ($opt === 'inline')
    {
        echo exec_tpl('window', 'title=Quick insert image', 'style=media/style.css', 'content='.exec_tpl('media/general'));
    }
    else
    {
        echoheader('-@media/style.css', 'Media manager');
        echo exec_tpl('media/general');
        echofooter();
    }
}