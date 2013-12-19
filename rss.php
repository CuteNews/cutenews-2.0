<?php

require_once('core/init.php');

// plugin tells us: he is fork, stop
if ( hook('fork_rss', false) ) return;

$rss = getoption('#rss');
if (empty($rss)) die (proc_tpl('help/manual/rss') );

$config_http_script_dir = getoption('http_script_dir');
$rss_encoding           = $rss['encoding'];
$rss_news_include_url   = $rss['news_include_url'];
$rss_title              = $rss['title'];
$rss_language           = $rss['language'];

// -------
header("Content-type: text/xml", true);

echo "<?xml version=\"1.0\" encoding=\"$rss_encoding\" ?>
<?xml-stylesheet type=\"text/css\" href=\"$config_http_script_dir/skins/rss_style.css\" ?>
<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
<channel>
<title>$rss_title</title>
<link>$rss_news_include_url</link>
<language>$rss_language</language>
<description></description>
<!-- <docs>This is an RSS 2.0 file intended to be viewed in a newsreader or syndicated to another site. For more information on RSS check: http://www.feedburner.com/fb/a/aboutrss</docs> -->
<generator>CuteNews</generator>
<atom:link href=\"".$config_http_script_dir."/rss.php\" rel=\"self\" type=\"application/rss+xml\" />";

// ---------------------------------------------------------------------------------------------------------------------
if (isset($_GET['number'])) $number = intval($_GET['number']); else $number = 15;
if (isset($_GET['only_active']) && $_GET['only_active']) $only_active = $_GET['only_active']; else $only_active = true;

$template = 'rss';
include 'show_news.php';

echo '</channel></rss>';
