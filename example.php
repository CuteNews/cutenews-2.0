<?php

    require_once("cn_api.php");
	$entry = cn_api_get_entry();

    //$template = 'bootstrap'; // for test

    // Play with settings --------------------------------------------------
    $pw = REQ('pw');

    $theme = 'cosmo'; // default theme

    if (isset($pw['PHP_SELF']) && $pw['PHP_SELF']) $PHP_SELF = $pw['PHP_SELF'];
    if (isset($pw['template']) && $pw['template']) $template = $pw['template'];
    if (isset($pw['theme']) && $pw['theme']) $theme = $pw['theme'];

    if (isset($pw['start_from']) && $pw['start_from']) $start_from = $pw['start_from'];
    if (isset($pw['number']) && $pw['number']) $number = $pw['number'];
    if (isset($pw['archive']) && $pw['archive']) $archive = $pw['archive'];
    if (isset($pw['category']) && $pw['category']) $category = $pw['category'];
    if (isset($pw['ucat']) && $pw['ucat']) $ucat = $pw['ucat'];
    if (isset($pw['sortby']) && $pw['sortby']) $sortby = $pw['sortby'];
    if (isset($pw['dir']) && $pw['dir']) $dir = $pw['dir'];
    if (isset($pw['page_alias']) && $pw['page_alias']) $page_alias = $pw['page_alias'];
    if (isset($pw['tag']) && $pw['tag']) $tag = $pw['tag'];
    if (isset($pw['user_by']) && $pw['user_by']) $user_by = $pw['user_by'];

    if (isset($pw['static']) && $pw['static']) $static = $pw['static'];
    if (isset($pw['reverse']) && $pw['reverse']) $reverse = $pw['reverse'];
    if (isset($pw['only_active']) && $pw['only_active']) $only_active = $pw['only_active'];
    if (isset($pw['no_prev']) && $pw['no_prev']) $no_prev = $pw['no_prev'];
    if (isset($pw['no_next']) && $pw['no_next']) $no_next = $pw['no_next'];
    // ---------------------------------------------------------------------

    if (isset($_GET['do'])&& $_GET['do'] == "rss") include("rss.php");
?>
<html>
<head>
    <title><?php echo ($entry) ? $entry['t'] : 'Example page'; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?php if ($entry) { echo '<meta name="description" content="'. $entry['tg']. '" />'; } ?>

	 <!-- **CSS - stylesheets** -->
	<link href="libs/css/<?php echo $theme;?>.min.css" rel="stylesheet">
    <link href="libs/css/font-awesome.min.css" rel="stylesheet">

	<!-- **JS Javascripts** -->
    <script src="libs/js/jquery.js"></script>
    <script src="libs/js/bootstrap.min.js"></script>

    <script>
        window.onload = function() {
            var edt_comm_mode = document.getElementById('edt_comm_mode');
            if (edt_comm_mode != null) {
                window.scrollTo(0,9999);
            }
        }
    </script>

    <style>
        img { max-width: 100%; }
        td, th { vertical-align: top; padding: 5px; }
    </style>
</head>
<body>

    <section>
        <div class="container">
			 <div class="row">
				<div class="col-sm-5">
					<img src="<?php echo getoption('http_script_dir'); ?>/skins/images/cutenews-logo.png"/>
				</div>

				<div class="col-sm-7">
                    <h1>Put here your title</h1>
                    <div>and this is your description</div>
				</div>
			</div>

        </div>
        <div style="clear: both"></div>

    </section>

   <section id="blog">

        <div class="container">
            <div class="col-sm-8">

                <div class="nav nav-tabs">
                    <?php

                    $_self_page = explode('/', PHP_SELF);
                    $_self_page = $_self_page[count($_self_page)-1];
                    if ($_self_page[0] === '/') { $_self_page = substr($_self_page, 1); }
                    $_self_page = getoption('http_script_dir') . '/' . $_self_page;

                    ?>
                    <div style="float: right"><a href="<?php echo getoption('http_script_dir'); ?>/rss.php"><img src="<?php echo getoption('http_script_dir'); ?>/skins/images/rss_icon.gif" alt="RSS"></a></div>
                    <b>Navigation</b>:
                    <a href="<?php echo $_self_page; ?>">Main page</a> |
                    <a href="<?php echo $_self_page; ?>?do=archives">Archives</a> |
                    <a href="<?php echo $_self_page; ?>?do=rss">RSS</a> |
                    <a href="<?php echo $_self_page; ?>?do=stats">Stats</a> |
                    <a href="#">Link 1</a> |
                    <a href="#">Link 2</a> | ...
                </div>

                <!-- MAIN CONTENT, FIRST -->

                <?php

                /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                Here we decide what page to include
                ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
                if (isset($_GET['search']) && $_GET['search']) {
                    include ("search.php");
                }
                else if (isset($_GET['do']) && $_GET['do'] == 'archives') {
                    include ("show_archives.php");
                }
                else if (isset($_GET['do']) && $_GET['do'] == "stats") {
                    echo "You can download the stats addon and include it here to show how many news, comments ... you have"; // include("$path/stats.php");
                } else {
                    include ("show_news.php");
                }
                ?>

            </div>

            <div class="col-sm-4">

                <!-- SECTION: WIDGETS -->
                <h3>Widgets</h3>
                <div>Plugin widgets there.</div>
                <?php cn_widget('calendar'); ?>
                <br/>

                <!-- SECTION: SEARCH -->
                <h3>Quick search</h3>
                <form action="<?php echo PHP_SELF; ?>" method="GET">
                    <input type="hidden" name="dosearch" value="Y" />
                    <div>
                        <input style="width: 200px; padding: 4px;" class="text" type="text" name="search" value="<?php echo cn_htmlspecialchars(REQ('search')); ?>" />
                        <input style="width: 75px; padding: 4px;"  class="submit" type="submit" value="search it!" />
                    </div>
                </form>

                <!-- SECTION: LIVE EXAMPLE -->
                <h3>Play with parameters</h3>
                <form action="<?php echo PHP_SELF; ?>" method="POST">
                <table>
                    <tr><td>$PHP_SELF</td> <td><input type="text" name="pw[PHP_SELF]" value="<?php echo cn_htmlspecialchars($pw['PHP_SELF'] ? $pw['PHP_SELF'] : PHP_SELF); ?>" /></td></tr>
                    <tr><td>$template</td> <td><input type="text" name="pw[template]" value="<?php echo cn_htmlspecialchars($pw['template'] ? $pw['template'] : 'Default'); ?>" /></td></tr>
                    <tr><td>$start_from</td> <td><input type="text" name="pw[start_from]" value="<?php echo intval($pw['start_from']); ?>" /></td></tr>
                    <tr><td>$number</td> <td><input type="text" name="pw[number]" value="<?php echo intval($pw['number']); ?>" /></td></tr>
                    <tr><td>$archive</td> <td><input type="text" name="pw[archive]" value="<?php echo intval($pw['archive']); ?>" /></td></tr>
                    <tr><td>$user_by</td> <td><input type="text" name="pw[user_by]" value="<?php echo cn_htmlspecialchars($pw['user_by']); ?>" /></td></tr>

                    <tr><td>$category</td> <td><input type="text" name="pw[category]" value="<?php echo cn_htmlspecialchars($pw['category']); ?>" /></td></tr>
                    <tr><td>$ucat</td> <td><input type="text" name="pw[ucat]" value="<?php echo cn_htmlspecialchars($pw['ucat']); ?>" /></td></tr>

                    <tr>
                        <td>$sortby</td> <td><select name="pw[sortby]">
                                <option value="">latest</option>
                                <option <?php if ($pw['sortby'] == 'date') echo 'selected'; ?>>date</option>
                                <option <?php if ($pw['sortby'] == 'title') echo 'selected'; ?>>title</option>
                                <option <?php if ($pw['sortby'] == 'comments') echo 'selected'; ?>>comments</option>
                                <option <?php if ($pw['sortby'] == 'author') echo 'selected'; ?>>author</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>$dir</td> <td>
                            <select name="pw[dir]">
                                <option value="">as is</option>
                                <option <?php if ($pw['dir'] == 'D') echo 'selected'; ?> value="D">desc</option>
                                <option <?php if ($pw['dir'] == 'A') echo 'selected'; ?> value="A">asc</option>
                            </select>
                        </td>
                    </tr>

                    <tr><td>$page_alias</td> <td><input type="text" name="pw[page_alias]" value="<?php echo (isset($pw['page_alias'])?cn_htmlspecialchars($pw['page_alias']):''); ?>"></td></tr>
                    <tr><td>$tag</td> <td><input type="text" name="pw[tag]" value="<?php echo (isset($pw['tag'])?cn_htmlspecialchars($pw['tag']):''); ?>"></td></tr>

                    <tr><td>$static</td> <td><input type="checkbox" name="pw[static]" value="Y" <?php if (isset($pw['static'])&&$pw['static']) echo ' checked="checked" '; ?> /></td></tr>
                    <tr><td>$reverse</td> <td><input type="checkbox" name="pw[reverse]" value="Y" <?php if (isset($pw['reverse'])&&$pw['reverse']) echo ' checked="checked" '; ?> /></td></tr>
                    <tr><td>$only_active</td> <td><input type="checkbox" name="pw[only_active]" value="Y" <?php if (isset($pw['only_active'])&&$pw['only_active']) echo ' checked="checked" '; ?> /></td></tr>
                    <tr><td>$no_prev</td> <td><input type="checkbox" name="pw[no_prev]" value="Y" <?php if (isset($pw['no_prev'])&&$pw['no_prev']) echo ' checked="checked" '; ?> /></td></tr>
                    <tr><td>$no_next</td> <td><input type="checkbox" name="pw[no_next]" value="Y" <?php if (isset($pw['no_next'])&&$pw['no_next']) echo ' checked="checked" '; ?> /></td></tr>
                    <tr><td>&nbsp;</td><td><input type="submit" value="Check result" /><td></tr>
                </table>
                </form>
                <br/>

                <!-- SECTION: BANNER -->
                <h3>Banners / Sponsors</h3>
                <div>put some banners here and another banners here</div>
                <br/>

                <!-- SECTION: FRIENDS -->
                <h3>Friends</h3>
                <div><a target="_blank" href="http://cutephp.com">CutePHP Scripts</a></div>
                <div><a target="_blank" href="http://news.google.com">Google News</a></div>
                <div><a target="_blank" href="http://mozilla.org">Mozilla.org</a></div>

            </div>
        </div>


    </section>

    <div id="footer"> &copy; your site, put your footer and copyright here </div>

</body>
</html>