<?php

    // init
    list($source, $page, $per_page, $entries, $showed, $entries_total, $userlist) = _GL('source, page, per_page, entries, entries_showed:intval, entries_total:intval, userlist');
    list($sort, $dir, $ptree, $YS, $MS, $DS, $TY, $TM, $TD) = _GL('sort, dir, ptree, year_selected, mon_selected, day_selected, TY, TM, TD');
    list($nactive, $ndraft, $nprospect, $has_next, $archives) = _GL('nactive, ndraft, nprospect, has_next, archives');
    list($category_filters, $user_filters, $cat_filter) = _GL('category_filters, user_filters, cat_filter');

    $i          = 0;
    $page       = intval($page);
    $archive_id = intval(REQ('archive_id','GPG'));
    $category   = cn_get_categories();

    // MESSAGES BLOCK
    cn_snippet_messages();
    cn_snippet_bc();

?>
<section>
	<div class="container">

        <div class="row breadcrumb">

            <div class="col-sm-6">
                <?php echo i18n('Entries on page'); ?>: <?php foreach (array(25, 50, 100, 250) as $_per_page) { echo ' <a href="'.cn_url_modify('mod=editnews', "per_page=$_per_page").'" '.($per_page == $_per_page ? 'class="bd"' : '').'>'.$_per_page.'</a> '; } ?>
                <a class="btn btn-primary" href="#" onclick="DoDiv('filters'); return false;">[Change filters]</a>
            </div>

            <div class="col-sm-6">
            <?php
                echo i18n('Showed <b>%1</b> ', $showed);
                if ($nprospect) { echo i18n('(postponed <b>%1</b>)', $nprospect); }
                echo i18n(' from total <b>%1</b> ', $entries_total);
            ?>
            </div>

        </div>

        <div class="row breadcrumb">
            <div class="col-sm-6">

                <b>sort by</b>  /
                <?php

                    // sort method
                    echo ' <a href="'.cn_url_modify('mod=editnews', 'sort=date').'" '.($sort == 'date' ? 'class="bd"':'') . '>date</a> /';
                    echo ' <a href="'.cn_url_modify('mod=editnews', 'sort=comments').'" '.($sort == 'comments' ? 'class="bd"':'').'>comments</a> /';
                    echo ' <a href="'.cn_url_modify('mod=editnews', 'sort=author').'" '.($sort == 'author' ? 'class="bd"':'').'>author</a> ';

                    // sort order
                    echo ' &nbsp; <a href="'.cn_url_modify('mod=editnews', 'dir=a').'" '.($dir == 'a' ? 'class="bd"':'').'>&uarr; ASC</a> &nbsp; ';
                    echo ' <a href="'.cn_url_modify('mod=editnews', 'dir=d').'" '.($dir == 'd' ? 'class="bd"':'').'>&darr; DESC</a> ';
                ?>

            </div>

            <div class="col-sm-6">

                Source:
                <a href="<?php echo cn_url_modify('mod=editnews', 'source,year,mon,day,archive_id,page,cat_filter'); ?>" <?php if ($source == '') { echo 'class="bd"'; } ?>>Active</a> /
                <?php if ($archives) { ?><a href="<?php echo cn_url_modify('mod=editnews', 'year,mon,day,page,archive_id,cat_filter', 'source=archive'); ?>" <?php if ($source == 'archive') echo 'class="bd"'; ?>>Archives (<?php echo $archives; ?>)</a> /<?php } ?>
                <a href="<?php echo cn_url_modify('mod=editnews', 'year,mon,day,archive_id,page,cat_filter', 'source=draft'); ?>" <?php if ($source == 'draft') { echo 'class="bd"'; } ?>>Draft (<?php echo $ndraft; ?>) </a>

            </div>
            <div style="clear:both;"></div>

            <div style="<?php if (empty($category_filters) && empty($user_filters)) { echo 'display: none;'; } ?>" id="filters">

                <form action="<?php echo PHP_SELF; ?>" method="GET">

                    <?php echo cn_snippet_get_hidden(); ?>
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                            <td>By category</td>
                            <td>By user</td>
                        </tr>
                        <tr>
                            <td><select name="add_category_filter" class="selectpicker" data-hide-disabled="true" data-live-search="false" data-size="5" data-style="btn-default" data-width="fit" >
                                <option value="0">--</option>
                                <?php foreach ($category as $catid => $cat) { echo '<option value="'.$catid.'">'.cn_htmlspecialchars($cat['name']).'</option>'; } ?>
                                </select>
                            </td>

                            <td>
                                <select name="add_user_filter" class="selectpicker" data-hide-disabled="true" data-live-search="false" data-size="5" data-style="btn-default" data-width="fit" >
                                    <option value="">--</option>
                                    <?php foreach ($userlist as $user => $num) { echo '<option value="'.cn_htmlspecialchars($user).'">'.cn_htmlspecialchars($user).' ('.$num.')</option>'; } ?>
                                </select>
                                <button class="btn btn-primary" type="submit" value=" Add new filter ">Add new filter</button>
                            </td>

                        </tr>
                        <tr>

                            <td>
                            <?php
                                arsort($category_filters);
                                foreach ($category_filters as $id)
                                {
                                    if(isset($category[$id]))
                                    {
                                        echo ' [<a href="'.cn_url_modify('mod=editnews', 'cat_filter', "rm_category_filter=$id").'" style="color: red;">&ndash;</a>] <b>'.$category[$id]['name'].'</b> &nbsp; ';
                                    }
                                }
                            ?>
                            </td>
                            <td>
                            <?php
                                arsort($user_filters);
                                foreach ($user_filters as $id)
                                {
                                    echo ' [<a href="'.cn_url_modify('mod=editnews', "rm_user_filter=$id").'" style="color: red;">&ndash;</a>] <b>'.cn_htmlspecialchars($id).'</b> &nbsp; ';
                                }
                            ?>
                            </td>
                        </tr>

                    </table>

                </form>

            </div>
        </div>

		<?php // --------------------------------------------------------------------------------------------------------------- ?>
		<div class="row">
		<form action="<?php echo PHP_SELF; ?>" method="POST">

			<?php cn_form_open('mod, source, archive_id'); ?>
			<input type="hidden" name="action" value="massaction">

            <!-- Browse periods -->
            <div class="col-sm-2" id="browse_dates">

                <!-- news list -->
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">

                    <tr>
                        <th><nobr><a href="<?php echo cn_url_modify('mod=editnews', 'mon,day,year,archive_id'); ?>">All periods</a></nobr></th>
                    </tr>
                    <tr>
                        <td>
                            <?php if ($source == 'archive') { ?>

                                <div id="archives_list">
                                    <?php foreach ($ptree as $id => $item)
                                    { ?>
                                        <a class="arch<?php if ($archive_id == $id) { echo '-b'; } ?>" href="<?php echo cn_url_modify('mod=editnews', 'mon,day', "archive_id=$id"); ?>">
                                            <em><?php echo date('Y-m-d H:i', $id); ?></em> <?php if ($item['c']) { echo '<small>(' . intval($item['c']) . ')</small>'; } ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php
                            } else {

                                foreach ($TY as $yc => $cy) { ?>

                                    <div class="years<?php if ($yc == $YS) { echo '-b' . ($MS ? '' : 'wm'); } ?>">
                                        <a href="<?php echo cn_url_modify('mod=editnews', 'mon,day', "year=$yc"); ?>"><?php echo $yc; ?></a>
                                    </div>

                                    <?php if ($YS == $yc) { ?>

                                        <?php foreach ($TM as $mc => $cm) { ?>

                                            <div class="mons<?php if ($mc == $MS) { echo '-b' . ($DS ? '' : 'wm');} ?>">
                                                <a href="<?php echo cn_url_modify('mod=editnews', 'day', "year=$yc", "mon=$mc"); ?>"><?php echo cn_modify_month($mc); ?></a>
                                            </div>

                                            <?php if ($mc == $MS) { ?>

                                                <?php foreach ($TD as $dc => $cd) { ?>

                                                    <div class="days<?php if ($dc == $DS){ echo '-b';} ?>">
                                                        <a href="<?php echo cn_url_modify('mod=editnews', "year=$yc", "mon=$mc", "day=$dc"); ?>"><?php echo $dc; ?></a>
                                                        <?php if ($cd) { echo '<small>(' . $cd . ')</small>'; } ?>
                                                    </div>

                                                <?php }
                                            }
                                        }
                                    }
                                }
                            } ?>
                        </td>
                    </tr>
                </table>
                </div>

                <!-- category list -->
                <?php if ($category) { ?>
                    <br/>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <tr><th><nobr><a href="<?php echo cn_url_modify('mod=editnews', 'cat_filter'); ?>">All categories</a></nobr></th></tr>
                        <tr>
                            <td >
                                <div>
                                    <a <?php if ($cat_filter === '-') { echo 'class="bold" '; } ?>href="<?php echo cn_url_modify('mod=editnews', 'cat_filter=-'); ?>">Without category</a>
                                </div>
                                <hr/>
                                <?php foreach ($category as $id => $cat) {
                                    echo '<div style="word-wrap:break-word; "><a '.(($id == $cat_filter) || in_array($id, $category_filters) ? 'class="bold" ' : '').'href="'.cn_url_modify('mod=editnews', "cat_filter=$id").'">'.cn_htmlspecialchars($cat['memo'] ? $cat['memo'] : $cat['name']).'</b></div>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    </div>
                <?php } ?>

            </div>

            <!-- List news entries -->
            <div class="col-sm-10">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">

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

                            <tr style="background: <?php echo $entry['is_pros'] ? '#E0E0E0' : ($i++ % 2? '#F7F6F4' : '#FFFFFF'); ?>" class="hover"  >

                                <?php hook('template/editnews/list_item_before', array($ID, $entry)); ?>
                                <td>
                                    <div style="word-wrap: break-word;">
                                    <?php if ($entry['can']) {

                                        $pg = isset($entry['pg']) ? $entry['pg'] : '';
                                        $title = $entry['title'] ? $entry['title'] : '<no title:'.(isset($entry['id']) ? $entry['id'] : 0).'>';?>
                                        <a title="<?php echo cn_htmlspecialchars($title.($pg ? ' ('.$pg.')' : '')); ?>" href="<?php echo cn_url_modify('mod=editnews', 'action=editnews', "id=$ID"); ?>"><?php echo cn_htmlspecialchars($title); ?></a>

                                    <?php } else {

                                        if (getoption('rw_engine')) {
                                            echo '<a href="'.PHP_SELF . cn_put_alias(intval($entry['id'])).'">'.cn_htmlspecialchars($entry['title']).'</a>';
                                        } else {
                                            echo '<a href="'.PHP_SELF.'?id=' . cn_put_alias(intval($entry['id'])).'">'.cn_htmlspecialchars($entry['title']).'</a>';
                                        }
                                    } ?>
                                    </div>
                                </td>
                                <td align='center'><?php $co = isset($entry['co']) ? $entry['co'] : array(); echo count($co); ?></td>
                                <td align='center'><?php

                                    // show category name(s)
                                    $_cats = count($entry['cats']);

                                    // No category
                                    if (($_cats == 0 || count($category) == 0)) {
                                        echo '-';
                                    }
                                    // Single category
                                    elseif ($_cats == 1) {

                                        if(isset($category[$entry['cats'][0]])) {
                                            echo '<a href="'.cn_url_modify('mod=editnews', 'add_category_filter='.$entry['cats'][0]).'">'.$category[$entry['cats'][0]]['name'].'</a>';
                                        } else {
                                            echo '-';
                                        }

                                    // Multiply
                                    } else {

                                        $_cat_name = array();
                                        foreach ($entry['cats'] as $_cid) {
                                            if(isset($category[$_cid])) {
                                                $_cat_name[] = $category[$_cid]['name'];
                                            }
                                        }

                                        echo '<a href="'.cn_url_modify('mod=editnews', 'add_category_filter='.join(',', $entry['cats'])).'" title="'.join(', ', $_cat_name).'"><b>multiply</b></a>';
                                    }

                                ?></td>
                                <td align="center" title="<?php echo $entry['date_full']; ?>"><nobr><?php echo $entry['date']; ?></nobr></td>
                                <td align="center"><a href="<?php echo cn_url_modify('mod=editnews', 'add_user_filter='.$entry['user']); ?>"><?php echo cn_htmlspecialchars($entry['user']); ?></a><sup></td>
                                <?php hook('template/editnews/list_item_after', array($ID, $entry)); ?>
                                <td align="center"><?php if ($entry['can']) { ?><input name="selected_news[]" value="<?php echo $ID; ?>" style="border:0;" type='checkbox'><?php } ?></td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <?php if ($source == 'archive') { ?>
                                <td style="background: #f0f0f0;  padding: 16px; text-align: center;">
                                    --- Please, select archive to show news ---<br>
                                </td>
                            <?php } else { ?>
                                <td style="background: #ffe0e0;  padding: 16px; text-align: center;">
                                    --- No news were found matching your criteria ---<br>
                                </td>
                            <?php }  ?>
                        </tr>

                    <?php } ?>
                    </table>
                </div>

                <div class="form-group pull-right">
                    <label>With selected</label>
                    <select name="subaction" class="selectpicker" data-hide-disabled="true" data-live-search="false" data-size="5" data-style="btn-default" data-width="fit">
                        <option value="">-- Choose Action --</option>
                        <?php if ($source == 'draft' && test('Nua')) { ?><option value="mass_approve"><?php echo i18n('Approve news'); ?></option><?php } ?>
                        <?php if (test('Nud')) { ?><option value="mass_delete"><?php echo i18n('Delete news'); ?></option><?php } ?>
                        <?php if (test('Nua')) { ?><option value="switch_to_html"><?php echo i18n('Switch to HTML'); ?></option><?php } ?>
                        <option value="mass_move_to_cat"><?php echo i18n('Change category'); ?></option>
                        <?php hook('template/editnews/actions'); ?>
                    </select>

                    <input class="btn btn-primary" type="submit" value="Go">
                </div>
                <div style="clear:both;"></div>

            </div>
			<!--fin container-->

			<div class="list-footer">

				<!-- Make pagination -->
				<?php if ($page || $has_next) { ?>
					<div class="pagination">
						<?php
							if ($page - $per_page >= 0)
							{
								echo '<a href="'.cn_url_modify('mod=editnews', 'page='.($page - $per_page)).'">&lt;&lt; Prev page</a>';
							}
							elseif ($page && $page < $per_page)
							{
								echo '<a href="'.cn_url_modify('mod=editnews', 'page').'">&lt;&lt; Prev page</a>';
							}
							else
							{
								echo '&lt;&lt; Prev page';
							}

							echo ' [<b>'.intval($page / $per_page).'</b>] ';

							if ($has_next)
							{
								echo '<a href="'.cn_url_modify('mod=editnews', 'page='.($page + $per_page)).'">Next page &gt;&gt;</a>';
							}
						?>
					</div>
				<?php } ?>
			</div>

		</form>
		</div>
	</div>
</section>



