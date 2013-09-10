<?php

    list($target_id) = GET('target_id');
    if ($target_id == '') die('No target ID');

?>
<html>
<head>
    <title>Cute codes</title>
    <script type="text/javascript" src="skins/cute.js"></script>
    <style type="text/css">
        <!--
        body, td { text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; background: white; }
        a:active,a:visited,a:link {color: #446488; text-decoration: none; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt;}
        a:hover {font-size : 8pt; color: #000000; font-family: Verdana; text-decoration: none; }
        table.spanned span { color: #808080; }
        table.spanned td { height: 21px; }
        table.spanned td.row { background: #F7F6F4; }
        -->
    </style>
</head>

<body>
<script type="text/javascript">
    <!--
    function insertcode(type, var1)
    {
        var code = '';
        var has_wrap = false;

        if (var1 != null)
        {
            switch (type)
            {
                case 'link':

                    has_wrap = var1;
                    code = '[link=' + var1 + ']' + var1 + '[/link]';
                    break;

                case 'image':

                    code = '[image=' + var1 + ']';
                    break;

                case 'color':

                    has_wrap = var1;
                    code = '[color=' + var1 + ']   [/color]';
                    break;

                case 'size':

                    has_wrap = var1;
                    code = '[size=' + var1 + ']   [/size]';
                    break;

                case 'font':

                    has_wrap = var1;
                    code = '[font=' + var1 + ']   [/font]';
                    break;

                case 'align':

                    has_wrap = var1;
                    code = '[align=' + var1 + ']   [/align]';
                    break;

                case 'youtube':

                    code = '[youtube]' + var1 + '[/youtube]';
                    break;

                case 'list':

                    code = "[list]\n[*]Text1\n[*]Text2\n[*]Text3\n[/list]\n";
                    alert('Sample List will be inserted into the textarea');
                    break;

                default:
            }

            var wrp = false;
            if (has_wrap !== false)
                wrp = bb_wrap('<?php echo $target_id; ?>', type, opener.document, has_wrap);

            if (wrp === false)
            {
                opener.document.getElementById('<?php echo $target_id; ?>').value += code;
            }

            if (document.getElementById('ifclose').checked == true)
            {
                opener.document.getElementById('<?php echo $target_id; ?>').focus();
                window.close();
                opener.document.getElementById('<?php echo $target_id; ?>').focus();
            }
        }
    }
    //-->
</script>


<h3><b>QuickTags</b></h3>
<table class="spanned">

    <tr class="row">
        <td> <a href="javascript:insertcode('link', prompt('Enter the complete URL of the hyperlink', 'http://') )">Insert Link</a> </td>
        <td> [link=<span>URL</span>]<span>Text</span>[/link]</td>
    </tr>

    <tr>
        <td><a href="javascript:insertcode('image', prompt('Enter URL of the Image:', 'http://') )">Insert Image</a></td>
        <td>[image=<span>URL</span>]</td>
    </tr>

    <tr>
        <td><a href="javascript:insertcode('list', 'none' )">Insert List</a></td>
        <td>[list]<span>[*]Text1[*]Text2</span>[/list]</td>
    </tr>

    <tr>
        <td><a href="javascript:insertcode('color', prompt('Enter color of the text (blue, red, green, fuchsia)', '') )">Text Color</a></td>
        <td>[color=<span>COLOR</span>]<span>Text</span>[/color]</td>
    </tr>

    <tr class="row">
        <td><a href="javascript:insertcode('size', prompt('Enter size of the text (in points format)',''))">Text Size</a></td>
        <td>[size=<span>SIZE</span>]<span>Text</span>[/size]</td>
    </tr>

    <tr>
        <td><a href="javascript:insertcode('font', prompt('Enter font of the text (verdana, arial, times, courier)','') )">Text Font</a></td>
        <td>[font=<span>FONT</span>]<span>Text</span>[/font]</td>
    </tr>

    <tr class="row">
        <td><a href="javascript:insertcode('align', prompt('Enter align of the text (right, left, center, justify)','') )">Text Align</a></td>
        <td>[align=<span>ALIGN</span>]<span>Text</span>[/align]</td>
    </tr>

    <tr class="row">
        <td><a href="javascript:insertcode('youtube', prompt('Youtube link',''), '' )">Youtube embed</a></td>
        <td>[youtube]<span>URL</span>[/youtube]</td>
    </tr>

</table>
<p><input type="checkbox" id="ifclose" checked="checked"/> Close this window after I insert code</p>
</body>
</html>