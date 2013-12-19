<?php // init

list($source, $page, $per_page, $entries, $showed, $entries_total, $userlist) = _GL('source, page, per_page, entries, entries_showed:intval, entries_total:intval, userlist');
list($sort, $dir, $ptree, $YS, $MS, $DS, $TY, $TM, $TD) = _GL('sort, dir, ptree, year_selected, mon_selected, day_selected, TY, TM, TD');
list($nactive, $ndraft, $nprospect, $has_next, $archives) = _GL('nactive, ndraft, nprospect, has_next, archives');
list($category_filters, $user_filters, $cat_filter) = _GL('category_filters, user_filters, cat_filter');

$i          = 0;
$page       = intval($page);
$archive_id = intval(REQ('archive_id','GPG'));
$category   = cn_get_categories();

?>
<div class="panel">

    <div style="float: right;">
        Entries on page: <?php foreach (array(25, 50, 100, 250) as $_per_page) echo ' <a href="'.cn_url_modify("per_page=$_per_page").'" '.($per_page == $_per_page ? 'class="b"' : '').'>'.$_per_page.'</a> '; ?>
        <a style="color: #008080;" href="#" onclick="DoDiv('filters'); return false;">[Change filters]</a>
    </div>

    <?php

        echo i18n('Showed <b>%1</b> ', $showed);
        if ($nprospect) echo i18n('(postponed <b>%1</b>)', $nprospect);
        echo i18n(' from total <b>%1</b> ', $entries_total);

    ?>

</div>

<div class="source">

    <div style="float: right;">
        <div><b>sort by</b>  /
        <?php

            // sort method
            echo ' <a href="'.cn_url_modify('sort=date').'" '.($sort == 'date'?'class="bd"':'').'>date</a> /';
            echo ' <a href="'.cn_url_modify('sort=comments').'" '.($sort == 'comments'?'class="bd"':'').'>comments</a> /';
            echo ' <a href="'.cn_url_modify('sort=author').'" '.($sort == 'author'?'class="bd"':'').'>author</a> ';

            // sort order
            echo ' &nbsp; <a href="'.cn_url_modify('dir=a').'" '.($dir == 'a'? 'class="bd"':'').'>&uarr; ASC</a> &nbsp; ';
            echo ' <a href="'.cn_url_modify('dir=d').'" '.($dir == 'd'?'class="bd"':'').'>&darr; DESC</a> ';

        ?>
        </div>
    </div>

    Source:
    <a href="<?php echo cn_url_modify('source,year,mon,day,archive_id,page,cat_filter'); ?>" <?php if ($source == '') echo 'class="b"'; ?>>Active</a> /
    <?php if ($archives) { ?><a href="<?php echo cn_url_modify('year,mon,day,page,archive_id,cat_filter', 'source=archive'); ?>" <?php if ($source == 'archive') echo 'class="b"'; ?>>Archives (<?php echo $archives; ?>)</a> /<?php } ?>
    <a href="<?php echo cn_url_modify('year,mon,day,archive_id,page,cat_filter', 'source=draft'); ?>" <?php if ($source == 'draft') echo 'class="b"'; ?>>Draft (<?php echo $ndraft; ?>) </a>
    <div style="clear:both;"></div>

    <div style="<?php if (empty($category_filters) && empty($user_filters)) echo 'display: none;'; ?>" id="filters">

        <form action="<?php echo PHP_SELF; ?>" method="GET">

            <?php echo cn_snippet_get_hidden(); ?>
            <table>
                <tr>
                    <td>By category</td>
                    <td>By user</td>
                </tr>
                <tr>
                    <td><select name="add_category_filter" style="width: 420px;">
                        <option value="0">--</option>
                        <?php foreach ($category as $catid => $cat) echo '<option value="'.$catid.'">'.cn_htmlspecialchars($cat['name']).'</option>'; ?>
                        </select>
                    </td>


                    <td><select name="add_user_filter" style="width:200px;">
                            <option value="">--</option>
                            <?php foreach ($userlist as $user => $num) echo '<option value="'.cn_htmlspecialchars($user).'">'.cn_htmlspecialchars($user).' ('.$num.')</option>'; ?>
                        </select>
                    </td>

                    <td colspan="2"><input type="submit" value=" Add new filter " /></td>

                </tr>
                <tr>

                    <td><?php
                        foreach ($category_filters as $id) echo ' [<a href="'.cn_url_modify('cat_filter', "rm_category_filter=$id").'" style="color: red;">&ndash;</a>] <b>'.$category[$id]['name'].'</b> &nbsp; ';
                        ?>
                    </td>

                    <td><?php
                        foreach ($user_filters as $id) echo ' [<a href="'.cn_url_modify("rm_user_filter=$id").'" style="color: red;">&ndash;</a>] <b>'.cn_htmlspecialchars($id).'</b> &nbsp; ';
                        ?>
                    </td>
                </tr>

            </table>
        </form>

    </div>

</div>

<br/>

<?php // --------------------------------------------------------------------------------------------------------------- ?>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, source, archive_id'); ?>
    <input type="hidden" name="action" value="massaction">

    <table width="100%" class="std-table">
    <tr valign="top">

        <!-- Browse periods -->
        <td style="background: #f8f8f8; border: 0; display: block;" id="browse_dates">

            <!-- news list -->
            <table class="std-table" width="100%">
                <tr><th><nobr><a href="<?php echo cn_url_modify('mon,day,year,archive_id'); ?>">All periods</a></nobr></th></tr>
                <tr>
                    <td>
                        <?php if ($source == 'archive') { ?>

                            <div id="archives_list">
                            <?php foreach ($ptree as $id => $item) { ?>
                                <a class="arch<?php if ($archive_id == $id) echo '-b'; ?>" href="<?php echo cn_url_modify('mon,day', "archive_id=$id"); ?>"><em><?php echo date('Y-m-d H:i', $id); ?></em> <?php if ($item['c']) echo '<small>('.intval($item['c']).')</small>'; ?></a>
                            <?php } ?>
                            </div>

                        <?php } else { foreach ($TY as $yc => $cy) { ?>
                            <div class="years<?php if ($yc == $YS) echo '-b' . ($MS? '' : 'wm'); ?>"><a href="<?php echo cn_url_modify('mon,day', "year=$yc"); ?>"><?php echo $yc;?></a></div>
                            <?php if ($YS == $yc) { ?>
                                <?php foreach ($TM as $mc => $cm) { ?>
                                    <div class="mons<?php if ($mc == $MS) echo '-b'. ($DS? '' : 'wm'); ?>"><a href="<?php echo cn_url_modify('day', "year=$yc", "mon=$mc"); ?>"><?php echo cn_modify_month($mc);?></a></div>
                                    <?php if ($mc == $MS) { ?>
                                        <?php foreach ($TD as $dc => $cd) { ?>
                                            <div class="days<?php if ($dc == $DS) echo '-b'; ?>"><a href="<?php echo cn_url_modify("year=$yc", "mon=$mc", "day=$dc"); ?>"><?php echo $dc;?></a> <?php if ($cd) echo '<small>('.$cd.')</small>'; ?></div>
                                        <?php } } } } } } ?>
                    </td>
                </tr>
            </table>

            <!-- category list -->
            <?php if ($category) { ?>
                <br/>
                <table class="std-table" width="100%">
                    <tr><th><nobr><a href="<?php echo cn_url_modify('cat_filter'); ?>">All categories</a></nobr></th></tr>
                    <tr>
                        <td >
                            <div><a <?php if ($cat_filter === '-') echo 'class="bold" '; ?>href="<?php echo cn_url_modify('cat_filter=-'); ?>">Free news only</a></div>
                            <hr/>
                            <?php
                                foreach ($category as $id => $cat)
                                    echo '<div style="word-wrap:break-word; width: 200px;"><a '.($id == $cat_filter ? 'class="bold" ' : '').'href="'.cn_url_modify("cat_filter=$id").'">'.cn_htmlspecialchars($cat['memo'] ? $cat['memo'] : $cat['name']).'</b></div>';
                            ?>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </td>

        <!-- List news entries -->
        <td style="border: 0;">

            <table width="100%" class="std-table">

                <?php if ($showed) { ?>

                    <tr>
                        <?php echo hook('template/editnews/list_header_before'); ?>
                        <th>Title</th>
                        <th width="1" align="center" title="Comments number">Com</th>
                        <th align="center">Category</th>
                        <th width="75" align="center">Date</th>
                        <th align="center">Author</th>
                        <?php echo hook('template/editnews/list_header_after'); ?>
                        <th width="1" align="center"><input style="border: 0; background: transparent;" type=checkbox name=master_box title="Check All" onclick="check_uncheck_all('selected_news[]');"> </th>
                    </tr>

                    <?php foreach ($entries as $ID => $entry) { ?>

                        <tr style="background: <?php echo $entry['is_pros'] ? '#E0E0E0' : ($i++%2? '#F7F6F4' : '#FFFFFF'); ?>" class="hover"  >

                            <?php hook('template/editnews/list_item_before', array($ID, $entry)); ?>
                            <td>
                                <div style="word-wrap:break-word; width:200px;">
                                <?php if ($entry['can']) {

                                    $title = $entry['title'] ? $entry['title'] : '<no title:'.$entry['id'].'>';
                                    ?>
                                    <a title="<?php echo cn_htmlspecialchars($title.($entry['pg'] ? ' ('.$entry['pg'].')' : '')); ?>" href="<?php echo cn_url_modify('action=editnews', "id=$ID"); ?>"><?php echo cn_htmlspecialchars($title); ?></a>
                                <?php } else echo cn_htmlspecialchars($entry['title']); ?>
                                </div>
                            </td>
                            <td align='center'><?php echo count($entry['co']); ?></td>
                            <td align='center'><?php

                                // show category name(s)
                                $_cats = count($entry['cats']);

                                // No category
                                if ($_cats == 0) echo '-';
                                // Single category
                                elseif ($_cats == 1)
                                {
                                    echo '<a href="'.cn_url_modify('add_category_filter='.$entry['cats'][0]).'">'.$category[$entry['cats'][0]]['name'].'</a>';
                                }
                                // Multiply
                                else
                                {
                                    $_cat_name = array();
                                    foreach ($entry['cats'] as $_cid) $_cat_name[] = $category[$_cid]['name'];
                                    echo '<a href="'.cn_url_modify('add_category_filter='.join(',', $entry['cats'])).'" title="'.join(', ', $_cat_name).'"><b>multiply</b></a>';
                                }

                            ?></td>
                            <td align="center" title="<?php echo $entry['date_full']; ?>"><nobr><?php echo $entry['date']; ?></nobr></td>
                            <td align="center"><a href="<?php echo cn_url_modify('add_user_filter='.$entry['user']); ?>"><?php echo cn_htmlspecialchars($entry['user']); ?></a><sup></td>
                            <?php hook('template/editnews/list_item_after', array($ID, $entry)); ?>
                            <td align="center"><?php if ($entry['can']) { ?><input name="selected_news[]" value="<?php echo $ID; ?>" style="border:0;" type='checkbox'><?php } ?></td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>
                    <tr>
                        <td style="background: #ff8080; color: white; padding: 16px; text-align: center;">
                        --- No news were found matching your criteria ---<br>
                        </td>
                    </tr>
                <?php } ?>

            </table>

            </td>
        </tr>
    </table>

    <div class="list-footer">
        <div class="actions">

                With selected:
                <select name="subaction">
                    <option value="">-- Choose Action --</option>
                    <?php if ($source == 'draft' && test('Nua')) { ?><option title="Approve selected news" value="mass_approve"><?php echo i18n('Approve news'); ?></option><?php } ?>
                    <?php if (test('Nud')) { ?><option title="Delete all selected news" value="mass_delete"><?php echo i18n('Delete news'); ?></option><?php } ?>
                    <option title="Move all selected news to one category" value="mass_move_to_cat"><?php echo i18n('Change category'); ?></option>
                    <?php hook('template/editnews/actions'); ?>
                </select>

                <input type="submit" value="Go">
        </div>

        <!-- Make pagination -->
        <?php if ($page || $has_next) { ?>
            <div class="pagination">
                <?php
                    if ($page - $per_page >= 0)
                        echo '<a href="'.cn_url_modify('page='.($page - $per_page)).'">&lt;&lt; Prev page</a>';
                    elseif ($page && $page < $per_page)
                        echo '<a href="'.cn_url_modify('page').'">&lt;&lt; Prev page</a>';
                    else
                        echo '&lt;&lt; Prev page';

                    echo ' [<b>'.intval($page / $per_page).'</b>] ';

                    if ($has_next) echo '<a href="'.cn_url_modify('page='.($page + $per_page)).'">Next page &gt;&gt;</a>';
                ?>
            </div>
        <?php } ?>
    </div>

</form>

