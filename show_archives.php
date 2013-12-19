<?php

/**
 * @desc show_archives.php is DEPRECATED, use show_news.php instead
 * In news is [archid] field, copied by migration script
 */

require_once ('core/init.php');

// Check including & init
check_direct_including('show_archives.php');

$_list_archives = db_get_archives();

if (isset($static) && $static)
    $_archive = 0;
else
    $_archive = REQ('archive');

// Select
if (!$_archive)
{
    krsort($_list_archives);
    foreach ($_list_archives as $id => $info)
    {
        $count = intval($info['c']);

        if ($url = cn_rewrite('archive', $id))
            $arch_url = $url;
        else
            $arch_url = cn_url_modify('archive='.$id);

        echo "<a href=\"$arch_url\">".date("d M Y", $info['min']) ." &ndash; ".date("d M Y", $info['max'])." (<b>$count</b>)</a><br />";
    }

    $_found_archives = count($_list_archives);
    unset($static, $id);
}
// Delegate all deprecated archive list to show news
else
{
    include 'show_news.php';
}