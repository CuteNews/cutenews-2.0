<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

// hooks
add_hook('index/invoke_module', '*add_news_invoke');

function add_news_invoke()
{
    $FlatDB = new FlatDB();

    cn_bc_add('Dashboard', PHP_SELF . '?mod=main');
    cn_bc_add('News listing', PHP_SELF . '?mod=editnews');
    cn_bc_add('Add new');

    // loadall
    list($article_type, $preview) = GET('postpone_draft, preview', 'GETPOST');    
    list($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year) = GET('from_date_hour, from_date_minutes, from_date_seconds, from_date_month, from_date_day, from_date_year', 'GETPOST');
    list($title, $page, $category, $short_story, $full_story, $if_use_html, $vConcat, $vTags, $faddm) = GET('title, page, category, short_story, full_story, if_use_html, concat, tags, faddm', 'GETPOST');
    $categories = cn_get_categories(false);
    list($morefields) = cn_get_more_fields($faddm);
    
    $is_active_html =test('Csr');

    // Prepare data to add new item
    if (request_type('POST'))
    {        
        cn_dsi_check();
        
        if (!preg_match("~^[0-9]{1,}$~", $from_date_hour) or 
            !preg_match("~^[0-9]{1,}$~", $from_date_minutes) or 
            !preg_match("~^[0-9]{1,}$~", $from_date_seconds))
        {
            cn_throw_message( "You want to add article, but the hour format is invalid.", 'e');
        }
        
        // create publish time
        $c_time = mktime($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year);

        // flat category to array
        if ($category == '') 
        {       
            $category = array();
        }
        elseif (!is_array($category))
        {
            $category = array($category);
        }

        // article is draft?
        if ($article_type == 'draft') 
        {
            $draft = 1; 
        }
        else 
        {
            $draft = 0;
        }
        
        $if_use_html =$if_use_html ? TRUE : (getoption('use_wysiwyg') ? TRUE : FALSE);

        // draft, if Behavior Draft is set
        if (test('Bd')) 
        {
            $draft = 1;
        }

        // sanitize page name
        $page = preg_replace('/[^a-z0-9_\.]/i', '-', $page);
        if(empty($page)&& getoption('auto_news_alias'))
        {
            $page=  strtolower(preg_replace('/[^a-z0-9_\.]/i', '-',  cn_transliterate($title)));
        }
        
        // basic news
        $member = member_get();

        $entry = array();
        $entry['id'] = $c_time;
        $entry['t']  = cn_htmlclear($title);     
        $entry['u']  = $member['name'];
        $entry['c']  = news_make_category($category);
        $entry['s']  = cn_htmlclear($short_story);
        $entry['f']  = cn_htmlclear($full_story);
        $entry['ht'] = $if_use_html;
        $entry['st'] = $draft ? 'd' : '';
        $entry['co'] = array(); // 0 comments
        $entry['cc'] = $vConcat ? TRUE : FALSE;
        $entry['tg'] = strip_tags($vTags);
        $entry['pg'] = $page;

        // Check page alias for exists
        if ($page && bt_get_id($page, 'pg_ts') && !$preview)
        {
            cn_throw_message('Page alias already exists', 'e');
        }
        else
        {
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
            {
                cn_throw_message($disallow_message, 'e');
            }
        }

        // ----
        if (!$preview)
        {
            if (!getoption('disable_title') && empty($title))
            {
                cn_throw_message('The title cannot be blank', 'e');
            }
             
            if (getoption('news_title_max_long') && strlen($title)>getoption('news_title_max_long'))
            {
                cn_throw_message ('The title cannon be greater then '.getoption('news_title_max_long').' charecters','e');            
            }
            
            if (!getoption('disable_short') && empty($short_story))
            {
                cn_throw_message('The story cannot be blank', 'e');
            }

            // no errors in a[rticle] area
            if (cn_get_message('e', 'c') == 0)
            {
                // Add page alias
                bt_set_id($page, $c_time, 'pg_ts');
                bt_set_id($c_time, $page, 'ts_pg');                
                
                $sc = $draft ? 'draft' : '';
                $es = db_news_load( db_get_nloc($entry['id']) );

                // make unique id
                while (isset($es[$c_time])) 
                {
                    $c_time++;
                }

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

                // ------------------------

                $FlatDB->cn_update_date($c_time, 0);
                $FlatDB->cn_source_update($c_time, $draft ? 'D' : '');
                $FlatDB->cn_add_categories($entry['c'], $c_time);
                $FlatDB->cn_add_tags($entry['tg'], $c_time);
                $FlatDB->cn_user_sync($entry['u'], $c_time);

                // ------------------------
                
                // increase user count written news
                $cnt = intval($member['cnt']) + 1;
                db_user_update($member['name'], "cnt=$cnt");

                // do update meta-index
                db_index_update_overall($sc);

                // Notify for unapproved
                if (getoption('notify_unapproved') && test('Bd'))
                {
                    cn_send_mail
                    (
                        getoption('notify_email'), i18n('CuteNews unapproved article was added'),
                        "CuteNews - Unapproved article was added CuUnArWaAd",
                        cn_replace_text( cn_get_template('notify_unapproved', 'mail'), '%username%, %article_title%', $member['name'], $title )
                    );
                }

                $FlatDB->cache_clean();

                // view in editor
                cn_relocation(PHP_SELF.'?mod=editnews&action=editnews&id='.$c_time.'&m=added');
            }
        }
        // show news preview
        else 
        {                     
            //correct preview links
            $preview_html=  preg_replace('/href="(.*?)"/', 'href="#"', entry_make($entry, 'active'));
            $preview_html_full= preg_replace('/href="(.*?)"/', 'href="#"',entry_make($entry, 'full'));
            cn_assign('preview_html, preview_html_full, gstamp', $preview_html , $preview_html_full, $c_time);
        }
    }

    if(empty($category))
    {
        $category=array();
    }
    // -----------------------------------------------------------------------------------------------------------------
    cn_assign('categories, vCategory, vTitle, vShort, vFull, is_active_html, vUseHtml, vConcat, vTags, morefields,vPage',
              $categories, $category, $title, $short_story, $full_story,$is_active_html, $if_use_html, $vConcat, $vTags, $morefields, $page);

    // ---
    echoheader("addedit@addedit/main.css", i18n("Add News")); echo exec_tpl('addedit/main'); echofooter();
}