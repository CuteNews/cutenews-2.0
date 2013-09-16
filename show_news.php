<?php

require_once (dirname(__FILE__).'/core/init.php');

// Quick Redirect
if (isset($_GET['cn_rewrite_url']) && $_GET['cn_rewrite_url'])
{
    // Remove request string
    unset($_GET['cn_rewrite_url']);

    // Query
    include CN_REWRITE;
    die();
}

// plugin tells us: he is fork, stop
if ( hook('fork_news', false) ) return;

// Check including & init
check_direct_including('show_news.php');

global $PHP_SELF;

// Store GET
$bGET = $_GET;

// Get external control
list($subaction, $category, $nocategory, $ucat, $id) = GET('subaction, category, nocategory, ucat, id', 'GPG');

// Sanitize variables
$category   = preg_replace('/\s/', '', $category);
$ucat       = preg_replace('/\s/', '', $ucat);

cn_extrn_init();
hook('show_news/social_init');

// Decoding requested categories
list ($requested_cats, $is_in_category) = cn_get_requested_cats($category, $ucat, $nocategory);

// Allowed modules
$allow_add_comment  = false;
$allow_full_story   = false;
$allow_active_news  = false;
$allow_comments     = false;

// Short urls [only id]
if ($subaction == '' && $id) $subaction = 'showfull';

// ID starts from 'c' symbol
if ($id[0] === '.')
{
    $subaction = 'showcomments';
    $_GET['id'] = $_POST['id'] = $id = substr($id, 1);
}

// Show news only is in category
if ($is_in_category && empty($CN_HALT))
{
    // --- Determine what user want to do ---
    hook('show_news/determs_before');

    if (empty($static) and in_array($subaction, array("showcomments", "showfull", "addcomment", "only_comments")))
    {
        if ($subaction == "addcomment")
        {
            $allow_add_comment  = true;
            $allow_comments     = true;
        }
        elseif ($subaction == "showcomments")
        {
            $allow_comments     = true;
        }
        elseif ($subaction == "showfull")
        {
            $allow_full_story   = true;
        }

        // Additional tuning
        if (($subaction == "showcomments" || $allow_comments == true) && getoption('show_full_with_comments'))
            $allow_full_story = true;

        if ($subaction == "showfull" && getoption('show_comments_with_full'))
            $allow_comments = true;

        // For popup
        if ($subaction == "only_comments")
        {
            $allow_comments     = true;
            $allow_full_story   = false;
        }
    }
    else
    {
        $allow_active_news = true;
    }

    hook('show_news/determs_after');

    // Main operations
    include SERVDIR . '/core/includes/dispatcher.php';
}

// Unset all used variables
unset ($dir, $sortby, $archive, $only_active, $no_prev, $no_next, $category, $nocat, $id);
unset ($template, $number, $start_from, $requested_cats, $reverse, $page_alias, $static, $subaction, $translate);

// Restore GET
$_GET = $bGET;

echo '<!-- News Powered by CuteNews: http://cutephp.com/ -->';