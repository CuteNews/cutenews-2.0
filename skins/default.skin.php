<?PHP

global $skin_header, $skin_menu, $skin_footer, $skin_prefix;

$skin_prefix = "";

// ********************************************************************************
// Skin MENU
// ********************************************************************************

$skin_menu = cn_get_menu() . '<div style="clear:both;"></div>';

// ********************************************************************************
// Skin HEADER
// ********************************************************************************
$skin_header = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta name="robots" content="noindex" />
    <link rel="shortcut icon" type="image/ico" href="skins/images/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="skins/default.css">

    <title>{title}</title>
    <script type="text/javascript" src="skins/cute.js"></script>
    {CustomJS}
    <style type="text/css"><!-- {CustomStyle} --></style>
</head>

<body>

<div style="width: 800px; margin: 16px auto 8px auto;" id="wrapper">

    <div class="header-text">{header-text}</div>
    <div class="header-time">{header-time}</div>
    <div class="navigation">{menu}</div>
    <div id="contents">

        <div class="breadcrumbs">{breadcrumbs}</div>

HTML;

// ********************************************************************************
// Skin FOOTER
// ********************************************************************************
$skin_footer = <<<HTML
        <div style="clear:both;"></div>
    </div>
</div>
<div style="text-align: center;"><span style="color: #888888; font-size: 10px;">Execution time: {exec-time} s.</span>{copyrights}</div>
</body></html>
HTML;

?>
