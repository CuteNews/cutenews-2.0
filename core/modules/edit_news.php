<?php if (!defined('EXEC_TIME')) die('Access restricted');

add_hook('index/invoke_module', '*edit_news_invoke');

// @Router
function edit_news_invoke()
{
    list($action) = GET('action', 'GPG');

    // [DETECT ACTION]
    if ($action == 'editnews') edit_news_action_edit();
    elseif ($action == 'massaction') edit_news_action_massaction();
    elseif ($action == 'delete') edit_news_delete();
    else edit_news_action_list();

    die();
}

// Since 2.0: List all news
// ---------------------------------------------------------------------------------------------------------------------
function edit_news_action_list()
{
    // init
    list($source, $archive_id, $per_page, $sort, $dir, $YS, $MS, $DS, $page) = GET('source, archive_id, per_page, sort, dir, year, mon, day, page', 'GET,POST');
    list($add_category, $add_user, $rm_cat, $rm_user, $cat_filter) = GET('add_category_filter, add_user_filter, rm_category_filter, rm_user_filter, cat_filter', 'GET');

    // defaults
    $has_next = FALSE;
    $page  = intval($page);
    $ctime = ctime();
    $nocat = FALSE;

    if ($per_page == 0)
        $per_page = 25;

    if ($sort == '')
        $sort = 'date';

    if ($sort == 'date' && !$dir)
        $dir = 'd';

    if ($dir == '')
        $dir = 'a';

    // --- changes in acp filters ---
    list($cfilter, $ufilter) = cn_cookie_unpack('filter_cat, filter_user');

    if ($add_category)
    {
        $sp = spsep($add_category);
        foreach ($sp as $id) $cfilter[$id] = $id;
    }

    if ($add_user)
    {
        $sp = spsep($add_user);
        foreach ($sp as $id) $ufilter[$id] = $id;
    }

    if ($rm_cat)
    {
        $sp = spsep($rm_cat);
        foreach ($sp as $id) unset($cfilter[$id]);
    }

    if ($rm_user)
    {
        $sp = spsep($rm_user);
        foreach ($sp as $id) unset($ufilter[$id]);
    }

    cn_cookie_pack('filter_cat, filter_user', $cfilter, $ufilter);

    // Override cfilter
    if ($cat_filter)
    {
        if ($cat_filter !== '-')
            $cfilter = array(intval($cat_filter));
        else
            $nocat = TRUE;
    }

    // ----------------------------------------------------
    $opts = array
    (
        'source'     => $source,
        'archive_id' => $archive_id,
        'sort'       => $sort,
        'dir'        => $dir,
        'start'      => $page,
        'per_page'   => ($per_page + 1),
        'cfilter'    => $cfilter,
        'ufilter'    => $ufilter,
        'nocat'      => $nocat,
        'nlpros'     => TRUE, // load prospected anyway
        'by_date'    => "$YS-$MS-$DS",
    );

    list($entries, $rev) = cn_get_news($opts);

    // Detect next exists
    if (count($entries) > $per_page)
    {
        end($entries);
        unset($entries[key($entries)]);

        $has_next = TRUE;
    }

    // Load meta-data (and userlist data)
    if ($archive_id && $source == 'archive')
        $meta = db_index_meta_load("archive-$archive_id", TRUE);
    else
        $meta = db_index_meta_load($source, TRUE);

    // Meta-data for draft only
    $meta_draft = db_index_meta_load('draft');
    $ptree      = $meta['locs'];
    $userlist   = $meta['uids'];
    $nprospect  = intval($rev['cpostponed']);
    $ndraft     = is_array($meta_draft['locs']) ? intval(array_sum($meta_draft['locs'])) : 0;
    $found_rows = is_array($meta['locs']) ? intval(array_sum($meta['locs'])) : 0;
    $archives   = count(db_get_archives());

    // ---
    // Decode proto tree for list news
    $tree_years = array();
    $tree_mons  = array();
    $tree_days  = array();

    // Is draft or active (or prospected)
    if ($source !== 'archive')
    {
        if ($ptree) foreach ($ptree as $nloc => $c)
        {
            list($Y, $M, $D) = explode('-', $nloc);
            $tree_years[$Y] += $c;

            if ($Y == $YS)
            {
                $tree_mons[$M] += $c;
                if ($M == $MS) $tree_days[$D] = $c;
            }
        }
    }
    // Is archive
    else
    {
        $ptree = db_get_archives();

        $found_rows = 0;
        if ($archive_id)
        {
            $found_rows = $ptree[$archive_id]['c'];
        }
        else
        {
            foreach ($ptree as $item)
                $found_rows += $item['c'];

            $entries = array();
        }

        $nprospect = 0;
    }

    // ----------------------------------------------------
    foreach ($entries as $id => $entry)
    {
        $can = FALSE;
        $nv_user = db_user_by_name($entry['u']);

        // User not exists, deny, except admins
        if (!$nv_user && !test('Nva'))
            $can = FALSE;
        elseif (test('Nvs', $nv_user, TRUE) || test('Nvg', $nv_user) || test('Nva'))
            $can = test_cat($entry['c']);

        $entries[$id]['user']       = $entry['u'];
        $entries[$id]['date']       = $YS ? date('M, d H:i', $id) : date('M, d Y H:i', $id);
        $entries[$id]['date_full']  = date('Y M d, H:i:s', $id);
        $entries[$id]['user']       = $entry['u'];
        $entries[$id]['comments']   = count($entry['co']);
        $entries[$id]['title']      = $entry['t'];
        $entries[$id]['cats']       = spsep($entry['c']);
        $entries[$id]['is_pros']    = $id > $ctime ? TRUE : FALSE;
        $entries[$id]['can']        = $can;
    }

    // clear differs for cn_url_*
    unset($_GET['add_category_filter'], $_GET['add_user_filter']);
    unset($_GET['rm_category_filter'], $_GET['rm_user_filter']);

    // ------
    cn_assign('sort, dir, source, per_page, entries_showed, entries_total, entries, page, userlist, category_filters, user_filters, cat_filter',
              $sort, $dir, $source, $per_page, count($entries), $found_rows, $entries, $page, $userlist, $cfilter, $ufilter, $cat_filter);

    cn_assign('year_selected, mon_selected, day_selected, TY, TM, TD, ptree', $YS, $MS, $DS, $tree_years, $tree_mons, $tree_days, $ptree);
    cn_assign('nprospect, ndraft, has_next, archives', $nprospect, $ndraft, $has_next, $archives);

    echoheader('editnews@editnews/main.css', 'News list');
    echo exec_tpl('editnews/list'); echofooter();
}

// Since 2.0: Edit news section
// ---------------------------------------------------------------------------------------------------------------------
function edit_news_action_edit()
{
    $preview_html   = '';
    $ID             = intval(REQ('id', 'GETPOST'));

    list($status, $preview) = GET('m, preview');
    list($vConcat, $vTags, $faddm, $archive_id, $source)  = GET('concat, tags, faddm, archive_id, source', 'GETPOST');

    // get news part by day
    $news = db_news_load(db_get_nloc($ID));

    if ($ID == 0)
        msg_info("Can't edit news without ID");

    if (!isset($news[$ID]))
        msg_info("News entry not found!");

    // load entry
    $entry = $news[$ID]; $oldentry=$entry;

    // disallowed by category
    if (!test_cat($entry['c']))
        msg_info("You can't view entry. Category disallow");

    // set status message
    if ($status == 'added') cn_throw_message('News was added');
    if ($status == 'moved') cn_throw_message('Moved to another time');

    // load more fields
    list($morefields) = cn_get_more_fields($entry['mf']);

    // do save news?
    if (request_type('POST'))
    {
        // check exists news
        if (isset($news[$ID]))
        {
            // extract data
            $entry = $storent = $news[$ID];

            // Prepare text
            list($title, $page, $category, $short_story, $full_story, $if_use_html, $postpone_draft) = GET('title, page, category, short_story, full_story, if_use_html, postpone_draft', 'GETPOST');

            // Change date?
            list($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year) = GET('from_date_hour, from_date_minutes, from_date_seconds, from_date_month, from_date_day, from_date_year', 'GETPOST');
            $c_time = intval(mktime($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year));

            // sanitize page name
            $page = preg_replace('/[^a-z0-9_]/i', '-', $page);

            // current source is archive, active (postponed) or draft news
            $draft_target  = $postpone_draft == 'draft' ? TRUE : FALSE;

            // User can't post active news
            if (test('Bd') && $draft_target !== 'draft') $draft_target = 'draft';

            // if archive_id is present, unable send to draft
            $current_source = $archive_id ? "archive-$archive_id" : ($source == 'draft' ? 'draft' : '');
            $target_source  = $archive_id ? "archive-$archive_id" : ($draft_target ? 'draft' : '');
            $if_use_html    = $if_use_html ? TRUE : (getoption('use_wysiwyg') ? TRUE : FALSE);

            $entry['t'] = cn_htmlspecialchars($title);
            $entry['c'] = is_array($category) ? join(',', $category) : $category;
            $entry['s'] = cn_htmlclear($short_story);
            $entry['f'] = cn_htmlclear($full_story);
            $entry['ht'] = $if_use_html;
            $entry['st'] = $draft_target ? 'd' : '';
            $entry['pg'] = $page;
            $entry['cc'] = $vConcat ? TRUE : FALSE;
            $entry['tg'] = strip_tags($vTags);

            // apply more field (for news & frontend)
            list($entry, $disallow_message) = cn_more_fields_apply($entry, $faddm);
            list($morefields) = cn_get_more_fields($faddm);

            // has message from function
            if ($disallow_message)
                cn_throw_message($disallow_message, 'e');

            // Make preview
            if ($preview)
            {
                $preview_html = entry_make($entry, 'active');
            }
            // Save new data
            elseif (REQ('do_editsave', 'POST'))
            {
                if (!getoption('disable_title') && empty($title))
                    cn_throw_message('The title cannot be blank', 'e');

                if (!getoption('disable_short') && empty($short_story))
                    cn_throw_message('The story cannot be blank', 'e');

                // Check for change alias
                $pgts = bt_get_id($ID, 'ts_pg');
                if ($pgts && $pgts !== $page)
                {
                    if ($page)
                    {
                        if (bt_get_id($page, 'pg_ts'))
                            cn_throw_message('For other news page alias already exists!', 'e');
                    }
                    else
                    {
                        bt_del_id($pgts, 'pg_ts');
                        bt_del_id($ID, 'ts_pg');
                    }
                }

                // no errors in a[rticle] area
                if (cn_get_message('e', 'c') == 0)
                {
                    $ida = db_index_load($current_source);
                    $idd = db_index_load($target_source);
                    $x_cnt=  edit_news_load_indexes_cnt();
                    // Time is changed
                    if ($c_time != intval($ID))
                    {
                        // Load next block (or current)
                        $next = db_news_load(db_get_nloc($c_time));

                        if (isset($next[$c_time]))
                        {
                            cn_throw_message('The article time already busy, select another', 'e');
                        }
                        else
                        {
                            // set new time
                            $entry['id'] = $c_time;
                            $next[$c_time] = $entry;

                            // remove old news [from source / dest]
                            if (isset($news[$ID])) unset($news[$ID]);
                            if (isset($next[$ID])) unset($next[$ID]);

                            // remove old index
                            if (isset($idd[$ID])) unset($idd[$ID]);

                            // Delete old indexes
                            $_ts_id = bt_get_id($ID, 'nts_id');
                            bt_del_id($ID, 'nts_id');

                            // Update
                            bt_set_id($_ts_id, $c_time, 'nid_ts');
                            bt_set_id($c_time, $_ts_id, 'nts_id');

                            // save 2 blocks
                            db_save_news($news, db_get_nloc($ID));
                            db_save_news($next, db_get_nloc($c_time));

                            cn_throw_message('News moved from <b>'.date('Y-m-d H:i:s', $ID).'</b> to <b>'.date('Y-m-d H:i:s', $c_time).'</b>');
                        }
                    }
                    else
                    {
                        $news[$ID] = $entry;
                        db_save_news($news, db_get_nloc($ID));

                        cn_throw_message('News was edited');
                    }
                    $x_cnt=  edit_news_update_index_cnt($x_cnt, $entry, 'update', $oldentry);
                    edit_news_close_indexes_cnt($x_cnt);
                    // Update tags, etc
                    db_update_aux($entry, 'update', $storent);

                    // Update page aliases
                    $_ts_pg = bt_get_id($ID, 'ts_pg');

                    bt_del_id($ID, 'ts_pg');
                    bt_del_id($_ts_pg, 'pg_ts');

                    if ($page)
                    {
                        bt_set_id($c_time, $page, 'ts_pg');
                        bt_set_id($page, $c_time, 'pg_ts');
                    }

                    // 1) remove from old index
                    if (isset($ida[$ID]))
                        unset($ida[$ID]);

                    // 2) add new index
                    $idd[$c_time] = db_index_create($entry);

                    // 3) sync indexes
                    db_index_save($ida, $current_source);   db_index_update_overall($current_source);
                    db_index_save($idd, $target_source);    db_index_update_overall($target_source);
                }
            }
        }
        else
        {
            msg_info("News entry not found or has been deleted");
        }
    }

    // Assign template vars
    $category      = spsep($entry['c']);
    $categories    = cn_get_categories();
    $title         = $entry['t'];
    $short_story   = $entry['s'];
    $page          = $entry['pg'];
    $full_story    = $entry['f'];
    $is_draft      = $entry['st'] == 'd';
    $vConcat       = $entry['cc'];
    $vTags         = $entry['tg'];
    $if_use_html   = $entry['ht'];
    $is_active_html =test('Csr');
    cn_assign
    (
        'categories, vCategory, vTitle, vPage, vShort, vFull, vUseHtml, preview_html, gstamp, is_draft, vConcat, vTags, morefields, archive_id, is_active_html',
        $categories, $category, $title, $page, $short_story, $full_story, $if_use_html, $preview_html, $ID, $is_draft, $vConcat, $vTags, $morefields, $archive_id, $is_active_html
    );

    cn_assign("EDITMODE", 1);

    // show edit page
    echoheader("addedit@addedit/main.css", i18n("Edit news")); echo exec_tpl('addedit/main'); echofooter();
}

// Since 2.0: Archive, Delete, Change category etc.
// ---------------------------------------------------------------------------------------------------------------------
function edit_news_action_massaction()
{
    list($subaction, $source, $archive_id) = GET('subaction, source, archive_id');

    // Mass Delete
    if ($subaction == 'mass_delete')
    {
        if (!test('Nud'))
            msg_info("Operation not permitted for you");

        list($selected_news) = GET('selected_news');

        $count = count($selected_news);
        if (confirm_first() && $count == 0)
            msg_info('Error: no none entry selected');

        if (confirm_post("Delete selected news ($count)"))
        {
            if ($source == 'archive')
                $source = 'archive-'.intval($archive_id);

            $idx = db_index_load($source);
            $x_cnt=  edit_news_load_indexes_cnt();
            // do delete news
            foreach ($selected_news as $id)
            {
                $news = db_news_load(db_get_nloc($id));
                $x_cnt=  edit_news_update_index_cnt($x_cnt, $news[$id], 'delete');
                // Delete tags, etc
                db_update_aux($news[$id], 'delete');

                if (isset($news[$id]))
                    unset($news[$id]);

                if (isset($idx[$id]))
                    unset($idx[$id]);

                // Remove from meta-index (auto_id)
                $_ts_id = bt_get_id($id, 'nts_id');
                bt_del_id($id, 'nts_id');
                bt_del_id($_ts_id, 'nid_ts');

                // Remove page alias
                $_ts_pg = bt_get_id($id, 'ts_pg');
                bt_del_id($id, 'ts_pg');
                bt_del_id($_ts_pg, 'pg_ts');

                // Save block
                db_save_news($news, db_get_nloc($id));
            }
            edit_news_close_indexes_cnt($x_cnt);
            db_index_save($idx, $source);
            db_index_update_overall($source);

            // Update archive list
            if ($archive_id)
            {
                $min = min(array_keys($idx));
                $max = max(array_keys($idx));
                $cnt = count($idx);

                db_archive_meta_update($archive_id, $min, $max, $cnt);
            }

            msg_info('News deleted');
        }
        else
        {
            msg_info("No one entry deleted");
        }

        msg_info('News not deleted');
    }
    // Mass change category
    elseif ($subaction == 'mass_move_to_cat')
    {
        cn_assign('catlist', cn_get_categories());

        if (confirm_post( exec_tpl('addedit/changecats') ))
        {
            cn_dsi_check();

            list($news_ids, $cats, $source) = GET('selected_news, cats, source', 'POST');
            $nc = news_make_category(array_keys($cats));
            
            $x_cnt=  edit_news_load_indexes_cnt();
            // Load index for update categories
            $idx = db_index_load($source);
            foreach ($news_ids as $id)
            {
                $loc     = db_get_nloc($id);
                $entries = db_news_load( $loc );

                // Catch user trick
                if (!test_cat($entries[$id]['c']))
                    msg_info('Not allowed change category for id = '.$id);
                
                $oldentry=$entries[$id];
                $idx[$id][0] = $nc;
                $entries[$id]['c'] = $nc;
                $x_cnt=  edit_news_update_index_cnt($x_cnt, $entries[$id], 'update', $oldentry);
                db_save_news($entries, $loc);
            }
            edit_news_close_indexes_cnt($x_cnt);
            // Save updated block
            db_index_save($idx, $source);

            msg_info('Successful processed');
        }
        else
        {
            msg_info('Operation declined by user');
        }
    }
    // Mass approve action
    elseif ($subaction == 'mass_approve')
    {
        if (!test('Nua'))
            msg_info("Operation not permitted for you");

        list($selected_news) = GET('selected_news');

        $ida = db_index_load('');
        $idd = db_index_load('draft');

        // do approve news
        foreach ($selected_news as $id)
        {
            $news = db_news_load(db_get_nloc($id));
            $news[$id]['st'] = '';

            // 1) remove from draft
            unset($idd[$id]);

            // 2) add to active index
            $ida[$id] = db_index_create($news[$id]);

            // save block
            db_save_news($news, db_get_nloc($id));
        }

        // save indexes
        db_index_save($ida);            db_index_update_overall();
        db_index_save($idd, 'draft');   db_index_update_overall('draft');

        msg_info('News was approved');
    }
    else msg_info('Select action to process');
}

// Delete single item
function edit_news_delete()
{
    cn_dsi_check();

    if (!test('Nud'))
        msg_info("Unable to delete news: no permission");

    list($id, $source) = GET('id, source', 'GET');

    $ida  = db_index_load($source);
    $nloc = db_get_nloc($id);
    $db   = db_news_load($nloc);

    $x_cnt=edit_news_load_indexes_cnt();
    $x_cnt=edit_news_update_index_cnt($x_cnt, $db[$id], 'delete');
    edit_news_close_indexes_cnt($x_cnt);
    // update tags, etc
    db_update_aux($db[$id], 'delete');

    unset($db[$id]);
    unset($ida[$id]);

    // Remove from meta-index
    $_ts_id = bt_get_id($id, 'nts_id');
    bt_del_id($id, 'nts_id');
    bt_del_id($_ts_id, 'nid_ts');

    // Remove page alias
    $_ts_pg = bt_get_id($id, 'ts_pg');
    bt_del_id($id, 'ts_pg');
    bt_del_id($_ts_pg, 'pg_ts');

    // save block
    db_save_news($db, $nloc);

    db_index_save($ida, $source);
    db_index_update_overall($source);

    cn_relocation(cn_url_modify(array('reset'), 'mod=editnews', "source=$source"));
}

function edit_news_update_index_cnt($idxs,$entry,$action,$oldentry=NULL)
{
    switch ($action) {
        case 'delete':
                $idxs['d']=db_index_operation($idxs['d'], array('key'=>  db_get_nloc($entry['id']),'val+'=>-1));
                $idxs['c']=db_index_operation($idxs['c'], array('key'=>  $entry['c'],'val+'=>-1));
                $idxs['tg']=db_index_operation($idxs['tg'], array('key'=>  $entry['tg'],'val+'=>-1));
            break;
        case 'update':  
                if(!is_null($oldentry))
                {
                    $idxs['d']=db_index_operation($idxs['d'], array('key'=>  db_get_nloc($oldentry['id']),'val+'=>-1));
                    $idxs['c']=db_index_operation($idxs['c'], array('key'=>  $oldentry['c'],'val+'=>-1));
                    $idxs['tg']=db_index_operation($idxs['tg'], array('key'=>  $oldentry['tg'],'val+'=>-1));      
                    $idxs['d']=db_index_operation($idxs['d'], array('key'=>  db_get_nloc($entry['id']),'val+'=>1));
                    $idxs['c']=db_index_operation($idxs['c'], array('key'=>  $entry['c'],'val+'=>1));
                    $idxs['tg']=db_index_operation($idxs['tg'], array('key'=>  $entry['tg'],'val+'=>1));                   
                }
            break;
        default:
            break;
    }
    return $idxs;
}

function edit_news_load_indexes_cnt()
{
    $format='key:val+';
    $idx_date=  db_index_load_cnt($format, 'date');
    $idx_cat =  db_index_load_cnt($format, 'category');
    $idx_tags=  db_index_load_cnt($format, 'tags');
    
    return array('d'=>$idx_date,'c'=>$idx_cat,'tg'=>$idx_tags);
}

function edit_news_close_indexes_cnt($idxs)
{
    foreach ($idxs as $item)
    {
        db_index_close_cnt($item);
    }
}