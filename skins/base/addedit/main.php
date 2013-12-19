<?php

    // initialize
    list($preview_html, $vTitle, $vPage, $categories, $vCategory, $gstamp, $archive_id) = _GL('preview_html, vTitle, vPage, categories, vCategory, gstamp, archive_id');
    list($vShort, $EDITMODE, $vFull, $is_active_html, $vUseHtml, $is_draft, $vConcat, $vTags) = _GL('vShort, EDITMODE, vFull, is_active_html, vUseHtml, is_draft, vConcat, vTags');
    list($_dateD, $_dateM, $_dateY, $_dateH, $_dateI, $_dateS) = make_postponed_date($gstamp);
    list($morefields) = _GL('morefields');
    list($id) = GET('id', "GETPOST");

    // CKEDITOR INITIALIZE BLOCK
    $CKEDITOR_Active = getoption('use_wysiwyg');

    // DRAFT
    if (test('Bd'))
        $is_draft = TRUE;

    // MESSAGES BLOCK
    cn_snippet_messages();

    if ($preview_html) { ?>

        <div style="margin: 10px 0 30px 0;">
            <div style="border: 1px dashed gray; float: left; width: 50%;">
                <div style="background: #eeeeee; border-bottom: 1px solid #cccccc; margin: 0; padding: 4px; text-align: center;"><b>PREVIEW ACTIVE NEWS ENTRY</b></div>
                <div style="border:4px solid #f0f0a0;"><?php echo $preview_html; ?></div> </div>
            <div style="clear:left;"></div>
            <div style="height: 8px"></div>
        </div>

    <?php } ?>

<form method="post" action="<?php echo PHP_SELF; ?>">

    <?php cn_form_open('mod, action, archive_id, source'); ?>
    <input type="hidden" name="id" value="<?php echo intval($id); ?>" />

    <div class="section">
        <div class="name">Article title<?php if (!getoption('disable_title')) echo ' <span class="req">*</span>'; ?></div>
        <div><input type="text" style="width: 100%" value="<?php echo cn_htmlspecialchars($vTitle); ?>" name="title" tabindex=1></div>
    </div>

    <?php hook('template/AdditionalFieldsTop'); ?>

    <!-- Categories -->
    <?php if ($categories) { // Categories not exists ?>
        <div class="section">
            <div class="name">Category [<a href="#" onclick="return(tiny_msg(this));" title="Categories help you organize your news">?</a>]</div>

            <?php if (getoption('category_style') == 'select') { ?>

                <select name="category">
                    <option value="">---</option>
                    <?php foreach ($categories as $catid => $cat_data) { ?>
                        <option <?php if (in_array($catid, $vCategory)) echo 'selected="selected"'; ?> value="<?php echo $catid; ?>"><?php echo cn_htmlspecialchars($cat_data['name']); ?></option>
                    <?php } ?>
                </select>

            <?php } else { ?>

                <div class="panel category-panel">
                    <?php foreach ($categories as $catid => $cat_data) { ?>
                        <span class="category-item">
                             <input id="chkbx<?php echo $catid ?>" type="checkbox" name="category[]" <?php if ($vCategory && in_array($catid, $vCategory)) echo 'checked'; ?> value="<?php echo $catid;?>" class="chkbox-fix"/>
                             <label for="chkbx<?php echo $catid ?>">   
                                    <?php echo cn_htmlspecialchars($cat_data['name']); ?>
                             </label>
                        </span>
                    <?php } ?>
                    <div style="clear: both;"> </div>
                </div>

            <?php } ?>
        </div>
    <?php } ?>

    <!-- MORE FIELDS -->
    <?php include SKIN.'/dashboard/snippet/morefields.php'; ?>

    <!-- Short story -->
    <div class="section">

        <div class="name">Short Story<?php if (!getoption('disable_short')) echo ' <span class="req">*</span>'; ?></div>
        <div><textarea rows="12" cols="74" id="short_story" name="short_story" tabindex=2><?php echo cn_htmlspecialchars($vShort); ?></textarea></div>

        <?php if ($CKEDITOR_Active == 0) { ?>
            <div class="ballon">
                <?php $GLOBALS['callback'] = 'short_story'; include SKIN.'/dashboard/snippet/editor.php'; ?>
                <?php hook('Hook_AdditionalFieldsShort'); ?>
            </div>
        <?php } ?>

    </div>

    <!-- Full story -->
    <div class="section" id="full-story" <?php if (!$vFull) echo 'style="display: none;"'; ?>>

        <div class="name">Full Story (optional)</div>
        <div> <textarea rows="12" cols="74" id="full_story" name="full_story" tabindex=3><?php echo cn_htmlspecialchars($vFull); ?></textarea> </div>

        <?php if ($CKEDITOR_Active == 0) { ?>
            <div class="ballon">
                <?php $GLOBALS['callback'] = 'full_story'; include SKIN.'/dashboard/snippet/editor.php'; ?>
                <?php hook('Hook_AdditionalFieldsFull'); ?>
            </div>
        <?php } ?>

    </div>

    <div class="section">

        <div style="float: right">            
            <?php if ($EDITMODE && test('Nud') && $archive_id == 0) { ?>
                <input type="button" onClick="if (confirm('Please confirm')) { window.location = '<?php echo cn_url_modify('action=delete', cn_snippet_digital_signature('a')); ?>'; return true; } else return false;" value="Delete">
            <?php } ?>
            <?php if (empty($_POST['full_story'])) { ?><input type=button onClick="ShowOrHide('full-story','');" value="Toggle Full-Story"><?php } ?>
            <input type=submit name="do_editsave" style='font-weight: bold' title="Post the New Article" value="     <?php if (!$EDITMODE) echo 'Add News'; else echo 'Edit'; ?>     " accesskey="s">
        </div>
        <input type="checkbox" name="preview" value="preview" style="border:0; background-color: transparent; vertical-align: middle;"> <span style="font-size: 14px;">Preview</span>
        <div style="clear: both"></div>
    </div>

    <!-- SOME ADDITIONAL FIELDS -->

    <br/>
    <!-- Page alias -->
    <div class="section">

        <div class="name">Article meta  [<a href="#" onclick="return(tiny_msg(this));" title="Some additional optional data">?</a>]</div>
        <hr/>

        <div class="name">Page alias</div>
        <div><input type="text" style="width: 100%" value="<?php echo cn_htmlspecialchars($vPage); ?>" name="page" tabindex=4></div>
        <div style="font-size: 10px; color: #808080; margin: 0 0 15px 0;">The unique name of the page. Use $page_alias parameter before include show_news.php, charset [a-zA-Z0-9_-]</div>

        <!-- tags line -->
        <div class="section">
            <div class="name">Tagline</div>
            <div><input type="text" style="width: 100%" value="<?php echo cn_htmlspecialchars($vTags); ?>" name="tags" tabindex=5></div>
            <div style="font-size: 10px; color: #808080; margin: 0 0 15px 0;">List the tags for news, separated by commas</div>
        </div>
    </div>

    <?php hook('template/AdditionalFieldsBottom'); ?>

    <div class="section" id="options">

        <div class="name">Article options</div>
        <hr/>

        <div>
            <?php hook('template/addedit_news_opts'); ?>

            <div>                
                <?php if ($CKEDITOR_Active == 0 && $is_active_html) { ?>                
                    <div><label for='html'> <input id='html' class="chkbox-fix" type="checkbox" value="1" name="if_use_html" <?php if ($vUseHtml) echo 'checked'; ?>> Use HTML in this article</label></div>
                <?php } else { ?>
                    <?php if($is_active_html) { ?>                         
                        <div><label for='html'> <input id='html' class="chkbox-fix" type="checkbox" value="1" name="if_use_html" checked="checked" disabled="disabled"> Use HTML in this article</label></div>
                    <?php } else { ?>
                        <input type="hidden" name="if_use_html" value="1"/>
                    <?php } ?>
                <?php } ?>
                <div><label for='html'> <input id='concat' class="chkbox-fix" type="checkbox" value="Y" name="concat" <?php if ($vConcat) echo 'checked'; ?>> Concate short and full story</label></div>
            </div>
            <br/>

            <div>
                Publish date
                <select name="from_date_day"><?php echo $_dateD; ?></select>
                <select name="from_date_month"><?php echo $_dateM; ?></select>
                <select name="from_date_year"><?php echo $_dateY; ?></select>
                @ <input value='<?php echo $_dateH; ?>' style="text-align: center;" name="from_date_hour" size=3 type=text title='24 Hour format [hh]'  /> :
                <input value="<?php echo $_dateI; ?>" style="text-align: center;" name="from_date_minutes" size=3 type=text title='Minutes [mm]' />
                <input value="<?php echo $_dateS; ?>" style="text-align: center;" name="from_date_seconds" size=3 type=text title='Seconds [ss]' />
            </div>

            <br/>
            <div>
                <?php if (!$archive_id) { ?>

                    <?php if (!test('Bd')) { ?>

                        <label for='active'><input checked id='active' style="border:0; background-color: transparent" type=radio value="active" name="postpone_draft"> <b>Normal</b>, article is active</label>
                        &nbsp;&nbsp;&nbsp;

                    <?php } ?>

                    <label for='draft'><input id='draft' style="border:0; background-color: transparent" type=radio value="draft" <?php if ($is_draft) echo "checked"; ?> name="postpone_draft"> <b>Draft</b>, article is unapproved</label>

                <?php } ?>
            </div>


        </div>

        <?php hook('template/AdditionalFieldsOptions'); ?>

    </div>
</form>

<?php if ($CKEDITOR_Active) cn_snippet_ckeditor('full_story, short_story');