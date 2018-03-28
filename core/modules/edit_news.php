<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

add_hook('index/invoke_module', '*edit_news_invoke');

// @Router
function edit_news_invoke()
{
    list($action) = GET('action', 'GPG');

    // [DETECT ACTION]
    switch ($action) {

        case 'editnews': edit_news_action_edit(); break;
        case 'massaction': edit_news_action_massaction(); break;
        case 'delete': edit_news_delete(); break;
        default: edit_news_action_list(); break;
    }

    die();
}

// Since 2.0: List all news
// ---------------------------------------------------------------------------------------------------------------------
function edit_news_action_list()
{
    cn_bc_add('Dashboard', PHP_SELF.'?mod=main');
    cn_bc_add('News listing', PHP_SELF.'?mod=editnews');

    // init
    list($source, $archive_id, $per_page, $sort, $dir, $YS, $MS, $DS, $page) = GET('source, archive_id, per_page, sort, dir, year, mon, day, page', 'GET,POST');
    list($add_category, $add_user, $rm_cat, $rm_user, $cat_filter) = GET('add_category_filter, add_user_filter, rm_category_filter, rm_user_filter, cat_filter', 'GET');

    // defaults
    $has_next = FALSE;
    $page  = intval($page);
    $ctime = ctime();
    $nocat = FALSE;

    if ($per_page == 0) {
        $per_page = 25;
    }
    
    if ($sort == '') {
        $sort = 'date';
    }
    
    if ($sort == 'date' && !$dir) {
        $dir = 'd';
    }
    
    if ($dir == '') {
        $dir = 'a';
    }
    
    // --- changes in acp filters ---
    list($cfilter, $ufilter) = cn_cookie_unpack('filter_cat, filter_user');

    // Check category exists and remove if NOT exists
    $category_list = getoption('#category');
    foreach ($cfilter as $i => $_cat_id) {
        if (!isset($category_list[$_cat_id])) {
            unset($cfilter[$i]);
        }
    }

    if ($add_category) {

        $sp = spsep($add_category);
        foreach ($sp as $id) 
        {
            if(test_cat($id))
            {
                $cfilter[$id] = $id;
            }
        }
    }
    
    if ($add_user)
    {
        $sp = spsep($add_user);
        foreach ($sp as $id) 
        {
            $ufilter[$id] = $id;
        }
    }

    if ($rm_cat)
    {
        $sp = spsep($rm_cat);
        foreach ($sp as $id) 
        {
            unset($cfilter[$id]);
        }
            
    }

    if ($rm_user)
    {
        $sp = spsep($rm_user);
        foreach ($sp as $id) 
        {
            unset($ufilter[$id]);
        }
    }        

    // Add filter
    if ($cat_filter) {
        if ($cat_filter !== '-') {
            $filter = intval($cat_filter);
            if (test_cat($filter)) {
                $cfilter[$filter] = $filter;
            }
        } else {
            $nocat = TRUE;
        }
    }
    
    cn_cookie_pack('filter_cat, filter_user', $cfilter, $ufilter);
    
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
        'by_date'    => "$YS-$MS-$DS"
    );

    list($entries, $rev) = cn_get_news($opts);

    // Detect next exists
    if (count($entries) > $per_page)
    {
        end($entries);
        unset($entries[key($entries)]);

        $has_next = TRUE;
    }

    $meta = array();

    // Load meta-data (and userlist data)
    if ($archive_id && $source == 'archive')
    {
        $meta = db_index_meta_load("archive-$archive_id", TRUE);
    }
    else
    {
        $meta = db_index_meta_load($source, TRUE);
    }

    // Meta-data for draft only
    $meta_draft = db_index_meta_load('draft');
    $ptree      = isset($meta['locs']) ? $meta['locs']:false;
    $userlist   = $meta['uids'];
    $nprospect  = intval($rev['cpostponed']);
    $ndraft     = is_array($meta_draft['locs']) ? intval(array_sum($meta_draft['locs'])) : 0;
    $found_rows = isset($meta['locs']) && is_array($meta['locs']) ? intval(array_sum($meta['locs'])) : 0;
    $archives   = count(db_get_archives());

    // ---
    // Decode proto tree for list news
    $tree_years = array();
    $tree_mons  = array();
    $tree_days  = array();

    // Is draft or active (or prospected)
    if ($source !== 'archive')
    {
        if ($ptree) 
        {
            foreach ($ptree as $nloc => $c)
            {
                list($Y, $M, $D) = explode('-', $nloc);

                if (isset($tree_years[$Y]))
                {
                    $tree_years[$Y] += $c;
                }
                else 
                {
                    $tree_years[$Y] = $c;
                }

                if ($Y == $YS)
                {
                    if(isset($tree_mons[$M]))
                    {
                        $tree_mons[$M] += $c;
                    }
                    else
                    {
                        $tree_mons[$M]=$c;
                    }
                    if ($M == $MS)
                    {
                        $tree_days[$D] = $c;
                    }
                }
            }
        }
    }
    // Is archive
    else
    {
        $found_rows = 0;
        $ptree = db_get_archives();

        // Archive Id exists
        if ($archive_id)
        {
            $found_rows = $ptree[$archive_id]['c'];
        }
        // Not exists
        else
        {
            foreach ($ptree as $item)
            {
                $found_rows += $item['c'];
            }
            
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
        {
            $can = FALSE;
        }
        elseif (test('Nvs', $nv_user, TRUE) || test('Nvg', $nv_user) || test('Nva'))
        {
            $can = test_cat($entry['c']);
        }

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
    unset($_GET['add_category_filter'], $_GET['add_user_filter'], $_GET['rm_category_filter'], $_GET['rm_user_filter']);

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
    $flatdb         = new FlatDB();
    $preview_html   = $preview_html_full = '';
    $ID             = $gstamp = intval(REQ('id', 'GETPOST'));

    cn_bc_add('Dashboard', PHP_SELF);
    cn_bc_add('News listing', PHP_SELF . '?mod=editnews');
    cn_bc_add('Edit');

    list($status, $preview) = GET('m, preview');
    list($vConcat, $vTags, $faddm, $archive_id, $source)  = GET('concat, tags, faddm, archive_id, source', 'GETPOST');

    // get news part by day
    $news = db_news_load(db_get_nloc($ID));

    if ($ID == 0) {
        msg_info("Can't edit news without ID");
    }

    if (!isset($news[$ID])) {
        msg_info("News entry not found!");
    }

    // load entry
    $entry    = $news[$ID];
    $oldentry = $entry;

    // disallowed by category
    if (!test_cat($entry['c'])) {
        msg_info("You can't view entry. Category disallow");
    }

    // set status message
    if ($status == 'added') {
        cn_throw_message('News was added');
    }

    if ($status == 'moved') {
        cn_throw_message('Moved to another time');
    }

    // load more fields
    list($morefields) = cn_get_more_fields($entry['mf']);

    // do save news?
    if (request_type('POST')) {

        $flatdb->cache_clean();

        // check exists news
        if (isset($news[$ID])) {

            // extract data
            $entry = $storent = $news[$ID];

            // Prepare text
            list($title, $page, $category, $short_story, $full_story, $if_use_html, $postpone_draft) = GET('title, page, category, short_story, full_story, if_use_html, postpone_draft', 'GETPOST');

            // Change date?
            list($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year) = GET('from_date_hour, from_date_minutes, from_date_seconds, from_date_month, from_date_day, from_date_year', 'GETPOST');
            $c_time = intval(mktime($from_date_hour, $from_date_minutes, $from_date_seconds, $from_date_month, $from_date_day, $from_date_year));

            // sanitize page name
            $page = preg_replace('/[^a-z0-9_\.]/i', '-', $page);

            if (empty($page) && !empty($title) && getoption('auto_news_alias')) {
                $page = strtolower(preg_replace('/[^a-z0-9_\.]/i', '-', cn_transliterate($title)));
            }

            // current source is archive, active (postponed) or draft news
            $draft_target = ($postpone_draft === 'draft');

            // User can't post active news
            if (test('Bd') && $draft_target !== 'draft') {
                $draft_target = 'draft';
            }

            // if archive_id is present, unable send to draft
            $current_source = $archive_id ? "archive-$archive_id" : ($source == 'draft' ? 'draft' : '');
            $target_source  = $archive_id ? "archive-$archive_id" : ($draft_target ? 'draft' : '');
            $if_use_html    = $if_use_html ? TRUE : (getoption('use_wysiwyg') ? TRUE : FALSE);

            // Don't allow put [img] tag there
            $title = preg_replace('~\[\/?img(.+)\]~', '', $title);

            $entry['t'] = cn_htmlclear($title);
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
            if ($disallow_message) {
                cn_throw_message($disallow_message, 'e');
            }

            // Make preview
            if ($preview) {

                $gstamp = $entry['id'] = $c_time;

                // Disable all links
                $preview_html = preg_replace('/href="(.*?)"/', 'href="#"', entry_make($entry, 'active'));
                $preview_html_full = preg_replace('/href="(.*?)"/', 'href="#"', entry_make($entry, 'full'));

                // Disable all target
                $preview_html = preg_replace('/target=[^\s\b]+/i', '', $preview_html);
                $preview_html_full = preg_replace('/target=[^\s\b]+/i', '', $preview_html_full);

            }
            // Save new data
            elseif (REQ('do_editsave', 'POST')) {

                if (!getoption('disable_title') && empty($title)) {
                    cn_throw_message('The title cannot be blank', 'e');
                }

                if (!getoption('disable_short') && empty($short_story)) {
                    cn_throw_message('The story cannot be blank', 'e');
                }

                // Check for change alias
                $pgts = bt_get_id($ID, 'ts_pg');
                if ($pgts && $pgts !== $page) {
                    if ($page) {
                        if (bt_get_id($page, 'pg_ts')) {
                            cn_throw_message('For other news page alias already exists!', 'e');
                        }
                    } else {
                        bt_del_id($pgts, 'pg_ts');
                        bt_del_id($ID, 'ts_pg');
                    }
                }

                // no errors in a[rticle] area
                if (cn_get_message('e', 'c') == 0) {

                    $FlatDB  = new FlatDB();

                    $ida = db_index_load($current_source);
                    $idd = db_index_load($target_source);

                    // Time is changed
                    if ($c_time != intval($ID)) {

                        // Load next block (or current)
                        $next = db_news_load(db_get_nloc($c_time));

                        if (isset($next[$c_time])) {
                            cn_throw_message('The article time already busy, select another', 'e');

                        } else {

                            // set new time
                            $entry['id'] = $c_time;
                            $next[$c_time] = $entry;

                            // remove old news [from source / dest]
                            if (isset($news[$ID])) {
                                unset($news[$ID]);
                            }
                            
                            if (isset($next[$ID])) {
                                unset($next[$ID]);
                            }

                            // remove old index
                            if (isset($idd[$ID])) {
                                unset($idd[$ID]);
                            }

                            // Delete old indexes
                            $_ts_id = bt_get_id($ID, 'nts_id');
                            bt_del_id($ID, 'nts_id');

                            // Update
                            bt_set_id($_ts_id, $c_time, 'nid_ts');
                            bt_set_id($c_time, $_ts_id, 'nts_id');

                            // save 2 blocks
                            db_save_news($news, db_get_nloc($ID));
                            db_save_news($next, db_get_nloc($c_time));

							cn_throw_message('News moved from date \n'.date('d-m-Y g:i:a', $ID).'\n'.' to \n'. date('d-m-Y g:i:a', $c_time));
                        }

                    } else {

                        $news[$ID] = $entry;
                        db_save_news($news, db_get_nloc($ID));

                        cn_throw_message('News was edited');
                    }

                    // Update page aliases
                    $_ts_pg = bt_get_id($ID, 'ts_pg');

                    bt_del_id($ID, 'ts_pg');
                    bt_del_id($_ts_pg, 'pg_ts');

                    if ($page) {
                        bt_set_id($c_time, $page, 'ts_pg');
                        bt_set_id($page, $c_time, 'pg_ts');
                    }

                    // 1) remove from old index
                    if (isset($ida[$ID])) {
                        unset($ida[$ID]);
                    }

                    // Fill probably unused
                    $storent['tg'] = isset($storent['tg']) ? $storent['tg'] : '';

                    // 2) add new index
                    $idd[$c_time] = db_index_create($entry);

                    // 3) sync indexes
                    db_index_save($ida, $current_source);   db_index_update_overall($current_source);
                    db_index_save($idd, $target_source);    db_index_update_overall($target_source);

                    // ------
                    // UPDATE categories
                    $FlatDB->cn_remove_categories($storent['c'], $storent['id']);
                    $FlatDB->cn_add_categories($entry['c'], $c_time);

                    // UPDATE tags
                    $FlatDB->cn_remove_tags($storent['tg'], $storent['id']);
                    $FlatDB->cn_add_tags($entry['tg'], $c_time);

                    // UPDATE date / id storage [with comments count]
                    $FlatDB->cn_update_date($entry['id'], $storent['id'], count($storent['co']));
                    // ------
                }
            }
        } else {
            msg_info("News entry not found or has been deleted");
        }
    }

    if (empty($entry['pg'])&&isset($entry['t'])&& getoption('auto_news_alias')) {
        $entry['pg'] = strtolower(preg_replace('/[^a-z0-9_\.]/i', '-', cn_transliterate($entry['t'])));
    }

    // Assign template vars
    $category      = spsep($entry['c']);
    $categories    = cn_get_categories(false);
    $title         = isset($entry['t'])? $entry['t']:'';
    $short_story   = isset($entry['s'])? $entry['s']:'';
    $page          = isset($entry['pg'])? $entry['pg']:'';
    $full_story    = isset($entry['f'])? $entry['f']:'';
    $is_draft      = isset($entry['st'])? $entry['st'] == 'd' : false;
    $vConcat       = isset($entry['cc'])? $entry['cc']:'';
    $vTags         = isset($entry['tg'])? $entry['tg']:'';
    $if_use_html   = isset($entry['ht'])? $entry['ht']:false;
    $is_active_html = test('Csr');

    cn_assign(
        'categories, vCategory, vTitle, vPage, vShort, vFull, vUseHtml, preview_html, preview_html_full, gstamp, is_draft, vConcat, vTags, morefields, archive_id, is_active_html',
        $categories, $category, $title, $page, $short_story, $full_story, $if_use_html, $preview_html, $preview_html_full, $gstamp, $is_draft, $vConcat, $vTags, $morefields, $archive_id, $is_active_html
    );

    cn_assign("EDITMODE", 1);

    // show edit page
    echoheader("addedit@addedit/main.css", i18n("Edit news")); echo exec_tpl('addedit/main'); echofooter();
}

// Since 2.0: Archive, Delete, Change category etc.
// ---------------------------------------------------------------------------------------------------------------------
function edit_news_action_massaction()
{
    $FlatDB = new FlatDB();

    list($subaction, $source, $archive_id) = GET('subaction, source, archive_id');

    // Mass Delete
    if ($subaction == 'mass_delete')
    {
        if (!test('Nud'))
        {
            cn_throw_message("Operation not permitted for you",'w');
        }
        
        list($selected_news) = GET('selected_news');

        if (empty($selected_news))
        {
            cn_throw_message("No one news selected", 'e');
        }
        else
        {
            $count = count($selected_news);
            if (confirm_first() && $count == 0)
            {
                cn_throw_message('No none entry selected','e');
            }

            if (confirm_post("Delete selected news ($count)"))
            {
                if ($source == 'archive')
                {
                    $source = 'archive-'.intval($archive_id);
                }

                $idx = db_index_load($source);

                // do delete news
                foreach ($selected_news as $id)
                {
                    $news  = db_news_load(db_get_nloc($id));

                    $storent = $news[$id];

                    if (isset($news[$id]))
                    {
                        unset($news[$id]);
                    }

                    if (isset($idx[$id]))
                    {
                        unset($idx[$id]);
                    }

                    // Remove from meta-index (auto_id)
                    $_ts_id = bt_get_id($id, 'nts_id');
                    bt_del_id($id, 'nts_id');
                    bt_del_id($_ts_id, 'nid_ts');

                    // Remove page alias
                    $_ts_pg = bt_get_id($id, 'ts_pg');
                    bt_del_id($id, 'ts_pg');
                    bt_del_id($_ts_pg, 'pg_ts');

                    // ------
                    if(isset($storent['c']))
                    {
                        $FlatDB->cn_remove_categories($storent['c'], $storent['id']);
                    }

                    if(isset($storent['tg']))
                    {
                        $FlatDB->cn_remove_tags($storent['tg'], $storent['id']);
                    }

                    $FlatDB->cn_update_date(0, $storent['id']);

                    if(isset($storent['u']))
                    {
                        $FlatDB->cn_user_sync($storent['u'], 0, $storent['id']);
                    }
                    // ------

                    // Save block
                    db_save_news($news, db_get_nloc($id));
                }

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

                $FlatDB->cache_clean();
                cn_throw_message('News deleted');
            }
            else
            {
                cn_throw_message("No one entry deleted", 'e');
            }
        }
    }
    // Mass change category
    elseif ($subaction == 'mass_move_to_cat')
    {
        cn_assign('catlist', cn_get_categories(false));

        $news_ids = GET('selected_news');

        // Disable commit without news
        if (empty($news_ids) || (count($news_ids) == 1 && !$news_ids[0]))
        {
            cn_throw_message("No one news selected", 'e');
        }
        else
        {
            if (confirm_post( exec_tpl('addedit/changecats') ))
            {
                cn_dsi_check();

                list($news_ids, $cats, $source) = GET('selected_news, cats, source', 'POST');
                $nc = news_make_category(array_keys($cats));

                // Load index for update categories
                $idx = db_index_load($source);
                foreach ($news_ids as $id)
                {
                    $loc     = db_get_nloc($id);
                    $entries = db_news_load( $loc );

                    // Catch user trick
                    if (!test_cat($entries[$id]['c']))
                    {
                        cn_throw_message('Not allowed change category for id = '.$id,'w');
                    }

                    $storent = $entries[$id];

                    $idx[$id][0] = $nc;
                    $entries[$id]['c'] = $nc;

                    // ------
                    $FlatDB->cn_remove_categories($storent['c'], $storent['id']);
                    $FlatDB->cn_add_categories($nc, $storent['id']);
                    // ------

                    db_save_news($entries, $loc);
                }

                // Save updated block
                db_index_save($idx, $source);
                cn_throw_message('Successful processed');

                $FlatDB->cache_clean();
            }
            else
            {
                cn_throw_message('Operation declined by user','e');
            }
        }
    }
    // Mass approve action
    elseif ($subaction == 'mass_approve')
    {
        if (!test('Nua'))
        {
            msg_info("Operation not permitted for you");
        }

        list($selected_news) = GET('selected_news');

        if (empty($selected_news))
        {
            cn_throw_message('No one draft selected', 'e');
        }
        else
        {
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

            $FlatDB->cache_clean();
            cn_throw_message('News was approved');
        }
    }
    // Bulk switch to HTML
    elseif ($subaction == 'switch_to_html')
    {
        list($selected_news) = GET('selected_news');

        if (empty($selected_news)) {
            cn_throw_message('News not selected', 'e');
        }
        else {
            // do approve news
            foreach ($selected_news as $id)
            {
                $news = db_news_load(db_get_nloc($id));
                $news[$id]['ht'] = TRUE;
                db_save_news($news, db_get_nloc($id));
            }

            cn_throw_message('News was switched to HTML');
        }
    }
    else
    {
        cn_throw_message('Select action to process','w');
    }
    
    edit_news_action_list();
}

// Delete single item
function edit_news_delete()
{
    cn_dsi_check();

    if (!test('Nud'))
    {
        msg_info("Unable to delete news: no permission");
    }

    $FlatDB = new FlatDB();
    list($id, $source) = GET('id, source', 'GET');

    $ida  = db_index_load($source);
    $nloc = db_get_nloc($id);
    $db   = db_news_load($nloc);

    // ------
    $FlatDB->cn_remove_categories($db[$id]['c'], $db[$id]['id']);
    $FlatDB->cn_update_date(0, $db[$id]['id']);
    $FlatDB->cn_user_sync($db[$id]['u'], 0, $db[$id]['id']);
    $FlatDB->cn_remove_tags($db[$id]['tg'], $db[$id]['id']);
    // ------

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

    $FlatDB->cache_clean();
}
