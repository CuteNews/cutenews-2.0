<?php if (!defined('EXEC_TIME')) die('Access restricted');

// hooks
add_hook('index/invoke_module', '*add_news_invoke');

function add_news_invoke()
{
    // loadall
    list($article_type, $preview) = GET('postpone_draft, preview', 'GETPOST');
    list($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year) = GET('from_date_hour, from_date_minutes, from_date_seconds, from_date_month, from_date_day, from_date_year', 'GETPOST');
    list($title, $page, $category, $short_story, $full_story, $if_use_html, $vConcat, $vTags, $faddm) = GET('title, page, category, short_story, full_story, if_use_html, concat, tags, faddm', 'GETPOST');

    $categories = cn_get_categories();
    list($morefields) = cn_get_more_fields($faddm);

    // Prepare data to add new item
    if (request_type('POST'))
    {
        cn_dsi_check();

        if (!preg_match("~^[0-9]{1,}$~", $from_date_hour) or !preg_match("~^[0-9]{1,}$~", $from_date_minutes) or !preg_match("~^[0-9]{1,}$~", $from_date_seconds))
            cn_throw_message( "You want to add article, but the hour format is invalid.", 'e');

        // create publish time
        $c_time = mktime($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year);

        // flat category to array
        if ($category == '') $category = array();
        elseif (!is_array($category)) $category = array($category);

        // article is draft?
        if ($article_type == 'draft') $draft = 1; else $draft = 0;
        $if_use_html = $if_use_html ? TRUE : (getoption('use_wysiwyg') ? TRUE : FALSE);

        // draft, if Behavior Draft is set
        if (test('Bd')) $draft = 1;

        // sanitize page name
        $page = preg_replace('/[^a-z0-9_]/i', '-', $page);

        // basic news
        $member = member_get();

        $entry = array();
        $entry['id'] = $c_time;
        $entry['t']  = $title;
        $entry['u']  = $member['name'];
        $entry['c']  = news_make_category($category);
        $entry['s']  = $short_story;
        $entry['f']  = $full_story;
        $entry['ht'] = $if_use_html;
        $entry['st'] = $draft ? 'd' : '';
        $entry['co'] = array(); // 0 comments
        $entry['cc'] = $vConcat ? TRUE : FALSE;
        $entry['tg'] = $vTags;
        $entry['pg'] = $page;

        // Check page alias for exists
        if ($page && bt_get_id($page, 'pg_ts'))
        {
            cn_throw_message('Page alias already exists', 'e');
        }
        else
        {
            // Add page alias
            bt_set_id($page, $c_time, 'pg_ts');
            bt_set_id($c_time, $page, 'ts_pg');

            // Get latest id for news
            $latest_id = intval( bt_get_id('latest_id', 'conf') );
            $latest_id++;

            bt_set_id($latest_id, $c_time, 'nid_ts');
            bt_set_id($c_time, $latest_id, 'nts_id');
            bt_set_id('latest_id', $latest_id, 'conf');

            // apply more field
            list($entry, $disallow_message) = cn_more_fields_apply($entry, $faddm);

            // has message from function
            if ($disallow_message)
                cn_throw_message($disallow_message, 'e');
        }

        // ----
        if (!$preview)
        {
            if (!getoption('disable_title') && empty($title))
                cn_throw_message('The title cannot be blank', 'e');

            if (!getoption('disable_short') && empty($short_story))
                cn_throw_message('The story cannot be blank', 'e');

            // no errors in a[rticle] area
            if (cn_get_message('e', 'c') == 0)
            {
                $sc = $draft ? 'draft' : '';
                $es = db_news_load( db_get_nloc($entry['id']) );

                // make unique id
                while (isset($es[$c_time])) $c_time++;

                // override ts
                $entry['id'] = $c_time;

                // add default group permission
                $member = member_get();

                // add to database
                $es[$c_time] = $entry;

                // do save item
                db_save_news($es, db_get_nloc($c_time));

                // add news to index
                db_index_add($c_time, $entry['c'], $member['id'], $sc);

                // increase user count written news
                $cnt = intval($member['cnt']) + 1;
                db_user_update($member['name'], "cnt=$cnt");

                // do update meta-index
                db_index_update_overall($sc);
                db_update_aux($entry, 'add');

                // Notify for unapproved
                if (getoption('notify_status') && getoption('notify_unapproved') && test('Bs'))
                {
                    cn_send_mail
                    (
                        getoption('notify_email'), i18n('CuteNews unapproved article was added'),
                        "CuteNews - Unapproved article was added CuUnArWaAd",
                        cn_replace_text( cn_get_template('notify_unapproved', 'mail'), '%username%, %article_title%', $member['name'], $title )
                    );
                }

                // view in editor
                cn_relocation(PHP_SELF.'?mod=editnews&action=editnews&id='.$c_time.'&m=added');
            }
        }
        // show news preview
        else cn_assign('preview_html', entry_make($entry, 'active') );
    }

    // -----------------------------------------------------------------------------------------------------------------

    cn_assign('categories, vCategory, vTitle, vShort, vFull, vUseHtml, vConcat, vTags, morefields',
              $categories, $category, $title, $short_story, $full_story, $if_use_html, $vConcat, $vTags, $morefields);

    // ---
    echoheader("addedit@addedit/main.css", i18n("Add News")); echo exec_tpl('addedit/main'); echofooter();
}