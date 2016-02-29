<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

global $PHP_SELF;

// Only internal inclusion
global $translate;

/**
 * @INPUT variables, used in this module
 * @Basic $template, $requested_cats, $archive, $static, $reverse, $start_from, $only_active, $number
 * @Aux $sortby, $dir
 */

// External&Internal inclusion
list($dir, $sortby, $archive, $only_active, $no_prev, $no_next, $user_by, $tag, $nocat) = GET('dir, sortby, archive, only_active, no_prev, no_next, user_by, tag, nocat', 'GPG');
list($template, $number, $start_from, $requested_cats, $reverse, $page_alias, $static, $static_path) = GET('template, number, start_from, requested_cats, reverse, page_alias, static, static_path', 'GPG');

$number     = intval($number);
$archive    = preg_replace('/[^0-9]/', '', $archive);
$start_from = intval($start_from);

// Set default vars
if (!$template)
{
    $template = 'Default';
}

if (!$number)
{
    $number = getoption('active_news_def');
}

if (!is_array($requested_cats))
{
    $requested_cats = array();
}

if (!is_array($translate))
{
    $translate = array();
}

if ($user_by)
{
    $user_by = spsep($user_by);
}

if ($static_path)
{
    $PHP_SELF = $static_path;
}

// Backup for PHP_SELF
$_bc_PHP_SELF = $PHP_SELF;

// Reverse news (by user, or site option)
if ($reverse || getoption('reverse_active'))
{
    $sortby = 'ASC';
    $dir = 'R';
}

// Override site option by user
if (getoption('reverse_active') && !is_null($reverse) && !$reverse)
{
    $dir = '';
}

// Cancel some param by static
if ($static) 
{
    $start_from = 0;
}

// Select source
$_source = 'A2';
if ($archive) 
{
    $_source = 'archive';
}
elseif ($only_active) 
{
    $_source = '';
}

// Make settings
$opts = array
(
    'source'     => $_source,

    'sort'       => $sortby,
    'dir'        => $dir,
    'start'      => $start_from,
    
    'per_page'   => $number,
    'archive_id' => $archive,
    'nocat'      => $nocat,

    'tag'        => $tag,
    'cfilter'    => array_keys($requested_cats),
    'ufilter'    => $user_by,
    'page_alias' => $page_alias,
    'only_active'=> $only_active
);

list($entries, $rs) = cn_get_news($opts);

// Count showed rows
$_cn = $rs['overall'];

if (empty($entries)) { $_cn = 0; }

// Check end of list
$echo = array();

// Delete unused params from GET-request for active news
cn_rm_GET('no_prev,no_next,source,number,start_from,reverse,static,sortby,dir,per_page,category,nocategory,page_alias,only_active,user_by');

// show news (include arhives, too)
foreach ($entries as $entry)
{
    cn_translate_active_news($entry, $translate);    
    $echo[] = entry_make($entry, 'active', $template);
}

// No Entries
if (($_show_rows = count($echo)) == 0) 
{
    echo i18n('No entries to show');
}

// Re-Request this parameters for news listing
cn_set_GET('source,number,start_from,reverse,static,sortby,dir,per_page,archive,category,nocategory,ucat,template=Default,page_alias,only_active,user_by');

// Show news list
echo join('', $echo);

// Get config
$_enable_pagination = getoption('disable_pagination') ? FALSE : TRUE;

// Enable ignore rewrite for pagination
$_ignore_rewrite    = isset($ignore_rewrite) && $ignore_rewrite ? true : false;
$ignore_rewrite     = false;

// No pagination, if showed rows less than number
if ($_cn <= $number || !$number)
{
    $_enable_pagination = FALSE;
}

// in case of pagination
if ($_enable_pagination)
{    
    $PSTF = array('category' => '');
    $out = cn_get_template('prev_next', $template);

    // <!--- PREV
    $_prev_num = $start_from - $number;

    // Back to previous page
    if ($_prev_num >= 0)
    {
        if (getoption('rw_engine') && !$_ignore_rewrite)
        {
            if ($tag)
            {
                $url = cn_rewrite('tag', $tag, $_prev_num, $PSTF);
            }
            else
            {
                $url = cn_rewrite('list', $_prev_num, $archive, $PSTF);
            }
        }
        else 
        {
            $url = cn_url_modify("start_from=$_prev_num");
        }

        $PREV = '<a class="cn-previous-news" href="'.$url.'">\\1</a>';        
    }
    // Back to first page
    elseif ( $start_from && $_prev_num>0 )
    {
        if (getoption('rw_engine') && !$_ignore_rewrite)
        {
            if ($tag)
            {
                $url = cn_rewrite('tag', $tag, 0, $PSTF);
            }
            else
            {
                $url = cn_rewrite('list', 0, $archive, $PSTF);
            }
        }
        else 
        {
            $url = cn_url_modify('start_from');
        }

        $PREV = '<a class="cn-previous-news" href="'.$url.'">\\1</a>';        
    }
    else
    {
        $PREV = '\\1';
    }

    // NEXT --->
    if ($number && ($start_from + $number < $_cn))
    {
        $_next_num = $start_from + $number;

        if (getoption('rw_engine') && !$_ignore_rewrite)
        {
            if ($tag)
            {
                $url = cn_rewrite('tag', $tag, $_next_num, $PSTF);
            }
            else
            {
                $url = cn_rewrite('list', $_next_num, $archive, $PSTF);
            }
        }
        else 
        {
            $url = cn_url_modify("start_from=$_next_num");
        }

        $NEXT = '<a class="cn-next-news" href="'.$url.'">\\1</a>';
    }
    else 
    {
        $NEXT = '\\1';
    }

    // Settings for Prev and Next
    if ($no_prev) 
    {
        $PREV = '';
    }
    if ($no_next) 
    {
        $NEXT = '';
    }

    // One is has
    if (!$no_prev || !$no_next)
    {
        $links  = '';
        $out = preg_replace('/\[prev\-link\](.*)\[\/prev\-link\]/is', $PREV, $out);
        $out = preg_replace('/\[next\-link\](.*)\[\/next\-link\]/is', $NEXT, $out);

        // Get page count, if $number is present
        $pages  = $number ? (intval($_cn / $number) + (($_cn % $number == 0) ? 0 : 1)) : 0;        
        // Limits
        $limit_skip   = false;
        $page_limits  = getoption('pagination_pages') ? getoption('pagination_pages') : 10;
        $current_page = intval($start_from / $number);        
        $limit_left   = $current_page - $page_limits;
        $limit_right  = $current_page + $page_limits;

        for ($i = 0; $i < $pages; $i++)
        {
            $_next_num = $number * $i;

            if (getoption('rw_engine') && !$_ignore_rewrite)
            {
                if ($tag)
                {
                    $url = cn_rewrite('tag', $tag, $_next_num, $PSTF);
                }
                else
                {
                    $url = cn_rewrite('list', $_next_num, $archive, $PSTF);
                }
            }
            else
            {
                $url = cn_url_modify("start_from=$_next_num");
            }

            if ($limit_skip == false && ($limit_left >= $i || $i > $limit_right))
            {
                $links .= '<span class="cn-page-skip">...</span> ';
                $limit_skip = true;
            }
            elseif ($i != $current_page) //$start_from != $_next_num
            {                
                $links .= '<a class="cn-page-news" href="'.$url.'">'.($i+1).'</a> ';
                $limit_skip = false;
            }
            else
            {
                $links .= '<span class="cn-current-page-news">'.($i+1).'</span> ';
                $limit_skip = false;
            }
        }
        
        $out = str_replace('{pages}', $links, $out);
        echo $out;
    }
}
