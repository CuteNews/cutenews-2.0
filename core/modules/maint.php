<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

add_hook('index/invoke_module', '*maint_invoke');

function maint_invoke() {

    $sub = REQ('sub');

    if (!$sub) {
        $sub = 'migrate';
    }

    cn_assign('sub', $sub);

    // Top level (dashboard)
    cn_bc_add('Dashboard', cn_url_modify(array('reset')));
    cn_bc_add('Maintenance', cn_url_modify());

    // ----
    $fn_req = "maintenance_{$sub}";
    if (function_exists($fn_req)) {
        call_user_func($fn_req);
    }

    // REPAIR purpose
    // - migrate
    // - optimize category
    // - update indexes in meta-...
    // - update users uids
}

// ---------------------------------------------------------------------------------------------------------------------

// @system Save old news to old DB
function convert_old_news($source_id, $codepage, $version)
{
    $news = file($source_id);
    foreach ($news as $new)
    {
        $new = html_entity_decode($new);
        $item = explode('|', $new);
        $options = array();

        $MF = array();
        $MF['avatar'] = $item[5];

        // Extract more fields
        $data = spsep(trim($item[8]), ';');
        foreach ($data as $_opt)
        {
            list ($a, $b) = explode('=', $_opt, 2);
            $a = str_replace(array("{I}","{kv}","{eq}","{eol}"), array('{','|',';','=',"\n"), $a);
            $b = str_replace(array("{I}","{kv}","{eq}","{eol}"), array('{','|',';','=',"\n"), $b);
            if ($a && $b)
            {
                $MF[ $a ] = $b;
                $overall_morefields[$a] = $a;
            }
        }

        // Extract options
        $data = spsep(trim($item[9]), ';');
        foreach ($data as $_opt)
        {
            list ($a, $b) = explode('=', $_opt, 2);
            $a = str_replace(array("{I}","{kv}","{eq}","{eol}"), array('{','|',';','=',"\n"), $a);
            $b = str_replace(array("{I}","{kv}","{eq}","{eol}"), array('{','|',';','=',"\n"), $b);
            if ($a && $b) $options[ $a ] = $b;
        }

        // Convert with mb-string
        if (function_exists('iconv') && $codepage)
        {
            $item[2] =  iconv($codepage, 'UTF-8', $item[2]);
            $item[3] =  iconv($codepage, 'UTF-8', $item[3]);
            $item[4] =  iconv($codepage, 'UTF-8', $item[4]);
        }

        // use html? only for 1.5.x
        $ht = ($version == '1.5.x' ? (($options['use_html']) ? TRUE : FALSE) : 0);

        // Create entry
        $entry = array
        (
            'id' => ($id = $item[0]),
            'u'  => $item[1],
            't'  => $item[2],
            's'  => str_replace('{nl}', "\n", $item[3]),
            'f'  => str_replace('{nl}', "\n", $item[4]),
            'c'  => $item[6],
            'co' => array(),
            // rate is DEPRECATED [7] 1.5.x
            'mf' => $version == '1.5.x' ? $MF : array(),
            'ht' => $ht,
        );

        // Update news
        $db = db_news_load( $nloc = db_get_nloc($id) );
        $db[$id] = $entry;
        db_save_news($db, $nloc);
    }
}

// @system Save old comments to news db
function convert_old_comments($source_id, $codepage)
{
    $cr = fopen($source_id, 'r');
    while ($comm = fgets($cr))
    {
        list($news_id, $comm_body) = explode('|>|', $comm, 2);

        $com = array();
        $ls = explode('||', $comm_body);

        foreach ($ls as $hs) 
        {
            if (trim($hs))
            {
                $ex = explode('|', $hs);

                // Convert with mb-string
                if (function_exists('iconv') && $codepage)
                {
                    $ex[4] =  iconv($codepage, 'UTF-8', $ex[4]);
                }

                $com[$ex[0]] = array
                (
                    'id' => $ex[0],
                    'u'  =>isset($ex[1])? $ex[1]:'',
                    'e'  =>isset($ex[2])? $ex[2]:'',
                    'ip' =>isset($ex[3])? $ex[3]:'',
                    'c'  =>isset($ex[4])? $ex[4]:'',
                    'ed' => 0,
                );
            }
        }
        // Update comments for news
        $db = db_news_load( $nloc = db_get_nloc($news_id) );
        $db[$news_id]['co'] = $com;
        db_save_news($db, $nloc);
    }
    fclose($cr);
}

// Update 2.0+ Cutenews News indexes
function convert_update_indexes($index, $file)
{
    $fx  = file($file);
    $idx = db_index_load($index);
    $ls  = array();

    foreach ($fx as $xs)
    {
        $item = explode('|', $xs);
        $db   = db_news_load(db_get_nloc($item[0]));
        $user = db_user_by_name($item[1]);

        $idx[$item[0]] = array($item[6], $user['id'], count($db[$item[0]]['co']));
        $ls[] = $item[0];
    }

    // update index
    db_index_save($idx, $index);
    db_index_update_overall($index);
    
    $max=!empty($ls)?max($ls):0;
    $min=!empty($ls)?min($ls):0;
    return array($min, $max, count($ls));
}


// Since 2.0.1
// ---------------------------------------------------------------------------------------------------------------------
function maintenance_sysconf()
{
    list($path, $edit, $save_conf) = GET('path, edit, save_conf', 'POSTGET');

    $saved = FALSE;

    $epath = spsep($path, '/');
    foreach ($epath as $id => $vp) 
    {
        if (!trim($vp)) 
        {
            unset($epath[$id]);
        }
        $epath = array_slice($epath, 0);
    }

    // get path
    if (count($epath) > 0)
    {
        $epath[0] = $epath[0][0] === '#' ? $epath[0] : '#'.$epath[0];

        $path = implode('/', $epath);
    }
    else 
    {
        $path = '';
    }

    if (request_type('POST'))
    {
        cn_dsi_check();

        if ($path)
        {
            setoption("$path/$edit", $save_conf);
        }
        else
        {
            setoption("#$edit", $save_conf);
        }

        $saved = TRUE;
    }

    $cfg = getoption($path);
    cn_assign('config, path', $cfg, $path);

    if ($edit)
    {
        echo exec_tpl('window', 'title=Edit template part', 'content='.exec_tpl('maint/window/template', array('edit' => $edit, 'path' => $path, 'template' => $cfg[$edit], 'saved' => $saved)));
    }
    else
    {
        echoheader('-@dashboard/style.css', 'System config debug');
        echo exec_tpl('maint/maintenance'); echo exec_tpl('maint/sysconf');
        echofooter();
    }
}

//Since 2.0.2 load external data file
// ---------------------------------------------------------------------------------------------------------------------
function maint_touch_get($target)
{
    if (!file_exists($target))
    {
        return array();
    }
    
    $fc = file($target); unset($fc[0]); $fc = join('', $fc);

    if ($fc[0] === '{') {
        return $fc ? unserialize($fc) : array();
    }
    else {
        $fc = base64_decode($fc);
        return $fc ? unserialize($fc) : array();
    }
}

//Since 2.0.2 load option from external config
// ---------------------------------------------------------------------------------------------------------------------
function maint_getoption($cfg,$opt_name)
{
    if ($opt_name === '')
    {
        return $cfg;
    }
    if ($opt_name[0] == '#')
    {
        $cfn = spsep(substr($opt_name, 1), '/');
        foreach ($cfn as $id)
        {
            if (isset($cfg[$id])) 
            {
                $cfg = $cfg[$id];
            }
            else
            {
                $cfg = array();
                break;
            }
        }

        return $cfg;
    }
    else
    {
        return isset($cfg['%site'][$opt_name]) ? $cfg['%site'][$opt_name] : FALSE;
    }        
}

function maint_get_user_data($base_dir,$id)
{    
    $user=array();
    $cu=  maint_touch_get(cn_path_construct($base_dir,'users').substr(md5($id), 0, 2).'.php');
 
    if (isset($cu['id'][$id]))
    {
        $name=$cu['id'][$id];
        $ud=  maint_touch_get(cn_path_construct($base_dir,'users').substr(md5($name), 0, 2).'.php');
        
        $user = $ud['name'][$name];    
        if (isset($user['more']) && $user['more'])
        {
           $user['more'] = unserialize($user['more']);
        }
        else
        {
           $user['more'] = array();               
        }
    }
    return $user;
}

// Since 2.0
// ---------------------------------------------------------------------------------------------------------------------
function maintenance_migrate()
{
    list($version, $sample_id, $codepage, $preview_html, $old_dir, $convert, $conv) = GET('version, sample_id, codepage, preview_html, old_dir, convert, conv');

    if (!$old_dir)
    {
        $old_dir = SERVDIR;
    }
    
    if (request_type('POST'))
    {
        // ======== CONVERT =========
        if ($convert)
        {
            if ($version == '1.5.x' || $version=='2.0.x')
            {
                $data_dir = $old_dir . DIRECTORY_SEPARATOR . 'cdata';
            }
            else
            {
                $data_dir = $old_dir . DIRECTORY_SEPARATOR . 'data';
            }
            
            $is_error = is_dir($data_dir);
            
            if (empty($conv))
            {
                cn_throw_message('No selected options for migration','e');
            }
            $overall_count=0;

            // ---
            if ($is_error && isset($conv['users']) && $conv['users'])
            {
                $message = FALSE;

                if ($version == '2.0.x')
                {                         
                    $base_path = $data_dir . DIRECTORY_SEPARATOR . 'users.txt';
                    $overall_count = 0;

                    if (file_exists($base_path))
                    {
                        $uf = file($base_path);
                        foreach ($uf as $u)
                        {
                            list($h, $acl) = explode(':', $u, 2);

                            $id = base_convert($h, 36, 10);

                            //get user data from old base
                            $user = maint_get_user_data($data_dir, $id);

                            if (!empty($user))
                            {
                                // Check user exist in curr base
                                $tu = db_user_by_name($user['name']);
                                $is_user =! (empty($tu) && $tu['name'] == $user['name']);

                                // Add user before
                                db_user_add($user['name'], $user['acl'], $id);

                                if (!$is_user)
                                {
                                    db_user_update($user['name'], 'pass=' . $user['pass']);
                                }

                                // overall update for exists and not exists
                                db_user_update($user['name'], 
                                        'nick='.(isset($user['nick'])?$user['nick']:''),
                                        'email='.(isset($user['email'])?$user['email']:''),
                                        'cnt='.(isset($user['cnt'])?$user['cnt']:''), 
                                        'e-hide='.(isset($user['e-hide'])?$user['e-hide']:''), 
                                        'avatar='.(isset($user['avatar'])?$user['avatar']:''), 
                                        'lts='.(isset($user['lts'])?$user['lts']:''));

                                $overall_count++;
                            }
                        }

                        cn_throw_message('Users updated ('.$overall_count.')');
                    }
                    else
                    {
                        cn_throw_message('Users file not found','e');
                    }
                }
                else
                {
                    $overall_count = 0;
                    $base_path = $data_dir . DIRECTORY_SEPARATOR . 'users.db.php';

                    if (file_exists($base_path))
                    {
                        $users = file($base_path);
                        unset($users[0]);
                        
                        foreach ($users as $u_item)
                        {
                            $item = explode('|', $u_item);
                            $overall_count++;

                            if (!db_user_by_name($item[2]))
                            {
                                db_user_add($item[2], $item[1], $item[0]);
                                db_user_update($item[2], 'pass='.$item[3]);
                            }

                            // overall update for exists and not exists
                            db_user_update($item[2], 'nick='.$item[4], 'email='.$item[5], 'cnt='.$item[6], 'e-hide='.$item[7], 'avatar='.$item[8], 'lts='.$item[9]);
                        }

                        cn_throw_message('Users updated ('.$overall_count.')');
                    }
                    else
                    {
                        cn_throw_message('Users file not found','e');
                    }                    
                }
            }

            // ---
            if ($is_error && isset($conv['news']) && $conv['news'])
            {
                if ($version=='2.0.x')
                {                    
                    $base_dir = $data_dir . DIRECTORY_SEPARATOR . 'news';

                    if (is_dir($base_dir))
                    {
                        $overall_count = 0;
                        $news = scan_dir($base_dir, '\d+-\d+-\d+');

                        foreach ($news as $n)
                        {
                            $entries = array();
                            $old_news = maint_touch_get($base_dir . DIRECTORY_SEPARATOR . $n);
                            $overall_count += count($old_news);

                            if (!is_array($old_news)) { $old_news = array(); }
                            foreach ($old_news as $en)
                            {                            
                                $uid = db_user_by_name($en['u']);
                                db_index_add($en['id'], $en['c'], $uid['id']);

                                if (function_exists('iconv') && $codepage)
                                {
                                    $en['s'] = iconv($codepage, 'UTF-8', $en['s']);
                                    if (!empty($en['co']))
                                    {
                                        $en['co']['c'] = iconv($codepage, 'UTF-8', $en['co']['c']);
                                    }
                                }

                                $entries[$en['id']] = $en;
                            }

                            db_save_news($entries, pathinfo($n, PATHINFO_FILENAME) );
                        }
                    }
                    else
                    {
                        cn_throw_message('News dirrectory not found','e');
                    }
                }
                else
                {
                    $sources = array
                    (
                        '' => 'news.txt',
                        'draft' => 'unapproved_news.txt',
                        'iactive' => 'postponed_news.txt'
                    );

                    $overall_count = 0; 
                    $overall_morefields = array();

                    // Get news from all sources
                    foreach ($sources as $source)
                    {
                        $sid = $data_dir . DIRECTORY_SEPARATOR . $source;

                        if (file_exists($sid))
                        {
                            convert_old_news($sid, $codepage, $version);
                        }
                        else 
                        {
                            cn_throw_message('File '.$source.' not found','e');
                        }
                    }
                    // For active news
                    $comm_file = $data_dir . DIRECTORY_SEPARATOR . 'comments.txt';
                    if (file_exists($comm_file))
                    {
                        convert_old_comments($comm_file, $codepage);
                    }
                    else 
                    {
                        cn_throw_message('Comments file not found','e');
                    }

                    // Update indexes
                    foreach ($sources as $src_id => $source)
                    {
                        $base_path = $data_dir . DIRECTORY_SEPARATOR . $source;

                        if (file_exists($base_path))
                        {
                            list(,,$cnt) = convert_update_indexes($src_id, $base_path);
                            $overall_count += $cnt;
                        }
                        else 
                        {
                            cn_throw_message('Index file '.$source.' not found','e');
                        }
                    }

                    // get more-fields from cfg
                    $base_path = $data_dir . DIRECTORY_SEPARATOR . 'conf.php';

                    if (file_exists($base_path))
                    {
                        $fconf = file($base_path);
                        
                        unset($fconf[0]); $conf = unserialize(join('', $fconf));

                        // unused, but exists morefields in news
                        foreach ($overall_morefields as $name)
                        {
                            if (!isset($conf['more_fields'][$name]))
                            {
                                $conf['more_fields'][$name] = $name;
                            }
                        }

                        // update additionals fields
                        $mlist = getoption('#more_list');
                        if ($conf['more_fields']) 
                        {
                            foreach ($conf['more_fields'] as $id => $name)
                            {
                                if ($name[0] === '&')
                                {
                                    $req = TRUE;
                                    $name = substr($name, 1);
                                }
                                else 
                                {
                                    $req = FALSE;
                                }

                                $mlist[$id] = array('grp' => '', 'type' => 'text', 'desc' => $name, 'meta' => '', 'req' => $req);
                            }
                        }

                        // save new morefields to config
                        setoption('#more_list', $mlist);
                    }
                    else
                    {
                        cn_throw_message('Additional fields not migrate','e');
                    }
                }

                cn_throw_message('Converted news ('.$overall_count.'), additional fields, comments');
            }

            // ---
            if ($is_error&&isset($conv['archives'])&&$conv['archives'])
            {                
                if($version=='2.0.x')
                {
                    $base_dir=$data_dir.DIRECTORY_SEPARATOR.'news';
                    $ar_file=$base_dir.DIRECTORY_SEPARATOR.'archive.txt';
                    if(file_exists($ar_file))
                    {
                        $archive_file=file($ar_file);
                        $overall_count=0;
                        if(!empty($archive_file))
                        {
                            list($aid,$id_from,$id_to,$cc)=  explode('|', $archive_file[0]);
                            if(is_dir($base_dir))
                            {
                                $news = scan_dir($base_dir, '\d+-\d+-\d+');                         
                                foreach ($news as $n)
                                {                                    
                                    $old_news=maint_touch_get($base_dir.DIRECTORY_SEPARATOR.$n);                        
                                    $entryes=array();

                                    foreach ($old_news as $en_id => $en)
                                    {                            
                                        if($en['id']>=$id_from&&$en['id']<=$id_to)
                                        {
                                            $uid=db_user_by_name($en['u']);
                                            db_index_add($en['id'], $en['c'], $uid);
                                            $entryes[$en_id]=$en;
                                        }
                                    }
                                    $overall_count+=count($entryes);
                                    db_save_news($entryes,  pathinfo($n,PATHINFO_FILENAME) );    
                                }                                                                       
                                db_make_archive($id_from, $id_to);
                            }
                            else
                            {
                                cn_throw_message('Archive directory not found','e');
                            }                        
                        }
                    }
                    else
                    {
                        cn_throw_message('Archive file not found','e');   
                    }
                }
                else
                {
                    $arx = array();
                    $overall_count = 0;
                    $base_dir=$data_dir.DIRECTORY_SEPARATOR.'archives';
                    if(is_dir($base_dir))
                    {
                        $dir = scan_dir($base_dir, 'news.arch');

                        foreach ($dir as $arch_id) 
                        {
                            if (preg_match('/(\d+)/', $arch_id, $ca))
                            {
                                // add news
                                convert_old_news(cn_path_construct($data_dir,'archives').$ca[1].'.news.arch', $codepage, $version);

                                // add comments
                                convert_old_comments(cn_path_construct($data_dir,'archives').$ca[1].'.comments.arch', $codepage);

                                // update indexes
                                list($min, $max, $count) = convert_update_indexes('archive-'.$ca[1], cn_path_construct($data_dir,'archives').$ca[1].'.news.arch');

                                $arx[$ca[1]] = array($min, $max, $count);

                                // Update archive meta-info
                                db_archive_meta_update($ca[1], $min, $max, $count);

                                $overall_count++;
                            }
                        }
                    }
                    else
                    {
                        cn_throw_message('Archive directory not found','e');
                    }                    
                }
                cn_throw_message('Converted archives ('.$overall_count.')');
            }

            // ---
            if ($is_error&&isset($conv['sc'])&&$conv['sc'])
            {
                if($version=='2.0.x')
                {
                    $cfg_path=$data_dir .DIRECTORY_SEPARATOR.'conf.php';            
                    if(file_exists($cfg_path))
                    {
                        $cfg=  maint_touch_get($cfg_path);

                        if(isset($conv['ovconf'])&&$conv['ovconf'])
                        {
                            $syscon = maint_getoption($cfg,'#%site');
                            setoption('#%site', $syscon);
                            cn_throw_message('System configurations updated');                    
                        }
                        
                        $tpl = maint_getoption($cfg,'#templates');
                        setoption('#templates', $tpl);                    
                        cn_throw_message('Template updated');

                        $old_sr = maint_getoption($cfg,'#rword');
                        $cur_sr = getoption('#rword');
                        $sr = array_merge_recursive($old_sr,$cur_sr);
                        setoption('#rword', $sr);
                        cn_throw_message('Word replacement updated');

                        $old_cats = maint_getoption($cfg, '#category');
                        $cur_cats = getoption('#category');
                        $cats = array_merge_recursive($old_cats, $cur_cats);
                        setoption('#category', $cats);
                        cn_throw_message('Category updated');    

                        $old_ipban  = maint_getoption($cfg,'#ipban');                    
                        $cur_ipban=  getoption('#ipban');                    
                        $ipban=  array_merge_recursive($old_ipban,$cur_ipban);                    
                        setoption('#ipban', $ipban);
                        cn_throw_message('IP Ban updated');
                    }
                    else
                    {
                        cn_throw_message('Configuration file not found','e');
                    }
                }
                else
                {
                    // CONVERT CONFIG
                    // -----------------
                    $conf_dir=$data_dir .DIRECTORY_SEPARATOR.'config.php';
                    if(file_exists($conf_dir))
                    {
                        include $conf_dir;

                        if(isset($conv['ovconf'])&&$conv['ovconf'])
                        {
                            $syscon = getoption('#%site');

                            $_cnv  = 'use_wysiwyg,skin,date_adjust,smilies,allow_registration,';
                            $_cnv .= 'registration_level,ban_attempts,use_replacement,userlogs,allowed_extensions,';
                            $_cnv .= 'full_popup_string,timestamp_active,use_captcha,flood_time,';
                            $_cnv .= 'comment_max_long,comments_per_page,comments_popup_string,';
                            $_cnv .= 'timestamp_comment,notify_email,fb_i18n,fb_appid,';
                            $_cnv .= 'fb_comments,fb_box_width,fbcomments_color,fblike_style,fblike_width,';
                            $_cnv .= 'fblike_font,fblike_color,fblike_verb,tw_url,tw_text,tw_show_count,tw_via,tw_recommended,tw_hashtag,';
                            $_cnv .= 'tw_lang';

                            // Convert strings
                            $_cnv = spsep($_cnv);
                            foreach ($_cnv as $item)
                            {
                                $rqs = "config_$item";                        
                                $var =isset($$rqs)?$$rqs:null;

                                if (!empty($var)&& preg_match('/^[0-9]$/', $var))
                                {
                                    $var = intval($var);
                                }

                                $syscon[$item] = $var;
                            }

                            $_ynb  = 'reverse_active,full_popup,show_comments_with_full,reverse_comments,only_registered_comment,allow_url_instead_mail,';
                            $_ynb .= 'show_full_with_comments,comments_popup,auto_archive,';
                            $_ynb .= 'notify_registration,notify_comment,notify_archive,notify_unapproved,use_fbcomments,fb_inactive,use_fblike,use_twitter,fblike_send_btn,fblike_show_faces,tw_large';
                            $_ynb = spsep($_ynb);

                            foreach ($_ynb as $item)
                            {
                                $rqs = "config_$item";
                                $var = isset($$rqs)?$$rqs:null;

                                if ($var == 'on' || $var == 'yes') 
                                {
                                    $var = 1;
                                }
                                elseif ($var == 'no' || $var == 'disabled') 
                                {
                                    $var = 0;
                                }

                                $syscon[$item] = $var;
                            }

                            // Use CKEditor
                            $syscon['use_wysiwyg'] = ($syscon['use_wysiwyg'] == 'ckeditor') ? 1 : 0;

                            // Save new options
                            setoption('#%site', $syscon);
                            cn_throw_message('System configurations updated');
                        }
                        // CONVERT TEMPLATES
                        // ----------------------------------
                        $tpl = getoption('#templates');
                        $dir = scan_dir($data_dir, '\.tpl');
                        if(!empty($dir))
                        {
                            foreach ($dir as $template)
                            {
                                include $data_dir . DIRECTORY_SEPARATOR . $template;

                                if (preg_match('/(.*)\.tpl/i', $template, $c))
                                {
                                    $tpl[strtolower($c[1])] = array
                                    (
                                        'active' => isset($template_active) ? $template_active : '',
                                        'full' => isset($template_full) ? $template_full : '',
                                        'comment' => isset($template_comment) ? $template_comment : '',
                                        'form' => isset($template_form) ? $template_form : '',
                                        'prev_next' => isset($template_prev_next) ? $template_prev_next : '',
                                        'comments_prev_next' => isset($template_comments_prev_next) ? $template_comments_prev_next : '',
                                    );
                                }
                            }

                            setoption('#templates', $tpl);
                            cn_throw_message('Template updated');
                        }
                        else
                        {
                            cn_throw_message('No templates for update');
                        }                    
                        // REPLACES
                        // -----------------
                        $fn = $data_dir.DIRECTORY_SEPARATOR.'replaces.php';
                        if (file_exists($fn))
                        {
                            $sr = getoption('#rword');
                            $rp = file($fn); unset($rp[0]);
                            foreach ($rp as $item)
                            {
                                list($lt, $rt) = explode('=', $item, 2);
                                $sr[$lt] = $rt;
                            }
                            setoption('#rword', $sr);
                            cn_throw_message('Word replacement updated');
                        }
                        else
                        {
                            cn_throw_message('Word replacement file not found','e');
                        }
                        // CATEGORY
                        // -----------------
                        $fn = $data_dir.DIRECTORY_SEPARATOR.'category.db.php';
                        if(file_exists($fn))
                        {
                            $cd = file($fn);
                            $cats = getoption('#category');

                            foreach ($cd as $item)
                            {
                                list($_id, $_name, $_icon, $_acl) = explode('|', $item);

                                if ($_acl == 2) 
                                {
                                    $ACL = '1,2'; // Editors and Admins
                                }
                                elseif ($_acl == 1) 
                                {
                                    $ACL = '1'; // Only admin
                                }
                                else 
                                {
                                    $ACL = ''; // Everyone
                                }

                                $cats[$_id] = array
                                (
                                    'name' => $_name,
                                    'icon' => $_icon,
                                    'acl' => $ACL,
                                );
                            }

                            $cats['#'] =!empty($cats)?max(array_keys($cats)):0;

                            setoption('#category', $cats);
                            cn_throw_message('Category updated');
                        }
                        else
                        {
                            cn_throw_message('Category file not found','e');
                        }
                        // IPBAN
                        // -----------------
                        $fn     = $data_dir.DIRECTORY_SEPARATOR.'ipban.db.php';
                        if(file_exists($fn))
                        {
                            $tc     = file($fn);
                            $ipban  = getoption('#ipban');

                            foreach ($tc as $item)
                            {
                                list($_ip, $_tb) = explode('|', $item);
                                $ipban[$_ip] = array(intval($_tb), 0);
                            }

                            setoption('#ipban', $ipban);
                            cn_throw_message('IP Ban updated');
                        }
                        else
                        {
                            cn_throw_message('IP Ban file not found','e');
                        }
                    }
                    else 
                    {
                        cn_throw_message('Configuration file not found','e');
                    }
                }
            }
            
            if(!$is_error)
            {
                cn_throw_message('Incorrect news directory or CuteNews source version','e');
            }
        }
        // ======== PREVIEW ONLY =========
        else
        {
            if ($version == '1.5.x')
            {
                $data_dir = cn_path_construct($old_dir,'cdata') . 'news.txt';
            }
            elseif ($version == '1.4.x')
            {
                $data_dir = cn_path_construct( $old_dir , 'data') . 'news.txt';
            }
            elseif($version=='2.0.x')
            {
                $data_dir=  cn_path_construct($old_dir,'cdata','news');
            }
            
            // ---
            if ($sample_id && isset($data_dir))
            {           
                if(file_exists($data_dir))
                {
                    $preview_html = '';
                    if($version!='2.0.x')
                    {                    
                        $r = fopen($data_dir, 'r');
                        while ($a = fgets($r))
                        {
                            list($id,,,$ss,$fs) = explode('|', $a);
                            if ($id == $sample_id)
                            {
                                $preview_html = $ss.$fs;
                            }
                        }
                        fclose($r);
                    }
                    else
                    {
                        $news=  maint_touch_get($data_dir.db_get_nloc($sample_id).'.php');                    
                        if(isset($news[$sample_id]))
                        {                        
                            $preview_html=$news[$sample_id]['s'];
                        }
                    }
                    if(empty($preview_html))
                    {
                        cn_throw_message('News with ID '.$sample_id.' not exist','e');
                    }
                    // Convert
                    if (function_exists('iconv') && $codepage)
                    {
                        $preview_html = iconv($codepage, 'UTF-8', $preview_html);
                    }
                }
                else
                {
                    cn_throw_message('Incorrect news directory or CuteNews source version','e');
                }
            }
            else
            {
                cn_throw_message('Not set old news ID for preview','e');
            }
        }
    }

    if (!function_exists('iconv'))
    {
        cn_throw_message('mbstring not installed! Convert tool not work', 'e');
    }

    cn_assign('version, sample_id, codepage, preview_html, old_dir', $version, $sample_id, $codepage, $preview_html, $old_dir);

    // -- render
    echoheader('-@dashboard/style.css', 'Maintenance');
    echo exec_tpl('maint/maintenance'); echo exec_tpl('maint/migrate');
    echofooter();
}
