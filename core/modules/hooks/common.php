<?php

// Since 2.0.1: Raw extends
function cn_extrn_raw_template($template, $apply_patch = NULL)
{
    global $_raw_md5;

    $variables = array();

    // Template <RAW>
    if (preg_match_all('/<\!\-\-RAW\-\-(.*?)\-\-RAW\-\->/s', $template, $cp, PREG_SET_ORDER))
    {
        foreach ($cp as $out)
        {
            if (is_null($apply_patch))
            {
                $md5 = md5($out[0]);
                $variables[$md5] = $out[1];

                $template = str_replace($out[0], '<!--RAW--('.$md5.')--RAW-->', $template);
            }
            else
            {
                $md5 = substr($out[1], 1, -1);
                $template = str_replace('<!--RAW--('.$md5.')--RAW-->', $apply_patch[$md5], $template);
            }
        }
    }

    // Text <raw> convers
    if (is_null($apply_patch))
    {
        $template = str_replace(array_keys($_raw_md5), array_values($_raw_md5), $template);
        unset($_raw_md5);
    }

    return array($template, $variables);
}

// Since 2.0: Replace words
function cn_extrn_replace($input)
{
    if (!getoption('use_replacement'))
    {
        return $input;
    }
    
    if ($rword = getoption('#rword'))
    {
        foreach ($rword as $f => $t)
        {
            $input = preg_replace('/'.preg_sanitize($f).'/i', trim($t), $input);
        }
    }

    return $input;
}

// Since 2.0: Do replace more fields
function cn_extrn_morefields($t, $e)
{    
    $mb = member_get($e['u']);

    // Personal info
    if (preg_match_all('/\{personal\-(.*?)\}/i', $t, $c, PREG_SET_ORDER))
    {
        foreach ($c as $v)
        {
            if (isset($mb['more'][$v[1]])) 
            {
                $r = $mb['more'][$v[1]]; 
            }
            else 
            {
                $r = '';
            }
            $t = str_replace($v[0], cn_htmlspecialchars($r), $t);
        }
    }
    
    if(isset($e['mf']))
    {
        $mf = join('|', array_keys($e['mf']));
        // Common purpose more fields
        if (preg_match_all('/\{('.$mf.')\}/i', $t, $c, PREG_SET_ORDER))
        {
            foreach ($c as $v)
            {
                $t = str_replace($v[0], $e['mf'][$v[1]], $t);
            }
        }
    }
    return $t;
}

// Since 2.0: Catch recursive [if]...[/if] blocks
function cn_extrn_if_cond($template)
{
    $MAX_LEVEL = 8;

    for ($i = 0; $i < $MAX_LEVEL; $i++)
    {
        if (preg_match_all('/\[if (.*?)\](.*)\[\/if\]/i', $template, $cp, PREG_SET_ORDER))
        {
            foreach ($cp as $ifc)
            {
                $R = TRUE;

                $cond   = trim($ifc[1]);
                $cond_s = explode('&&', $cond);

                // Check AND parts
                foreach ($cond_s as $cond)
                {
                    $cond = trim($cond);
                    if ($cond[0] === '!')
                    {
                        if (substr($cond, 1)) { $R = FALSE; break; }
                    }
                    elseif (!$cond) { $R = FALSE; break; }
                }

                $template = str_replace($ifc[0], $R ? $ifc[2] : '', $template);
            }
        }
        else break;
    }

    return $template;
}

// Since 2.0: Initialize & require scripts
function cn_extrn_init()
{
    global $template;

    if ($template == 'rss')
    {
        return;
    }

    $i18n = getoption('i18n');
    if (!$i18n) 
    {
        $i18n = 'en_US';
    }

    // Facebook initialzie
    if ((getoption('use_fbcomments') || getoption('use_fblike')) && !mcache_get('fb_js_on') && $template != 'rss')
    {
        echo str_replace( array('{appID}', '{fbi18n}'), array(getoption('fb_appid'), str_replace('-', '_', $i18n)), read_tpl('fb_comments'));
        mcache_set('fb_js_on', true);
    }

    // Definition G+ code uses
    if (getoption('use_gplus') && !mcache_get('gplus_js_on') && $template != 'rss')
    {
        echo str_replace( '{lang}', $i18n, read_tpl('google_plus'));
        mcache_set('gplus_js_on', true);
    }

    // First init CN script
    if (!mcache_get('cn:extr_init'))
    {
        echo preg_replace('/\s{2,}/s', ' ', read_tpl('cnscript'));
        mcache_set('cn:extr_init', true);
    }

}

// hook for widget
function login_guest($keep_data = NULL, $username = NULL)
{
    global $_SESS;

    cn_extrn_init();

    // Logout
    if (isset($_GET['widget_personal_logout']))
    {
        $_SESSION = array();
    }
    
    // Send new data
    $_SESSION['.CSRF'] = md5(mt_rand());

    if (!member_get())
    {
        // Widget's login form
        echo proc_tpl('widgets/personal_login_form',
            "CSRF=".$_SESSION['.CSRF'],
            'KEEP='.base64_encode(serialize($keep_data)),
            'MSG='.cn_front_msg_show('login', 'widget_personal_msg'),
            'username='.$username,
            'rememberme='.(isset($_POST['cn_remember_me']) && !empty($_POST['cn_remember_me'])?'checked':'')
            );
    }
}

/* ---------------------------------------------
 * Initialize & process widgets
 * --------------------------------------------- */

// Keep personal $_POST data for login. Do login.
if (isset($_POST['widget_personal_keep']))
{
    global $_SESS;

    $_REQ = $_POST;
    $_POST = unserialize(base64_decode($_POST['widget_personal_keep']));

    // Try authorize
    if ($_REQ['widget_personal_action'] === 'login')
    {
        if (!isset($_SESSION['.CSRF'])) { $_SESSION['.CSRF'] = ''; }
        if ($_REQ['widget_personal_csrf'] && $_REQ['widget_personal_csrf'] === $_SESSION['.CSRF'])
        {
            $login = $_REQ['widget_personal_username'];
            $pass  = $_REQ['widget_personal_password'];
            $rem   = isset($_REQ['widget_personal_rememberme'])&&!empty($_REQ['widget_personal_rememberme']);

            // Get User Session
            $_SESSION['user'] = $login;
            $user = member_get();

            if ($user['acl'] == ACL_LEVEL_ADMIN)
            {
                cn_front_message("Admin login denied from this place", 'login');                
                $_SESSION['user'] = null;
            }
            elseif ($login && $pass)
            {                          
                $gp = hash_generate($pass);                
                if (in_array($user['pass'], $gp))
                {
                    $_SESSION['user'] = $login;
                    if ($rem) { $_POST['cn_remember_me'] = $rem; }
                    $_POST['CN_COOKIE_POSTPROCESS'] = TRUE;                    
                }
                else
                {
                    $_SESSION['user'] = null;
                    cn_front_message('Invalid login or password', 'login');
                }                
            }
            else
            {
                $_SESSION['user'] = null;
            }
        }
        else 
        {
            cn_front_message("CSRF attempt!", 'login');                
            $_SESSION['user'] = null;
        }
            
    }
}
