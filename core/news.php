<?php

if (!defined('EXEC_TIME')) die('Access restricted');

// ******************************************************************************************************
// NEWS MODIFIERS, PARTIAL/FULL REPLACE PLACEHOLDERS IN TEMPLATES
// ******************************************************************************************************

function cn_bb_decode($bb)
{
    $result = array();
    if (preg_match_all('/(\w+)\=([^\s\=]+)/i', $bb, $c, PREG_SET_ORDER))
        foreach ($c as $k) $result[$k[1]] = $k[2];

    return $result;
}

function cn_helper_category($e)
{
    $nice = '';
    $cat = cn_get_categories(TRUE);

    $sp = spsep($e['c']);
    foreach ($sp as $cid)
        if (isset($cat[$cid]))
            $nice[] = $cat[ intval($cid) ];

    return $nice;
}

function cn_helper_html_text($e, $p)
{
    $text = $e[$p];

    // replace word module
    $text = cn_extrn_replace($text);

    // html-specialchars
    if ($e['ht'])
        $text = hook('kses', $text);
    else
        $text = nl2br(cn_htmlspecialchars($text));

    // 0) Special
    $text = preg_replace('/\[(\/?)list\]/i', '<\\1ul>', $text);
    $text = preg_replace('/\[\*\]/i', '<li>', $text);

    // 1) catch [bb-tag] ... [bb-tag] -- double. Recursive search
    do
    {
        if (preg_match_all('/\[([a-z0-9_-]+)(.*?)\](.*)\[\/\\1\]/is', $text, $cp, PREG_SET_ORDER))
        {
            foreach ($cp as $ci)
            {
                $repl = '';
                $name = $ci[1];
                $func = "cn_modify_s2bb_{$name}";

                if (function_exists($func))
                    $repl = call_user_func($func, $ci[3], $ci[2], cn_bb_decode($ci[2]));

                // do replace [bb-tag]...[/bb-tag]
                $text = str_replace($ci[0], $repl, $text);
            }
        }
    }
    while (count($cp) > 0);

    // 2) catch [bb-tag] -- single
    if (preg_match_all('/\[([a-z0-9_-]+)(.*?)\]/is', $text, $cp, PREG_SET_ORDER))
    {
        foreach ($cp as $ci)
        {
            $repl = '';
            $name = $ci[1];
            $func = "cn_modify_s1bb_{$name}";
            
            if (function_exists($func))
                $repl = call_user_func($func, $ci[2], cn_bb_decode($ci[2]));
                
            // do replace [bb-tag]
            $text = str_replace($ci[0], $repl, $text);
        }
    }

    return $text;
}

function cn_helper_bb_decode($bb)
{
    $a_opts = '';
    $bb = cn_bb_decode($bb);

    if ($bb['target']) $a_opts = 'target="'.cn_htmlspecialchars($bb['target']).'" ';
    return array($a_opts);
}

function cn_helper_smiles($template)
{
    $sml = spsep(getoption('smilies'));
    $be  = getoption('base64_encode_smile');

    // catch smilies
    if (preg_match_all('/\:([a-z_]+?)\:/i', $template, $ct, PREG_SET_ORDER))
    {
       foreach ($ct as $c)
       {
           if (!in_array($c[1], $sml))
               continue;

           if ($be)
               $url = "data:image/png;base64,".base64_encode(join('', file(SERVDIR.'/skins/emoticons/'.$c[1].'.gif')));
           else
               $url = getoption('http_script_dir')."/skins/emoticons/".$c[1].".gif";

           $template = str_replace($c[0], '<img src="'.$url.'" />', $template);
       }
    }

    return $template;
}

// BB-TAGS -------------------------------------------------------------------------------------------------------------
function cn_modify_s1bb_upimage($o)
{
    list($src, $dt) = explode(' ', $o);
    return '<img '.$dt.' src="'.getoption('http_script_dir').'/skins/images/upskins/images/'.substr($src, 1).'" style="border: none;" alt="" />';
}

function cn_modify_s2bb_link($t, $o)
{
    return '<a href="'.substr($o, 1).'">'.$t.'</a>';
}

function cn_modify_s1bb_image($t, $o) { return cn_modify_s1bb_upimage($t, $o); } // alias upimage

function cn_modify_s2bb_b($t) { return '<strong>'.$t.'</strong>'; }
function cn_modify_s2bb_i($t) { return '<em>'.$t.'</em>'; }
function cn_modify_s2bb_u($t) { return '<span style="text-decoration: underline;">'.$t.'</span>'; }

function cn_modify_s2bb_color($t, $o) { return '<span style="color: '.preg_replace('/[^a-z0-9\-\_\#]/i','', substr($o, 1)).';">'.$t.'</span>'; }
function cn_modify_s2bb_size($t, $o) { return '<span style="font-size: '.intval(substr($o, 1)).'pt;">'.$t.'</span>'; }
function cn_modify_s2bb_font($t, $o) { return '<span style="font-family: '.preg_replace('/[^a-z0-9\-\_]/i','', substr($o, 1)).';">'.$t.'</span>'; }
function cn_modify_s2bb_align($t, $o) { return '<div style="text-align: '.preg_replace('/[^a-z\-]/i','', substr($o, 1)).';">'.$t.'</div>'; }

function cn_modify_s2bb_quote($t, $o)
{
    $o = substr($o, 1);
    return '<blockquote class="cn_blockquote"><div class="cn_blockquote_title">quote'.($o ? ' ('.$o.')' : '').':</div><hr /><div class="cn_blockquote_body">'.$t.'</div><hr /></blockquote>';
}

function cn_modify_s2bb_img($t, $bb)
{
    $bb = cn_bb_decode($bb);

    $w = $bb['width'] ? ' width="'.intval($bb['width']).'" ' : '';
    $h = $bb['height'] ? ' height="'.intval($bb['height']).'" ' : '';
    $a = $bb['alt'] ? ' alt="'.cn_htmlspecialchars($bb['alt']).'" ' : '';

    // Default upload dir
    $upext = getoption('uploads_ext') ?  getoption('uploads_ext') : getoption('http_script_dir') . '/uploads';

    if (!preg_match('/https?:\/\//i', $t))
        $t = $upext . '/' . str_replace('%2F', '/', urlencode($t));

    if ($bb['popup'])
        return '<a href="'.$t.'" target="_blank"><img src="'.$t.'"'.$w.$h.$a.'/></a>';
    else
        return '<img src="'.$t.'"'.$w.$h.$a.'/>';
}

function cn_modify_s2bb_more($t)
{
    $ID = md5(mt_rand());

    $echo = '<div class="cn_more_button"><a href="#" onclick="cn_more_expand(\'more_link_'.$ID.'\'); return false;">'.i18n("Expand more").'</a></div>';
    $echo .= '<div class="cn_more_link" style="display: none;" id="more_link_'.$ID.'">'.$t.'</div>';

    return $echo;
}

function cn_modify_s2bb_youtube($t)
{
    $yt_url = '';

    if (preg_match('/watch.+?v\=([^&]+)/i', $t, $c))
        $yt_url = $c[1];

    if ($yt_url)
        return '<iframe width="640" height="360" src="http://www.youtube.com/embed/'.$yt_url.'?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>';

    return $t;
}

// Since 2.0.1: tag [cdata] ..[cdata].. [/cdata] save contains text
function cn_modify_s2bb_cdata($t)
{
    global $_raw_md5;

    $_raw_md5[ $raw = '<!--raw--('.md5($t).')--raw-->' ] = $t;
    return $raw;
}

// NEWS ----------------------------------------------------------------------------------------------------------------
function cn_modify_title($e)
{
    return cn_helper_smiles( cn_helper_html_text($e, 't') );
}

function cn_modify_short_story($e)
{
    return cn_helper_smiles( cn_helper_html_text($e, 's') );
}

function cn_modify_full_story($e)
{
    // Concate flag short + full
    if ($e['cc'])
    {
        $e['f'] = $e['s'] . "\n\n" . $e['f'];
    }
    // Full story not present, use short
    elseif (trim($e['f']) === '')
        $e['f'] = $e['s'];

    return cn_helper_smiles( cn_helper_html_text($e, 'f') );
}

// make date
function cn_modify_date($e)
{
    global $template;

    if ($template == 'rss')
        return date('r', $e['id']);
    else
        return date(getoption('timestamp_active'), $e['id']);
}

function cn_modify_author($e)
{
    $user = db_user_by_name($e['u']);
    if (is_null($user))
        return cn_htmlspecialchars($e['u']);

    // base username
    $username = cn_htmlspecialchars($e['u']);

    // user has nick
    if ($user['nick']) $username = cn_htmlspecialchars($user['nick']);

    // user allow to show his email?
    if ($user['e-hide'])
        return $username;
    else
        return '<a href="mailto:'.cn_htmlspecialchars($user['email']).'">'.$username.'</a>';
}

function cn_modify_author_name($e)
{
    if ($e['u'])
        return cn_htmlspecialchars($e['u']);
    else
        return '<b>author undefined</b>';
}

function cn_modify_comments_num($e)
{
    return count($e['co']);
}

function cn_modify_category($e)
{
    $ns = array();
    $cs = cn_helper_category($e);
    foreach ($cs as $cat)
        if ($cat['name'])
            $ns[] = $cat['name'];

    return cn_htmlspecialchars(join(', ', $ns));
}

function cn_modify_category_icon($e)
{
    $ns = array();
    $cs = cn_helper_category($e);
    foreach ($cs as $cat)
        if ($cat['icon'])
            $ns[] = '<img src="'.$cat['icon'].'" />';

    return join(' ', $ns);
}

function cn_modify_month($e)
{
    // Convert digit month to name
    if (!is_array($e))
        $e = array('id' => strtotime(date('Y-'.$e.'-d')));

    $mon = intval(date('m', $e['id']));
    $mons = explode(',', getoption('mon_list'));
    return $mons[$mon-1];
}

function cn_modify_weekday($e)
{
    $week = intval(date('w', $e['id']));

    $weeks = explode(',', getoption('week_list'));
    return $weeks[$week];
}

function cn_modify_year($e)
{
    return intval(date('Y', $e['id']));
}

function cn_modify_day($e)
{
    return intval(date('d', $e['id']));
}

function cn_modify_hours($e)
{
    $hour = intval(date('H', $e['id']));
    return $hour < 10 ? '0' . $hour : $hour;
}

function cn_modify_minute($e)
{
    $minute = intval(date('i', $e['id']));
    return $minute < 10 ? '0'.$minute : $minute;
}

function cn_modify_since($e)
{
    $ts = ctime();
    $diff = $ts - $e['id'];

    $out = time_since_format($diff);
    $res = '';
    foreach ($out as $id => $v)
        if ($id != 's') $res[] = "$v $id.";

    // a seconds ago...
    if (!$res) $res[] = "0 m.";

    return join(' ', $res);
}

// Since 2.0.1
function cn_modify_tagline($e)
{
    global $template, $PHP_SELF;

    $tag_extrn = strtolower(trim(REQ('tag')));

    $echo = array();
    $x = spsep($e['tg']);

    $ix = 1;
    $tc = count($x);

    foreach ($x as $tag)
    {
        $tag  = trim($tag);
        $esrc = cn_get_template('tagline', $template);

        // tag selected?
        if ($tag_extrn === strtolower($tag))
            $esrc = preg_replace('/\{tag\:selected\|(.*?)\}/i', '\\1', $esrc);
        else
            $esrc = preg_replace('/\{tag\:selected\|(.*?)\}/i', '', $esrc);

        // get url tag
        if (preg_match_all('/\{url(.*?)\}/i', $esrc, $c, PREG_SET_ORDER))
        {
            foreach ($c as $v)
            {
                $disable_rw = FALSE;
                $_phpself   = $PHP_SELF; // save php-self
                $_get       = $_GET;

                // Additional parameters
                $group = $v[1] ? cn_params(substr($v[1], 1)) : array();

                // manual php-self setting
                if (isset($group['php_self']))
                {
                    $PHP_SELF = $group['php_self'];
                    unset($group['php_self']);
                }

                // Manual rewrite disable
                if ($group[':disable_rw'])
                {
                    $disable_rw = TRUE;
                    unset($group[':disable_rw']);
                }

                // Tagline - remove ID
                unset($_GET['id']);

                if (getoption('rw_engine') && !$disable_rw)
                    $url = cn_rewrite('tag', $tag, 0, $group);
                else
                    $url = cn_url_modify("tag=$tag", array('group' => $group));

                $esrc = str_replace($v[0], $url, $esrc);
                $PHP_SELF = $_phpself;  // store php-self
                $_GET     = $_get;      // store GET
            }
        }

        if ($ix === $tc)
            $esrc = preg_replace('/\{comma\|.*?\}/is', '', $esrc);
        else
            $esrc = preg_replace('/\{comma\|(.*?)\}/is', '\\1', $esrc);

        $echo[] = str_replace('{tag}', cn_htmlspecialchars($tag), $esrc);

        $ix++;
    }

    return join('', $echo);
}

function cn_modify_page_alias($e) { return $e['pg']; }
function cn_modify_phpself() { global $PHP_SELF; return $PHP_SELF; }
function cn_modify_go_back() { return '<a href="javascript:history.go(-1)">Go back</a>'; }
function cn_modify_cute_http_path() { return getoption('http_script_dir'); }
function cn_modify_news_id($e) { return intval($e['id']); }
function cn_modify_category_ids($e) { return $e['c']; }
function cn_modify_category_id($e) { return intval($e['c']); }
function cn_modify_rss_news_include_url($e)
{
    $id = cn_put_alias($e['id']);

    if (getoption('rw_engine'))
        return cn_rewrite('rss', $id);
    else
        return getoption('#rss/news_include_url') . '?id='.$id;
}
function cn_modify_archive_id($e) { return intval($e['arch']); }

// --- Social buttons ---
function cn_modify_fb_comments($e)
{
    global $template, $allow_active_news, $PHP_SELF;

    if ($template == 'rss')
        return '';

    $unique_url = 'http://'.$_SERVER['SERVER_NAME'] . $PHP_SELF . '?id='.$e['id'];
    if (getoption('use_fbcomments') && (!$allow_active_news || $allow_active_news && getoption('fb_inactive')))
        return '<div class="fb-comments cutenews-fb-comments" data-href="'.$unique_url.'" data-num-posts="'.getoption('fb_comments').'" data-width="'.getoption('fb_box_width').'" data-colorscheme="'.getoption('fbcomments_color').'"></div>';

    return '';
}

function cn_modify_fb_like($e)
{
    global $template, $PHP_SELF;
    if ($template == 'rss')
        return '';

    $unique_url = 'http://'.$_SERVER['SERVER_NAME'] . $PHP_SELF . '?id='.$e['id'];
    if (getoption('use_fblike'))
        return '<div class="fb-like cutenews-fb-comments" data-href="'.$unique_url.'" data-send="'.(getoption('fblike_send_btn') ? "true" : "false").'" data-layout="'.getoption('fblike_style').'" data-width="'.getoption('fblike_width').'" data-show-faces="'.(getoption('fblike_show_faces')? "true" : "false").'" data-font="'.getoption('fblike_font').'" data-colorscheme="'.getoption('fblike_color').'" data-action="'.getoption('fblike_verb').'"></div>';

    return '';
}

function cn_modify_twitter($e)
{
    global $template, $PHP_SELF;

    if ($template == 'rss')
        return '';

    if (!getoption('use_twitter'))
        return '';

    $data_href    = 'http://'.$_SERVER['SERVER_NAME'] . $PHP_SELF . '?id='.$e['id'];
    $twitter_text = getoption('tw_text') ? getoption('tw_text') : cn_htmlspecialchars($e['title']);

    $i18n = getoption('i18n');
    if (!$i18n) $i18n = 'en_US';

    return '<div class="cutenews-twitter-send"><a href="https://twitter.com/share" class="twitter-share-button" data-url="'.trim($data_href).'" data-text="'.trim($twitter_text).'" data-via="'.trim(getoption('tw_via')).'" data-related="'.trim(getoption('tw_recommended')).'" data-count="'.getoption('tw_show_count').'" data-hashtags="'.trim(getoption('tw_hashtag')).'" data-lang="'.str_replace('_', '-', $i18n.'" data-size="'.(getoption('tw_large')? "large" : "medium")).'"></a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>';
}

function cn_modify_gplus($e)
{
    global $template;

    if ($template == 'rss')
        return '';

    if (getoption('use_gplus'))
        return '<div class="g-plusone" data-href="'.cn_url_modify(array('reset'), 'id=' . $e['id']).'" data-size="'.getoption('gplus_size').'" data-annotation="'.getoption('gplus_annotation').'" data-width="'.getoption('gplus_width').'"></div>';

    return '';
}

// Users online sysmod
function cn_modify_online() // Since 2.0
{
    if ($expire = getoption('client_online'))
    {
        $online = cn_touch_get('/cdata/online.php', TRUE);
        return count($online['%']);
    }

    return '';
}

function cn_modify_online_own_hits() // Since 2.0
{
    if ($expire = getoption('client_online'))
    {
        $online = cn_touch_get('/cdata/online.php', TRUE);
        return intval($online['%'][CLIENT_IP]);
    }

    return '';
}

// ---------------------------------------------------------------------------------------------------- NEWS BB LINK ---

// make simple link to full story
function cn_modify_bb_link($e, $t, $bb)
{
    list($opts) = cn_helper_bb_decode($bb);

    $id = intval($e['id']);
    $id = cn_put_alias($id);

    if (NULL === ($url = cn_rewrite('full_story', $id)))
        $url = cn_url_modify('subaction', "id=$id");

    return '<a '.$opts.'href="'.$url.'">'.$t.'</a>';
}

// make printable link
function cn_modify_bb_print($e, $t, $bb)
{
    list($opts) = cn_helper_bb_decode($bb);

    if (defined('AREA') && AREA == 'ADMIN')
        return $t;

    $id = intval($e['id']);
    $id = cn_put_alias($id);

    if (NULL === ($url = cn_rewrite('print', $id)))
        $url = getoption('http_script_dir').'/print.php?id='.$id;

    return '<a '.$opts.'href="'.$url.'">'.$t.'</a>';
}

// make comment link
function cn_modify_bb_full_link($e, $t, $bb)
{
    $action     = REQ('action', 'GPG');
    list($opts) = cn_helper_bb_decode($bb);

    if ($e['f'] == '' and $action !== 'showheadlines')
        return '<!-- no full story-->';

    $id = intval($e['id']);
    $id = cn_put_alias($id);

    if (getoption('full_popup'))
    {
        return '<a href="#" onclick="window.open(\''.getoption('http_script_dir').'/print.php?id='.$id.'&popup=news\', \'Comment news\', \''.getoption('full_popup_string').'\'); return false;">'.$t.'</a>';
    }
    else
    {
        if (NULL === ($url = cn_rewrite('full_story', $id)))
            $url = cn_url_modify("id=$id");

        return '<a '.$opts.'href="'.$url.'">'.$t.'</a>';
    }
}

function cn_modify_bb_com_link($e, $t)
{
    $id = intval($e['id']);
    $id = cn_put_alias($id);

    if (getoption('comments_popup'))
    {
        return '<a href="#" onclick="window.open(\''.getoption('http_script_dir').'/print.php?id='.$id.'&popup=comment\', \'Comment news\', \'' . getoption('comments_popup_string').'\'); return false;">'.$t.'</a>';
    }
    else
    {
        if (NULL === ($url = cn_rewrite('comments', $id)))
            $url = cn_url_modify("id=." . $id);

        return '<a href="'.$url.'">'.$t.'</a>';
    }
}

// make [edit] for administrator
function cn_modify_bb_edit($e, $t, $bb)
{
    list($opts) = cn_helper_bb_decode($bb);

    if (test('Cen'))
    {
        $URL = getoption('http_script_dir').'/index.php?mod=editnews&amp;action=editnews&amp;id='.intval($e['id']);
        return '<a '.$opts.'href="'.$URL.'">'.$t.'</a>';
    }

    return '';
}

// [truncate bb-tag]
function cn_modify_bb_truncate($e, $t, $o)
{
    $num = intval(preg_replace('/[^0-9]/', '', $o));
    return word_truncate($t, $num);
}

// make mail
function cn_modify_bb_mail($e, $t)
{
    $user = db_user_by_name($e['u']);

    if ($user['e-hide']) return $user['name'];
    return '<a href="mailto:'.$user[ 'email' ].'">'.$t.'</a>';
}

// multicategory bb-tag [cat-NUM]... [$catid] ...[/cat]
function cn_modify_bb_cat($e, $t, $c)
{
    $cw = spsep($e['c']);
    $c  = intval(substr($c, 1)) - 1;

    if (isset($cw[$c]) && $cw[$c])
        return str_replace('[$catid]', $cw[$c], $t);

    return '';
}

function cn_modify_bb_loggedin($e, $t)
{
    $user = member_get();
    if ($user) return $t;
    else return '';
}

// --- frontend accessible ---
function cn_modify_bb_img($e, $t, $bb) { return cn_modify_s2bb_img($t, $bb); }
function cn_modify_bb_more($e, $t) { return cn_modify_s2bb_more($t); }
function cn_modify_bb_youtube($e, $t) { return cn_modify_s2bb_youtube($t); }

// COMMENTS ------------------------------------------------------------------------------------------------------------
function cn_modify_comm_author($e)
{
    //in comments e-mail mast hide
    $user = db_user_by_name($e['u']);

    if (!$user) $user = array
    (
        'name'   => $e['u'],
        'email'  => $e['e'],
        'e-hide' => '',
        'nick'   => ''
    );

    // Got nick?
    $username = $user['nick'] ? $user['nick'] : $user['name'];

            // User exists
    return $user['name']?cn_htmlspecialchars($username):'Anonymous';
}

function cn_modify_comm_date($e)
{
    return date(getoption('timestamp_comment', $e['id']));
}

function cn_modify_comm_mail($e)
{
    $user = db_user_by_name($e['u']);
    return cn_htmlspecialchars($user['email']);
}

function cn_modify_comm_input_username()
{
    global $_SESS;
    $user = member_get();

    // User is authorized
    if ($user)
    {
        return '<input type="text" class="cn_comm_username" value="'.cn_htmlspecialchars($user['name']).'" disabled="disabled" />';
    }
    else
    {
        $name_input = REQ('name', 'POST') ? REQ('name', 'POST') : $_SESS['guest_name'];
        return '<input type="text" class="cn_comm_username" name="name" value="'.cn_htmlspecialchars($name_input).'"/>';
    }
}

function cn_modify_comm_input_email()
{
    global $_SESS;
    $user = member_get();

    // User is authorized
    if ($user)
    {
        return '<input type="text" class="cn_comm_email" value="'.cn_htmlspecialchars($user['email']).'" disabled="disabled" />';
    }
    else
    {
        $email_input = REQ('mail', 'POST') ? REQ('mail', 'POST') : $_SESS['guest_email'];
        return '<input type="text" name="mail" class="cn_comm_email" value="'.cn_htmlspecialchars($email_input).'"/>';
    }
}

function cn_modify_comm_input_commentbox($e)
{
    $edit_id = REQ('edit_id');
    $cm_text = REQ('comments', 'POST');

    $username    = $e['co'][$edit_id]['u'];
    $member      = member_get();
    $target_user = db_user_by_name($username);

    // Check ACL for edit
    if (test('Mes') && $username == $member['name'] || test('Meg', $target_user) || test('Mea'))
        $cm_text = str_replace('[', '&#91;', $e['co'][$edit_id]['c']);

    return '<textarea cols="40" rows="6" name="comments" class="cn_comm_textarea" id="ncomm_'.$e['id'].'">'.cn_htmlspecialchars($cm_text).'</textarea>';
}

function cn_modify_comm_smilies($e)
{
    return insert_smilies('ncomm_'.$e['id']);
}

function cn_modify_comm_remember_me()
{
    global $_SESS;
    $member_name = REQ('member_name');

    $user = member_get();
    $name = '';

    if (isset($_SESS['guest_name'])) $name = $_SESS['guest_name'];
    elseif (!is_null($user)) $name = $user['name'];

    if ($name)
    {
        return '<span class="cn_com_logged">'.i18n('Logged as').' <b>'.cn_htmlspecialchars($name).'</b></span> <span class="cn_com_forger"><a href="#" onclick="forget_me(); return false;">'.i18n('Forget me').'</a></span>';
    }
    else
    {
        $echo = '<input class="cn_comm_remember" type="checkbox" name="cn_remember_me" value="Y" /> '.i18n('Remember me').' ';
        if ($member_name) $echo .= '<span class="cn_comm_forget"><a href="#" onclick="forget_me(); return false;">'.i18n('Forget me').'</a></span>';
        return $echo;
    }
}

function cn_modify_bb_comm_captcha($e, $t)
{
    if (getoption('use_captcha') && !member_get())
        return $t;
    else
        return '';
}

function cn_modify_comm_captcha($e)
{
    $ID = 'cn_comm_captcha_'.$e['id'];
    $cpath = getoption('http_script_dir') . '/captcha.php';

    if (getoption('hide_captcha') && function_exists('imagecreatetruecolor'))
    {
        // Create obfuscated captcha
        $captcha = new SimpleCaptcha();
        $captcha->imageFormat   = 'png';
        $captcha->session_var   = 'CSW';
        $captcha->scale         = 2;
        $captcha->blur          = true;
        $captcha->resourcesPath = SERVDIR.'/core/captcha/resources';

        // Image generation
        ob_start(); $captcha->CreateImage(true); $captcha_text = ob_get_clean();

        cn_save_session(TRUE);
        $echo = '<div class="cn_comm_captcha"><img src="data:image/png;base64,'.base64_encode($captcha_text).'" /></div>';
    }
    else
    {
        $echo = '<div class="cn_comm_captcha"><a href="#" onclick="cn_get_id(\''.$ID.'\').src = \''.$cpath.'?r=\' + Math.random(); return false;"><img src="'.$cpath.'" alt="CAPTCHA, click to refresh" id="'.$ID.'"/></a>';
    }

    $echo .= '<div class="cn_comm_cinput"><input type="text" name="cm_captcha" value=""/></div>';
    return $echo;
}

function cn_modify_bb_comm_submit($e, $t)
{
    $echo = '<input type="submit" value="'.cn_htmlspecialchars($t).'" class="cn_submit_bb"/>';

    if (test('Mea') && REQ('edit_id'))
        $echo .= '<input type="submit" name="cm_edit_comment" value="Edit comment" class="cn_edit_bb"/>';

    return $echo;
}

// --
function cn_modify_comm_comment($e)
{
    return cn_helper_smiles( cn_helper_html_text($e, 'c') );
}

function cn_modify_comm_username()
{
    $user = member_get();
    return $user ? $user['name'] : '';
}

function cn_modify_comm_usermail()
{
    $user = member_get();
    return $user ? $user['email'] : '';
}

function cn_modify_comm_comment_id($e)
{
    return intval($e['id']);
}

function cn_modify_comm_comment_iteration()
{
    global $_comment_iterator;
    return intval($_comment_iterator);
}

// [comment_edit] .. [/] only for Admin or owner (registered user)
function cn_modify_bb_comm_edit($e, $t)
{
    $user = member_get();
    $edit_link = '<a href="'.cn_url_modify('id='.$_GET['id'], 'edit_id='.intval($e['id'])).'">'.$t.'</a>';

    if (test('Mes') && $e['u'] == $user['name'])
        return $edit_link;

    if (test('Mea'))
        return $edit_link;

    return '';
}

function cn_modify_bb_comm_delete($e, $t)
{
    $user = member_get();
    if (test('Mda'))
        return str_replace('%cbox', '<input type="checkbox" name="comm_delete[]" value="'.intval($e['id']).'" />', $t);

    return '';
}

function cn_modify_bb_comm_edited($e, $t)
{
    if ($e['ed'])
        return  str_replace('%edited', date(getoption('timestamp_active'), $e['ed']), $t);

    return '';
}
