<?php if (!defined('EXEC_TIME')) die('Access restricted');

add_hook('index/invoke_module', '*maint_invoke');

function maint_invoke()
{
    $sub = REQ('sub');

    if (!$sub) $sub = 'migrate';

    cn_assign('sub', $sub);

    // Top level (dashboard)
    cn_bc_add('Dashboard', cn_url_modify(array('reset')));
    cn_bc_add('Maintenance', cn_url_modify());

    // ----
    $fn_req = "maintenance_{$sub}";
    if (function_exists($fn_req))
        call_user_func($fn_req);

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
function convert_old_comments($source_id, $codepage, $version)
{
    $cr = fopen($source_id, 'r');
    while ($comm = fgets($cr))
    {
        list($news_id, $comm_body) = explode('|>|', $comm, 2);

        $com = array();
        $ls = explode('||', $comm_body);

        foreach ($ls as $hs) if (trim($hs))
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
                'u'  => $ex[1],
                'e'  => $ex[2],
                'ip' => $ex[3],
                'c'  => $ex[4],
                'ed' => 0,
            );
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

    return array(min($ls), max($ls), count($ls));
}


// Since 2.0.1
// ---------------------------------------------------------------------------------------------------------------------
function maintenance_sysconf()
{
    list($path, $edit, $save_conf) = GET('path, edit, save_conf', 'POSTGET');

    $saved = FALSE;

    $epath = spsep($path, '/');
    foreach ($epath as $id => $vp) if (!trim($vp)) unset($epath[$id]); $epath = array_slice($epath, 0);

    // get path
    if (count($epath) > 0)
    {
        $epath[0] = $epath[0][0] === '#' ? $epath[0] : '#'.$epath[0];
        $path = join('/', $epath);
    }
    else $path = '';

    if (request_type('POST'))
    {
        cn_dsi_check();

        if ($path)
            setoption("$path/$edit", $save_conf);
        else
            setoption("#$edit", $save_conf);

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

// Since 2.0
// ---------------------------------------------------------------------------------------------------------------------
function maintenance_migrate()
{
    list($version, $sample_id, $codepage, $preview_html, $old_dir, $convert, $conv) = GET('version, sample_id, codepage, preview_html, old_dir, convert, conv');

    if (!$old_dir)
        // $old_dir = SERVDIR;
        $old_dir = '/mnt/hdd/foxtail/www/cn/c150/cms';

    if (request_type('POST'))
    {
        // ======== CONVERT =========
        if ($convert)
        {
            if ($version == '1.5.x')
                $data_dir = $old_dir . '/cdata';
            else
                $data_dir = $old_dir . '/data';

            // ---
            if ($conv['users'])
            {
                $users = file("$data_dir/users.db.php");
                unset($users[0]);

                $overall_count = 0;
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

            // ---
            if ($conv['news'])
            {
                $sources = array
                (
                    '' => 'news.txt',
                    'draft' => 'unapproved_news.txt',
                    'iactive' => 'postponed_news.txt',
                );

                $overall_count = 0;
                $overall_morefields = array();

                // Get news from all sources
                foreach ($sources as $source)
                    convert_old_news("$data_dir/$source", $codepage, $version);

                // For active news
                convert_old_comments($data_dir . '/comments.txt', $codepage, $version);

                // Update indexes
                foreach ($sources as $src_id => $source)
                {
                    list(,,$cnt) = convert_update_indexes($src_id, "$data_dir/$source");
                    $overall_count += $cnt;
                }

                // get more-fields from cfg
                $fconf = file($data_dir . '/conf.php');
                unset($fconf[0]); $conf = unserialize(join('', $fconf));

                // unused, but exists morefields in news
                foreach ($overall_morefields as $name)
                    if (!isset($conf['more_fields'][$name]))
                        $conf['more_fields'][$name] = $name;

                // update additionals fields
                $mlist = getoption('#more_list');
                if ($conf['more_fields']) foreach ($conf['more_fields'] as $id => $name)
                {
                    if ($name[0] === '&')
                    {
                        $req = TRUE;
                        $name = substr($name, 1);
                    }
                    else $req = FALSE;

                    $mlist[$id] = array('grp' => '', 'type' => 'text', 'desc' => $name, 'meta' => '', 'req' => $req);
                }

                // save new morefields to config
                setoption('#more_list', $mlist);
                cn_throw_message('Converted news ('.$overall_count.'), additional fields, comments');
            }

            // ---
            if ($conv['archives'])
            {
                $arx = array();
                $overall_count = 0;
                $dir = scan_dir("$data_dir/archives", 'news.arch');

                foreach ($dir as $arch_id) if (preg_match('/(\d+)/', $arch_id, $ca))
                {
                    // add news
                    convert_old_news("$data_dir/archives/".$ca[1].'.news.arch', $codepage, $version);

                    // add comments
                    convert_old_comments("$data_dir/archives/".$ca[1].'.comments.arch', $codepage, $version);

                    // update indexes
                    list($min, $max, $count) = convert_update_indexes('archive-'.$ca[1], "$data_dir/archives/".$ca[1].'.news.arch');

                    $arx[$ca[1]] = array($min, $max, $count);

                    // Update archive meta-info
                    db_archive_meta_update($ca[1], $min, $max, $count);

                    $overall_count++;
                }

                cn_throw_message('Converted archives ('.$overall_count.')');
            }

            // ---
            if ($conv['sc'])
            {
                // CONVERT CONFIG
                // -----------------

                include $data_dir . '/config.php';

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
                    $var = $$rqs;

                    if (preg_match('/^[0-9]$/', $var))
                        $var = intval($var);

                    $syscon[$item] = $var;
                }

                $_ynb  = 'reverse_active,full_popup,show_comments_with_full,reverse_comments,only_registered_comment,allow_url_instead_mail,';
                $_ynb .= 'show_full_with_comments,comments_popup,auto_archive,';
                $_ynb .= 'notify_registration,notify_comment,notify_archive,notify_unapproved,use_fbcomments,fb_inactive,use_fblike,use_twitter,fblike_send_btn,fblike_show_faces,tw_large';
                $_ynb = spsep($_ynb);

                foreach ($_ynb as $item)
                {
                    $rqs = "config_$item";
                    $var = $$rqs;

                    if ($var == 'on' || $var == 'yes') $var = 1;
                    elseif ($var == 'no' || $var == 'disabled') $var = 0;

                    $syscon[$item] = $var;
                }

                // Use CKEditor
                $syscon['use_wysiwyg'] = ($syscon['use_wysiwyg'] == 'ckeditor') ? 1 : 0;

                // Save new options
                setoption('#%site', $syscon);
                cn_throw_message('System configurations updated');

                // CONVERT TEMPLATES
                // ----------------------------------
                $tpl = getoption('#templates');
                $dir = scan_dir("$data_dir", '\.tpl');
                foreach ($dir as $template)
                {
                    include $data_dir . '/' . $template;

                    if (preg_match('/(.*)\.tpl/i', $template, $c))
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

                setoption('#templates', $tpl);
                cn_throw_message('Template updated');

                // REPLACES
                // -----------------
                $fn = "$data_dir/replaces.php";
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

                // CATEGORY
                // -----------------
                $fn = "$data_dir/category.db.php";
                $cd = file($fn);
                $cats = getoption('#category');

                foreach ($cd as $item)
                {
                    list($_id, $_name, $_icon, $_acl) = explode('|', $item);

                    if ($_acl == 2) $ACL = '1,2'; // Editors and Admins
                    elseif ($_acl == 1) $ACL = '1'; // Only admin
                    else $ACL = ''; // Everyone

                    $cats[$_id] = array
                    (
                        'name' => $_name,
                        'icon' => $_icon,
                        'acl' => $ACL,
                    );
                }

                $cats['#'] = max(array_keys($cats));

                setoption('#category', $cats);
                cn_throw_message('Category updated');

                // IPBAN
                // -----------------
                $fn     = "$data_dir/ipban.db.php";
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

        }
        // ======== PREVIEW ONLY =========
        else
        {
            if ($version == '1.5.x')
                $data_dir = $old_dir . '/cdata/news.txt';
            elseif ($version == '1.4.x')
                $data_dir = $old_dir . '/data/news.txt';

            // ---
            if ($sample_id && isset($data_dir))
            {
                $preview_html = '';
                $r = fopen($data_dir, 'r');
                while ($a = fgets($r))
                {
                    list($id,,,$ss,$fs) = explode('|', $a);
                    if ($id == $sample_id)
                        $preview_html = $ss.$fs;
                }
                fclose($r);

                // Convert
                if (function_exists('iconv') && $codepage)
                    $preview_html = iconv($codepage, 'UTF-8', $preview_html);
            }
        }
    }

    if (!function_exists('iconv'))
        cn_throw_message('mbstring not installed! Convert tool not work', 'e');

    cn_assign('version, sample_id, codepage, preview_html, old_dir', $version, $sample_id, $codepage, $preview_html, $old_dir);

    // -- render
    echoheader('-@dashboard/style.css', 'Maintenance');
    echo exec_tpl('maint/maintenance'); echo exec_tpl('maint/migrate');
    echofooter();
}
