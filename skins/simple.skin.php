<?PHP

global $skin_header, $skin_menu, $skin_footer, $skin_prefix;

$skin_prefix = "";
// ********************************************************************************
// Skin MENU
// ********************************************************************************
$skin_menu = cn_get_menu() . '<div style="clear:both;"></div>';

// ***********
// Skin HEADER
// ***********
$skin_header = <<<HTML
<html>
<head>
<title>CuteNews</title>
<meta name="robots" content="noindex" />
<link rel="shortcut icon" type="image/ico" href="skins/images/favicon.ico"/>
<script type="text/javascript" src="skins/cute.js"></script>
{CustomJS}
<style type='text/css'>
<!--
select, option, textarea, input {
 BORDER: #808080 1px solid;
 COLOR: #000000;
 FONT-SIZE: 11px;
 FONT-FAMILY: Verdana, Arial;
 BACKGROUND-COLOR: #ffffff
}

input[type=submit]:hover, input[type=button]:hover{
background-color:#EBEBEB !important;
}

a:active,a:visited,a:link {color: #446488; text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt;}
a:hover {color: #00004F; text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; }
a.nav:active, a.nav:visited,  a.nav:link { color: #000000; font-size : 10px; font-weight: bold; font-family: Verdana, Arial, Helvetica; text-decoration: none;}
a.nav:hover { font-size : 10px; font-weight: bold; color: black; font-family: Verdana, Arial, Helvetica; text-decoration: underline; }
.bborder        { background-color: #FFFFFF; }
.panel                { border-radius: 8px; border: 1px dotted silver; background-color: #F7F6F4; padding: 4px;}
BODY, TD, TR {text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; cursor: default;}
.nav { padding: 4px 8px; }
{CustomStyle}
-->
</style>
</head>
<body bgcolor=white style="width: 685px; margin: 10px auto;">
<table border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="bborder" bgcolor="#FFFFFF" >
<table border=0 cellpadding=0 cellspacing=0 bgcolor="#ffffff" width="685" >
<tr>
<td bgcolor="#F7F6F4" align="center" height="24" style="-moz-border-radius: 3em 3em 0em 0em; border-left: 1px transparent; border-top: 1px transparent; border-right: 1px transparent; border-bottom: #808080 1px solid;">
<table cellpadding=5 cellspacing=0 border=0>
<tr>
 <td>
{menu}
 </td>
</tr>
</table>
</td>
</tr>
<tr>
<td height="19">

<!--SELF-->
<table border=0 cellpadding=0 cellspacing=15 width="100%" height="100%" >
<tr>
<td width="100%" height="100%" >
<!--MAIN area-->
HTML;
// ***********
// Skin FOOTER
// ***********
$skin_footer = <<<HTML
<!--/MAIN area-->
</tr>
</table>
</td>
</tr>
<tr >
<td bgcolor="#F7F6F4" height="24" align="center" style="-moz-border-radius: .0em .0em 3em 3em; border-left: 1px transparent; border-bottom: 1px transparent; border-right: 1px transparent; border-top: 1px solid #808080; ">
{copyrights}
</td>
</tr>
</center>
</table></td></tr></table>
&nbsp;
</body></html>

HTML;
?>