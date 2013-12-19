<?php

require_once ('core/init.php');

// plugin tells us: he is fork, stop
if ( hook('fork_search', false) ) return;

// Check including
check_direct_including('search.php');

// Variables by default
list($template, $dosearch, $search, $user, $archives) = GET('template, dosearch, search, user, archives');
list($search_st, $number) = GET('search_st, number');
list($_fd, $_fm, $_fy) = GET('from_date_day, from_date_month, from_date_year');
list($_td, $_tm, $_ty) = GET('to_date_day, to_date_month, to_date_year');

// Default date range
if ($_fm && $_fd && $_fy)
    $date_from = mktime(0, 0, 0, intval($_fm), intval($_fd), intval($_fy));
else
    $date_from = ctime() - 3600*24*365*3;

if ($_tm && $_td && $_ty)
    $date_to = mktime(0, 0, 0, intval($_tm), intval($_td), intval($_ty));
else
    $date_to = ctime() + 3600*24*365*3;

$search_st = intval($search_st);
$number    = intval($number);

if (!$template) $template = "Default";
if (!$number) $number = 25;

$form = cn_get_template('search', $template);
$form = str_replace('{php_self}', PHP_SELF, $form);

// Basic Input Box
$form = str_replace('{search_basic}', '<input type="hidden" name="template" value="'.cn_htmlspecialchars(REQ('template')).'" /><input type="hidden" name="dosearch" value="yes" /><input class="cn_search_basic" type="text" name="search" value="'.cn_htmlspecialchars($search).'" />', $form);
$form = str_replace('{author}', '<input class="cn_author" type="text" name="user" value="'.cn_htmlspecialchars($user).'" />', $form);
$form = str_replace('{in_archives}', '<input class="cn_in_archives" type="checkbox" name="archives" value="Y" '.($archives ? 'checked' : '').' />', $form);

// Make submit button
$form = preg_replace('/\[submit\](.*?)\[\/submit\]/', '<input class="cn_submit" type="submit" value="\\1" />', $form);

// Hidden fields placeholder [hid=name] --> input:hidden
if (preg_match_all('/\[hid=(.*?)\]/i', $form, $c, PREG_SET_ORDER)) foreach ($c as $v)
    $form = str_replace($v[0], '<input type="hidden" name="'.$v[1].'" value="'.REQ($v[1]).'" />', $form);

// Date selection
list($_df, $_mf, $_yf) = make_postponed_date($date_from);
list($_dt, $_mt, $_yt) = make_postponed_date($date_to);

$_ds = array
(
    '{select=year:from}' => array('from_date_year', $_yf),
    '{select=mon:from}' => array('from_date_month', $_mf),
    '{select=day:from}' => array('from_date_day', $_df),
    '{select=year:to}' => array('to_date_year', $_yt),
    '{select=mon:to}' => array('to_date_month', $_mt),
    '{select=day:to}' => array('to_date_day', $_dt),
);

foreach ($_ds as $id => $opt)
    $form = str_replace($id, '<select name="'.$opt[0].'" class="cn_'.$opt[0].'">'.$opt[1].'</select>', $form);

echo $form;

// ---------------------------------------------------------------------------------------------------------------------
if ($dosearch)
{
    $mc_start = microtime(true);

    // Remove parameters for go to news from searchbox
    $_static_qr = 'dosearch,archives,search,from_date_year,from_date_month,from_date_day,to_date_year,to_date_month,to_date_day,search_st,number,archive,template';

    $st = -1;
    $_next_link = FALSE;
    $_number    = $number;
    $archive_id = 0;

    // get archive list
    $_list_archives = db_get_archives();
    krsort($_list_archives);
    reset($_list_archives);

    $news = db_index_load('');
    $c_time = ctime();

    if (strlen($search) < 3)
    {
        echo "<div>Too short request!</div>";
    }
    else
    {
        $block = '';
        $found = 0;

        do
        {
            reset($news);

            // repeat, while data exists
            while ($news)
            {
                // pop top element
                $id = key($news);
                unset($news[$id]);

                if ($id > $c_time) continue;
                if ($id < $date_from || $id > $date_to) continue;

                $nbp = db_get_nloc($id);
                if ($block !== $nbp)
                    $ent = db_news_load($block = $nbp);

                // @syslog internal error
                if (empty($ent)) continue;

                $item = $ent[$id];

                $FN = FALSE;
                $Fs = $item['f'];
                $Ss = $item['s'];

                $_query = spsep($search, ' ');
                foreach ($_query as $vq) if (strpos($Fs, $vq) !== FALSE || strpos($Ss, $vq) !== FALSE) $FN = TRUE;

                if ($user && !$user != $item['u']) $FN = FALSE;
                if (!$FN) continue;

                $st++;
                if ($st < $search_st) continue;

                if ($found == 0)
                    echo "<p class='cutenews_found_news'>".i18n('Search results for')." &quot;".cn_htmlspecialchars($search)."&quot;</p><div class='cn_search_body'>";

                $found++;
                $title = cn_htmlspecialchars($item['t']);

                // Call: id, archiveid, template
                if (getoption('rw_engine'))
                {
                    $url = cn_rewrite('full_story', $id);
                    if (getoption('search_hl')) $url .= "?qhl=".urlencode($search);
                }
                else
                {
                    if (getoption('search_hl'))
                        $url = cn_url_modify($_static_qr, 'id='.$id, "qhl=".urlencode($search));
                    else
                        $url = cn_url_modify($_static_qr, 'id='.$id);
                }

                echo "<div class='cutenews_search_item'>$itemid <b><a href='$url'>$title</a></b> (". date("d F, Y", $id) .")</div>";

                $_number--;
                if ($_number == 0)
                {
                    $_next_link = TRUE;
                    break 2;
                }
            }

            // Next archive, if present
            if ($archives && count($_list_archives))
            {
                $archive_id = key($_list_archives);

                // Load ID from archives
                $news = array_keys(db_index_load("archive-$archive_id"));
                krsort($news);

                unset($_list_archives[$archive_id]);
            }
            // Only in active news, or no archives left
            else break;
        }
        while (TRUE);

        // show results ------------
        if (!$found)
        {
            echo "<p class='cutenews_not_match'>".i18n('There are no news articles matching your search criteria')."</p>";
        }
        else
        {   // Close "cn_search_body"
            echo "</div>";
        }

        echo '<div class="cn_paginate_search">';
        if ($search_st - $number >= 0 && $number) echo ' <a href="'.cn_url_modify('search_st='.($search_st - $number)).'">&lt;&lt; Prev</a> ';
        if ($search_st) echo ' (skip <b class="search_skip">'.$search_st.'</b> items) ';
        if ($_next_link) echo ' <a href="'.cn_url_modify('search_st='.($search_st + $number)).'">Next &gt;&gt;</a>';
        echo '</div>';
    }

    echo '<p class="cutenews_search_results"><i>'.i18n('Search performed for').' '.round(microtime(true) - $mc_start, 4).' s.</i></p>';
}

return TRUE;