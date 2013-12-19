<?PHP

global $skin_header, $skin_menu, $skin_footer, $skin_prefix;

$skin_prefix = "";
// *********
// Skin MENU
// *********
$skin_menu = cn_get_menu() . '<div style="clear:both;"></div>';

// *******************
//  Template -> Header
// *******************
$skin_header = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta name="robots" content="noindex" />
<link rel="shortcut icon" type="image/ico" href="skins/images/favicon.ico"/>
<script type="text/javascript" src="skins/cute.js"></script>
{CustomJS}
<style type="text/css">
<!--
SELECT, option, textarea, input {
BORDER: #000000 1px solid;
COLOR: #000000;
FONT-SIZE: 11px;
FONT-FAMILY: Verdana; BACKGROUND-COLOR: #ffffff
}
BODY {text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt;}
TD {text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt;}
a:active,a:visited,a:link {color: #446488; text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt;}
a:hover {color: #00004F; text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt;}

a.nav {  padding:2px;}
a.nav:active, a.nav:visited,  a.nav:link { color: #000000; font-size : 10px; font-weight: bold; font-family: Verdana, Arial, Helvetica; text-decoration: none;}
a.nav:hover { font-size : 10px; font-weight: bold; color: black; font-family: Verdana, Arial, Helvetica; background-color:000000; color:FFFFFF}

.bborder        { background-color: #FFFFFF; border: 1px #000000 solid; }
.panel                {border-radius: 8px; border: 1px dotted #B4D2E7; background-color: #ECF4F9;padding: 4px;}
BODY, TD, TR {text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; cursor: default;}

input[type=submit]:hover, input[type=button]:hover{
background-color: #E0EDF3 !important;
.nav { padding: 4px 8px; }
}
{CustomStyle}
-->
</style>
<title>CuteNews</title>
</head>
<body bgcolor="#A5CBDE">
<center>
<table width="565" border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="bborder" bgcolor="#FFFFFF" width="777">
<table border=0 cellpadding=0 cellspacing=0 bgcolor="#ffffff" width="645" >
<tr>
<td width="645" align="center">
{menu}
</td>
</tr>
<tr>
<td bgcolor="#000000" width="802" height="1"><img src="skins/images/blank.gif" width=1 height=1></td>
</tr>
<tr><td bgcolor="#FFFFFF" width="802" height="9"><img src="skins/images/blank.gif" width=1 height=5></td></tr>
<tr>
<td width="802" height="42">
</center>
<table border=0 cellpadding=0 cellspacing=10 width="100%" height="100%" >
<tr>
<td width="98%" height="46%">
<!--MAIN area-->
HTML;
// ********************************************************************************
//  Template -> Footer
// ********************************************************************************
$skin_footer=<<<HTML
<!--MAIN area-->
</tr>
</table>
</td>
</tr></table></td></tr></table>
<br /><center>{copyrights}
</body></html>
HTML;
?>