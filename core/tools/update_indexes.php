<?php

define('EXEC_TIME', TRUE);
ini_set('max_execution_time', 0);

echo "START\n";
echo "-----\n";

// Include core
include dirname(__FILE__).'/../core.php';

// CDATA home path
$cdata_dir = realpath(dirname(__FILE__) . '/../..') . '/cdata';

// Fetch all news
$dir = scan_dir($dir_news = "$cdata_dir/news");

// Total News
$news = array();
$arch = array();
$data = array();

// Automatic archive less than 30 days
$lowest_time = time() -  30*86400;

foreach ($dir as $filename)
{
    if (preg_match('/\d+-\d+-\d+/', $filename))
    {
        $file = cn_touch_get($target = "$dir_news/$filename");

        // Remove empty files
        if (empty($file))
        {
            unlink($target);
            echo "$filename\n";
        }
        else
        {
            foreach ($file as $id => $item)
            {
                $date = date('Y-m-d', $id);

                // user | category | tag
                $data[$id] = "$id|" . base64_encode($item['u']) . '|' .  base64_encode($item['c']) . '|' .  base64_encode($item['tg']);

                if ($id < $lowest_time)
                {
                    $arch[date('Y-m', $id)][] = $id;
                }
                else
                {
                    $news[$id] = $date;
                }
            }
        }
    }
}

krsort($news);

// News and postponed
// -----------------------------------------
$wa = fopen("$dir_news/news.txt", "w");
foreach ($news as $id => $date)
{
    fwrite($wa, $data[$id]."\n");
}
fclose($wa);

// Save archives
// -----------------------------------------
$wa = fopen("$dir_news/archives.txt", "w");
foreach ($arch as $date => $nlist)
{
    arsort($nlist);

    $time = strtotime("$date-01");
    $w = fopen("$dir_news/news-$time.txt", "w");
    foreach ($nlist as $id)
    {
        fwrite($w, $data[$id]."\n");
    }
    fclose($w);


    fwrite($wa, "$time|" . count($nlist) . "\n");

}
fclose($wa);


// users | tag