<?php

list($version, $sample_id, $codepage, $preview_html, $old_dir) = _GL('version, sample_id, codepage, preview_html, old_dir');

cn_snippet_messages();

?>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod', 'opt'); ?>

    <table>
        <tr>
            <td align="right">Migrate from <span class="required">*</span></td>
            <td>
                <select name="version">
                    <option>1.5.x</option>
                    <option <?php if ($version == '1.4.x') echo 'selected'; ?>>1.4.x</option>
                </select>
            </td>
        </tr>

        <tr>
            <td align="right">Path to old CN dir <span class="required">*</span></td>
            <td><input type="text" style="width: 350px;" name="old_dir" value="<?php echo cn_htmlspecialchars($old_dir); ?>"/></td>
        </tr>

        <tr>
            <td align="right">News codepage [<a href="#" title="You may test codepage convert via specify ID from old news db (see &id=<ID> in browser path), and click to button 'Preview'" onclick="return(tiny_msg(this));">?</a>]</td>
            <td>
                <select name="codepage">
                    <option value="">UTF-8</option>
                    <option <?php if ($codepage == 'cp1251') echo 'selected'; ?> value="cp1251">Windows-1251</option>
                    <option <?php if ($codepage == 'iso-8859‑1') echo 'selected'; ?> value="iso-8859‑1">ISO 8859‑1</option>
                    <option <?php if ($codepage == 'cp1250') echo 'selected'; ?> value="cp1250">Windows-1250</option>
                    <option <?php if ($codepage == 'cp1252') echo 'selected'; ?> value="cp1252">Windows-1252</option>
                    <option <?php if ($codepage == 'cp1255') echo 'selected'; ?> value="cp1255">Windows-1255</option>
                    <option <?php if ($codepage == 'cp1256') echo 'selected'; ?> value="cp1256">Windows-1256</option>
                    <option <?php if ($codepage == 'iso-8859‑2') echo 'selected'; ?> value="iso-8859‑2">ISO 8859‑2</option>
                    <option <?php if ($codepage == 'iso-8859‑3') echo 'selected'; ?> value="iso-8859‑3">ISO 8859‑3</option>
                    <option <?php if ($codepage == 'iso-8859‑4') echo 'selected'; ?> value="iso-8859‑4">ISO 8859‑4</option>
                    <option <?php if ($codepage == 'iso-8859‑5') echo 'selected'; ?> value="iso-8859‑5">ISO 8859‑5</option>
                    <option <?php if ($codepage == 'iso-8859‑6') echo 'selected'; ?> value="iso-8859‑6">ISO 8859‑6</option>
                    <option <?php if ($codepage == 'iso-8859‑7') echo 'selected'; ?> value="iso-8859‑7">ISO 8859‑7</option>
                    <option <?php if ($codepage == 'iso-8859‑8') echo 'selected'; ?> value="iso-8859‑8">ISO 8859‑8</option>
                    <option <?php if ($codepage == 'iso-8859‑9') echo 'selected'; ?> value="iso-8859‑9">ISO 8859‑9</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑10">ISO 8859‑10</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑11">ISO 8859‑11</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑12">ISO 8859‑12</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑13">ISO 8859‑13</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑14">ISO 8859‑14</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑15">ISO 8859‑15</option>
                    <option <?php if ($codepage == 'iso-8859‑10') echo 'selected'; ?> value="iso-8859‑16">ISO 8859‑16</option>
                    <option <?php if ($codepage == 'koi8-r') echo 'selected'; ?> value="koi8-r">KOI8-R</option>
                    <option <?php if ($codepage == 'koi8-u') echo 'selected'; ?> value="koi8-u">KOI8-U</option>
                </select>

                Old ID <input type="text" name="sample_id" value="<?php echo cn_htmlspecialchars($sample_id); ?>" />
            </td>
        </tr>

        <tr><td style="text-align: right;"><input type="checkbox" name="conv[users]" value="Y" /></td><td>Convert users (<b>first</b>)</td></tr>
        <tr><td style="text-align: right;"><input type="checkbox" name="conv[news]" value="Y" /></td><td>Convert all news, comments, more fields</td></tr>
        <tr><td style="text-align: right;"><input type="checkbox" name="conv[archives]" value="Y" /></td><td>Convert archives</td></tr>
        <tr><td style="text-align: right;"><input type="checkbox" name="conv[sc]" value="Y" /></td><td>Convert sysconf (configs, category, ipban, templates, replace words)</td></tr>

        <tr>
            <td align="right"><input type="submit" name="convert" value="Do convert" /></td>
            <td><button name="preview">Preview</button></td>
        </tr>
    </table>

    <hr/>
    <?php if ($preview_html) { ?><h3>Preview. Check for correct codepage</h3> <div style="border: 1px dotted #000; padding: 8px;"><?php echo strip_tags($preview_html); ?></div><?php } ?>

</form>