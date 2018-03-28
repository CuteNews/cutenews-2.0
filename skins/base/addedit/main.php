<?php

    // initialize
    list($preview_html,$preview_html_full , $vTitle, $vPage, $categories, $vCategory, $gstamp, $archive_id) = _GL('preview_html, preview_html_full, vTitle, vPage, categories, vCategory, gstamp, archive_id');    
    list($vShort, $EDITMODE, $vFull, $is_active_html, $vUseHtml, $is_draft, $vConcat, $vTags) = _GL('vShort, EDITMODE, vFull, is_active_html, vUseHtml, is_draft, vConcat, vTags');
    list($_dateD, $_dateM, $_dateY, $_dateH, $_dateI, $_dateS) = make_postponed_date($gstamp);
    list($morefields) = _GL('morefields');
    list($id) = GET('id', "GETPOST");

    // CKEDITOR INITIALIZE BLOCK
    $CKEDITOR_Active = getoption('use_wysiwyg');
    $is_draft = test('Bd');

    cn_snippet_messages();
	cn_snippet_bc();

    if ($preview_html) { ?>

        <section>
            <div class="container">

                <div style="margin: 10px 0 30px 0;">
                    <div style="border: 1px dashed gray; float: left; width: 100%;">
                        <div style="background: #eeeeee; border-bottom: 1px solid #cccccc; margin: 0; padding: 4px; text-align: center;"><b>PREVIEW ACTIVE NEWS ENTRY</b></div>
                        <div style="border:4px solid #f0f0a0;"><?php echo $preview_html; ?></div> </div>
                    <div style="clear:left;"></div>
                    <div style="height: 8px"></div>
                </div>

                <?php if($preview_html_full) { ?>
                    <div style="margin: 10px 0 30px 0;">
                        <div style="border: 1px dashed gray; float: left; width: 100%;">
                            <div style="background: #eeeeee; border-bottom: 1px solid #cccccc; margin: 0; padding: 4px; text-align: center;"><b>PREVIEW FULL STORY ENTRY</b></div>
                            <div style="border:4px solid #f0f0a0;"><?php echo $preview_html_full; ?></div> </div>
                        <div style="clear:left;"></div>
                        <div style="height: 8px"></div>
                    </div>
                <?php } ?>
            </div>
        </section>

    <?php } ?>

<section>
	<div class="container">

		<form method="post" action="<?php echo PHP_SELF; ?>">

			<?php cn_form_open('mod, action, archive_id, source'); ?>
			<input type="hidden" name="id" value="<?php echo intval($id); ?>" />

			<div>
				<label for="articletitle"><?php echo i18n('Article title');?>:</label>
				<?php if (!getoption('disable_title')) { echo ' <span class="required">*</span>'; } ?>
				<div><input id="articletitle" class="form-control" type="text" value="<?php echo cn_htmlspecialchars($vTitle); ?>" name="title" tabindex="1" <?php if (!getoption('disable_title')) { /*echo ' required ';*/ } ?> ></div>
			</div>

			<?php hook('template/AdditionalFieldsTop'); ?>

			<!-- Categories -->
			<?php if ($categories) { // Categories not exists ?>

                <br/>
				<div>
                    <label for="categories"><?php echo i18n('Category');?>:</label> [<a href="#" onclick="swal('<?php echo i18n('Categories help you organize your news');?>','','info');">?</a>]

					<?php if (getoption('category_style') == 'select') { ?>

						<select id="categories" name="category">
							<option value="">---</option>
							<?php foreach ($categories as $catid => $cat_data) { ?>
								<option <?php if (in_array($catid, $vCategory)) { echo 'selected="selected"'; } ?> value="<?php echo $catid; ?>"><?php echo cn_htmlspecialchars($cat_data['name']); ?></option>
							<?php } ?>
						</select>

					<?php } else { ?>

						<div class="well">
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
			<?php //include SKIN.'/dashboard/snippet/slider.php'; ?>

			<!--Publish date-->
			<div>
                <br/>
                <div class="form-group">
                    <label for="publish-date"><?php echo i18n('Publish date');?></label>
                        <div class="well">

                            <select id="" name="from_date_day" class="selectpicker" data-hide-disabled="true" data-live-search="true" data-size="5" data-style="btn-primary" data-width="fit">
                                <?php echo $_dateD; ?>
                            </select>
                            <select id="" name="from_date_month" class="selectpicker" data-hide-disabled="true" data-live-search="true" data-size="5" data-style="btn-primary" data-width="fit">
                                <?php echo $_dateM; ?>
                            </select>
                            <select id="" name="from_date_year" class="selectpicker" data-hide-disabled="true" data-live-search="true" data-size="5" data-style="btn-primary" data-width="fit">
                                <?php echo $_dateY; ?>
                            </select>

                            <label>@</label>

                            <input value="<?php echo $_dateH; ?>" class="form-control" style="width:50px;display:inline;" name="from_date_hour" size=3 type=text title='24 Hour format [hh]'  /><label>:</label>
                            <input value="<?php echo $_dateI; ?>" class="form-control" style="width:50px;display:inline;" name="from_date_minutes" size=3 type=text title='Minutes [mm]' /><label>:</label>
                            <input value="<?php echo $_dateS; ?>" class="form-control" style="width:50px;display:inline;" name="from_date_seconds" size=3 type=text title='Seconds [ss]' />

                        </div>
                </div>
			</div>
			<!--short story, full story, page alias, article options-->
			<div>
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#menu1"><?php echo i18n('Short Story'); ?><?php if (!getoption('disable_short')) { echo ' <span class="required">*</span>'; } ?></a></li>
					<li><a data-toggle="tab" href="#menu2"><?php echo i18n('Full Story'); ?> (optional)</a></li>
					<li><a data-toggle="tab" href="#menu3"><?php echo i18n('Page alias and tagline'); ?>: </a></li>
					<li><a data-toggle="tab" href="#menu4"><?php echo i18n('Article options');?></a></li>
				</ul>

				<div class="tab-content card">
					<div id="menu1" class="tab-pane fade in active">

						<!-- Short story -->
						<div>

							<!--label for="short_story">Short Story:</label-->
							<textarea class="form-control" rows="12" cols="74" id="short_story" name="short_story" tabindex="2" <?php if (!getoption('disable_short')) { echo ' required '; } ?> ><?php echo cn_htmlspecialchars($vShort); ?></textarea>

							<?php if ($CKEDITOR_Active == 0) { ?>
								<div class="well">
									<?php $GLOBALS['callback'] = 'short_story'; include SKIN.'/dashboard/snippet/editor.php'; ?>
									<?php hook('Hook_AdditionalFieldsShort'); ?>
								</div>
							<?php } ?>

						</div>
					</div>
					<div id="menu2" class="tab-pane fade">

						<!-- Full story -->
						<div id="full-story">

						   <!--label for="full_story">Full Story (optional):</label-->
						   <textarea class="form-control" rows="12" cols="74" id="full_story" name="full_story" tabindex=3><?php echo cn_htmlspecialchars($vFull); ?></textarea>

							<?php if ($CKEDITOR_Active == 0) { ?>
								<div class="well">
									<?php $GLOBALS['callback'] = 'full_story'; include SKIN.'/dashboard/snippet/editor.php'; ?>
									<?php hook('Hook_AdditionalFieldsFull'); ?>
								</div>
							<?php } ?>

						</div>
					</div>

					<!-- SOME ADDITIONAL FIELDS -->
					<div id="menu3" class="tab-pane fade">
						<div class="well">
							<!-- Page alias -->
							<div class="form-group">
							<label for="page_alias" >Page alias:</label>
							<input id="page_alias" class="form-control" type="text" value="<?php echo cn_htmlspecialchars($vPage); ?>" name="page" tabindex=4>
							<small>The unique name of the page. Use $page_alias parameter before include show_news.php, charset [a-zA-Z0-9_-]</small>
							</div>
							<!-- tags line -->
							<div class="form-group">
							<label for="tagline" >Tagline:</label>
							<input id="tagline" class="form-control" type="text" value="<?php echo cn_htmlspecialchars($vTags); ?>" name="tags" tabindex=5>
							<small>List the tags for news, separated by commas</small>
							</div>
						</div>

						<?php hook('template/AdditionalFieldsBottom'); ?>
					</div>

					<div id="menu4" class="tab-pane fade">
						<div class="well" id="options">
							<div>
								<?php hook('template/addedit_news_opts'); ?>

								<div class="form-group">

                                    <!-- CkEditor disabled, and active html -->
									<?php if ($CKEDITOR_Active == 0 && $is_active_html) { ?>

                                        <div class="checkbox">
											<label for="html">
                                                <input id='html' type="checkbox" value="1" name="if_use_html" <?php if ($vUseHtml) echo 'checked'; ?> /> Use HTML in this article
                                            </label>
										</div>

									<?php } else { ?>

										<?php if ($is_active_html) { ?>
											<div class="checkbox">
												<label for='html'> <input id='html' type="checkbox" value="1" name="if_use_html" checked="checked" disabled="disabled"> Use HTML in this article</label>
											</div>
										<?php } else { ?>
											<input type="hidden" name="if_use_html" value="1"/>
										<?php } ?>

									<?php } ?>

									<div class="checkbox">
										<label for='concat'> <input id='concat' type="checkbox" value="Y" name="concat" <?php if ($vConcat) echo 'checked'; ?>> Concate short and full story</label>
									</div>
								</div>

								<div class="form-group">
									<?php if (!$archive_id) { ?>

										<?php if (!test('Bd')) { ?>
										<div class="radio">
											<label for='active'><input checked id="active" type="radio" value="active" name="postpone_draft"> <b>Normal, article is active</b></label>
											&nbsp;&nbsp;&nbsp;
										</div>

										<?php } ?>
										<div class="radio">
											<label for='draft'><input id="draft" type=radio value="draft" <?php if ($is_draft) echo "checked"; ?> name="postpone_draft"> <b>Draft, article is unapproved</b></label>
										</div>
									<?php } ?>
								</div>


							</div>

							<?php hook('template/AdditionalFieldsOptions'); ?>

						</div>
					</div>
				</div>
			</div>

			<!--Buttons Actions-->
			<div>
				<div class="well">
					<div class="pull-right">
						<?php if ($EDITMODE && test('Nud') && $archive_id == 0) { ?>
							<input class="btn btn-primary" type="button" onClick="if (confirm('Please confirm')) { window.location = '<?php echo cn_url_modify('action=delete', cn_snippet_digital_signature('a')); ?>'; return true; } else return false;" value="<?php echo i18n('Delete');?>">
						<?php } ?>
						<?php if (empty($_POST['full_story'])) { ?>
							<!--input class="btn btn-primary" type="button" onClick="ShowOrHide('full-story','');" value="Toggle Full-Story"-->
						<?php } ?>

						<input class="btn btn-primary" type="submit" name="do_editsave" title="Post the New Article" value="     <?php if (!$EDITMODE) { echo i18n('Add News'); } else { echo i18n('Edit'); } ?>     " accesskey="s">

					</div>
					<input type="hidden" name="preview" id="chkPreview" value=""/>
					<input class="btn btn-primary" type="submit" value="  <?php echo i18n('Preview');?>  " onclick="CheckPreview();" />
					<div style="clear: both"></div>
				</div>
			</div>
		</form>
	</div>
</section>

<?php if ($CKEDITOR_Active) cn_snippet_ckeditor('full_story, short_story');