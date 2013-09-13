<?php

list($sub, $categories, $all_tpls) = _GL('sub, categories, all_tpls');

// Options
list($rss_news_include_url, $rss_encoding, $rss_language, $rss_title) = _GL('rss_news_include_url, rss_encoding, rss_language, rss_title');

cn_snippet_bc();

?>
<ul class="sysconf_top">
    <li<?php if ($sub == '') echo " class='selected'"; ?>><a href="<?php echo cn_url_modify('sub='); ?>">Integrate news in site</a></li>
    <li<?php if ($sub == 'rss') echo " class='selected'"; ?>><a href="<?php echo cn_url_modify('sub=rss'); ?>">RSS Setup</a></li>
</ul>
<div style="clear: both;"></div>

<form action="<?php echo PHP_SELF; ?>" method="POST">
<?php cn_form_open('mod, opt, sub'); ?>

<!-- site news integration -->
<?php if ($sub == '') { ?>

    <p>Welcome to the News Integration Wizard. This tool will help you to integrate the news that you have published using CuteNews, into your existing Webpage.</p>

    <?php if (request_type('POST')) { // Show form ?>
        <div class="notice-form">
            <h2>Insert this code in site </h2>
            <pre>
&lt;?php

<?php

    // Detect numeric for number
    if (REQ('w_number'))
    {
        if (preg_match('/^[0-9]+$/', REQ('w_number')))
            echo '    $number = '.REQ('w_number').';' . "\n";
        else
            echo '    $number = "'.cn_htmlspecialchars(REQ('w_number')).'";' . "\n";
    }

    // Detect numeric for start from
    if (REQ('w_start_from'))
    {
        if (preg_match('/^[0-9]+$/', REQ('w_start_from')))
            echo '    $start_from = '.REQ('w_start_from').';' . "\n";
        else
            echo '    $start_from = "'.cn_htmlspecialchars(REQ('w_start_from')).'";' . "\n";
    }

    if (REQ('w_template') && REQ('w_template') !== 'default') echo '    $template = "'.cn_htmlspecialchars(REQ('w_template')).'";' . "\n";
    if (REQ('w_allcategory') == FALSE && REQ('w_category')) echo '    $category = "'.(is_array(REQ('w_category')) ? cn_htmlspecialchars(join(',', REQ('w_category'))) : '').'";' . "\n";
    if (REQ('w_reverse')) echo '    $reverse = TRUE;' . "\n";
    if (REQ('w_only_active')) echo '    $only_active = TRUE;' . "\n";
    if (REQ('w_static')) echo '    $static = TRUE;' . "\n";

?>    include("<?php echo SERVDIR.'/show_news.php'; ?>");

?&gt;</pre>
        </div>

    <?php } ?>

    <table class="std-table" width="100%">

        <tr>
            <td bgcolor="#F7F6F4" style="padding: 3px; border-bottom:1px solid gray;" colspan="2"><b>Quick Customization...</b></td>
        </tr>

        <tr>
            <td><b><br>Number of Active News to Display:</b></td>
            <td rowspan="2" align="center"><input style="text-align: center" name="w_number" size="11" value="<?php echo cn_htmlspecialchars(REQ('w_number')); ?>"></td>
        </tr>

        <tr>
            <td style="padding-left:10px">
                <i>if the active news are less then the specified number to show, the rest
                of the news will be fetched from the archives (if any)</i>
            </td>
        </tr>

        <tr>
            <td><b><br>Template to Use When Displaying News:</b></td>
            <td rowspan="2" align="center">
                <select name="w_template">
                    <?php foreach ($all_tpls as $template_id => $template) { ?>
                        <option <?php if (REQ('w_template') == $template_id) echo 'selected="selected"'; ?> value="<?php echo $template_id; ?>)"><?php echo cn_htmlspecialchars(ucfirst($template)); ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>

        <tr>
            <td  style="padding-left:10px"><i>using different templates you can customize the look of your news, comments etc.</i></td>
        </tr>

        <tr>
            <td><b>Categories to Show News From:</b></td>
            <td rowspan="2" align="center">

                <?php if (REQ('w_category')) { ?>

                    <select <?php if (REQ('w_allcategory')) echo 'style="display: none;"'; else echo 'style="display: block;"'; ?> name="w_category[]" id="category" multiple="">
                        <?php if (is_array($categories)) foreach ($categories as $id => $cat) { ?>
                            <option <?php if (is_array(REQ('w_category')) && in_array($id, REQ('w_category'))) echo 'selected="selected"'; ?> value="<?php echo $id; ?>">(ID:<?php echo $id; ?>) <?php echo $cat['name']; ?></option>
                        <?php } ?>
                    </select>
                        <br>
                    <label for="allcategory">
                        <input id="allcategory" <?php if (REQ('w_allcategory')) echo "checked=''"; ?> onclick="if (this.checked) { getElementById('category').style.display='none';} else {getElementById('category').style.display='';}" type="checkbox" value="yes" name="w_allcategory"> Or show from all allowed categories
                    </label>

                <?php } else { ?><em>All</em><?php } ?>

            </td>
        </tr>

        <tr>
            <td style="padding-left:10px">
                <i>you can specify only from which categories news will be displayed, hold CTRL to select multiple categories (if any)</i>
            </td>
        </tr>

        <tr>
            <td bgcolor="#F7F6F4" style="padding:3px; border-bottom:1px solid gray;" colspan="2"><b>Advanced Settings...</b></td>
        </tr>

        <tr>
            <td><b>Start 'Displaying' From...</b></td>
            <td rowspan="2" align="center"><input name="w_start_from" size="11" style="text-align: center" value="<?php echo cn_htmlspecialchars(REQ('w_start_from')); ?>"></td>
        </tr>

        <tr>
            <td style="padding-left:10px">
            <i>if Set, the displaying of the news will be started from the specified
            number (eg. if set to 2 - the first 2 news will be skipped and the rest
            shown)</i>
            </td>
        </tr>

        <tr>
            <td><b>Reverse News Order:</b></td>
            <td rowspan="2" align="center"><input type="checkbox" <?php if (REQ('w_reverse')) echo "checked=''"; ?> value="yes" name="w_reverse"></td>
        </tr>

        <tr>
            <td style="padding-left:10px"><i>if Yes, the order of which the news are shown will be reversed</i></td>
        </tr>

        <tr>
            <td><b>Show Only Active News:</b></td>
            <td rowspan="2" align="center"><input type="checkbox" <?php if (REQ('w_only_active')) echo "checked=''"; ?> value="yes" name="w_only_active"></td>
        </tr>

        <tr>
            <td style="padding-left:10px">
                <i>if Yes, even if the number of news you requested to be shown is bigger
                than all active news, no news from the archives will be shown</i>
            </td>
        </tr>

        <tr>
            <td><b>Static Include:</b></td>
            <td rowspan="2" align="center"><input type="checkbox" <?php if (REQ('w_static')) echo "checked=''"; ?> value="yes" name="w_static"></td>
        </tr>

        <tr>
            <td style="padding-left:10px;">
                <i>if Yes, the news will be displayed but will not show the full story and comment pages when requested. useful for
                <a href="#" onclick="<?php echo cn_snippet_open_win(PHP_SELF.'?mod=help&section=multiple_includes'); ?>">multiple includes</a>.</i>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="border-top:1px solid gray; text-align: right; padding: 10px;">
                <input type="submit" style="font-weight:bold;" value="Proceed to Integration &gt;&gt;">
            </td>
        </tr>

    </table>

<?php } elseif ($sub == 'rss') { ?>

    <div style="padding: 8px; color: #555555;">
        <p>Rich Site Summary (sometimes referred to as Really Simple Syndication);</p>
        <p>RSS allows a web developer to share the content on his/her site. RSS repackages the web content as a list of data items, to which you can subscribe from a directory of RSS publishers.</p>
        <p>RSS 'feeds' can be read with a web browser or special RSS reader called a content aggregator.</p>
    </div>

    <?php if (request_type('POST')) { // Show form

        $rss_clause = array();
        if (REQ('rss_category') && !REQ('rss_allcategory')) $rss_clause[] = 'category='.join(',', REQ('rss_category'));
        if (REQ('rss_number')) $rss_clause[] = 'number=' . intval( REQ('rss_number') );
        $rss_clause = join('&amp;', $rss_clause);
        if ($rss_clause) $rss_clause = "?$rss_clause";
    ?>
        <div class="notice-form">
            <div style="float: right"><a target="_blank" title="RSS Feed" href="<?php echo getoption('http_script_dir'); ?>/rss.php<?php echo $rss_clause; ?>"><img src="/skins/images/rss_icon.gif" border=0/></a></div>
            <h2>Generated html-code for site</h2>

                <pre>&lt;a title="RSS Feed" href="<?php echo getoption('http_script_dir'); ?>/rss.php<?php echo $rss_clause; ?>"&gt;
    &lt;img src="<?php echo getoption('http_script_dir'); ?>/skins/images/rss_icon.gif" border=0 /&gt;
&lt;/a&gt;</pre>

        </div>
    <?php } ?>

    <table class="std-table" width="100%" >
        <tr>
            <td width=100% bgcolor="#F7F6F4">&nbsp;<b>URL of the page where you include your news</b><br>&nbsp;<i>example: http://mysite.com/news.php</i><br>&nbsp;<i>or: /example2.php</i></td>
            <td bgcolor="#F7F6F4" colspan=2><input name="rss_news_include_url" value="<?php echo cn_htmlspecialchars($rss_news_include_url); ?>" type=text size=30>
        <tr>
            <td>Title of the RSS feed</td>
            <td colspan=2><input name="rss_title" value="<?php echo cn_htmlspecialchars($rss_title); ?>" size=30></td>
        </tr>
        <tr>
            <td bgcolor="#F7F6F4">Character Encoding (default: <i>UTF-8</i>)</td>
            <td bgcolor="#F7F6F4" colspan=2 ><input name="rss_encoding" value="<?php echo cn_htmlspecialchars($rss_encoding); ?>" size=20></td>
        </tr>
        <tr>
            <td>Language (default: <i>en-us</i>)</td>
            <td><input name="rss_language" value="<?php echo cn_htmlspecialchars($rss_language); ?>" size=5></td>
        </tr>
        <tr>
            <td bgcolor="#F7F6F4">Number of articles to be shown in the RSS (default:10):</td>
            <td bgcolor="#F7F6F4"><input id=number size=5 type="text" size="20" name="rss_number" value="<?php echo cn_htmlspecialchars(REQ('rss_number')); ?>"></td>
        </tr>

        <tr>
            <td valign="top"><b>Show articles only from these categories:</b></td>
            <td align="center" rowspan="2">

                <?php if (REQ('rss_allcategory')) { ?>

                    <select <?php if (REQ('rss_allcategory')) echo 'style="display: none;"'; else echo 'style="display: block;"'; ?> name="rss_category[]" id="category" multiple="">
                        <?php if (is_array($categories)) foreach ($categories as $id => $cat) { ?>
                            <option <?php if (is_array(REQ('rss_category')) && in_array($id, REQ('rss_category'))) echo 'selected="selected"'; ?> value="<?php echo $id; ?>">(ID:<?php echo $id; ?>) <?php echo $cat['name']; ?></option>
                        <?php } ?>
                    </select>

                    <br>
                    <label for="allcategory">
                        <input id="allcategory" <?php if (REQ('rss_allcategory')) echo "checked=''"; ?> onclick="if (this.checked) { getElementById('category').style.display='none';} else {getElementById('category').style.display='';}" type="checkbox" value="yes" name="rss_allcategory"> Or show from all allowed categories
                    </label>

                <?php } else { ?><em>All</em><?php } ?>


            </td>
        </tr>
        <tr>
            <td style="padding-left:10px">
                <i>you can specify only from which categories news will be displayed, hold CTRL to select multiple categories (if any)</i>
            </td>
        </tr>

        <tr>
            <td colspan="3" bgcolor="#e0ffe0" style="padding: 10px;">After you have selected your preferred settings, click the 'Generate HTML
                Code' button and you are ready to insert this code into your page. The
                generated code will be of a linked RSS image that will be pointing to your
                RSS feed (<b>rss.php</b>).</td>
        </tr>

        <tr>
            <td colspan="2" style="border-top:1px solid gray; text-align: right; padding: 10px;">
                <input type="submit" style="font-weight:bold;" value="Save &amp; Proceed &gt;&gt;">
            </td>
        </tr>

    </table>

<?php } ?>

</form>