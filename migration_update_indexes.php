<?php

include dirname(__FILE__).'/core/init.php';

/**
 * @desc Update indexes
 * @warning Call directy this file and REMOVE after!
 */

$FlatDB = new FlatDB();

// Clean unsuccessful
@unlink(SERVDIR . '/cdata/news/cat.idx');
@unlink(SERVDIR . '/cdata/news/date.idx');
@unlink(SERVDIR . '/cdata/news/so_news.idx');
@unlink(SERVDIR . '/cdata/news/tags.idx');
@unlink(SERVDIR . '/cdata/news/users.idx');

// Scan news file
$files = scan_dir(SERVDIR.'/cdata/news', '\d+-\d+-\d+');

// Add overall news
foreach ($files as $news_file)
{
    $dt = cn_touch_get("/cdata/news/$news_file");
    foreach ($dt as $id => $data)
    {
        $FlatDB->cn_update_date($id);
        $FlatDB->cn_user_sync($data['u'], $id);
        $FlatDB->cn_add_tags($data['tg'], $id);
        $FlatDB->cn_add_categories($data['c'], $id);
    }
}

echo "UPDATE SUCCESS. REMOVE THIS FILE AFTER!";